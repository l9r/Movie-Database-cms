<?php namespace Lib\Titles;

use Carbon\Carbon;
use Lib\Services\Db\Writer;
use Title, Season, Episode, Helpers, App;
use Lib\Repositories\Data\DataRepositoryInterface as Provider;

class SeasonRepository
{
    /**
     * Season model instance.
     * 
     * @var Season
     */
    private $season;

    /**
     * dbWriter instance.
     * 
     * @var \Lib\Services\Db\Writer
     */
    private $dbWriter;

    /**
     * Episode model instance.
     * 
     * @var Episode
     */
    private $episode;

    /**
     * Title model instance.
     * 
     * @var Title
     */
    private $title;

    /**
     * Data provider instance.
     * 
     * @var \Lib\Repositories\Data\DataProviderInterface;
     */
    private $provider;

    public function __construct(Season $season, Writer $dbWriter, Episode $episode, Title $title, Provider $provider) {
        $this->season   = $season;
        $this->dbWriter = $dbWriter;
        $this->episode  = $episode;
        $this->title    = $title;
        $this->provider = $provider;
    }

    /**
     * Prepares single season for displaying.
     * 
     * @param  Title  $title
     * @param  string $num
     * @return Title
     */
    public function prepareSingle(Title $title, $num)
    {
        //if we're not allowed to update the title or data provider is db, bail
        if ( ! $title->allow_update || $this->provider->name === 'db') {
            return $title;
        }

        //update season using themoviedb
        if ($this->provider->name === 'tmdb' && $title->tmdb_id) {
            return $this->fetchFromTmdb($title);
        }

        //update season using IMDb
        else if ($this->provider->name === 'imdb' && $title->imdb_id) {
            return $this->fetchFromImdb($title, $num);
        }

        return $title;
    }

    /**
     * Handles single season loading if provider is not tmdb.
     * 
     * @param  DataProviderInterface $provider
     * @param  Title $title
     * @param  int/string $num
     * @return Title
     */
    public function fetchFromImdb($title, $num)
    {
        $season = Helpers::extractSeason($title, $num);

        //get all episodes for season
        $episodes = $this->provider->getSingleSeason($title, $num);

        if ( ! $episodes) $episodes = array();
                
        //insert episodes and change fully_scraped flag to 1
        $this->dbWriter->CompileBatchInsert('episodes', $episodes)->save();
        $this->dbWriter->CompileInsert('seasons', array('id' => $season->id, 'fully_scraped' => 1, 'updated_at' => Carbon::now()))->save();

        return $title->with('season.episode')->findOrFail($title->id);
    }

    /**
     * Fetches all seasons and episodes for given series.
     * 
     * @param  Title  $title    
     * @param  TmdbParser $provider
     * @return Title
     */
    private function fetchFromTmdb(Title $title)
    {
        $first = $title->season->first();

        if ( ! $first) App::abort(404);

        //if first season is fully scraped and has tmdb id
        //means all seasons are fully scraped, so we'll just return
        if ($first->fully_scraped && $first->title_tmdb_id && Carbon::parse($first->updated_at)->addDays(7) >= Carbon::now()) {
            return $title;
        }

        $seasons = $this->provider->getFullAllSeasons($title);
        $this->dbWriter->saveFullAllSeasons($seasons);

        return $title->with('season.episode')->findOrFail($title->id);
    }

    /**
     * Finds season by title id and season number.
     * 
     * @param  string $title
     * @param  int/string $num
     * @return Season/404
     */
    public function findById($id, $num)
    {
        return $this->title->find($id)->season()->whereNumber($num)->firstOrFail();
    }

    /**
     * Handles new season creation from input.
     * 
     * @param  array $input
     * @return Season
     */
    public function create(array $input)
    {
        $temp = str_random(10);
        $input['temp_id'] = $temp;

        $this->dbWriter->CompileInsert('seasons', $input)->save();

        return $this->season->where('temp_id', $temp)->limit(1)->get(array('id'))->first();
    }

    /**
     * Handles season deletion.
     * 
     * @param  int/string $series series id
     * @param  int/string $season season id
     * @return void
     */
    public function delete($series, $season)
    {
        $this->episode->where('season_id', '=', $season)->delete();
        $this->season->destroy($season);
    }

    /**
     * Fetches series with all seasons and episodes.
     * 
     * @param  string $title
     * @return Title
     */
    public function withSeasonsEpisodes($title)
    {
        $id = Helpers::extractId($title);

        return $this->title->with('season.episode')
                           ->whereType('series')
                           ->findOrFail($id);
    }
}