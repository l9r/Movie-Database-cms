<?php

class MoviesController extends TitleController {

	/**
	 * Instantiate new movie controller instance.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Displays the movies main page.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$title = $this->repo->byUri($id);

		if ($title->type !== 'movie') App::abort(404);

		if ($this->repo->needsScraping($title))
		{
			$title = $this->repo->getCompleteTitle($title);
		}

		$lists = App::make('Lib\Repositories\User\UserRepositoryInterface')->getListsByTitleId($id);

		return View::make('Titles.Show')->withTitle($title)->withLists($lists);
	}

	/**
	 * Displays a grid of titles with pagination.
	 *
	 * @return View
	 */
	public function index()
	{
		$q = Request::query();

		//make index page crawlable by google
		if (isset($q['_escaped_fragment_']))
		{
			$movies = Title::where('type', 'movie')->paginate(16);

			return View::make('Titles.CrawlableIndex')->with('movies', $movies)->with('type', 'movies');
		}

		return View::make('Titles.Index')->withType('movie');
	}
}