<?php

use Lib\Services\Validation\PageValidator;
use Lib\Page\PageRepository as Repo;

class PageController extends \BaseController {

	/**
	 * Page validator instance.
	 * 
	 * @var Lib\Services\Validation\PageValidator
	 */
	private $validator;

	/**
	 * Page validator instance.
	 * 
	 * @var Lib\Repositories\Page\PageRepositoryInterface
	 */
	protected $repo;

	/**
	 * Create new PageController isntance.
	 * 
	 * @param PageValidator $validator
	 * @param Repo $repository
	 */
	function __construct(PageValidator $validator, Repo $repo)
	{
		if (App::environment() === 'demo')
		{
			$this->beforeFilter('logged', array('only' => array('store', 'destroy')));
			$this->beforeFilter('is.admin', array('only' => array('store', 'destroy')));
		} 
		else
		{
			$this->beforeFilter('logged', array('except' => 'show'));
			$this->beforeFilter('is.admin', array('except' => 'show'));
		}

		$this->repo = $repo;
		$this->validator = $validator;
	}

	/**
	 * Show the form to create new page.
	 *
	 * @return View
	 */
	public function create()
	{
		return View::make('Pages.Create');
	}

	/**
	 * Store a new page in database.
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

		if ($this->repo->saveOrUpdate($input))
		{
			return Response::json(trans('dash.pagePublishSuccess'), 201);
		}		
	}

	/**
	 * Display the requested page.
	 *
	 * @param  string slug
	 * @return View
	 */
	public function show($slug)
	{
		$page = $this->repo->findBySlug($slug);

		return View::make('Pages.Show')->withPage($page);
	}


	/**
	 * Show form for editing given page.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$page = $this->repo->find($id);

		return View::make('Pages.Create')->withPage($page);
	}

	/**
	 * Delete page from database.
	 *
	 * @param  int  $id
	 * @return Json
	 */
	public function destroy($id)
	{
		if ($this->repo->destroy($id))
		{
			return Response::json(trans('dash.pageDelSuccess'), 200);
		}
	}
}
