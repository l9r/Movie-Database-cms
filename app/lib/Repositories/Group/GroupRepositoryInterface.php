<?php namespace Lib\Repositories\Group;

interface GroupRepositoryInterface
{
	/**
	 * Creates a new group.
	 * 
	 * @param  array  $input
	 * @return void
	 */
	public function create(array $input);

	/**
	 * Updates a group.
	 *
	 * @param  array  $input

	 */
	public function update(array $input, $id);

	/**
	 * Deletes specified group.
	 * 
	 * @param  array $id

	 */
	public function delete($id);

	/**
	 * Clears group table activity log.
	 * 
	 * @return void
	 */
	public function clearLog();
}