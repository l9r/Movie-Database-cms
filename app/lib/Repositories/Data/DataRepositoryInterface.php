<?php namespace Lib\Repositories\Data;

use Actor;
use Title;

interface DataRepositoryInterface
{
    /**
	 * Get Titles background.
	 * 
	 * @return string
	 */
	public function getBackground();

	/**
	 * Get full title info from provider.
	 * 
	 * @return self
	 */
	public function getFullTitle(Title $model);

	/**
	 * Get Titles id.
	 * 
	 * @return string
	 */
	public function getId();

	/**
	 * Get Titles title.
	 * 
	 * @return string
	 */
	public function getTitle();

	/**
	 * Get Titles runtime.
	 * 
	 * @return string
	 */
	public function getRuntime();

	/**
	 * Get Titles release date.
	 * 
	 * @return string
	 */
	public function getReleaseDate();

	/**
	 * Get Titles genre.
	 * 
	 * @return string
	 */
	public function getGenre();

	/**
	 * Get Titles tagline.
	 * 
	 * @return string
	 */
	public function getTagline();

	/**
	 * Gets basic information about all series seasons.
	 * 
	 * @param  int $id 
	 * @return array
	 */
	public function getAllSeasons($id);

	/**
	 * Get current title directors.
	 * 
	 * @return string
	 */
	public function getDirectors();

	/**
	 * Get current title writers.
	 * 
	 * @return string
	 */
	public function getWriters();

	/**
	 * Get Titles poster.
	 * 
	 * @return string
	 */
	public function getPoster();

	/**
	 * Get Titles images.
	 * 
	 * @return array
	 */
	public function getImages();

	/**
	 * Get Titles cast.
	 * 
	 * @return array
	 */
	public function getCast();

	/**
	 * Get Titles trailer.
	 * 
	 * @return string
	 */
	public function getTrailer();

	/**
	 * Get Titles type.
	 * 
	 * @return string
	 */
	public function getType();
}