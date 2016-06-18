<?php namespace Lib\Repositories\ActorData;

use Actor;
use Lib\Repositories\Data\TmdbData;
use Symfony\Component\DomCrawler\Crawler;

interface ActorDataRepositoryInterface
{
	/**
	 * Gets general information about the actor.
	 * 
	 * @return array
	 */
	public function getGenInfo();

	/**
	 * Gets Actors name.
	 * 
	 * @return string.
	 */
	public function getName();

	/**
	 * Gets Actors birth date.
	 * 
	 * @return string.
	 */
	public function getBirthDate();

	/**
	 * Gets Actors birth place.
	 * 
	 * @return string.
	 */
	public function getBirthPlace();

	/**
	 * Gets Actors filmographuy.
	 * 
	 * @return array.
	 */
	public function getFilmography();

	/**
	 * Gets Actors image.
	 * 
	 * @return string.
	 */
	public function getImage();

	/**
	 * Gets Actors biography.
	 * 
	 * @return string.
	 */
	public function getBio();

	/**
	 * Gets Actors full biography url.
	 * 
	 * @return string.
	 */
	public function getBioLink();

	/**
	 * Gets titles actor is know for.
	 * 
	 * @return array.
	 */
	public function getKnownFor();

	/**
	 * Fetches provided actors full info from tmdb.
	 * 
	 * @param  actor model $actor 
	 * @return self
	 */
	public function getActor(Actor $actor);
}