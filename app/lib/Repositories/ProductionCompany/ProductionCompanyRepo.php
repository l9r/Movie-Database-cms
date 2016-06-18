<?php namespace Lib\Repositories\ProductionCompany;

use Lib\Repository;
use Lib\Services\Images\ImageSaver;
use DB, ProductionCompany;

class ProductionCompanyRepo extends Repository implements ProductionCompanyRepositoryInterface
{
	/**
	 * ProductionCompany model instance.
	 * 
	 * @var ProductionCompany
	 */
	protected $model;

	/**
	 * Images handler instance.
	 * 
	 * @var Lib\Services\Images\ImageSaver
	 */
	private $images;

	public function __construct(ProductionCompany $productionCompany, ImageSaver $images)
	{
		$this->model   = $productionCompany;
		$this->images = $images;
	}

	/**
	 * Add new production company.
	 * 
	 * @param  array $data
	 * @return string message
	 */
	public function add($data, $logo = null, $cP = null)
	{
		$productionCompany = new ProductionCompany;
		$productionCompany->name = $data['name'];
		$productionCompany->description = $data['description'];
		$productionCompany->website = $data['website'];

		try
		{
			if($logo['avatar'])
			{
				$logoPath = $this->uploadLogo($logo, $data['name']);
				$productionCompany->logo = $logoPath;
			}
			if($cP['bg'])
			{
				$coverPhotoPath = $this->uploadCp($cP, $data['name']);
				$productionCompany->cover_photo = $coverPhotoPath;
			}

			$productionCompany->save();

			return 'success';

		}catch (\Exception $e)
		{
			return 'error';
		}

	}

	/**
	 * Deletes the provided production company from database.
	 * 
	 * @param  mixed $id
	 * @return string message
	 */
	public function delete($id)
	{
		$prodCompany = ProductionCompany::find($id);

		if(!$prodCompany)
		{
			return 'not-found';
		}

		$prodCompany->delete();
		return 'success';
	}

	/**
	 * Updates production company information from input.
	 *
	 * @param $id
	 * @param  array $data
	 * @param null $logo
	 * @param null $cP
	 * @return string message
	 */
	public function update($id, $data)
	{
		$productionCompany = ProductionCompany::find($id);

		if(!$productionCompany)
		{
			return 'not-found';
		}

		$productionCompany->name = $data['name'];
		$productionCompany->description = $data['description'];
		$productionCompany->website = $data['website'];

		if($productionCompany->save())
		{
			return 'success';
		}
		else
		{
			return 'error';
		}
	}

	/**
	 * Uploads provided logo and associates with production company.
	 *
	 * @param $logo
	 * @param $name
	 * @return string
	 */
	public function uploadLogo($logo, $name)
	{
		$paths['big'] = "assets/uploads/production-companies/logos/".time().'-'.$name.'-logo'.'.jpg';
		$this->images->saveAvatar($logo, $paths, 300, 300);
		return $paths['big'];
	}

	/**
	 * Uploads provided cover photo and associates with production company.
	 *
	 * @param $logo
	 * @param $name
	 * @return string
	 */
	public function uploadCp($logo, $name)
	{
		$path = "assets/uploads/production-companies/cover-photos/".time().'-'.$name.'-cover-photo'.'.jpg';
		$this->images->saveBg($logo, $path);
		return $path;
	}

	public function find($id)
	{
		return $this->model->with('titles')->find($id);
	}

}