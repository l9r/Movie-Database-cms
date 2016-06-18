<?php namespace Lib\Repositories\TVNetwork;

use Lib\Repository;
use Lib\Services\Images\ImageSaver;
use DB, TVNetwork;

class TVNetworkRepo extends Repository implements TVNetworkRepositoryInterface
{
	/**
	 * TVNetwork model instance.
	 * 
	 * @var $tvNetwork
	 */
	protected $model;

	/**
	 * Images handler instance.
	 * 
	 * @var Lib\Services\Images\ImageSaver
	 */
	private $images;

	public function __construct(TVNetwork $tvNetwork, ImageSaver $images)
	{
		$this->model   = $tvNetwork;
		$this->images = $images;
	}

	/**
	 * Add new tv network.
	 * 
	 * @param  array $data
	 * @return string message
	 */
	public function add($data, $logo = null, $cP = null)
	{
		$tvNetwork = new TVNetwork;
		$tvNetwork->name = $data['name'];
		$tvNetwork->description = $data['description'];
		$tvNetwork->website = $data['website'];

		try
		{
			if($logo['avatar'])
			{
				$logoPath = $this->uploadLogo($logo, $data['name']);
				$tvNetwork->logo = $logoPath;
			}
			if($cP['bg'])
			{
				$coverPhotoPath = $this->uploadCp($cP, $data['name']);
				$tvNetwork->cover_photo = $coverPhotoPath;
			}

			$tvNetwork->save();

			return 'success';

		}catch (\Exception $e)
		{
			return 'error';
		}

	}

	/**
	 * Deletes the provided tv network from database.
	 * 
	 * @param  mixed $id
	 * @return string message
	 */
	public function delete($id)
	{
		$tvNetwork = TVNetwork::find($id);

		if(!$tvNetwork)
		{
			return 'not-found';
		}

		$tvNetwork->delete();
		return 'success';
	}

	/**
	 * Updates tv network information from input.
	 *
	 * @param $id
	 * @param  array $data
	 * @param null $logo
	 * @param null $cP
	 * @return string message
	 */
	public function update($id, $data)
	{
		$tvNetwork = TVNetwork::find($id);

		if(!$tvNetwork)
		{
			return 'not-found';
		}

		$tvNetwork->name = $data['name'];
		$tvNetwork->description = $data['description'];
		$tvNetwork->website = $data['website'];

		if($tvNetwork->save())
		{
			return 'success';
		}
		else
		{
			return 'error';
		}
	}

	/**
	 * Uploads provided logo and associates with tv network.
	 *
	 * @param $logo
	 * @param $name
	 * @return string
	 */
	public function uploadLogo($logo, $name)
	{
		$paths['big'] = "assets/uploads/tv-networks/logos/".time().'-'.$name.'-logo'.'.jpg';
		$this->images->saveAvatar($logo, $paths, 300, 300);
		return $paths['big'];
	}

	/**
	 * Uploads provided cover photo and associates with tv network.
	 *
	 * @param $logo
	 * @param $name
	 * @return string
	 */
	public function uploadCp($logo, $name)
	{
		$path = "assets/uploads/tv-networks/cover-photos/".time().'-'.$name.'-cover-photo'.'.jpg';
		$this->images->saveBg($logo, $path);
		return $path;
	}

	public function find($id)
	{
		return $this->model->with('titles')->find($id);
	}

}