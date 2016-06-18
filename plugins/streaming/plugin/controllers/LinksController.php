<?php

use Lib\Lists\ListRepository;

class LinksController extends BaseController
{
	/**
	 * Link model instance.
	 * 
	 * @var Link
	 */
	private $model;

	public function __construct(Link $model)
	{
		$this->model = $model;

		if (App::environment() !== 'demo')
		{
			$this->beforeFilter('logged', array('only' => array('delete', 'deleteAll')));
		} 

		$this->beforeFilter('csrf', array('on' => 'post'));	
		$this->beforeFilter('links:approve', array('only' => 'approve'));
		$this->beforeFilter('links:delete', array('only' => array('delete', 'deleteAll', 'detach')));
	}

	public function approve($id)
	{
		$this->model->where('id', $id)->update(array('approved' => 1));

		Event::fire('eloquent.saved: Link', array($this->model));
		
		return Response::json(trans('stream::main.linkApproveSuccess'), 200);
	}

	/**
	 * Attach a link to a title.
	 * 
	 * @return Response
	 */
	public function attach()
	{
		$input = Input::except('_token');

		if ( ! Input::get('label') || ! Input::get('url') || ! Input::get('type')) {
			return Response::json(trans('stream::main.fillAllRequiredFields'), 400);
		}

		if ( ! isset($input['title_id']))
		{
			return Response::json(trans('dash.somethingWrong'), 500);
		}

		//if link is submitted by admin approve it, otherwise don't
		if (Helpers::hasAccess('superuser')) {
			$input['approved'] = 1;
		} else {
			$input['approved'] = 0;
		}

		//check if the url already exists if we are not updating an existing link
		if ( ! isset($input['id']) && $this->model->where('url', $input['url'])->first()) {
			return Response::json(trans('stream::main.urlExists'), 400);
		}

		if (isset($input['id']))
		{
			//make sure we don't overwrite reports with 0
			if (isset($input['reports'])) unset($input['reports']);

			$this->model->where('id', $input['id'])->update($input);
		}
		else
		{
			$this->model->fill($input)->save();
		}
		
		return Response::json($this->model, 201);
	}

	/**
	 * Detach link from specified title.
	 * 
	 * @return Response
	 */
	public function detach()
	{
		$input = Input::except('_token');

		if ( ! isset($input['title_id']))
		{
			return Response::json(trans('dash.somethingWrong'), 500);
		}

		$link = $this->model->where('url', $input['url'])->where('title_id', $input['title_id'])->first();
		
		if ($link && (! (int)$link->approved || Helpers::hasAccess('super'))) {
			$link->delete();
		}

		return Response::json(trans('stream::main.detachSuccess'), 200);
	}

	public function report()
	{
		$ip = Request::getClientIp();
		$id = Input::get('link_id');

		//if this ip already reported this link we'll bail with error message
		if (DB::table('reports')->where('link_id', $id)->where('ip_address', $ip)->first())
		{
			return Response::json(trans('stream::main.reportFail'), 400);
		}

		//increment reports by 1
		$this->model->where('id', $id)->increment('reports');

		//make note that this ip reported this link already so reports are unique per ip address
		DB::table('reports')->insert(array('ip_address' => $ip, 'link_id' =>  $id));

		return Response::json(trans('stream::main.reportSuccess'), 200);
	}

    public function rate() {
        $id     = Input::get('link_id');
        $method = Input::get('method') === 'decrement' ? 'decrement' : 'increment';
        $column = Input::get('rating') === 'positive' ? 'positive_votes' : 'negative_votes';

        $this->model->where('id', $id)->$method($column);

        return Response::json(trans('stream::main.voteSuccess'), 201);
    }

	/**
	 * Paginate all the links in database.
	 * 
	 * @return JSON
	 */
	public function paginate()
	{
		$repo = App::make('Lib\Repository');
		$repo->model = $this->model;

		return $repo->paginate(Input::all());
	}

	/**
	 * Delete a link with given id from database.
	 * 
	 * @param  int/string $id
	 * @return Response
	 */
	public function delete($id)
	{
		$this->model->destroy($id);

		return Response::json(trans('stream::main.linkDelSuccess'), 200);
	}

	/**
	 * Delete links that have more reports then passed number.
	 * 
	 * @return Response
	 */
	public function deleteAll()
	{
		if (Input::get('number') && Input::get('number') !== 0)
		{
			$this->model->where('reports', '>=', Input::get('number'))->delete();

			//fire event manually so we can flush the cache on it
			Event::fire('eloquent.deleted: Link', array($this->model));

			return Response::json(trans('stream::main.linkDelSuccess'), 200);
		}	
	}

}