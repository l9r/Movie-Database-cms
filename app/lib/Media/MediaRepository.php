<?php namespace Lib\Media;

use Image as Model;
use Lib\Repository;
use Intervention\Image\Image;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile as Upload;

class MediaRepository extends Repository {

	/**
	 * Media Model instance.
	 * 
	 * @var Media
	 */
	protected $model;

	/**
	 * Laravel filesystem instance.
	 * 
	 * @var use Illuminate\Filesystem\Filesystem
	 */
	private $fs;

	/**
	 * Intervention image library instance.
	 * 
	 * @var Intervention\Image\Image
	 */
	private $imagine;

	/**
	 * Create new EloMedia instance.
	 * 
	 * @param Media      $model
	 * @param Filesystem $fs
	 */
	public function __construct(Model $model, Filesystem $fs, Image $imagine)
	{
		$this->fs = $fs;
		$this->model = $model;
		$this->imagine = $imagine;
	}

	/**
	 * Find image by path.
	 * 
	 * @param  string $path
	 * @return Image
	 */
	public function byPath($path)
	{
		return $this->model->where('local', $path)->get()->first()->toArray();
	}

	/**
	 * Save image in filesystem and db and return the image url.
	 * 
	 * @param  Upload  $image        
	 * @param  boolean $useOriginalName
	 * @return Image
	 */
	public function saveImage(Upload $image, $useOriginalName = false)
	{
		$path = $this->makePath($image, $useOriginalName);
		
		$this->imagine->make($image->getRealPath())->save($path, 80);

		//save relative path in database instead of absolute so the site
		//won't get messed up if user changes domains.
		$relative = str_replace(public_path().'/', '', $path);

		$this->model->firstOrNew(array('local' => $relative, 'type' => 'upload'))->save();

		return $this->byPath($relative);
	}

	/**
	 * Makes a fully qualifies image path with either
	 * random name or original name.
	 * 
	 * @param  Upload  $image      
	 * @param  boolean $useOriginal
	 * @return string
	 */
	public function makePath(Upload $image, $useOriginal)
	{
		$base = public_path('assets/uploads/images/');

		if ($useOriginal)
		{	
			return $base.$image->getClientOriginalName().'.'.$image->getClientOriginalExtension();
		}

		return $base.str_random(10).'.'.$image->getClientOriginalExtension();
	}

	/**
	 * Delete media item from database as 
	 * well as filesystem.
	 * 
	 * @param  integer $id
	 * @return boolean
	 */
	public function destroy($id)
	{
		$img = $this->model->where('id', $id)->select('local')->first();

		$this->fs->delete(public_path($img->locale));

		return $this->model->destroy($id);
	}

}