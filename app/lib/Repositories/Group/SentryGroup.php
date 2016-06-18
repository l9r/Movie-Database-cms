<?php namespace Lib\Repositories\Group;

use Carbon\Carbon;
use Cartalyst\Sentry\Facades\Laravel\Sentry;
use Cartalyst\Sentry\Groups\GroupExistsException;
use Cartalyst\Sentry\Groups\GroupNotFoundException;
use Cartalyst\Sentry\Groups\NameRequiredException;
use DB, Group, Event;
use Lib\Repository;

class SentryGroup extends Repository implements GroupRepositoryInterface
{

	/**
	 * Group model instance.
	 *
	 * @var Group
	 */
	protected $model;

	/**
	 * Group model instance.
	 * 
	 * @var Group
	 */
	private $group;

	public function __construct(Group $group)
	{
		$this->model = $group;
	}

	/**
	 * Creates a new group.
	 * 
	 * @param  array  $input
	 * @return void
	 */
	public function create(array $input)
	{
		//create base group array
		$newGroup = array(
		      'name'        => $input['name'],
		      'permissions' => array(
				  	'superuser' => $input['superuser'],
		      		'titles.edit' => $input['titles_edit'],
		      		'titles.create' => $input['titles_create'],
		      		'titles.delete' => $input['titles_delete'],
					/*'news.edit' 	 => $input['news_edit'],
					'news.create'    => $input['news_create'],
					'news.delete'    => $input['news_delete'],
					'reviews.create' => $input['reviews_create'],
					'reviews.edit' 	 => $input['reviews_edit'],
					'reviews.delete' => $input['reviews_delete'],*/
					'people.create'  => $input['people_create'],
					'people.edit' 	 => $input['people_edit'],
					'people.delete'  => $input['people_delete'],
				    'users.create'  => $input['users_create'],
				    'users.edit' 	 => $input['users_edit'],
				    'users.delete'  => $input['users_delete'],
				    'slides.create'  => $input['slides_create'],
				    'slides.edit' 	 => $input['slides_edit'],
				    'slides.delete'  => $input['slides_delete'],
				  	'actions.manage'  => $input['actions_manage'],
				  	'settings.manage'  => $input['settings_manage'],
				  	'ads.manage'  => $input['ads_manage'],
				    'reviews.delete'  => $input['reviews_delete'],
				    'links.approve'  => $input['links_approve'],
				    'links.delete'  => $input['links_delete'],
				    'groups.create'  => $input['groups_create'],
				    'groups.edit' 	 => $input['groups_edit'],
				    'groups.delete'  => $input['groups_delete'],
				    'production_companies.create'  => $input['production_companies_create'],
				    'production_companies.edit'    => $input['production_companies_edit'],
				    'production_companies.delete'  => $input['production_companies_delete'],
				    'tv_networks.create'  => $input['tv_networks_create'],
				    'tv_networks.edit'    => $input['tv_networks_edit'],
				    'tv_networks.delete'  => $input['tv_networks_delete'],
		      		)
		  		);

		Sentry::createGroup($newGroup);
		\Cache::flush();
		Event::fire('Groups.Created', array($input, Carbon::now(), 'Created'));
	}

	/**
	 * Updates a group info.
	 *
	 * @param  array  $id

	 */
	public function update(array $input, $id)
	{
		try
		{
			$group = Sentry::findGroupByName($id);
			$group->name = $input['name'];
			$group->permissions = array(
				'superuser' => $input['superuser'],
				'titles.edit' => $input['titles_edit'],
				'titles.create' => $input['titles_create'],
				'titles.delete' => $input['titles_delete'],
				/*'news.edit' 	 => $input['news_edit'],
				'news.create'    => $input['news_create'],
				'news.delete'    => $input['news_delete'],
				'reviews.create' => $input['reviews_create'],
				'reviews.edit' 	 => $input['reviews_edit'],
				'reviews.delete' => $input['reviews_delete'],*/
				'people.create'  => $input['people_create'],
				'people.edit' 	 => $input['people_edit'],
				'people.delete'  => $input['people_delete'],
				'users.create'  => $input['users_create'],
				'users.edit' 	 => $input['users_edit'],
				'users.delete'  => $input['users_delete'],
				'slides.create'  => $input['slides_create'],
				'slides.edit' 	 => $input['slides_edit'],
				'slides.delete'  => $input['slides_delete'],
				'actions.manage'  => $input['actions_manage'],
				'settings.manage'  => $input['settings_manage'],
				'ads.manage'  => $input['ads_manage'],
				'reviews.delete'  => $input['reviews_delete'],
				'links.approve'  => $input['links_approve'],
				'links.delete'  => $input['links_delete'],
				'groups.create'  => $input['groups_create'],
				'groups.edit' 	 => $input['groups_edit'],
				'groups.delete'  => $input['groups_delete'],
				'production_companies.create'  => $input['production_companies_create'],
				'production_companies.edit' 	 => $input['production_companies_edit'],
				'production_companies.delete'  => $input['production_companies_delete'],
				'tv_networks.create'  => $input['tv_networks_create'],
				'tv_networks.edit'    => $input['tv_networks_edit'],
				'tv_networks.delete'  => $input['tv_networks_delete'],
			);

			if ($group->save())
			{
				\Cache::flush();
				return 'success';
			}
			else
			{
				return 'Please check your input';
			}

		}
		catch (NameRequiredException $e)
		{
			return 'Name field is required';
		}
		catch (GroupExistsException $e)
		{
			return 'Group already exists.';
		}
		catch (GroupNotFoundException $e)
		{
			return 'Group was not found.';
		}
	}

	/**
	 * Deletes specified group.
	 * 
	 * @param  array $id

	 */
	public function delete($id)
	{
		try
		{
			$group = Sentry::findGroupById($id);
			$group->where('id', $id)->forceDelete();
			\Cache::flush();
			Event::fire('Groups.Deleted', array($id, Carbon::now(), 'Deleted'));
			return 'success';
		}
		catch (GroupNotFoundException $e)
		{
			return 'Group was not found.';
		}
	}

	/**
	 * Clears group table activity log.
	 * 
	 * @return void
	 */
	public function clearLog()
	{
		DB::table('group_activity')->truncate();

		Event::fire('Groups.logCleared', array(null, Carbon::now(), 'Log Cleared'));
	}

}