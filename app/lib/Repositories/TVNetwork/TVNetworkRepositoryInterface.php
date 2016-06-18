<?php namespace Lib\Repositories\TVNetwork;

use TVNetwork;

interface TVNetworkRepositoryInterface
{
	
	/**
	 * Add new tv network.
	 * 
	 * @param  array $data
	 * @return self
	 */
	public function add($data, $logo = null, $cP = null);

	/**
	 * Deletes the provided tv network from database.
	 * 
	 * @param  integer $id
	 * @return  string.
	 */
	public function delete($id);

	/**
	 * Updates tv network information from input.
	 *
	 * @param $id
	 * @param  array $data
	 * @param null $logo
	 * @param null $cP
	 * @return
	 * @internal param TVNetwork $tvNetwork
	 */
	public function update($id, $data);

	/**
	 * Uploads provided logo and associates with tv network.
	 *
	 * @param  $logo
	 * @param $name
	 * @return
	 */
	public function uploadLogo($logo, $name);

	/**
	 * Uploads provided cover photo and associates with tv network.
	 *
	 * @param  $logo
	 * @param $name
	 * @return
	 */
	public function uploadCp($logo, $name);

	public function find($id);

}