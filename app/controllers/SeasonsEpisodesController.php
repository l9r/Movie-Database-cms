<?php

use Lib\Services\Presentation\DbPresenter;
use Lib\Services\Validation\EpisodeValidator;
use Lib\Titles\EpisodeRepository as EpRepo;

class SeasonsEpisodesController extends BaseController
{
    /**
     * Episode repository instance.
     * 
     * @var Lib\Titles\EpisodeRepository
     */
    private $episode;

    /**
     * Episode validator instance.
     * 
     * @var Lib\Services\Validation\EpisodeValidator
     */
    private $epValidator;

	public function __construct(EpisodeValidator $epValidator, EpRepo $episode)
	{
        $this->afterFilter('increment', array('only' => array('show')));
        $this->beforeFilter('is.admin', array('only' => array('store', 'update', 'destroy')));
        $this->beforeFilter('csrf', array('on' => 'post'));

        $this->episode = $episode;
        $this->epValidator = $epValidator;
	}

    public function show($series, $seasonNum, $episodeNum)
    {
        $title = Title::byId($series);

        foreach ($title->season as $value) {
            if ($value->number == $seasonNum) {
                $season = $value;
            }
        }

        if (isset($season)) {
            foreach ($season->episode as $value) {
                if ($value->episode_number == $episodeNum) {
                    $episode = $value;
                }
            }
        }

        if ( ! isset($episode)) {
            App::abort(404);
        }

        return View::make('Titles.Episodes.Show')
                ->with('title', $title)
                ->with('episode', $episode)
                ->with('season', $season);
    }

    /**
     * Stores newly created episode in database.
     *
     * @param  string $series
     * @param  string $season
     * @param  string $episode
     *
     * @return Redirect
     */
    public function store($series, $season)
    {
        $input = Input::except('_token');

        if ( ! $this->epValidator->with($input)->passes())
        {
            return Response::json($this->epValidator->errors(), 400);
        }

        $this->episode->create($input);

        return Response::json(trans('main.ep create success'), 201);
    }

    /**
     * Displays the page for creating a new episode.
     *
     * @param  string $id
     * @return View
     */
    public function create($series, $season)
    {
        $data = $this->title->byId($series);
        $data = new DbPresenter($data);

        return View::make('Titles.CreateEpisode')->withData($data)->withNum($season);
    }

    /**
     * Updates specified episode.
     * 
     * @param  string $series
     * @param  string $season
     * @param  string $episode
     * 
     * @return Redirect
     */
    public function update($series, $season, $episode)
    {
        $input = Input::except('_token', '_method');

        if ( ! $this->epValidator->with($input)->passes())
        {
            Response::json($this->epValidator->errors(), 400);
        }

        $this->episode->update($episode, $input);

        return Response::json(trans('main.ep update success'), 201);
    }

    /**
     * Deletes specified episode.
     * 
     * @param  string $series
     * @param  string $season
     * @param  string $episode
     * 
     * @return Redirect
     */
    public function destroy($series, $season, $episode)
    {
        $this->episode->delete($episode);

        return Response::json(trans('main.delete success'), 200);
    }
}