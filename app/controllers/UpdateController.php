<?php

class UpdateController extends BaseController
{
	public function __construct()
	{
		$this->beforeFilter('updated');
	}

	/**
	 * Shows index install view.
	 * 
	 * @return View
	 */
	public function update()
	{
		return View::make('Install.UpdateSchema');	
	}

	/**
	 * Creates database schema.
	 * 
	 * @return Redirect
	 */
	public function updateSchema()
	{
		ini_set('max_execution_time', 0);

		//create database schema
		Artisan::call('migrate');

		try {
			DB::table('options')->insert(array(
            	array('name' => 'menus', 'value' => '[{"name":"TopMenu","position":"header","active":"1","items":[{"label":"Movies","action":"movies.index","weight":"1","type":"route","children":[],"visibility":"everyone"},{"label":"Series","action":"series.index","weight":"2","type":"route","children":[],"visibility":"everyone"},{"label":"News","action":"news.index","weight":"3","type":"route","children":[],"visibility":"everyone"},{"label":"People","action":"people.index","weight":"4","type":"route","children":[],"visibility":"everyone"},{"label":"Dashboard","action":"dashboard","weight":"5","type":"route","children":[],"visibility":"admin"}]},{"name":"FooterMenu","position":"footer","active":"1","items":[{"label":"Privacy Policy","action":"privacy-policy","weight":1,"type":"page","children":[],"visibility":"everyone"},{"label":"Terms of Service","action":"tos","weight":"2","type":"page","children":[],"visibility":"everyone"}]}]'),
        	));
		} catch (Exception $e) {
			//
		}

		return Redirect::to('/')->withSuccess('Updated succesfully!');
	}
}