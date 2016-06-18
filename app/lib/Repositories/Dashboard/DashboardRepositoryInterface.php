<?php namespace Lib\Repositories\Dashboard;

interface DashboardRepositoryInterface
{
	/**
	 * Renders mini profile view of a given user.
	 * 
	 * @param  array $input
	 * @return string
	 */
	public function makeMiniProfile($input);
	
	/**
	 * Updates options in db.
	 * 
	 * @param  array  $options
	 * @return void
	 */
	public function updateOptions(array $options);
}