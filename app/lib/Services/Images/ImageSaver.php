<?php namespace Lib\Services\Images;

use File, Imagine;
use Lib\Services\Scraping\Curl;

class ImageSaver
{
	/**
	 * Curl instance.
	 * 
	 * @var Lib\Services\Scraping\Curl
	 */
	protected $scraper;

	public function __construct(Curl $scraper)
	{
		$this->scraper = $scraper;
	}

	/**
	 * Downloads and saves single image locally.
	 * 
	 * @param  string $url 
	 * @param  string $id
	 * @param  string/null $path
	 * @param  string/int $num
	 * @return void
	 */
	public function saveSingle($url, $id, $path = null, $num = '')
	{
		if ( ! $url) return;

		if ( ! $path)
		{
			$path = 'imdb/posters/';
		}
		
    	$image = $this->scraper->curl($url);
    	
    	//catch error if image we get passed is corrupt and return
    	//false so we wont save a reference of image that doesnt 
    	//exist in database.
    	try
    	{
    		Imagine::raw($image)->save(public_path($path) . $id . $num .'.jpg', 100);
    	}
    	catch(\Intervention\Image\Exception\InvalidImageDataStringException $e)
    	{
    		return false;
    	}
    	
    	return true;
	}

	/**
	 * Downloads and saves multiple images locally.
	 * 
	 * @param  string $url 
	 * @param  string $id
	 * @param  string/null $path
	 * @param  string/int $num
	 * @return void
	 */
	public function saveMultiple(array $urls, $id = null, $path)
	{
		if ( empty($urls)) return;

    	$images = $this->scraper->multiCurl($urls);
    	
    	foreach ($images as $k => $v)
    	{ 	
    		//we're saving cast images
    		if (strpos($k, 'nm') || ! $id)
    		{
    			try
    			{
    				Imagine::raw($v)->save(public_path($path) . $k . '.jpg', 100);
    			}
    			catch(\Exception $e){}
    			
    		}

    		//we're saving movie stills
    		else
    		{
    			try
    			{
    				$image = Imagine::raw($v);
    				$image->save(public_path($path) . $id . $k.'.jpg', 60);
    				$image->resize(400, null, function ($constraint) {
					    $constraint->aspectRatio();
					})->save(public_path($path) . $id . $k.'.thumb'.'.jpg', 90);
    			}
    			catch(\Exception $e){}
    		}   		
    	}
	}

	/**
	 * Saves avatar in filesystem.
	 * 
	 * @param  UploadedImage $image
	 * @param  string $path
	 * @return void
	 */
	public function saveAvatar($image, array $paths, $bsize = 100, $smsize = 100)
	{
		foreach ($paths as $k => $v)
		{
			//delete any previous user avatars
			File::delete(public_path($v));

			if ($k == 'big')
			{
				$encoded = Imagine::make($image['avatar']->getRealPath())
					->resize($bsize, $smsize)
					->encode('jpg');

				Imagine::make($encoded)->save(public_path($v));	
			}
			else
			{
				$encoded = Imagine::make($image['avatar']->getRealPath())
					->resize(35, 35)
					->encode('jpg');

				Imagine::make($encoded)->save(public_path($v));
			}
		}			
	}

	/**
	 * Saves user background in filesystem.
	 * 
	 * @param  UploadedImage $image
	 * @param  string $path
	 * @return void
	 */
	public function saveBg($image, $path)
	{
		$encoded = Imagine::make($image['bg']->getRealPath())
			->resize(1140, 400, true)
			->encode('jpg');

		Imagine::make($encoded)->save(public_path($path));			
	}

	/**
	 * Saves title image locally.
	 * 
	 * @param  UploadedImage $image
	 * @param  string $path
	 * @return void
	 */
	public function saveTitleImage($input, $name)
	{
		$encoded = Imagine::make($input['image']->getRealPath())
			->encode('jpg');

		Imagine::make($encoded)->save(public_path('assets/images/'.$name.'.jpg'));	
	}
}