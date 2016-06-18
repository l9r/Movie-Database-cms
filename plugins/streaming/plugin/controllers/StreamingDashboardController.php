<?php

class StreamingDashboardController extends DashboardController {

	public function __construct()
	{
		$dashboard = App::make('Lib\Repositories\Dashboard\DashboardRepositoryInterface');
		$validator = App::make('Lib\Services\Validation\DashboardValidator');
		$scraper   = App::make('Lib\Services\Scraping\Scraper');

		parent::__construct($dashboard, $validator, $scraper);
	}

	/**
	 * Links page.
	 * 
	 * @return View
	 */
	public function getLinks()
	{
		return View::make('Dashboard.Links');
	}

}