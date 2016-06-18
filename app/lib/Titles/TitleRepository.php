<?php namespace Lib\Titles;

use Carbon\Carbon;
use Lib\Repository;
use Lib\Services\Db\Writer;
use Intervention\Image\Image;
use Lib\Services\Images\ImageSaver as Imgs;
use Char, Actor, Title, Helpers, Event, App, DB;
use Lib\Repositories\Data\DataRepositoryInterface as Data;
use Lib\Reviews\ReviewRepository as RevRepo;

class TitleRepository extends Repository
{
    /**
     * Title model instance.
     *
     * @var Title
     */
    protected $model;

    /**
     * Writer instance.
     *
     * @var Lib\Services\Db\Writer
     */
    public $dbWriter;

    /**
     * Review model instance.
     *
     * @var Review
     */
    private $review;

    /**
     * ImagesHandler instance.
     *
     * @var Lib\Services\Handlers\ImagesHandler
     */
    private $images;

    /**
     * Data provider instance.
     *
     * @var Lib\Repositories\Data\DataProviderInterface
     */
    public $provider;

    /**
     * Options instace.
     *
     * @var Lib\Services\Options\Options
     */
    private $options;

    public function __construct(Title $title, Writer $dbWriter, Imgs $images, RevRepo $review, Data $provider)
    {
        $this->model    = $title;
        $this->dbWriter = $dbWriter;
        $this->images   = $images;
        $this->review   = $review;
        $this->provider = $provider;

        $this->options = App::make('options');
    }

    /**
     * Return new and upcoming movies.
     *
     * @param string/int $limit
     * @return Collection
     */
    public function newAndUpcoming($limit = 8)
    {
        return $this->model->whereNotNull('poster')
            ->whereNotNull('trailer')
            ->whereNotNull('release_date')
            ->whereNotNull('background')
            ->whereNotNull('genre')
            ->whereType('movie')
            ->has('director')
            ->where('release_date', '>=', Carbon::now()->addDays(-14)->toDateString())
            ->where('release_date', '<=', Carbon::now()->addDays(14)->toDateString())
            ->where('language', 'english')
            ->orderBy('tmdb_popularity', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Return new and upcoming movies.
     *
     * @param string/int $limit
     * @return Collection
     */
    public function topRated($limit = 8)
    {
        return $this->model->whereNotNull('poster')
            ->whereNotNull('mc_user_score')
            ->where('mc_num_of_votes', '>', 200)
            ->where('type', 'movie')
            ->where('release_date', '>=', Carbon::now()->addYears(-1))
            ->orderBy('mc_user_score', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Return most popular movies/series.
     *
     * @param string/int $limit
     * @return Collection
     */
    public function mostPopular($limit = 8)
    {
        return $this->model->whereNotNull('poster')
            ->whereNotNull('release_date')
            ->orderBy('views', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Restrict paginate query by given params.
     *
     * @param  array $params
     * @param  Builder $query
     * @return Builder
     */
    protected function appendParams(array $params, $query)
    {
        //append all the params to query from base paginate method
        $query = parent::appendParams($params, $query);

        //filter by genre
        if (isset($params['genres']))
        {
            $query = $query->hasGenres($params['genres']);
        }

        //filter to only ones where given actor appears in
        if (isset($params['cast']))
        {
            $query = $query->whereHas('actor', function($q) use ($params)
            {
                $q->where('name', 'like', $params['cast']);
            });
        }

        //filter to only ones user has added to given list
        if (isset($params['listName']) && isset($params['userId']))
        {
            $query = $query->inUsersList($params['listName'], (int) $params['userId']);
        }

        if ((isset($params['minRating']) && $params['minRating']) && (int) $params['minRating'] !== 1)
        {
            $query = $query->where('mc_user_score', '>=', (int) $params['minRating']);
        }

        if ((isset($params['maxRating']) && $params['maxRating']) && (int) $params['maxRating'] !== 10)
        {
            $query = $query->where('mc_user_score', '<=', (int) $params['maxRating']);
        }

        //Only return title released after given date
        if (isset($params['before']) && $date = Helpers::parseDate($params['before']))
        {
            $query = $query->where('release_date', '<=', Helpers::parseDate($params['before']));
        }

        //Only return titles released before given date
        if (isset($params['after']) && $date = Helpers::parseDate($params['after']))
        {
            $query = $query->where('release_date', '>=', Helpers::parseDate($params['after']));
        }

        return $query->whereNotNull('poster');
    }

    /**
     * Updates titles reviews from metacritic.
     *
     * @return void
     */
    public function updateReviews(Title $title)
    {
        $this->review->get($title)->parse()->saveFromMetacritic();
    }

    /**
     * Fetches and saves all available data about title.
     *
     * @param  Title $title
     * @return Title
     */
    public function getCompleteTitle(Title $title)
    {
        $provider = $this->provider->getFullTitle($title);

        $this->updateReviews($title);

        Event::fire('Titles.FullyScraped', array($provider, Carbon::now()));

        return $this->saveAndReturn($provider, $title->id);
    }

    /**
     * Get next and previous episode for latest season.
     *
     * @param  Title $title
     * @return stdClass
     */
    public function getNextPrevEpisodes($title)
    {
        $episodes = new \stdClass();

        $last = $title->season->last();

        if ($last) {
            $eps = $last->episode;

            foreach ($eps as $k => $ep)
            {
                $released = Carbon::parse($ep->release_date);

                if ($released->isFuture()) {
                    $episodes->next = $ep;
                    $episodes->previous = $eps[$k-1];
                    break;
                }
            }
        }

        return $episodes;
    }

    /**
     * Fetches title from db using uri 245-game-of-thrones etc.
     *
     * @param  string $title
     * @return Title
     */
    public function byUri($title)
    {
        //extract titles id in database from url
        $id = Helpers::extractId($title);

        //get title from db
        $title = $this->model->byId($id);

        //if title doesnt have any providers ids, is not created by user and provider is not db
        //we'll bail with 404
        if ( ! $title->imdb_id && ! $title->tmdb_id && $title->alow_update && $this->provider->name != 'db')
        {
            \App::abort(404);
        }

        return $title;
    }

    /**
     * Saves title to database and returns it.
     *
     * @param  DataProviderInterface $provider
     * @param  int $id
     * @return Title
     */
    public function saveAndReturn($provider, $id)
    {
        if ($provider && $id)
        {
            $str = str_random(15);

            $this->dbWriter->setProvider($provider, $id)
                ->setFlags( array('fully_scraped' => 1, 'temp_id' => $str) )
                ->saveAll();

            //we'll load title by temp id incase something gets
            //messed up because of different language titles
            return $this->byTempId($str);
        }
    }

    /**
     * Fetches single title by id from db.
     *
     * @param  int/string $id
     * @return Title
     */
    public function byId($id)
    {
        return $this->model->byId($id);
    }

    /**
     * Fetches single title by temp id from db.
     *
     * @param  int/string $id
     * @return Title
     */
    public function byTempId($id)
    {
        return $this->model->where('temp_id', $id)->firstOrFail();
    }

    /**
     * Checks if given title needs to be fully scraped.
     *
     * @param  Title  $title
     * @return boolean
     */
    public function needsScraping(Title $title)
    {
        $needs = true;

        //first check for fully_scraped flag, if its true
        //we wont update
        if ($title->fully_scraped)
        {
            $needs = false;
        }

        //next check if it was 5 days  since last update
        //if so we'll update title now
        if ( ! $title->updated_at || $title->updated_at->addDays(6) <= Carbon::now())
        {
            $needs = true;
        }

        $date = date('Y-m-d', strtotime($title->release_date));
        if ($title->review->isEmpty() && $date < Carbon::now()->toDateString() && $title->updated_at->addDays(1) <= Carbon::now())
        {
            $needs = true;
        }

        //finally check for provider and if we're allowed to update
        //the title
        if ($this->provider->name == 'db' || ! $title->allow_update || (! $title->tmdb_id && ! $title->imdb_id))
        {
            return false;
        }

        return $needs;
    }

    /**
     * Deletes a title from database.
     *
     * @param  int/string $id
     * @return void
     */
    public function delete($id)
    {
        $this->model->findOrFail($id)->delete();

        Event::fire('Titles.Deleted', array($this->model, Carbon::now()));
    }

    /**
     * Remove relevant records from pivot table.
     *
     * @param array $input
     * @return void
     */
    public function detachPeople($input)
    {
        //if we don't have the id we'll have to fetch it from db first
        if ( ! isset($input['resourceId']) && isset($input['resourceName']))
        {
            $resource = DB::table($input['type'].'s')->where('name', $input['resourceName'])->get(array('id'));
            $id = head($resource)->id;
        }
        else
        {
            $id = $input['resourceId'];
        }

        DB::table($input['type'].'s_titles')->where($input['type'].'_id', $id)->where('title_id', $input['titleId'])->delete();
    }

    /**
     * Upload and associate image to title.
     *
     * @param array $input
     * @return  void
     */
    public function uploadImage(array $input)
    {
        $title = $this->model->find($input['title-id']);
        $name  = str_random(25);
        $insert = array('local' => asset('assets/images/'.$name.'.jpg'), 'title_id' => $input['title-id']);

        $this->images->saveTitleImage($input, $name);
        $this->dbWriter->compileInsert('images', $insert)->save();

        Event::fire('Titles.Modified', array($input['title-id']));
    }

    /**
     * Fetches specified amount of titles that need scraping.
     *
     * @param  integer $amount
     * @return Collection
     */
    public function scrapable($amount = 10)
    {
        return $this->model->where('fully_scraped', 0)
            ->where('allow_update', 1)
            ->orderBy( Helpers::getOrdering(), 'desc')
            ->limit( (int) $amount)
            ->get();
    }
}