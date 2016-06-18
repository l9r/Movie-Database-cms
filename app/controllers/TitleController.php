<?php

use Carbon\Carbon;

class TitleController extends \BaseController {

	/**
	 * Title repository instance.
	 * 
	 * @var Lib\Titles\TitleRepository
	 */
	protected $repo;

   	/**
	 * validator instance.
	 * 
	 * @var Lib\Services\Validation\CreateTitleValidator
	 */
	protected $validator;

	/**
	 * Scraper instance.
	 * 
	 * @var Lib\Services\Scraping\Scraper
	 */
	protected $scraper;

	/**
	 * Options instance.
	 * 
	 * @var Lib\Services\Options\Options
	 */
	protected $options;

	public function __construct()
	{
		$this->afterFilter('increment', array('only' => array('show')));	
		$this->beforeFilter('titles:delete', array('only' => 'destroy'));
		$this->beforeFilter('reviews:update', array('only' => 'updateReviews'));
		$this->beforeFilter('csrf', array('on' => 'post'));

		//allow non-super users to view dashboard on demo environment
		if (App::environment() === 'demo')
		{
			$this->beforeFilter('titles:create', array('only' => array('store', 'scrapeFully', 'update', 'detachPeople', 'getData')));
		} 
		else
		{
			$this->beforeFilter('titles:create', array('only' => array('create', 'store', 'scrapeFully')));
			$edit = array('edit', 'update', 'editCast', 'editImages', 'uploadImage', 'attachImage', 'detachImage');
			$this->beforeFilter('titles:edit', array('only' => $edit));
			$this->beforeFilter('logged', array('except' => array('index', 'show', 'paginate')));
		}

		$this->repo     = App::make('Lib\Titles\TitleRepository');
		$this->validator = App::make('Lib\Services\Validation\TitleValidator');
		$this->scraper   = App::make('Lib\Services\Scraping\Scraper');
		$this->options   = App::make('options');
	}

    /**
     * Import data from external site for given id.
     *
     * @param $type
     * @param $providerName
     * @param $id
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getData($type, $providerName, $id) {
        $model = Title::firstOrCreate(array($providerName.'_id' => $id, 'type' => $type));
        $model->save();

        return $this->repo->getCompleteTitle($model)->load('Image', 'Actor', 'Writer', 'Director');
    }

	/**
	 * Displays the page for creating new series.
	 *
	 * @return View.
	 */
	public function create()
	{
		$relatedTo = DB::table('production_companies')->lists('name', 'id');
		$firstProdCompany = DB::table('production_companies')->take(1)->pluck('id');
		return View::make('Titles.Create', ['relatedTo' => $relatedTo, 'firstProdCompany' => $firstProdCompany]);
	}

	/**
	 * Display page for editing title.
	 *
	 * @param  string $id
	 * @return View
	 */
	public function edit($id)
	{
		$title = $this->repo->byUri($id);
		$firstProdCompany = DB::table('production_companies')->take(1)->pluck('id');
		if($title->type == 'movie')
			$relatedTo = DB::table('production_companies')->lists('name', 'id');
		else
			$relatedTo = DB::table('tv_networks')->lists('name', 'id');

		return View::make('Titles.Create', ['relatedTo' => $relatedTo, 'firstProdCompany' => $firstProdCompany])->withTitle($title);
	}

	/**
	 * Stores newly created series in database.
	 *
	 * @return Response
	 */
	public function store()
	{
		$input = Input::except('_token');

		if ( ! $this->validator->with($input)->passes())
		{
			return Response::json($this->validator->errors(), 400);
		}

		App::make('Lib\Titles\TitleCreator')->create($input);

		return Response::json(trans('dash.titleSaveSuccess'), 201);
	}

	public function paginate()
	{
		return $this->repo->paginate(Input::except('_token'));
	}

	/**
	 * Updates titles critic reviews.
	 * 
	 * @param  mixed  $title
	 * @return Redirect
	 */
	public function updateReviews($title = null)
	{
		if ( ! $title)
		{
			$title = $this->repo->byId( Input::get('id') );
		}

		$this->repo->updateReviews($title);

		return Redirect::back()->withSuccess( trans('main.reviews updated') );
	}

	/**
	 * Fully scrapes specified amount of titles in db.
	 * 
	 * @return Response
	 */
	public function scrapeFully()
	{
		$amount = Input::get('amount');
			
		$amount = $this->scraper->inDb($amount);

		return Redirect::back()->withSuccess( trans('dash.fully scraped', array('amount' => $amount)) );
	}
	
	/**
	 * Detaches cast or crew from title.
	 * 
	 * @return Redirect
	 */
	public function detachPeople()
	{
		$input = Input::except('_token');
	
		try {
			$this->repo->detachPeople($input);
		} catch (Exception $e) {
			return Response::json(trans('dash.somethingWrong'), 500);
		}

		return Response::json(trans('dash.detachSuccess'), 200);
	}

	/**
	 * Deletes a movie from database.
	 *
	 * @param  string $title
	 * @return JSOn
	 */
	public function destroy($id)
	{
		$this->repo->delete($id);

		return Response::json(trans('main.movie deletion successfull'), 200);
	}

	public function getRelatedToList($type)
	{

		if($type == 'movie')
		{
			$productionCompanies = DB::table('production_companies')->get(array('name', 'id'));

			return $productionCompanies;

		}elseif($type == 'series')
		{
			$tvNetworks = DB::table('tv_networks')->get(array('name', 'id'));

			return $tvNetworks;
		}
	}
}