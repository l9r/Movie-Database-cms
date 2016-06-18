<?php namespace Lib\Repositories\Data;

use Title, App;
use Lib\Services\Presentation\DbPresenter;

class DbData extends DbPresenter implements DataRepositoryInterface
{
	/**
	 * Name of provider.
	 * 
	 * @var string
	 */
	public $name = 'db';

	/**
	 * Get actor stub.
	 * 
	 * @param  Actor  $actor
	 * @return void
	 */
	public function getActor(actor $actor){}

	/**
	 * Redirects the call to imdb or tmdb provider if either id is set.
	 * 
	 * @param  Title $model
	 * @return Lib\Repositories\Data\DataProviderInterface
	 */
	public function getFullTitle(Title $model)
	{
		if ($model->tmdb_id)
		{
			return App::make('Lib\Repositories\Data\TmdbData')->getFullTitle($model);
		}
		elseif ($model->imdb_id)
		{
			return App::make('Lib\Repositories\Data\ImdbData')->getFullTitle($model);
		}
	}

	/**
	 * Get all seasons stub.
	 * 
	 * @param  string $id
	 * @return void
	 */
	public function getAllSeasons($id){}

	/**
	 * Falls back to updating now in theaters from tmdb.
	 * 
	 * @return array
	 */
	public function getNowPlaying()
	{
		$tmdb = App::make('Lib\Repositories\Data\TmdbData');
		return $tmdb->getNowPlaying();
	}
}