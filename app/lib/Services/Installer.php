<?php namespace Lib\Services;

use DB;
use App;
use Sentry;
use Config;
use Artisan;
use Exception;

class Installer {

	/**
	 * PHP Extensions and their expected state
	 * (enabled, disabled) in order for this 
	 * app to work properly.
	 * 
	 * @var array
	 */
	private $extensions = array(
		array('name' => 'fileinfo', 'expected' => true),
		array('name' => 'mbstring', 'expected' => true),
		array('name' => 'pdo', 'expected' => true),
		array('name' => 'pdo_mysql', 'expected' => true),
		array('name' => 'gd', 'expected' => true),
		array('name' => 'Mcrypt', 'expected' => true),
		array('name' => 'mysql_real_escape_string', 'expected' => false),
		array('name' => 'curl', 'expected' => true),
	);

	/**
	 * Directories that need to be writable.
	 * 
	 * @var array
	 */
	private $dirs = array('/uploads', '/uploads/images', '/uploads/avatars', 'imdb/bgs', 'imdb/cast', 'imdb/stills', 'imdb/episodes', 'imdb/posters');

	/**
	 * Fully qualified path to assets directory.
	 * 
	 * @var string
	 */
	private $assetPath;

	/**
	 * Symfony filesystem instance.
	 * 
	 * @var Symfony\Component\Filesystem\Filesystem
	 */
	private $files;

	/**
	 * Holds the compatability check results.
	 * 
	 * @var array
	 */
	private $compatResults = array();

	/**
	 * Check for any issues with the server.
	 * 
	 * @return array
	 */
	public function checkForIssues()
	{
		$this->compatResults['extensions'] = $this->checkExtensions();
		$this->compatResults['folders']    = $this->checkFolders();
		$this->compatResults['phpVersion'] = $this->checkPhpVersion();

		return $this->compatResults;
	}

	/**
	 * Check if we've got required php version.
	 * 
	 * @return integer
	 */
	public function checkPhpVersion()
	{
		return version_compare(PHP_VERSION, '5.3.7');
	}

	/**
	 * Check if required folders are writable.
	 * 
	 * @return array
	 */
	public function checkFolders()
	{
		$checked = array();
		$problem = false;

		$fs = App::make('Illuminate\Filesystem\Filesystem');
		$this->files = App::make('Symfony\Component\Filesystem\Filesystem');
		$this->assetPath = public_path('assets');

		//push all the directories in storage folder to dirs
		//array so we check if they are writable as well
		foreach ($fs->directories(storage_path()) as $sdir)
		{
			$this->dirs[] = $sdir;
		}

		foreach ($this->dirs as $dir)
		{
		 	if (str_contains($dir, 'imdb')) {
		 		$path = public_path($dir);
		 	} else {
		 		$path = str_contains($dir, 'storage') ? $dir : $this->assetPath . $dir;
		 	}

		 	//if direcotry is not writable attempt to chmod it now
		 	if ( ! is_writable($path))
		 	{
		 		try {
		 			$this->files->chmod($path, 0777, 0000, true);
		 		} catch (Exception $e){}
		 	}

		 	$writable = is_writable($path);

		 	$checked[] = array('path' => $path, 'writable' => $writable);

		 	if ( ! $problem) {
		 		$problem = $writable ? false : true;
		 	}
		}

		//make a notice if there was a problem or not with folders if
		//there wasn't a problem with extensions already
		if ( ! array_key_exists('problem', $this->compatResults) || ! $this->compatResults['problem'])
		{
			$this->compatResults['problem'] = $problem;
		}

		return $checked;
	}

	/**
	 * Check for any issues with php extensions.
	 * 
	 * @return array
	 */
	private function checkExtensions()
	{
		$problem = false;

		foreach ($this->extensions as &$ext)
		{
			$loaded = extension_loaded($ext['name']);

			//make notice if any extensions status
			//doesn't match what we need
			if ($loaded !== $ext['expected'])
			{
				$problem = true;
			}

			$ext['actual'] = $loaded;
		}

		$this->compatResults['problem'] = $problem;

		return $this->extensions;
	}

	/**
	 * Store admin account and basic details in db.
	 * 
	 * @param  array  $input
	 * @return void
	 */
	public function storeBasics(array $input)
	{
		//create admin account
		$input['activated'] = 1;
		$input['permissions'] = array('superuser' => 1);

		Sentry::createUser(array_except($input, array('site_name', 'site_description')));
	}

	/**
	 * Finalize the installation.
	 * 
	 * @param  array  $input
	 * @return void
	 */
	public function finalize(array $input)
	{
		//seed some stuff into db (slides, menus, installed flag etc)
		try {
			Artisan::call('db:seed');
		} catch (Exception $e) {}
	}

	/**
	 * Create database chema.
	 * 
	 * @param  array  $input
	 * @return void
	 */
	public function createSchema(array $input)
	{
		//we'll skip inserting db credentials if user has done
		//it manually already
		if ( ! isset($input['filledManually']))
		{
			$this->insertCredentials($input);

			foreach ($input as $name => $value) {
				Config::set('database.connections.mysql.'.$name, $value);
			}

		    DB::reconnect('mysql');
		}

		//create database schema
		//catch an error incase migrate table already exists
		try {
			Artisan::call('migrate:install');
		} catch (Exception $e) {}
		
		Artisan::call('migrate');
	}

	/**
	 * Insert user supplied db credentials into config file.
	 * 
	 * @param  array  $input
	 * @return void
	 */
	private function insertCredentials(array $input)
	{
		$fs = App::make('Illuminate\Filesystem\Filesystem');

		$config = $fs->get(app_path('config/database.php'));

		//replace database credentials with user supplied ones
		foreach ($input as $key => $value)
		{	
			$config = preg_replace("/(\/\/##.+?$key.+?=>.').*?(\',)/ms", '${1}'.$value.'${2}', $config);
		}
		
		//put new credentials in a config file
		$fs->put(app_path('config/database.php'), $config);
	}
}