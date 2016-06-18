<?php

use Lib\Actors\ActorRepository;
use Lib\Services\Options\Options;
use Lib\Services\Validation\ActorValidator;

class ActorController extends \BaseController {

	/**
	 * Actor repository instance.
	 * 
	 * @var use Lib\Actors\ActorRepository
	 */
	protected $repo;

	/**
	 * Validator instance.
	 * 
	 * @var Lib\Services\Validation\ActorValidator
	 */
	private $validator;

	/**
	 * Options instance.
	 * 
	 * @var Lib\Services\Options\Options;
	 */
	private $options;

	public function __construct(ActorRepository $actor, ActorValidator $validator)
	{
		//allow non-super users to view dashboard on demo environment
		if (App::environment() === 'demo')
		{
			$this->beforeFilter('is.admin', array('only' => array('store', 'update', 'knownFor', 'unlinkTitle')));
		} 
		else
		{
			$this->beforeFilter('logged', array('except' => array('index', 'show', 'paginate')));
			$this->beforeFilter('people:create', array('only' => array('create', 'store')));
			$this->beforeFilter('people:edit', array('only' => array('edit', 'update', 'editFilmo')));
		}

		$this->afterFilter('increment', array('only' => array('show')));	
		$this->beforeFilter('people:delete', array('only' => 'destroy'));
		$this->beforeFilter('csrf', array('on' => 'post'));

		$this->repo = $actor;
		$this->options   = App::make('options');
		$this->validator = $validator;
	}

	/**
	 * Displays a grid of actors.
	 *
	 * @return View
	 */
	public function index()
	{
		return View::make('Actors.Index');
	}

	/**
	 * Displays a page for creating new Actors.
	 *
	 * @return View
	 */
	public function create()
	{
		return View::make('Actors.Create');
	}

	/**
	 * Stores new actor in database.
	 *
	 * @return Redirect
	 */
	public function store()
	{
		$input = Input::except('_method', '_token');

		if ( ! $this->validator->with($input)->passes())
		{
			return Response::json($this->validator->errors(), 400);
		}

		$this->repo->create($input);

		return Response::json(trans('main.created actor successfully'), 201);
	}

	/**
	 * Show the main actor page.
	 *
	 * @param  string $id
	 * @return View
	 */
	public function show($id)
	{
		$provider = $this->options->getDataProvider();

		$actor = $this->repo->fetchFull($id);

		return View::make('Actors.Show')->withActor($actor)->withProvider($provider);
	}

	/**
	 * Displays the actor edit page.
	 *
	 * @param  mixed $id
	 * @return View
	 */
	public function edit($id)
	{
		$actor = $this->repo->fetchFull($id);

		return View::make('Actors.Create')->withActor($actor);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$input = Input::except('_method', '_token');

		if ( ! $this->validator->with($input)->passes())
		{
			return Response::json($this->validator->errors(), 400);
		}

		$this->repo->update($input, $id);

		return Response::json(trans('main.updated successfully', array('item' => $input['name'])), 201);
	}

	/**
	 * Removes specified title from actors filmography.
	 * 
	 * @return Redirect
	 */
	public function unlinkTitle()
	{
		$input = Input::except('_token');

		$this->repo->unlink($input);

		return Response::json(trans('main.unlinked successfully'), 200);
	}


	/**
	 * Change titles known for status in actors filmo.
	 * 
	 * @return Redirect
	 */
	public function knownFor()
	{
		$input = Input::except('_token');

		$this->repo->knownFor($input);

		return Response::json(trans('main.changed titles status'), 200);
	}

	/**
	 * Deletes actor from database.
	 *
	 * @param  int  $id
	 * @return Redirect
	 */
	public function destroy($id)
	{
		$this->repo->delete($id);

		return Response::json(trans('main.delete success'), 200);
	}

}