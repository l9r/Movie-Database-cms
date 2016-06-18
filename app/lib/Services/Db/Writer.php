<?php namespace Lib\Services\Db;

use DB, App, Title, Helpers, Event;
use Carbon\Carbon;
use Intervention\Image\Image;
use Lib\Repositories\Data\DataRepositoryInterface;

class Writer
{
	/**
	 * Data provider instance.
	 * 
	 * @var mixed
	 */
	private $provider;

	/**
	 * Compiled query.
	 * 
	 * @var string
	 */
	private $query;

	/**
	 * Bindings for query.
	 * 
	 * @var array
	 */
	private $values;

	/**
	 * Current title id.
	 * 
	 * @var string/int
	 */
	private $titleId;

	/**
	 * Current timestamp.
	 * 
	 * @var time
	 */
	private $date;

	/**
	 * Temp id for this title
	 * 
	 * @var string
	 */
	private $tempId;

	/**
	 * Flags to set on current insert.
	 * 
	 * @var array
	 */
	private $flags;

	/**
	 * General info about title.
	 * 
	 * @var array
	 */
	private $genInfo;

	/**
	 * Images handler instance.
	 * 
	 * @var Lib\Services\Images\ImageSaver
	 */
	private $images;

	/**
	 * Options instance.
	 * 
	 * @var Lib\Services\Options\Options
	 */
	private $options;

	public function __construct($provider=null, $flags=array())
	{
		$this->provider = $provider;
	
		//make current time/date stamp
		$this->date   = Carbon::now();

		//make random temparory id to use for getting ids back from db after batch insert
		$this->tempId =	str_random(15);

		//set any flags: featured, fully_scraped, now_playing etc
		$this->flags = $flags;

		$this->images = App::make('Lib\Services\Images\ImageSaver');

		$this->options = App::make('options');
	}

	/**
	 * Sets any flags needed on current insert.
	 * 
	 * @param array $flags
	 */
	public function setFlags(array $flags)
	{
		$this->flags = $flags;

		return $this;
	}

	/**
	 * Saves all the available data about current title.
	 * 
	 * @return void
	 */
	public function saveAll()
	{
		if ( ! $this->provider) {
			throw new Exception ('Invalid or no provider passed in');
		}

		if ($this->provider->getType() == 'series') {
			$this->saveAllSeasons($this->titleId);
		}

		$this->saveGenInfo();
		$this->saveImages();
		$this->saveCast();
		$this->saveDirectors();
		$this->saveWriters();
	}

	/**
	 * Provider setter.
	 *
	 * Can pass in data provider trough this or constructor.
	 *
	 * @param dataProviderInterface $provider
	 * @param int $id titles id in db 
	 */
	public function setProvider(DataRepositoryInterface $provider, $id)
	{
		$this->provider = $provider;
		$this->titleId  = $id;

		return $this;
	}

	private function saveAllSeasons($id)
	{
		$seasons = $this->provider->getAllSeasons($id);

		if ( ! $seasons) return;

		$this->compileBatchInsert('seasons', $seasons)->save();
	}

	/**
	 * Fully saves all series seasons including their  episodes.
	 * 
	 * @param  array  $seasons 
	 * @return void
	 */
	public function saveFullAllSeasons(array $seasons)
	{
		if ( ! $seasons) return;

		foreach ($seasons as $k => &$v)
		{
			$this->compileInsert('seasons', array_except($v, 'episodes'))->save();

			//only save tmdb images as imdb ones are already saved
			if ($this->options->saveTmdbImages() && $v['title_tmdb_id']) {
				$v['episodes'] = $this->handleEpisodeImages($v['episodes']);
			}
			
			$this->compileBatchInsert('episodes', $v['episodes'])->save();
		}
	}

	/**
	 * Save images of every episode locally and store new path on object.
	 * 
	 * @param  array  $episodes
	 * @return array
	 */
	private function handleEpisodeImages(array $episodes)
	{
		foreach ($episodes as $key => $episode)
		{			
			if ( ! $episode['poster']) continue;

			$path = 'imdb/episodes/';
			$num  = $episode['season_number'] . $episode['episode_number'];
			$id   = $episode['title_id'];

			$this->images->saveSingle($episode['poster'], $id, $path, $num);

			$episodes[$key]['poster'] = $path . $id . $num . '.jpg';
		}

		return $episodes;
	}

	/**
	 * Save directors and associte them with title
	 * 
	 * @return void
	 */
	private function saveDirectors()
	{
		$directors = $this->provider->getDirectors();

		if ( ! $directors) return;

		//compile directors insert array with temp id
		foreach ($directors as $k => $v)
		{
			$dirs[] = array('name' => $v, 'temp_id' => $this->tempId);
		}

		//insert directors
		$this->compileBatchInsert('directors', $dirs)->save();

		//fetch inserted directors ids from db
		$ids = DB::table('directors')->whereTemp_id($this->tempId)->lists('id');

		//associte directors with title
		$this->associate('directors_titles', $ids);
	}

	/**
	 * Save writers and associate them with title
	 * 
	 * @return void
	 */
	private function saveWriters()
	{
		$writers = $this->provider->getWriters();

		if ( ! $writers) return;

		//compile writers insert array with temp id
		foreach ($writers as $k => $v)
		{
			$wrtrs[] = array('name' => $v, 'temp_id' => $this->tempId);
		}

		//insert writers
		$this->compileBatchInsert('writers', $wrtrs)->save();

		//fetch inserted writers ids from db
		$ids = DB::table('writers')->whereTemp_id($this->tempId)->lists('id');

		//associte writers with title
		$this->associate('writers_titles', $ids);
	}

	/**
	 * Associates passed ids with the current title in passed table.
	 * 
	 * @param  string $table  pivot table name
	 * @param  array $ids  	  ids to associate with title
	 * @return void
	 */
	private function associate($table, $ids)
	{
		$pivots = array();

		//construct column name from passed in table name
		$column = trim($table, 's_titles') . '_id';

		//compile association insert
		foreach ($ids as $k => $v)
		{
			$pivots[] = array($column => $v, 'title_id' => $this->titleId);
		}

		//insert associations in db pivot table
		$this->compileBatchInsert($table, $pivots)->save();
	}

	/**
	 * Saves movie images.
	 * 
	 * @return void
	 */
	private function saveImages()
	{
		$images = $this->provider->getImages();

		if ( ! $images) return;

		//save images locally or hotlink depending on provider.
		if ($this->provider->name == 'imdb' || $this->options->saveTmdbImages())
		{
			$imgs = $this->saveImdbImages($images);
		}
		else
		{
			foreach ($images as $k => $v)
			{
				$imgs[] = array('web' => $v, 'title_id' => $this->titleId);
			}
		}
	
		$this->compileBatchInsert('images', $imgs)->save();
	}

	/**
	 * Saves imdb images locally.
	 * 
	 * @param  array $images
	 * @return array
	 */
	private function saveImdbImages(array $images)
	{
		$id = isset($this->genInfo['imdb_id']) ? $this->genInfo['imdb_id'] : $this->genInfo['tmdb_id'].$this->genInfo['type'];

		foreach (array_slice($images, 0, 6) as $k => $v)
		{
			$imgs[] = array('local' => "imdb/stills/{$id}$k.thumb.jpg",'title_id' => $this->titleId);
			$img = is_array($v) ? head($v) : $v;
			$urls[$k] =  str_contains($img, 'imdb') ? Helpers::size($img, 'original') : Helpers::original($img);
		}

		$this->images->saveMultiple($urls, $id, 'imdb/stills/', $k);

		return $imgs;
	}

	/**
	 * Saves general info about movie title, plot, release date etc.
	 * 
	 * @return void
	 */
	private function saveGenInfo()
	{
		$this->genInfo = $this->provider->getGenInfo();

		$this->finalizeGenInfo();

		$this->compileInsert('titles', $this->genInfo)->save();

		//get title id for actors, images etc association after we insert it
		$this->titleId = head(DB::table('titles')->whereTemp_id($this->genInfo['temp_id'])->lists('id'));
	}

	/**
	 * Prepares general info for inserting into db.
	 * 
	 * @return void
	 */
	private function finalizeGenInfo()
	{
		//add year to array so we can better recognize same movie/series
		//by using title/year unique key instead of just title
		$this->genInfo['year'] = Helpers::extractYear($this->genInfo['release_date']);

		$this->addJumboBackground();

		if ($this->provider->name == 'imdb' || $this->options->saveTmdbImages())
		{
			$this->addPoster();
		}

		//set any flags on title that we got passed in constructor
		foreach($this->flags as $k => $v)
		{
			$this->genInfo[$k] = $v;
		}

	}

	/**
	 * If provider is imdb saves poster locally and changes path.
	 *
	 * @return  void
	 */
	private function addPoster()
	{
		$id = isset($this->genInfo['imdb_id']) ? $this->genInfo['imdb_id'] : $this->genInfo['tmdb_id'].$this->genInfo['type'];

		if ($this->genInfo['poster'])
		{
			$this->images->saveSingle( Helpers::size($this->genInfo['poster'], 1.5), $id );
			$this->genInfo['poster'] = "imdb/posters/$id.jpg";
		}
		else
		{
			$this->genInfo['poster'] = null;
		}	
	}

	/**
	 * Saves titles cast to database.
	 * 
	 * @return void
	 */
	private function saveCast()
	{		
		$actCharIds = array();

		$cast = $this->provider->getCast();

		//if no cast found bail
		if (empty($cast)) return;

		//split cast array so we can insert actors/chars into their own tables with batch
		foreach ($cast as $k => $v)
		{		
			//pass in temp id into array so we can get back actors/chars ids in db after batch insert
			$actors[] = array_merge(array_except($v, 'char'), array('temp_id' => $this->tempId, 'updated_at' => $this->date));
			$actorsChars[$v['name']] = $v['char'];
		}
		
		//save actors/chars and fetch their ids in database
		$actorsIds  = $this->saveActors($actors);
		
		//compile an array of actor and char ids from db
		foreach ($actorsChars as $actor => $char)
		{
			try
			{
				$actCharIds[] = array('actor_id' => $actorsIds[$actor], 'char' => $char, 'title_id' => $this->titleId);
			}
			catch (\ErrorException $e) {}
			
		}

		//associate actors with chars, aswell as actors with title and chars with title
		$this->associateActorsChars($actCharIds);
	}

	/**
	 * Add jumbotron image background into general info insert.
	 * 
	 * @return void
	 */
	private function addJumboBackground()
	{
		if ($this->provider->name == 'imdb' || $this->options->saveTmdbImages())
		{
			$img = $this->provider->getBackground();

			if ($img)
			{
				$id = isset($this->genInfo['imdb_id']) ? $this->genInfo['imdb_id'] : $this->genInfo['tmdb_id'].$this->provider->getType();

				$this->images->saveSingle($img, $id, 'imdb/bgs/');

				$this->genInfo['background'] = "imdb/bgs/{$id}.jpg";
			}		
		}
		else
		{
			$this->genInfo['background'] = $this->provider->getBackground();
		}
	}

	/**
	 * Creates associations for many to manu relationships (pivot tables)
	 * 
	 * @param  array  $ids ids to associate
	 * @return void
	 */
	private function associateActorsChars(array $ids)
	{
		$at = array();

		//format ids array so we can pass it to compile insert method
		foreach ($ids as $k => $v)
		{
			//prepare actors_titles associate insert
			$at[] = array('actor_id' => $v['actor_id'], 'char_name' => $v['char'], 'title_id' => $this->titleId);
		}

		$this->compileBatchInsert('actors_titles', $at)->save();
	}

	/**
	 * Loops trough cast array and saves actors.
	 * 
	 * @param  array $cast 
	 * @return array inserted actors ids.
	 */
	private function saveActors(array $actors)
	{
		//save actor images locally or hotlink depending on provider.
		if ($this->provider->name == 'imdb' || $this->options->saveTmdbImages())
		{
			$actors = $this->saveImdbCastImages($actors);
		}

		$this->compileBatchInsert('actors', $actors)->save();

		//grab temp id from first actor in array
		$first = head($actors);
		$tempId = $first['temp_id'];

		return DB::table('actors')->whereTemp_id($tempId)->lists('id', 'name');
	}

	/**
	 * Saves imdb cast images locally.
	 * 
	 * @param  array $actors
	 * @return array
	 */
	private function saveImdbCastImages(array $actors)
	{
		$urls = array();

		foreach ($actors as $k => $v)
		{
			if ($v['image'])
			{
				$id = isset($v['imdb_id']) ? $v['imdb_id'] : $v['tmdb_id'].$this->provider->getType();

				$actors[$k]['image'] = "imdb/cast/{$id}.jpg";				
				$urls[$id] = str_contains($v['image'], 'imdb') ? Helpers::size($v['image'], 6) : $v['image'];
			}		
		}

		$this->images->saveMultiple($urls, null, 'imdb/cast/');

		return $actors;
	}

	/**
	 * Compile insert on duplicate update query for single insert.
	 * 
	 * @param  string $table
	 * @param  array $values
	 * @return self
	 */
	public function compileInsert($table, array $values)
	{
		if (empty($values)) return;
	
		$updates = array();

		foreach ($values as $column => $value)
		{
			if ($value !== null) {
				array_push($updates, "$column = values($column)");
			} else {
				unset($values[$column]);
			}
		}

		$placeholders = array_fill(0, count($values), '?');
		$placeholders = '(' . implode(', ', $placeholders) . ') ';

		$query = "INSERT INTO $table " . '(' . implode(',' , array_keys($values)) . ')' . ' VALUES ' . $placeholders . 
				 'ON DUPLICATE KEY UPDATE ' . implode(', ', $updates);
				
		$this->query = $query;
		$this->values = $values;

		$this->fireSavedEvent($table);

		return $this;
	}

	/**
	 * Compiles insert on duplicate update query for multiple inserts.
	 * @param  string $table
	 * @param  array $values
	 * @return self
	 */
	public function compileBatchInsert($table, array $values)
	{
		if (empty($values)) return $this;

		//count how many inserts we need to make
		$amount = count($values);

		//count in how many columns we're inserting
		$columns = array_fill(0, count(head($values)), '?');
		$columns = '(' . implode(', ', $columns) . ') ';
		
		//make placeholders for the amount of inserts we're doing
		$placeholders = array_fill(0, $amount, $columns);
		$placeholders = implode(',', $placeholders);
	
		$updates = array();

		//construct update part of the query if we're trying to insert duplicates
		foreach (head($values) as $column => $value)
		{
			array_push($updates, "$column = values($column)");
		}

		//final query
		$query = "INSERT INTO $table " . '(' . implode(',' , array_keys(head($values))) . ')' . ' VALUES ' . $placeholders . 
				 'ON DUPLICATE KEY UPDATE ' . implode(', ', $updates);

		$this->query = $query;
		$this->values = array_flatten($values);

		$this->fireSavedEvent($table);

		return $this;
	}

	/**
     * Inserts all the partially scraped titles from
     * performed tmdb api search query.
     * 
     * @param  array $titles
     * @return Collection
     */
    public function insertFromTmdbSearch(array $titles)
    {
        $first = head($titles);
        $tempId = $first['temp_id'];

        if ($this->options->saveTmdbImages())
        {
        	$urls = array();

	        foreach ($titles as $k => $v)
			{
				if ($v['poster'])
				{
					$id = $v['tmdb_id'].$v['type'];

					$titles[$k]['poster'] = "imdb/posters/{$id}.jpg";				
					$urls[$id] = $v['poster'];
				}		
			}

			$this->images->saveMultiple($urls, null, 'imdb/posters/');
        }

        $this->compileBatchInsert('titles', $titles)->save();

    	return Title::byTempId($tempId, 'tmdb_popularity');
    }

	/**
     * Inserts all the partially scraped titles from
     * performed imdb advanced search.
     * 
     * @param  array $titles
     * @return Collection
     */
    public function insertFromImdbSearch(array $titles)
    {
        $tempId = str_random(15);

    	$titles = $this->prepareImdbInsert($titles, $tempId);
      
        $this->compileBatchInsert('titles', $titles)->save();

    	return Title::byTempId($tempId, 'imdb_votes_num');
    }

    /**
     * Prepare imdb data array for inserting.
     * 
     * @param  array $titles
     * @param  string $tempId
     * @return array
     */
    private function prepareImdbInsert(array $titles, $tempId)
    {
        foreach ($titles as $k => $v)
        {             
            $titles[$k]['temp_id'] = $tempId;
            $titles[$k]['updated_at'] = $this->date;
            $titles[$k]['imdb_votes_num'] = Helpers::imdbVotes($titles[$k]['imdb_votes_num']);

            if ($v['poster'])
            {
                if ( $this->images->saveSingle( $v['poster'], $v['imdb_id'] ))
                {
                     $titles[$k]['poster'] = "imdb/posters/{$v['imdb_id']}" . '.jpg';
                }        	
            }                  
        }

        return $titles;
    }

	/**
	 * Updates options in db.
	 * 
	 * @param  array $options
	 * @return void
	 */
	public function updateOptions(array $options)
	{
		$values = '';
	
		foreach ($options as $name => $value)
		{
			$option = DB::table('options')->where('name', $name)->first();

			if ($option) {
				DB::table('options')->where('name', $name)->update(array('value' => $value));
			} else {
				DB::table('options')->insert(array('name' => $name, 'value' => $value));
			}
		}
	}

	/**
	 * Executes saved query.
	 * 
	 * @return void
	 */
	public function save()
	{
		if ($this->query && $this->values)
		{
			return DB::statement($this->query, array_values($this->values));
		}		
	}

	/**
	 * Fire saved event manually so we can flush
	 * cache when not using eloquent for insert statemenets.
	 * 
	 * @param  string $table
	 * @return void
	 */
	private function fireSavedEvent($table)
	{
		if ( ! str_contains($table, '_'))
		{
			$pl = App::make('Illuminate\Support\Pluralizer');

			$model = ucfirst($pl->singular($table));

			Event::fire("eloquent.saved: $model", array(new $model));
		}
	}
}