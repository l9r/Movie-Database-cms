<?php

use Carbon\Carbon;
use Lib\Services\Validation\GroupValidator;
use Lib\Repositories\Group\GroupRepositoryInterface as Repo;


class GroupController extends \BaseController {

	/**
	 * Validator instance.
	 * 
	 * @var Lib\Services\Validation\GroupValidator
	 */
	private $validator;

	/**
	 * Group repository instance.
	 * 
	 * @var Lib\Repositories\Group\GroupRepositoryInterface;
	 */
	private $group;

	public function __construct(GroupValidator $validator, Repo $group)
	{

		$this->beforeFilter('view_groups', array('only' => array('paginate')));

		$this->beforeFilter('groups:create', array('only' => array('store', 'createNew')));
		$this->beforeFilter('groups:delete', array('only' => array('destroy')));
		$this->beforeFilter('groups:edit', array('only' => array('update')));

		$this->beforeFilter('csrf', array('on' => 'post'));
		$this->validator = $validator;
		$this->group = $group;
	}

	/**
	 * Creates a new group.
	 *
	 * @return Redirect
	 */
	public function store()
	{	
		$input = Input::except('_token');

		if ( ! $this->validator->with($input)->passes())
		{
			return Redirect::back()->withErrors($this->validator->errors())->withInput($input);
		}

		$this->group->create($input);

		return Redirect::back()->withSuccess( trans('group created successfully') );
	}

	/**
	 * Create a new user. Admin only.
	 * 
	 * @return JSON
	 */
	public function createNew()
	{
		$input = Input::except('_token');

		if ( ! $this->validator->with($input)->passes())
		{
			return Response::json($this->validator->errors(), 400);
		}

		$this->group->create($input, true);
		\Cache::flush();
		return Response::json('Group created successfully', 201);
	}
	
	
	/**
	 * Update a new group.
	 *
	 * @return Redirect
	 */
	public function update($id)
	{
	
		$input = Input::except('_token');
		/*$name = Input::get('name');
		$permissions = Input::get('permissions');*/
		$result = $this->group->update($input, $id);

		if($result == 'success')
		{
			\Cache::flush();
			return Response::json('Group Updated successfully', 201);
		}

		//$result = $this->group->update($input, $id);
		/*DB::table('groups')
            ->where('name', $id)
            ->update(array('name' => $name, 'permissions' => $permissions));*/
		//$this->group->register($input, true);

		return Response::json('Please check your input', 400);

	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$this->group->delete($id);
		\Cache::flush();
		return Response::json('Group Deleted successfully', 201);
	}

	/**
	 * Clears the group activity logs in db.
	 * 
	 * @return Redirect back.
	 */
	public function clear()
	{
		$this->group->clearLog();	

		return Redirect::back()->with('Response', 'Group activity logs cleared successfully!');
	}


	/**
	 * Return groups for pagination.
	 *
	 * @return JSON
	 */
	public function paginate()
	{
		return $this->group->paginate(Input::except('_token'));
	}

}