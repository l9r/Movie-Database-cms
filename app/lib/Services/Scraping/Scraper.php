<?php namespace Lib\Services\Scraping;

use Helpers, DB, Title, Request, Exception;
use Lib\Services\Scraping\NewsScraper as News;
use Lib\Services\Scraping\BatchScraper as Batch;

class Scraper extends Curl
{
	/**
	 * BatchScraper instance.
	 * 
	 * @var Lib\Services\Scraping\BatchScraper;
	 */
	private $batchScraper;

	/**
	 * NewsScraper instance.
	 * 
	 * @var Lib\Services\Scraping\NewsScraper
	 */
	private $newsScraper;

	public function __construct(Batch $batchScraper, News $newsScraper)
	{
		$this->newsScraper = $newsScraper;
		$this->batchScraper = $batchScraper;
	}

	/**
	 * Scrapes titles from imdb advanced search.
	 * 
	 * @param  array  $input
	 * @return mixed
	 */
	public function imdbAdvanced(array $input)
	{
		return $this->batchScraper->imdbAdvanced($input);
	}

	/**
	 * Fetches titles using tmdb api discover query.
	 * 
	 * @param  array $input
	 * @return int how much titles scraped
	 */
	public function tmdbDiscover(array $input)
	{
		return $this->batchScraper->tmdbDiscover($input);
	}

	/**
	 * Fetches and saves now playing movies.
	 * 
	 * @return void
	 */
	public function updateNowPlaying()
	{
		$this->batchScraper->nowPlaying();
	}

	/**
	 * Fetches and saves all available information about titles,
	 * that arent fully scraped in database.
	 * 
	 * @param  int/string $input
	 * @return int/string
	 */
	public function inDb($input)
	{
		return $this->batchScraper->inDb($input);
	}

	/**
	 * Fetches featured trailers and their titles data.
	 * 
	 * @return voids
	 */
	public function featured()
	{
		$this->batchScraper->featured();
	}

	/**
	 * Updates news from external sources.
	 * 
	 * @return void
	 */
	public function updateNews()
	{
		$this->newsScraper->all();
	}
}