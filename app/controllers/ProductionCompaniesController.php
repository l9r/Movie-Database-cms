<?php

use Lib\Repositories\ProductionCompany\ProductionCompanyRepo as Repo;
use Lib\Services\Validation\ProductionCompanyValidator as ProdValidator;

class ProductionCompaniesController extends \BaseController {
	/**
	 * @var ProdValidator
	 */
	private $productionCompanyValidator;
	/**
	 * @var Repo
	 */
	private $productionCompanyRepo;

	/**
	 * Instantiate new production company controller instance.
	 * @param ProdValidator $productionCompanyValidator
	 * @param Repo $productionCompanyRepo
	 */
	public function __construct(ProdValidator $productionCompanyValidator, Repo $productionCompanyRepo)
	{

		$this->beforeFilter('production_companies:create', array('only' => array('store')));
		$this->beforeFilter('production_companies:delete', array('only' => array('destroy')));
		$this->beforeFilter('production_companies:edit', array('only' => array('update', 'uploadLogo', 'uploadCoverPhoto')));

		$this->beforeFilter('csrf', array('on' => 'post'));
		$this->productionCompanyValidator = $productionCompanyValidator;
		$this->productionCompanyRepo = $productionCompanyRepo;
	}

	/**
	 * Return production companies for pagination.
	 *
	 * @return JSON
	 */
	public function paginate()
	{
		return $this->productionCompanyRepo->paginate(Input::except('_token'));
	}

	public function store()
	{
		$input = Input::all();
		$input = array_except($input, '_token');

		$logo = array('avatar' => Input::file('logo'));
		$cP = array('bg' => Input::file('cover_photo'));

		if ( ! $this->productionCompanyValidator->with($input)->passes())
		{
            return View::make(Dashboard.ProductionCompanies);

//			return Response::json($this->productionCompanyValidator->errors(), 422);
		}

		$result = $this->productionCompanyRepo->add($input, $logo, $cP);
		if($result == 'success')
		{
			\Cache::flush();
            return View::make(Dashboard.ProductionCompanies);
//			return Response::json('New production company has been added successfully', 201);
		}else
		{
            return View::make(Dashboard.ProductionCompanies);
//			return Response::json('An Error has occurred, please try again later', 400);
		}
	}

	public function show($id)
	{

		$productionCompany = $this->productionCompanyRepo->find($id);

		if(!$productionCompany)
		{
			return Response::json("The selected production company doesn't exist", 400);
		}

		return Response::json($productionCompany, 200);

	}

	public function update($id)
	{

		$input = Input::except('_token', '_method');

		$validator = Validator::make($input, ProductionCompany::updateProductionCompanyInfo($id));

		if ($validator->fails())
		{
			return Response::json($validator->messages(), 422);
		}

		$result = $this->productionCompanyRepo->update($id, $input);
		if($result == 'success')
		{
			\Cache::flush();
			return Response::json('New production company has been added successfully', 200);
		}elseif('not-found')
		{
			return Response::json("The selected production company doesn't exist", 400);
		}else
		{
			return Response::json('An Error has occurred, please try again later', 400);
		}

	}

	public function uploadLogo($companyId)
	{

		$productionCompany = $this->productionCompanyRepo->find($companyId);

		if(!$productionCompany)
		{
			return Response::json("The selected production company doesn't exist", 400);
		}

		$logo = array('logo' => Input::file('logo'));

		if ( ! $this->productionCompanyValidator->setRules('logo')->with($logo)->passes())
		{
			return Response::json($this->productionCompanyValidator->errors(), 422);
		}

		$logo = array('avatar' => Input::file('logo'));

		try
		{

			$path = $this->productionCompanyRepo->uploadLogo($logo, $productionCompany->name);
			$productionCompany->logo = $path;
			$productionCompany->save();
			\Cache::flush();
			return Response::json('Selected production company logo has been uploaded successfully', 200);

		}catch (\Exception $e)
		{
			return Response::json('An Error has occurred, please try again later', 400);
		}
	}

	public function uploadCoverPhoto($companyId)
	{

		$productionCompany = $this->productionCompanyRepo->find($companyId);

		if(!$productionCompany)
		{
			return Response::json("The selected production company doesn't exist", 400);
		}

		$cP = array('cover_photo' => Input::file('cover_photo'));

		if ( ! $this->productionCompanyValidator->setRules('cover_photo')->with($cP)->passes())
		{
			return Response::json($this->productionCompanyValidator->errors(), 422);
		}

		$cP = array('bg' => Input::file('cover_photo'));

		try
		{

			$path = $this->productionCompanyRepo->uploadCp($cP, $productionCompany->name);
			$productionCompany->cover_photo = $path;
			$productionCompany->save();
			\Cache::flush();
			return Response::json('Selected production company cover photo has been uploaded successfully', 200);

		}catch (\Exception $e)
		{
			return Response::json('An Error has occurred, please try again later', 400);
		}
	}

	public function destroy($id)
	{

		$result = $this->productionCompanyRepo->delete($id);

		if($result == 'success')
		{
			\Cache::flush();
			return Response::json("The selected production company has been deleted successfully", 200);
		}elseif('not-found')
		{
			return Response::json("The selected production company doesn't exist", 400);
		}

	}

}