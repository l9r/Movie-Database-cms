<?php

use Lib\Lists\ListRepository;

class ListsController extends BaseController
{
	/**
	 * List repostiry instance.
	 * 
	 * @var Lib\Repositories\Lists\ListRepositoryInterface
	 */
	private $list;

	public function __construct(ListRepository $list)
	{
		$this->list = $list;
		$this->beforeFilter('csrf', array('on' => 'post'));
		$this->beforeFilter('logged');
	}

	/**
	 * Add title to specified list of user.
	 * 
	 * @return Response
	 */
	public function postAdd()
	{
		$input = Input::except('_token');

		if ( ! isset($input['title_id']))
		{
			return Response::json(trans('dash.somethingWrong'), 500);
		}

		$this->list->add($input);

		return Response::json(trans('main.added to list'), 201);
	}

	/**
	 * Remove title from specfied list of user.
	 * 
	 * @return Response
	 */
	public function postRemove()
	{
		$input = Input::except('_token');

		if (! isset($input['title_id']))
		{
			return Response::json(trans('dash.somethingWrong'), 500);
		}

		$this->list->remove($input);
	
		return Response::json(trans('main.removed from list'), 200);
	}

}