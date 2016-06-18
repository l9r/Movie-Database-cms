<?php namespace Lib\Services\Search;

interface SearchProviderInterface
{
	/**
	 * Performs search by users query.
	 *
	 * @param  string $query
	 * @return View
	 */
	public function byQuery($query);

}