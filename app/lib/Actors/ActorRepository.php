<?php namespace Lib\Actors;

use Lib\Repository;
use Lib\Services\Db\Writer;
use Lib\Services\Options\Options;
use Actor, Helpers, Title, Event, App, DB;
use Lib\Repositories\ActorData\ActorDataRepositoryInterface as ActorData;

class ActorRepository extends Repository
{
	/**
	 * Actor model instance.
	 * 
	 * @var Actor
	 */
	protected $model;

	/**
	 * Data repository instance.
	 * 
	 * @var Lib\Repositories\ActorData\ActorDataRepositoryInterface
	 */
	private $provider;

	/**
	 * Writer instance.
	 * 
	 * @var Lib\Services\Db\Writer
	 */
	private $dbWriter;

	/**
	 * Temp actor id store so we can
	 * associate actor with titles easily.
	 * 
	 * @var string/int
	 */
	private $actorId;

	/**
	 * Title model instance
	 * 
	 * @var Title
	 */
	private $title;

	/**
	 * Options instance.
	 * 
	 * @var Lib\Services\Options\Options
	 */
	private $options;

	public function __construct(Actor $actor, ActorData $provider, Writer $dbWriter, Title $title)
	{
		$this->model = $actor;
		$this->title = $title;
		$this->dbWriter = $dbWriter;
		$this->provider = $provider;

		$this->options = App::make('options');
	}

	/**
    * Returns most popular actors.
    * 
    * @param  int/string $limit
    * @return collection
    */
    public function popular($limit = 4)
    {
    	return $this->model->orderBy('views', 'desc')->limit($limit)->remember(1440, 'actors.popular')->get();
    }

	/**
	 * Updates actor with provided input.
	 * 
	 * @param  array  $input
	 * @param  int/string  $id
	 * 
	 * @return void
	 */
	public function update(array $input, $id)
	{
		$actor = $this->model->findOrFail($id);

		$this->updateModelAttr($actor, $input);

		Event::fire('Actor.Updated', array($actor->id));
	}

	/**
	 * Deletes actor from database.
	 * 
	 * @param  string $id
	 * @return void
	 */
	public function delete($id)
	{
		$this->model->destroy($id);

		Event::fire('Actor.Deleted', array($id));
	}

	/**
	 * Creates new actor in database.
	 * 
	 * @param  array  $input
	 * @return void
	 */
	public function create(array $input)
	{
		foreach ($input as $k => $v)
		{
			$this->model->$k = $v;
		}

		$this->model->save();

		Event::fire('Actor.Created', array($input['name']));
	}

	/**
	 * Removes title from actors filmography.
	 * 
	 * @param  array $input
	 * @return void
	 */
	public function unlink(array $input)
	{
		$actor = $this->model->findOrFail( $input['actor_id'] );

		$actor->title()->detach( $input['title_id'] );

		Event::fire('Actor.Updated', array($input['actor_id']));
	}

	/**
	 * Make actor known for title in his filmography.
	 *
	 * @param array $input
	 * @return Redirect
	 */
	public function knownFor(array $input)
	{
		$this->dbWriter->compileInsert('actors_titles', $input)->save();

		Event::fire('Actor.Updated', array($input['actor_id']));
	}

	/**
	 * Updates model with provided values.
	 * 
	 * @param  mixed $model
	 * @param  array $values
	 * @return void
	 */
	private function updateModelAttr($model, array $values)
	{
		foreach ($values as $k => $v)
		{
			if ($v || $v == '0') $model->$k = $v;
		}

		$model->save();
	}

	/**
	 * Fetches all available actor info.
	 * 
	 * first checks db if actor is fully scraped then returns,
	 * else gets all info about actor from provider saves
	 * to db and the returns.
	 *
	 * @param  string id
	 * @return Actor
	 */
	public function fetchFull($id)
	{
		$id = Helpers::extractId($id);

		$actor = $this->model->with('title')->findOrFail($id);

		$this->actorId = $actor->id;

		return $this->prepareActor($actor);	
	}

	/**
	 * Prepares actor for displaying.
	 * 
	 * @param  Actor  $actor
	 * @return Actor
	 */
	private function prepareActor(Actor $actor)
	{	
		if ($actor->fully_scraped || $this->options->getDataProvider() == 'db' || ! $actor->allow_update)
		{
			return $actor;
		}
		
		//get all avalaible actor data from provider and insert into db.
		try {	
			$this->saveFromExternal($this->provider->getActor($actor));
			
			$fullActor = $this->model->with('title')->findOrFail($this->modelId);
		} catch (\Exception $e) {
			return $actor;
		}

		return $fullActor;
	}

	/**
	 * Saves info about actor from external
	 * sources (imdb, tmdb etc).
	 * 
	 * @return void
	 */
	private function saveFromExternal($actor)
	{	
   		$this->saveGenInfo($actor);
   		$this->saveFilmography($actor);
   		$this->saveKnownFor($actor);
	}

	 /**
    * Saves general information about actor (name, bio etc).
    * 
    * @param  mixed actor 
    * @return void
    */
   private function saveGenInfo($actor)
   {
	   	$gen = $actor->getGenInfo();

	   	//set flags and temp id
	   	$temp = str_random(10);
	   	$gen['temp_id'] = $temp;
	    $gen['fully_scraped'] = 1;

	   	//insert and get back id in database
	   	$this->dbWriter->compileInsert('actors', $gen)->save();
	   	$this->modelId = $this->model->where('temp_id', $temp)->first()->id;
   }

    /**
    * Saves titles actor is known for and associates them.
    * 
    * @param  mixed $actor
    * @return void
    */
   private function saveKnownFor($actor)
   {
	   	$known = $actor->getKnownFor();

	   	if ($known)
	   	{
	   		$temp = str_random(10);
			$knownFor = $this->addTempId($known, $temp);

	   		$this->dbWriter->compileBatchInsert('titles', $knownFor)->save();

	   		$this->associate($temp, 1);
	   	}
	   	else
	   	{
	   		$id = $this->actorId;
	   		$titles = $this->title->whereHas('actor', function($q) use ($id)
	   		{
   				$q->where('actor_id', $id);
   				
	   		})->orderBy('mc_num_of_votes', 'desc')->limit(4)->lists('id');

	   		if ($titles)
	   		{
	   			DB::table('actors_titles')->where('actor_id', $this->actorId)->update(array('known_for' => 0));
		   		DB::table('actors_titles')->whereIn('title_id', $titles)->where('actor_id', $this->actorId)->update(array('known_for' => 1));
	   		} 	
	   	}
   }

    /**
    * Saves actors filmography.
    * 
    * @param  mixed $actor
    * @return void
    */
   private function saveFilmography($actor)
   {
		$filmo = $actor->getFilmography();
		$names = $actor->getCharNames();

		$temp = str_random(10);
	   	$filmography = $this->addTempId($filmo, $temp);

	   	$this->dbWriter->compileBatchInsert('titles', $filmography)->save();

	   	$this->associate($temp, 0, $names);
   }

   /**
    * Associates actors with titles.
    * 
    * @param  string $temp tempId of titles to associate.
    * @return void
    */
   private function associate($temp, $known = 0, $names = array())
   {
	   	$insert = array();

	   	$titles = $this->title->Where('temp_id', '=', $temp)->get(array('id', 'tmdb_id'));
	 
	   	foreach ($titles as $k => $v)
	   	{
	   		if (isset($names[$v['tmdb_id']])) {
	   			$insert[] = array('actor_id' => $this->modelId, 'title_id' => $v['id'], 'known_for' => $known, 'char_name' => $names[$v['tmdb_id']]);
	   		} else {
	   			$insert[] = array('actor_id' => $this->modelId, 'title_id' => $v['id'], 'known_for' => $known);
	   		}
	   		
	   	}

	   	$this->dbWriter->compileBatchInsert('actors_titles', $insert)->save();
   }

   /**
    * Adds temp id into each array item.
    * 
    * @param array $array 
    * @param string $temp
    */
   private function addTempId(array $array, $temp)
   {
	   	foreach ($array as $k => $v)
	   	{
	   		$array[$k]['temp_id'] = $temp;
	   	}

	   	return $array;
	}

}