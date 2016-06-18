<?php

class Entity extends Eloquent {

	/**
	 * Create new Elo instance.
	 * 
	 * @param array $attributes
	 */
	public function __construct($attributes = array())
	{
  		parent::__construct($attributes);
	}

	/**
	 * Column to order items on if none is passed.
	 * 
	 * @var string
	 */
	protected $defaultOrderColumn = 'created_at';

    /**
     * Limit items to only ones matching given title.
     * 
     * @param  Illuminate\Database\Eloquent\Builder $query 
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereTitleLike($query, $title = '')
    {
        if ($this->table == 'titles' || $this->table == 'news')
        {
        	return $query->where('title', 'LIKE', "%$title%");
        }
        elseif ($this->table == 'images')
        {
        	return $query->where('web', 'LIKE', "%$title%");
        }
        elseif ($this->table == 'users')
        {
        	return $query->where('username', 'LIKE', "%$title%");
        }
        elseif ($this->table == 'reviews')
        {
        	return $query->where('body', 'LIKE', "%$title%");
        }
        elseif ($this->table == 'links')
        {
        	return $query->where('label', 'LIKE', "%$title%");
        }

        return $query->where('name', 'LIKE', "%$title%");
    }

    /**
     * Order the query by given column and director or by
     * defaults if no arguments given.
     * 
     * @param  Illuminate\Database\Eloquent\Builder $query 
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeOrder($query, $order = null)
    {
    	$direction = 'desc';
    	$column = $this->defaultOrderColumn;
    	
        if ($order)
        {
        	//split string (created_atAsc) by camelCase into column and direction
        	$s = preg_split('/(?=[A-Z])/', $order);
        	
        	//if we've got direction overwrite default one
        	if (isset($s[1])) $direction = strtolower($s[1]);

        	//if we've got column overwrite default one
        	if (isset($s[0])) $column = $s[0];
        } 
        
        return $query->orderBy($column, $direction);
    }

	/**
	 * Compiles insert on duplicate update query for multiple inserts.
	 * 
	 * @param  string $table
	 * @param  array $values
	 * @return self
	 */
	public function saveOrUpdate(array $values, $table = null)
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
			array_push($updates, "$column = COALESCE(values($column), $column)");
		}

		$table = $table ? $table : $this->table;
		$prefixed = DB::getTablePrefix() ? DB::getTablePrefix().$table : $table;
		
		//final query
		$query = "INSERT INTO {$prefixed} " . '(' . implode(',' , array_keys(head($values))) . ')' . ' VALUES ' . $placeholders . 
				 'ON DUPLICATE KEY UPDATE ' . implode(', ', $updates);

		$this->fireSavedEvent($table);
		
		return DB::statement($query, array_values(array_flatten($values)));
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
		$pl = App::make('Illuminate\Support\Pluralizer');

		$model = ucfirst($pl->singular($table));

		Event::fire("eloquent.saved: $model", array($this));
	}
}