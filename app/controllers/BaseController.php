<?php

class BaseController extends Controller {

	public function paginate()
	{
		return $this->repo->paginate(Input::except('_token'));
	}

}