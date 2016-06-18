<?php namespace Lib\Repositories\ActorData;

use Carbon\Carbon;
use Actor, App, Exception, Helpers;
use Lib\Repositories\Data\TmdbData;
use Symfony\Component\DomCrawler\Crawler;

class TmdbActorData extends TmdbData implements ActorDataRepositoryInterface
{
	/**
	 * Unformatted actor information.
	 * 
	 * @var array
	 */
	private $data;

	/**
	 * Actors general information.
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
	 * Actors biography link.
	 * 
	 * @var string
	 */
	private $bioLink;

	/**
	 * Actors image url.
	 * 
	 * @var string
	 */
	private $image;

	/**
	 * Actors name.
	 * 
	 * @var string
	 */
	private $actorName;

	/**
	 * Title actor is known for.
	 * 
	 * @var array
	 */
	private $knownFor = array();

	private $charNames = array();

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
	 * Actors tmdbid.
	 * 
	 * @var string
	 */
	private $tmdbid;

	/**
	 * Actors imdbid.
	 * 
	 * @var string
	 */
	private $imdbid;

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
				'tmdb_id'		=> $this->tmdbid,
				'image'  		=> $this->getImage(),
				'bio'    		=> $this->getBio(),
				'full_bio_link' => $this->getBioLink(),
				'birth_date'    => $this->getBirthDate(),
				'birth_place'   => $this->getBirthPlace()
			);
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
		if (isset($this->data['name']))
		{
			return $this->data['name'];
		}

		return 'Unknown-' . str_random(5);
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
			$this->birthDate = $this->data['birthday'];

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
			$this->birthPlace = $this->data['place_of_birth'];

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
		$this->charNames = array();
		if ( ! $this->filmo && isset($this->data['combined_credits']['cast']))
		{
			//extract id, title, poster for each one and make multidim array from it
			foreach ($this->data['combined_credits']['cast'] as $k => $v)
			{
				$type   = ($v['media_type'] == 'movie' ? 'movie' : 'series');
				$poster = ( ! $v['poster_path'] ? null : $this->imgBase . 'w342' . $v['poster_path']); 
				$title  = ( isset($v['name']) ? $v['name'] : $v['title']);
				$rd     = ( array_key_exists('release_date', $v) ? $v['release_date'] : $v['first_air_date']);
				$year   = Helpers::extractYear($rd);

				$this->charNames[$v['id']] = $v['character'] ? $v['character'] : 'Unknown';

				$this->filmo[] = array('tmdb_id' => $v['id'], 'title' => $title, 'year' => $year,
								  'poster' => $poster, 'type' => $type, 'release_date' => $rd ? $rd : Carbon::now()->addYear()->year);
			}
		}
		
		return $this->filmo;
	}

	public function getCharNames()
	{
		return $this->charNames;
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
			$this->image = $this->data['profile_path'] ? $this->imgBase . 'w342' . $this->data['profile_path'] : null;
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
			$this->bio = $this->data['biography'];
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
			$this->bioLink = 'http://www.themoviedb.org/person/' . $this->tmdbid;
		}

		return $this->bioLink;
	}

	/**
	 * Gets titles actor is know for.
	 * 
	 * @return false
	 */
	public function getKnownFor()
	{		
		return false;
	}

	/**
	 * Fetches provided actors full info from tmdb.
	 * 
	 * @param  actor model $actor 
	 * @return self
	 */
	public function getActor(Actor $actor)
	{
		if ( ! $actor->imdb_id && ! $actor->tmdb_id) return $this;

		//if we don't find tmdbid on passed model we'll forward the
		//request to imdb parser instead
		if ( ! $actor->tmdb_id)
		{
			return App::make('Lib\Repositories\ActorData\ImdbActorData')->getActor($actor);
		}

		$url = $this->compileActorUrl($actor);
		$this->data = json_decode($this->call($url), true);
		$this->checkIfResponseValid($actor);
		
		$this->tmdbid = $this->data['id'];
		$this->imdbid = $this->data['imdb_id'];

		return $this;
	}

	/**
	 * If we've got back adult title or invalid response delete
	 * actor from db and throw 404.
	 * 
	 * @return void
	 */
	private function checkIfResponseValid($actor)
	{
		if (isset($this->data['adult']) && $this->data['adult'] === true ||
			isset($this->data['status_code']) && $this->data['status_code'] === 6 )
		{ 
			Actor::find($actor->id)->delete();
			App::abort(404);

		}
	}

	/**
	 * Compiles actors profile url.
	 * 
	 * @param  actor  $actor
	 * @return string
	 */
	private function compileActorUrl(actor $actor)
	{
		$lang = App::make('options')->getTmdbLang();

		return $this->base.'person/'.$actor->tmdb_id.'?'.$this->key."&language={$lang}".'&append_to_response=combined_credits';
	}
}