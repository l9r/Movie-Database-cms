<?php namespace Lib\Repositories\ProductionCompany;

use ProductionCompany;

interface ProductionCompanyRepositoryInterface
{
	
	/**
	 * Add new production company.
	 * 
	 * @param  array $data
	 * @return self
	 */
	public function add($data, $logo = null, $cP = null);

	/**
	 * Deletes the provided production company from database.
	 * 
	 * @param  integer $id
	 * @return  string.
	 */
	public function delete($id);

	/**
	 * Updates production company information from input.
	 *
	 * @param $id
	 * @param  array $data
	 * @param null $logo
	 * @param null $cP
	 * @return
	 * @internal param ProductionCompany $productionCompany
	 */
	public function update($id, $data);

	/**
	 * Uploads provided logo and associates with production company.
	 *
	 * @param  $logo
	 * @param $name
	 * @return
	 */
	public function uploadLogo($logo, $name);

	/**
	 * Uploads provided cover photo and associates with production company.
	 *
	 * @param  $logo
	 * @param $name
	 * @return
	 */
	public function uploadCp($logo, $name);

	public function find($id);

}