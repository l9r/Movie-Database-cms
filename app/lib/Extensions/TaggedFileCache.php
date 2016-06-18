<?php namespace Lib\Extensions;

use App, Closure, Exception;
use Illuminate\Cache\FileStore;
use Illuminate\Cache\StoreInterface;

class TaggedFileCache extends FileStore implements StoreInterface {

	/**
	 * The tag names.
	 * 
	 * @var array
	 */
	protected $tags = array();

	/**
	 * Create new taggedFileCache instance.
	 */
	function __construct()
	{
		$file = App::make('Illuminate\Filesystem\Filesystem');
		parent::__construct($file, storage_path('cache'));
	}

	/**
	 * Set given tags on the instance.
	 * 
	 * @param  mixed $names
	 * @return self
	 */
	public function tags($names)
	{
		$this->tags = is_array($names) ? $names : func_get_args();

		return $this;
	}

	/**
	 * Remove all items from the cache.
	 *
	 * @return void
	 */
	public function flush()
	{
		//make sure we don't error out if the directory doesn't exist
		try {
			foreach ($this->files->directories($this->directory()) as $directory)
			{
				$this->files->deleteDirectory($directory);
			}
		} catch (Exception $e) {
			//
		}

		$this->tags = array();
	}

	/**
	 * Determine if an item exists in the cache.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	public function has($key)
	{
		return ! is_null($this->get($key));
	}

	/**
	 * Get an item from the cache, or store the default value.
	 *
	 * @param  string   $key
	 * @param  int      $minutes
	 * @param  Closure  $callback
	 * @return mixed
	 */
	public function remember($key, $minutes, \Closure $callback)
	{
		
		// If the item exists in the cache we will just return this immediately
		// otherwise we will execute the given Closure and cache the result
		// of that execution for the given number of minutes in storage.
		if ($this->has($key)) return $this->get($key);

		$this->put($key, $value = $callback(), $minutes);

		$this->tags = array();

		return $value;
	}

	/**
	 * Get an item from the cache, or store the default value forever.
	 *
	 * @param  string   $key
	 * @param  Closure  $callback
	 * @return mixed
	 */
	public function rememberForever($key, Closure $callback)
	{
		// If the item exists in the cache we will just return this immediately
		// otherwise we will execute the given Closure and cache the result
		// of that execution for the given number of minutes. It's easy.
		if ($this->has($key)) return $this->get($key);

		$this->forever($key, $value = $callback());

		$this->tags = array();

		return $value;
	}

	/**
	 * Get the full path for the given cache key.
	 *
	 * @param  string  $key
	 * @return string
	 */
	protected function path($key)
	{
		$parts = array_slice(str_split($hash = md5($key), 2), 0, 2);
		$path  = $this->directory() . '/'.join('/', $parts).'/'.$hash;
		
		//unset the tags so we use the base cache folder if no
		//tags are passed with subsequent call to the same instance
		//of this class
		//$this->tags = array();

		return $path;
	}

	/**
	 * Return cache directory or cache tag
	 * directory if any tags are set on this instance.
	 * 
	 * @return string
	 */
	protected function directory()
	{
		$dir = $this->directory;

		if ( ! empty($this->tags))
		{
			foreach ($this->tags as $tag)
			{
				$dir .= '/' . $tag;
			}
		}

		return $dir;
	}
}