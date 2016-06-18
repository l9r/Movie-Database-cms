<?php

use Lib\Repositories\TVNetwork\TVNetworkRepo as Repo;
use Lib\Services\Validation\TVNetworkValidator as TVValidator;

class TVNetworksController extends \BaseController {
	/**
	 * @var TVValidator
	 */
	private $tvNetworkValidator;
	/**
	 * @var Repo
	 */
	private $tvNetworkRepo;

	/**
	 * Instantiate new tv network controller instance.
	 * @param TVValidator $tvNetworkValidator
	 * @param Repo $tvNetworkRepo
	 */
	public function __construct(TVValidator $tvNetworkValidator, Repo $tvNetworkRepo)
	{

		$this->beforeFilter('tv_networks:create', array('only' => array('store')));
		$this->beforeFilter('tv_networks:delete', array('only' => array('destroy')));
		$this->beforeFilter('tv_networks:edit', array('only' => array('update', 'uploadLogo', 'uploadCoverPhoto')));

		$this->beforeFilter('csrf', array('on' => 'post'));
		$this->tvNetworkValidator = $tvNetworkValidator;
		$this->tvNetworkRepo = $tvNetworkRepo;
	}

	/**
	 * Return tv networks for pagination.
	 *
	 * @return JSON
	 */
	public function paginate()
	{
		return $this->tvNetworkRepo->paginate(Input::except('_token'));
	}

	public function store()
	{
		$input = Input::all();
		$input = array_except($input, '_token');

		$logo = array('avatar' => Input::file('logo'));
		$cP = array('bg' => Input::file('cover_photo'));

		if ( ! $this->tvNetworkValidator->with($input)->passes())
		{
			return Response::json($this->tvNetworkValidator->errors(), 422);
		}

		$result = $this->tvNetworkRepo->add($input, $logo, $cP);
		if($result == 'success')
		{
			\Cache::flush();
			return Response::json('New tv network has been added successfully', 201);
		}else
		{
			return Response::json('An Error has occurred, please try again later', 400);
		}
	}

	public function show($id)
	{
		$tvNetwork = $this->tvNetworkRepo->find($id);

		if(!$tvNetwork)
		{
			return Response::json("The selected tv network doesn't exist", 400);
		}

		return Response::json($tvNetwork, 200);

	}

	public function update($id)
	{

		$input = Input::except('_token', '_method');

		$validator = Validator::make($input, TVNetwork::updateProductionCompanyInfo($id));

		if ($validator->fails())
		{
			return Response::json($validator->messages(), 422);
		}

		$result = $this->tvNetworkRepo->update($id, $input);
		if($result == 'success')
		{
			\Cache::flush();
            return View::make(Dashboard.TVNetworks);
//			return Response::json('New tv network has been added successfully', 200);
		} elseif('not-found')
		{
            return View::make(Dashboard.TVNetworks);
//			return Response::json("The selected tv network doesn't exist", 400);
		} else
		{
            return View::make(Dashboard.TVNetworks);
//			return Response::json('An Error has occurred, please try again later', 400);
		}

	}

	public function uploadLogo($tvNetworkId)
	{

		$tvNetwork = $this->tvNetworkRepo->find($tvNetworkId);

		if(!$tvNetwork)
		{
			return Response::json("The selected tv network doesn't exist", 400);
		}

		$logo = array('logo' => Input::file('logo'));

		if ( ! $this->tvNetworkValidator->setRules('logo')->with($logo)->passes())
		{
			return Response::json($this->tvNetworkValidator->errors(), 422);
		}

		$logo = array('avatar' => Input::file('logo'));

		try
		{

			$path = $this->tvNetworkRepo->uploadLogo($logo, $tvNetwork->name);
			$tvNetwork->logo = $path;
			$tvNetwork->save();
			\Cache::flush();
			return Response::json('Selected tv network logo has been uploaded successfully', 200);

		}catch (\Exception $e)
		{
			return Response::json('An Error has occurred, please try again later', 400);
		}
	}

	public function uploadCoverPhoto($tvNetworkId)
	{

		$tvNetwork = $this->tvNetworkRepo->find($tvNetworkId);

		if(!$tvNetwork)
		{
			return Response::json("The selected tv network doesn't exist", 400);
		}

		$cP = array('cover_photo' => Input::file('cover_photo'));

		if ( ! $this->tvNetworkValidator->setRules('cover_photo')->with($cP)->passes())
		{
			return Response::json($this->tvNetworkValidator->errors(), 422);
		}

		$cP = array('bg' => Input::file('cover_photo'));

		try
		{

			$path = $this->tvNetworkRepo->uploadCp($cP, $tvNetwork->name);
			$tvNetwork->cover_photo = $path;
			$tvNetwork->save();
			\Cache::flush();
			return Response::json('Selected tv network cover photo has been uploaded successfully', 200);

		}catch (\Exception $e)
		{
			return Response::json('An Error has occurred, please try again later', 400);
		}
	}

	public function destroy($id)
	{

		$result = $this->tvNetworkRepo->delete($id);

		if($result == 'success')
		{
			\Cache::flush();
			return Response::json("The selected tv network has been deleted successfully", 200);
		}elseif('not-found')
		{
			return Response::json("The selected tv network doesn't exist", 400);
		}

	}

}