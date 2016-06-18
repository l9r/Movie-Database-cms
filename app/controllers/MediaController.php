<?php

use Lib\Services\Validation\MediaValidator;
use Lib\Media\MediaRepository as Repo;

class MediaController extends \BaseController {

	/**
	 * Media validator instance.
	 * 
	 * @var Lib\Services\Validation\MediaValidator
	 */
	private $validator;

	/**
	 * Media repository implementation.
	 * 
	 * @var Lib\Repositories\Media\MediaRepositoryInteface
	 */
	protected $repo;

	/**
	 * Create new MediaController instance.
	 * 
	 * @param MediaValidator $validator
	 * @param Repo           $repo
	 */
	public function __construct(MediaValidator $validator, Repo $repo)
	{
		$this->repo = $repo;
		$this->validator = $validator;

		$this->beforeFilter('logged', array('only' => array('store', 'destroy')));
		$this->beforeFilter('is.admin', array('only' => array('store', 'destroy')));
		$this->beforeFilter('csrf', array('only' => array('store', 'destroy')));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return JSON array
	 */
	public function store()
	{
		$urls = array();

		$files = Input::file('files');
		$keep  = Input::get('useOriginalName') == 'true' ? true : false;
		
		//validate each file save it and push it to response array
		foreach ($files as $file)
		{
			if ( ! $this->validator->with(array('file' => $file))->passes())
			{
				return Response::json($this->validator->errors(), 400);
			}

			$url = $this->repo->saveImage($file, $keep);

			$urls[] = $url;
		}
		
		return $urls;
	}

	/**
	 * Remove the specified media item from database
	 * and filesystem.
	 *
	 * @param  int  $id
	 * @return JSON
	 */
	public function destroy($id)
	{
		if ($this->repo->destroy($id))
		{
			return Response::json(trans('dash.mediaDelSuccess'), 200);
		}
	}

	public function paginate()
	{
		return $this->repo->paginate(Input::all());
	}

}