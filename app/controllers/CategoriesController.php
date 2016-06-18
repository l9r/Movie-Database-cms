<?php

use Lib\Categories\CategoriesRepository;

class CategoriesController extends \BaseController {

	/**
	 * Categories repository instance.
	 * 
	 * @var Lib\Categories\CategoriesRepository
	 */
	protected $repo;

	public function __construct(CategoriesRepository $repo)
	{
		$this->repo = $repo;

		$this->beforeFilter('logged', array('on' => array('post', 'delete')));
        $this->beforeFilter('is.admin', array('on' => array('post', 'delete')));
		$this->beforeFilter('csrf', array('on' => 'post'));
	}

	/**
	 * Create new or update an existing category.
	 *
	 * @return Response
	 */
	public function store()
	{	
		$input = Input::except('_token');

		if ($this->repo->createOrUpdate($input)) {
			return Response::json(trans('dash.createCatSuccesss'), 201);
		}
	}

	public function attach()
	{
		if ($this->repo->attach(Input::except('_token'))) {
			return Response::json(trans('main.attached successfully'), 201);
		}
	}

	public function detach()
	{
		if ($this->repo->detach(Input::except('_token'))) {
			return Response::json(trans('main.detached successfully'), 200);
		}
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		if ($this->repo->delete($id)) {
			return Response::json(trans('dash.deleteCatSuccess'), 200);
		}
	}

}