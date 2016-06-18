<?php namespace Lib\Services\Scraping;

use Title, App, Event;
use Lib\Services\Db\Writer;
use Lib\Repositories\Data\ImdbData;
use Lib\Titles\TitleRepository as TitleRepo;
use Lib\Services\Scraping\Scraper;
use Lib\Repositories\Data\TmdbData as Tmdb;
use Lib\Reviews\ReviewRepository as Rev;
use Lib\Services\Search\ImdbSearch as Imdb;

class BatchScraper extends Curl
{
	
	/**
	 * DbTitle instance.
	 * 
	 * @var Lib\Titles\TitleRepository
	 */
	private $title;

	/**
	 * DbReview instance.
	 * 
	 * @var Lib\Reviews\ReviewRepository
	 */
	private $review;

	/**
	 * Writer instance.
	 * 
	 * @var Lib\Services\Db\Writer
	 */
	private $dbWriter;

	/**
	 * Main Scraper instance.
	 * 
	 * @var Lib\Services\Search\ImdbSearch
	 */
	private $imdbSearch;

	/**
	 * TmdbData instance.
	 * 
	 * @var Lib\Repositories\Data\TmdbData
	 */
	private $tmdb;

	public function __construct(TitleRepo $title, Writer $dbWriter, Rev $review, Imdb $imdbSearch, Tmdb $tmdb)
	{
		$this->tmdb 	  = $tmdb;
		$this->title      = $title;
		$this->review     = $review;
		$this->dbWriter   = $dbWriter;
		$this->imdbSearch = $imdbSearch;
	}

	/**
	 * Fetches and saves all available information about titles,
	 * that arent fully scraped in database.
	 * 
	 * @param  int/string $amount
	 * @return int/string
	 */
	public function inDb($amount = 10)
	{
		ini_set('max_execution_time', 0);

		$titles = $this->title->scrapable($amount);

		$count = 0;

		foreach ($titles as $k => $v)
		{
			$title = App::make('Lib\Titles\TitleRepository');

			$title->getCompleteTitle($v);
			$count++;
		}

		return $count;
	}

	/**
	 * Scrapes titles from imdb only from their ids array.
	 *
	 * @param array $ids
	 * @return array 
	 */
	public function imdbFromIds($ids)
	{
		$base = 'http://www.imdb.com/title/';

		//construct main and image imdb urls
		foreach ($ids as $id)
		{
			$main = $base . $id . '/';
			$images = $main . 'mediaindex?refine=still_frame';

			$html[] = $this->multiCurl(array($main, $images));
		}

		return $html;
	}

	/**
	 * Fetches titles using tmdb api discover query.
	 * 
	 * @param  array $input
	 * @return int how much titles scraped
	 */
	public function tmdbDiscover(array $input)
	{
		return $this->tmdb->discover($input);
	}

	/**
	 * Scrapes titles from imdb advanced search
	 * 
	 * @param  array $input
	 * @return void
	 */
	public function imdbAdvanced(array $input)
	{
		ini_set('max_execution_time', 0);

		$url = $this->compileImdbAdvancedUrl($input);

		$amount = $input['howMuch'];
		
		$currentPage = 1;

		while ($currentPage <= $amount)
		{
			$html = $this->curl($url);

			//increment current page by 100 as thats how many
			//titles page containts
			$currentPage = $currentPage + 100;

			//change url so it starts at 100 more then previous scrape
			$url = preg_replace('/&start=[0-9]+/', "&start=$currentPage", $url);

			$data = $this->imdbSearch->compileSearchResults($html);

			if ( ! $data) return false;
			
			$this->dbWriter->insertFromImdbSearch($data);
		}

		Event::Fire('Titles.Updated');

		return $currentPage;
	}

	/**
	 * Compiles imdb advanced search url from user input.
	 * 
	 * @param  array $input
	 * @return string
	 */
	private function compileImdbAdvancedUrl(array $input)
	{	
		$url = 'http://www.imdb.com/search/title?count=100';

		if (isset($input['minVotes']) && $input['minVotes'])
		{
			$url .= "num_votes={$input['minVotes']},";
		}
		
		$url .= '&title_type=feature,tv_movie,tv_series,tv_special,mini_series,documentary&sort=num_votes';

		if (isset($input['from']) && isset($input['to']))
		{
			$url .= "&release_date={$input['from']},{$input['to']}";
		}

		if (isset($input['minRating']) && $input['minRating'])
		{
			$url .= "&user_rating={$input['minRating']},";
		}

		if (isset($input['offset']) && $input['offset'])
		{
			$url .= "&start={$input['offset']}/";
		}
		else
		{
			$url .= "&start=1/";
		}

		return $url;
	}
}