<?php namespace Lib\News;

use News, Event;
use Carbon\Carbon;
use Lib\Repository;
use Lib\Services\Db\Writer;
use Lib\Services\Scraping\NewsScraper;
use Lib\Repositories\News\NewsRepositoryInterface;

class NewsRepository extends Repository
{
	/**
	 * Writer instance.
	 * 
	 * @var Lib\Services\Db\Writer
	 */
	private $dbWriter;

	/**
	 * News model instance.
	 * 
	 * @var News
	 */
	protected $model;

	/**
	 * News scraper instance
	 * 
	 * @var Lib\Services\Scraping\NewsScraper
	 */
	private $scraper;

	/**
	 * Instantiate dependencies.
	 * 
	 * @param Scraper $scraper
	 */
	public function __construct(Writer $dbWriter, News $news, NewsScraper $scraper)
	{
		
		$this->model     = $news;
		$this->scraper  = $scraper;
		$this->dbWriter = $dbWriter;
	}

	/**
	 * Fetches the data needed to make news
	 * index page.
	 * 
	 * @return Collection
	 */
	public function index()
	{
		return $this->model->newsIndex();
	}

	/**
     * Returns latest news items.
     * 
     * @param  int/string $limit
     * @return collection
     */
    public function latest($limit = 8)
    {
        //cache for 3 days
        return $this->model->orderBy('created_at', 'desc')->limit($limit)->remember(4320, 'news.latest')->get();
    }
	

	/**
	 * Stores new news item to database.
	 * 
	 * @param  array $input
	 * @return void
	 */
	public function store(array $input)
	{
		foreach ($input as $k => $v)
		{
			$this->model->$k = $v;
		}

		$this->model->save();

		Event::fire('News.Created', Carbon::now());
	}

	/**
	 * Handles news item deletion.
	 * 
	 * @param  int/string $id
	 * @return void
	 */
	public function delete($id)
	{
		$this->model->destroy($id);

		Event::fire('News.Deleted', Carbon::now());
	}

	/**
	 * Handles news items fetching from db.
	 * 
	 * @param  int/string $id
	 * @return News model
	 */
	public function byId($id)
	{
		return $this->model->findOrFail($id);
	}

	/**
	 * Handles news items updating.
	 * 
	 * @param  News $news
	 * @param  array $input
	 * @return void
	 */
	public function update(News $news, array $input)
	{
		foreach ($input as $k => $v)
		{
			if ($k == 'updateTitle')
			{
				$news->title = $v;
			}
			else
			{
				$news->$k = $v;
			}		
		}

		$news->save();

		Event::fire('News.Updated', Carbon::now());
	}

	/**
	 * Get and save full news item body from screenrant.
	 * 
	 * @param  News   $news 
	 * @return News
	 */
	public function getFullNewsItem(News $news)
	{
		$html = $this->scraper->getSingle($news->full_url);
		
		$news->body = $html;
		$news->fully_scraped = 1;
		$news->save();

		return $news;
	}
}