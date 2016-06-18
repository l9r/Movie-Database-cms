<?php namespace Lib\Repositories\ActorData;

use Carbon\Carbon;
use Lib\Services\Scraping\Curl;
use Helpers, Actor, Exception, App;
use Lib\Services\Images\ImageSaver;
use Symfony\Component\DomCrawler\Crawler;

class ImdbActorData implements ActorDataRepositoryInterface
{
	/**
	 * Crawler instance.
	 * 
	 * @var Symfony\Component\DomCrawler\Crawler
	 */
	private $crawler;

	/**
	 * General info about actor.
	 * 
	 * @var array
	 */
	private $genInfo;

	/**
	 * Actors biography.
	 * 
	 * @var string
	 */
	private $bio;

	/**
	 * Actors full biography link.
	 * 
	 * @var string
	 */
	private $bioLink;

	/**
	 * Actors image.
	 * 
	 * @var string
	 */
	private $image;

	/**
	 * Actors name.
	 * 
	 * @var string
	 */
	private $name;

	/**
	 * Titles actor is known for.
	 * 
	 * @var array
	 */
	private $knownFor;

	/**
	 * Actors awards.
	 * 
	 * @var string
	 */
	private $awards;

	/**
	 * Actors birthplace.
	 * 
	 * @var string
	 */
	private $birthPlace;

	/**
	 * Actors birthdate.
	 * 
	 * @var string
	 */
	private $birthDate;

	/**
	 * Actors filmography.
	 * 
	 * @var array
	 */
	private $filmo;

	/**
	 * Actors imdb id.
	 * 
	 * @var string
	 */
	private $imdbid;

	/**
	 * ImageSaver instance.
	 * 
	 * @var Lib\Services\Images\ImageSaver
	 */
	private $images;

	/**
	 * Images to save urls.
	 * 
	 * @var array
	 */
	private $imgUrls = array();

	/**
	 * Curl instance.
	 * 
	 * @var Lib\Services\Scraping\Curl
	 */
	private $scraper;

	public function __construct(ImageSaver $images, Curl $scraper)
	{
		$this->images = $images;
		$this->scraper = $scraper;
	}

	/**
	 * Gets general information about the actor.
	 * 
	 * @return array
	 */
	public function getGenInfo()
	{
		if ( ! $this->genInfo)
		{
			$this->genInfo = array(
			'name'   		=> $this->getName(),
			'imdb_id'		=> $this->imdbid,
			'image'  		=> $this->getImage(),
			'bio'    		=> $this->getBio(),
			'full_bio_link' => $this->getBioLink(),
			'birth_date'    => $this->getBirthDate(),
			'birth_place'   => $this->getBirthPlace(),
			'awards'        => $this->getAwards());
		}

		return $this->genInfo;
	}

	/**
	 * Gets Actors name.
	 * 
	 * @return string.
	 */
	public function getName()
	{
		if ( ! $this->name)
		{
			$this->name = head($this->crawler->filter('h1.header > span[itemprop="name"]')->extract(array('_text')));
		}

		return $this->name;
	}

	/**
	 * Gets Actors birth date.
	 * 
	 * @return string.
	 */
	public function getBirthDate()
	{
		if ( ! $this->birthDate)
		{
			$this->birthDate = head($this->crawler->filter('#name-born-info > time')->extract(array('datetime')));

		}

		return $this->birthDate;
	}

	/**
	 * Gets Actors birth place.
	 * 
	 * @return string.
	 */
	public function getBirthPlace()
	{
		if ( ! $this->birthPlace)
		{
			$this->birthPlace = last($this->crawler->filter('#name-born-info > a')->extract(array('_text')));
		}

		return $this->birthPlace;
	}

	/**
	 * Gets Actors filmographuy.
	 * 
	 * @return array.
	 */
	public function getFilmography()
	{
		$this->filmo = array();

		if ( ! $this->filmo)
		{
			$filmography = $this->crawler->filter('div#filmography > div.filmo-category-section > .filmo-row');

			foreach ($filmography as $k => $v)
			{			
				$crawler = new crawler($v);

				$type   =  $this->type($crawler->extract('_text'));
				$imdbid =  $this->id($crawler->filter('b > a')->extract('href'));
				$title  =  head($crawler->filter('b > a')->extract('_text'));
				$year   =  $this->year($crawler->filter('.year_column')->extract('_text'));

				$this->filmo[] = array('imdb_id' => $imdbid, 'title' => $title, 'type' => $type, 'year' => $year);
			}
		}

		return $this->filmo;
	}

	public function getCharNames()
	{
		return array();
	}

	/**
	 * Gets Actors awards information.
	 * 
	 * @return string.
	 */
	public function getAwards()
	{
		if ( ! $this->awards)
		{
			$awards = $this->crawler->filter('span[itemprop="awards"]')->extract(array('_text'));
			$this->awards = implode(' ', $awards);
		}

		return $this->awards;
	}

	/**
	 * Gets Actors image.
	 * 
	 * @return string.
	 */
	public function getImage()
	{
		if ( ! $this->image)
		{
			$url = head($this->crawler->filter('img#name-poster')->extract(array('src')));

			if ($url)
			{
				if ($this->images->saveSingle($url, $this->imdbid, 'imdb/cast/'))
				{
					$this->image = 'imdb/cast/' . $this->imdbid . '.jpg';
				}
			}		
		}

		return $this->image;
	}

	/**
	 * Gets Actors biography.
	 * 
	 * @return string.
	 */
	public function getBio()
	{
		if ( ! $this->bio)
		{
			$bio = head($this->crawler->filter('.name-trivia-bio-text > div[itemprop="description"]')->extract(array('_text')));
			$bio = str_replace('See full bio »', '', $bio);
			$this->bio = trim($bio);
		}

		return $this->bio;
	}

	/**
	 * Gets Actors full biography url.
	 * 
	 * @return string.
	 */
	public function getBioLink()
	{
		if ( ! $this->bioLink)
		{
			//get the full bio imdb url
			$bioLink = head($this->crawler->filter('.name-trivia-bio-text > div[itemprop="description"] > span > a')->extract(array('href')));

			//get the resource id
			preg_match('/(nm[0-9]+)/', $bioLink, $matches);

			//compile fully qualified url
			(isset($matches[0]) ? $this->bioLink = 'http://www.imdb.com/name/' . $matches[0] . '/bio' : $this->bioLink = '');
		}

		return $this->bioLink;
	}

	/**
	 * Gets titles actor is know for.
	 * 
	 * @return array.
	 */
	public function getKnownFor()
	{
		if ( ! $this->knownFor)
		{
			//grab all the titles actor is know for
			$known = $this->crawler->filter('div#knownfor > div');

			//extract id, title, poster for each one and make multidim array from it
			foreach ($known as $k => $v)
			{
				$crawler = new crawler($v);

				$imdbid  = $this->id($crawler->filter('a')->extract('href'));
				$title   = head($crawler->filter('a > img')->extract('title'));
				$poster  = $this->image($crawler->filter('a > img')->extract('src'), $imdbid);
				$year    = Helpers::extractYear( head($crawler->filter('a')->eq(1)->extract('_text')) );
				
				$this->knownFor[] = array('imdb_id' => $imdbid, 'title' => $title, 'poster' => $poster, 'year' => $year);
			}
		}

		$this->images->saveMultiple($this->imgUrls, null, 'imdb/posters/');

		return $this->knownFor;
	}

	/**
	 * Queues image to be saved locally and returns path.
	 * 
	 * @param  array  $image
	 * @param  string $imdbid
	 * @return void/string
	 */
	private function image(array $image, $imdbid)
	{
		if ( ! empty($image) && ! strpos(head($image), 'nopicture') )
		{
			$this->imgUrls[$imdbid] = Helpers::size( head($image), 2 );
			return "imdb/posters/$imdbid.jpg";
		}
	}

	/**
	 * Extracts imdb title id from string.
	 * 
	 * @param  array  $id 
	 * @return string
	 */
	private function id(array $id)
	{
		preg_match('/\/(tt[0-9]+)\//', head($id), $matches);

		return (isset($matches[1]) ? $matches[1] : '');
	}

	/**
	 * Figures out title type.
	 * 
	 * @param  array  $array
	 * @return string
	 */
	private function type(array $array)
	{
		if (strpos(head($array), 'Series'))
		{
			return 'series';
		}

		return 'movie';
	}

	/**
	 * Removes the white space from title year
	 * 
	 * @param  array $array
	 * @return string
	 */
	private function year(array $array)
	{
		$year = e(head($array));
		$year = preg_replace('/[^0-9-]/i', '', $year);
		
		return strlen($year) >= 4 ? $year : Carbon::now()->addYear()->year;
	}

	/**
	 * Gets actor info from imdb.
	 * 
	 * @param  actor model $actor 
	 * @return self
	 */
	public function getActor(Actor $actor)
	{
		if ( ! $actor->imdb_id && ! $actor->tmdb_id)
		{
			throw new Exception ('Couldn\'t find neither tmdb_id nor imdb_id on actor model');
		}

		//if we don't find imdbid on passed model we'll forward the
		//request to tmdb parser instead
		if ( ! $actor->imdb_id)
		{
			return App::make('Lib\Repositories\ActorData\TmdbActorData')->getActor($actor);
		}

		return $this->scrape($actor->imdb_id);
	}

	/**
	 * Scrapes given actors page.
	 *
	 * @param string $id
	 * @return self
	 */
	private function scrape($id)
	{
		$url  = 'http://www.imdb.com/name/' . "$id/";
		$html = $this->scraper->curl($url);
		$html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);

		$this->crawler = new crawler($html);
		$this->imdbid = $id;

		return $this;
	}
}