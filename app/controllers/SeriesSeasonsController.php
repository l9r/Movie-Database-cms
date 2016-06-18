<?php

use Lib\Titles\SeasonRepository as Srepo;
use Lib\Services\Validation\SeasonValidator;

class SeriesSeasonsController extends BaseController
{
    /**
     * Season repository instance.
     *
     * @var Lib\Titles\SeasonRepository
     */
    protected $season;

    /**
     * Season validator instance.
     *
     * @var Lib\Services\Validation\SeasonValidator
     */
    protected $seasonValidator;

	public function __construct(Srepo $season, SeasonValidator $seasonValidator)
	{
        $this->season = $season;
        $this->seasonValidator = $seasonValidator;

        $this->beforeFilter('is.admin', array('only' => array('store', 'update', 'destroy')));
	}

    /**
     * Show episodes list for a season.
     *
     * @param  string $title  series title
     * @param  int    $num    season number
     * @return View
     */
    public function show($title, $num)
    {
        if ($num == 0) App::abort(404);

        //fetch all seasons and episodes of series as we'll update all
        //of them at once if they need updating
        $title = $this->season->withSeasonsEpisodes($title);

        //if series doesn't have requested season we'll bail
        if (! isset($title['season'][$num - 1])) App::abort(404);

        //prepare the requested season for displaying
        $title = $this->season->prepareSingle($title, $num);

        try {
            $episodes = Helpers::extractSeason($title, $num)->episode;
        } catch (Exception $e) {
            App::abort(404);
        }

        return View::make('Titles.Seasons.Show')->withNum($num)->withTitle($title)->withEpisodes($episodes);
    }

    /**
     * Displays the page for creating a new season.
     *
     * @param  string/int $id
     * @return View
     */
    public function create($id)
    {
        $series = $this->title->byId($id);

        return View::make('Titles.CreateSeason')->withSeries($series);
    }

    /**
     * Stores newly created season in database.
     *
     * We'll use this method for updating seasons
     * aswell because we're updating on duplicate key.
     *
     * @return Redirect
     */
    public function store($series)
    {
        $input = Input::except('_token');

        if ( ! $this->seasonValidator->with($input)->passes())
        {
            return Response::json($this->seasonValidator->errors(), 400);
        }

        $id = $this->season->create($input)->id;

        return Response::json(array('message' => trans('main.season create success'),'id' => $id), 201);
    }

    /**
     * Deletes specified season.
     *
     * @param  string $series
     * @param  string $season
     * @return Redirect
     */
    public function destroy($series, $season)
    {
        $this->season->delete($series, $season);

        return Response::json(trans('main.deleted season success'), 200);
    }

    /**
     * Updates specified season.
     *
     * @param  string $series
     * @param  string $season
     * @return Redirect
     */
    public function update($series, $season)
    {
        $input = Input::except('_token');

        if ( ! $this->seasonValidator->with($input)->passes())
        {
            return Redirect::back()->withErrors($this->seasonValidator->errors())->withInput($input);
        }

        $this->season->create();
    }
}