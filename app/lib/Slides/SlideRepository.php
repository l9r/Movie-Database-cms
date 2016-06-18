<?php namespace Lib\Slides;

use Slide;
use Lib\Repository;

class SlideRepository extends Repository {

	/**
	 * Slide Model instance.
	 * 
	 * @var Slide
	 */
	protected $model;

	/**
	 * Create new Slide repository instance.
	 * 
	 * @param Slide $model
	 */
	public function __construct(Slide $model)
	{
		$this->model = $model;
	}

	/**
	 * Get the specified amount of slides.
	 * 
	 * @param  integer $limit
	 * @return Collection
	 */
	public function get($limit = 8)
    {
        return $this->model->limit($limit)->cacheTags('slides')->remember(2000)->get();
    }

	/**
	 * Save new slide to database.
	 * 
	 * @param  array  $input
	 * @return integer
	 */
	public function save(array $input)
	{
		if (isset($input['id']))
		{
			return $this->update($input);
		}

		foreach ($input as $attr => $value)
		{
			$this->model->$attr = $value;
		}
	
		return $this->model->save();
	}

	/**
	 * Update a slide in database.
	 * 
	 * @param  array  $input
	 * @return boolean
	 */
	public function update(array $input)
	{
		$model = $this->model->find($input['id']);

		foreach ($input as $attr => $value)
		{
			$model->$attr = $value;
		}

		return $model->save();
	}

	/**
	 * Delete slide from database.
	 * 
	 * @param  array  $input
	 * @return boolean
	 */
	public function delete(array $input)
	{
		if (isset($input['id']))
		{
			return $this->model->destroy($input['id']);
		}	
	}
}