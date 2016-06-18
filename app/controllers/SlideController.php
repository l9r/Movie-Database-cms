<?php

use Lib\Slides\SlideRepository;

class SlideController extends BaseController {

    /**
     * Slide repository implementation.
     * 
     * @var Lib\Repositories\Slide\SlideRepositoryInterface
     */
    private $repo;

    /**
     * Create new Slide Controller instance.
     */
    public function __construct(SlideRepository $repo)
    {
    	$this->repo = $repo;

        $this->beforeFilter('logged', array('only' => array('postAdd', 'postRemove')));
        $this->beforeFilter('slides_create_edit', array('only' => array('postAdd')));
        $this->beforeFilter('slides:delete', array('only' => array('postRemove')));

        $this->beforeFilter('csrf', array('only' => array('postAdd', 'postRemove')));
    }

    /**
     * Save new slide to database.
     * 
     * @return JSON
     */
    public function postAdd()
    {
        $input = Input::except('_token');

        $this->repo->save($input);

        return Response::json(trans('dash.slideSaveSuccess'), 201);
    }

    /**
     * Delete a slide from database.
     * 
     * @return JSON
     */
    public function postRemove()
    {
    	$input = Input::except('_token');
        
    	$this->repo->delete($input);

    	return Response::json(trans('dash.slideDeleteSuccess'), 200);
    }

}