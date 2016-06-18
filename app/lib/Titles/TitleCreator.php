<?php namespace Lib\Titles;

use Title, Event, DB;
use Carbon\Carbon;

class TitleCreator
{
    /**
     * Title model instance.
     * 
     * @var Title
     */
    private $model;

    /**
     * Holds relationships that need to be saved
     * to their own tables.
     * 
     * @var array
     */
    private $relations = array();

    /**
     * Random string.
     * 
     * @var string
     */
    private $random;

    /**
     * Create new TitleCreator instance.
     * 
     * @param Title $model
     */
    public function __construct(Title $model)
    {
        $this->model = $model;
        $this->random = str_random(10);
    }

    /**
     * Creates a new title using user input.
     * 
     * @param  array $data
     * @return Title
     */
    public function create(array $data)
    {
        //relations that need to be inserted in separate tables
        $rels = array('images', 'actors', 'directors', 'writers');

        foreach ($data as $name => $values)
        {
            //if it's a relation we'll need to save it to it's own table
            //so we'll add it to relations array and unset it from main one
            if (in_array($name, $rels))
            {
                $this->relations[$name] = $values;
                unset($data[$name]);
            }

            //remove any other arrays incase any slip in
            elseif (is_array($values))
            {
                unset($data[$name]);
            }
        }

        if (isset($data['release_date']))
        {
            $data['year'] = substr($data['release_date'], 0, 4);
        }

        $data['updated_at'] = Carbon::now();
        
        //save main movie details
        if (isset($data['id']) && $data['id'])
        {
            $this->model->firstOrCreate(array('id' => $data['id']))->fill($data)->save();
            $this->model->id = $data['id'];
        }
        else
        {
            $this->model->fill($data)->save();
        }

        //save relations
        $this->saveRelations();
        
        Event::fire('Titles.Created', array($this->model, Carbon::now()));

        return $this->model;
    }

    /**
     * Save title relations to a separate table.
     * 
     * @param  string $table  
     * @param  mixed  $values
     * @return void
     */
    private function saveRelations()
    {
        foreach ($this->relations as $table => $values)
        {
            if ($table == 'images')
            {
                $this->attachImages($values);
            }
            elseif ($table == 'actors')
            {
                $this->attachActors($values);
            }
            else
            {
                $this->attachRelations($table, $values);
            }
        }
    }

     /**
     * Attach images to title.
     * 
     * @param  array $images
     * @return void
     */
    private function attachImages(array $images)
    {
        foreach ($images as &$image)
        {
            $image['title_id'] = $this->model->id;

            //move image path to either local or web
            //attribute because path doesn't exist on the table
            if (str_contains($image['path'], 'http'))
            {
                $image['web'] = $image['path'];
            }
            else
            {
                $image['local'] = $image['path'];
            }

            unset($image['path']);
        }

        $this->model->saveOrUpdate($images, 'images');
    }

    /**
     * Attach actors to title.
     * 
     * @param  array $values
     * @return void
     */
    private function attachActors(array $values)
    {
        $insert = array();
       
        foreach ($values as $value)
        {
            $insert[] = array('actor_id' => $value['id'], 'title_id' => $this->model->id, 'char_name' => $value['char_name']);
        }

        $this->model->saveOrUpdate($insert, 'actors_titles');
    }

    /**
     * Attach relations to title.
     *
     * @param  string $table
     * @param  array $values
     * @return void
     */
    private function attachRelations($table, array $values)
    {
        $baseInsert  = array();
        $pivotInsert = array();
       
        //compile inserts to main relation table
        foreach ($values as $value)
        {
            $baseInsert[] = array('name' => $value, 'temp_id' => $this->random);
        }

        $this->model->saveOrUpdate($baseInsert, $table);

        //load the resources we just inserted so we can get the ids
        $resources = DB::table($table)->where('temp_id', $this->random)->lists('name', 'id');

        //compile inserts to pivot table so we can attach resources we just inserted to the title
        foreach ($resources as $id => $resource)
        {
            $pivotInsert[] = array(rtrim($table, 's').'_id' => $id, 'title_id' => $this->model->id);
        }

        $this->model->saveOrUpdate($pivotInsert, "{$table}_titles");
    }
}