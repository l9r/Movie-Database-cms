<?php namespace Lib\Services\Cache;

use App, Cache, Exception;

class CacheInvalidator {

	/**
	 * Models and their corresponding cache
	 * keys and tags that need to be flushed.
	 * 
	 * @var array
	 */
	private $keys = array(
		'Review'   => array('reviews.count'),
		'News'     => array('news.latest', 'news.count', '#news.pagination', '#news.singles'),	 
		'Title'    => array('titles.latest', 'titles.topRated', '#titles.pagination', '#titles.singles'),
		'Image'    => array('#images.pagination'),
		'Actor'    => array('#actors.pagination', 'actors.popular'),
		'Slide'    => array('#slides'),
		'User'     => array('#users.pagination'),
		'Category' => array('#categories.pagination'),
		'Link'     => array('#links.pagination'),
		'Page'     => array('#pages.pagination'),
	);

    /**
     * Listen for eloquent saved and deleted events.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     * @return void
     */
    public function subscribe($events)
    {
        $events->listen('eloquent.saved: *', 'Lib\Services\Cache\CacheInvalidator@invalidate');
        $events->listen('eloquent.deleted: *', 'Lib\Services\Cache\CacheInvalidator@invalidate');
    }

	/**
	 * Invalidate the cache using keys in $keys array.
	 * 
	 * @param  mixed $model
	 * @return void
	 */
	public function invalidate($model)
	{
		$name = get_class($model);
		
		if (isset($this->keys[$name]))
		{
			foreach ($this->keys[$name] as $key)
			{
				//catch exception in case we're trying to flush a
				//directory that doesn't exist in filesystem
				try {
					$this->flush($key);
				} catch (Exception $e) {}
			}
		}
	}

	/**
	 * Flush cache by specified key.
	 * 
	 * @param  string $key
	 * @return void
	 */
	private function flush($key)
	{
		//if key contains # symbol it means we need to flush
		//all the caches tagged with that key,otherwise we will
		//just flush single cache matching this key
		if (str_contains($key, '#'))
		{
			//separate tags are delimited by dot so we'll explod
			//by that
			$tags = explode('.', str_replace('#', '', $key));
	
			Cache::tags($tags)->flush();
		}
		else
		{
			Cache::forget($key);
		}

		//flush count always
		Cache::tags('count')->flush();
	}
}