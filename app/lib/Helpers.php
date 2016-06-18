<?php

use Carbon\Carbon;

class Helpers
{
	private static $provider;

	public static function getEpisodeImage($title, $episode)
	{
		if ($episode->poster) {
			return $episode->poster;
		}

		if ( ! $title->image->isEmpty()) {
			return $title->image->first()->path;
		}

		return asset('assets/images/noimageepisode.png');
	}

	public static function isDemo()
	{
		return gethostname() === 'mtdb.info';
	}

	/**
	 * Parse given date using carbon.
	 * 
	 * @param  string $date
	 * @return string
	 */
	public static function parseDate($date)
	{
		try {
			return Carbon::parse($date);
		} catch (\Exception $e) {}
	}

	/**
	 * Extract specified number of items with image from collection.
	 * 
	 * @param  Collection $collection 
	 * @param  integer    $limit     
	 * @return array
	 */
	public static function withImages($collection, $limit, $type = false)
	{
		$count = 0;
		$extracted = array();

		foreach ($collection as $key => $value) {
			if ($count >= $limit) break;

			if ( ! str_contains($value->poster, 'imdbnoimage') && ($type ? $value->type === $type : true)) {
				$extracted[] = $value;
				$count++;
			}
		}

		return $extracted;
	}

	/**
	 * Makes images thumb path from full sized image path.
	 * 
	 * @param  string $url
	 * @return string
	 */
	public static function thumb($url)
	{	
		if (str_contains($url, 'http')) return $url;

		$path = asset(preg_replace('/imdb\/stills\/(.+?)\.jpg/', 'imdb/stills/$1.thumb.jpg', $url));

		if (@getimagesize($path))
		{
			return $path;
		}
		
		return asset($url);
	}

	/**
	 * Sorts given collection by release date.
	 * 
	 * @param  Collection $col
	 * @return Collection
	 */
	public static function sortByYear($col)
	{
		$col->sort(function($a, $b)
    	{
	        preg_match('/(\d{4})/', $a->release_date ? $a->release_date : $a->year, $m);
	        $a = isset($m[0]) ? $m[0] : '';
	        
	        preg_match('/(\d{4})/', $b->release_date ? $b->release_date : $b->year, $m);
	        $b = isset($m[0]) ? $m[0] : '';

	        if ($a === $b) {
	            return 0;
	        }

	        return ($a < $b) ? 1 : -1;
   		});

   		return $col;
	}

	/**
	 * Returns tmdb original size image url.
	 * 
	 * @param  string $url
	 * @return string 
	 */
	public static function original($url)
	{
		return preg_replace('/\/w[0-9]+\//', '/original/', $url);
	}

	public static function getSimilar(Title $title)
	{
		$genres = explode($title->genre);
	}

	/**
	 * Returns small version of user avatar path.
	 * 
	 * @return string
	 */
	public static function smallAvatar()
	{
		$user = self::loggedInUser();

		if ($user->avatar)
		{
			return asset(str_replace('.jpg', '.small.jpg', $user->avatar));
		}
		
		return asset('assets/images/no_user_icon.png');		
	}

	/**
	 * Removes commas from imdb votes number and
	 * casts to integer.
	 * 
	 * @param  string $num
	 * @return int
	 */
	public static function imdbVotes($num)
	{
		$num = str_replace(',', '', (string) $num);

    	return (int) $num;
	}

	/**
     * Returns what to order titles on popularity wise.
     * 
     * @return string
     */
    public static function getOrdering()
    {
    	$opt = App::make('options');
    	$provider = $opt->getDataProvider();

        if ($provider == 'imdb')
        {
            $ordering = 'imdb_votes_num';
        }
        elseif ($provider == 'tmdb')
        {
            $ordering = 'tmdb_popularity';
        }
        elseif ($provider == 'db')
        {
            $ordering = 'views';
        }
        else
        {
        	$ordering = 'imdb_votes_num';
        }

        return $ordering;
    }

	/**
	 * Compiles validator error messages into string.
	 * 
	 * @param  array $messages
	 * @return string
	 */
	public static function compileErrorsForAjax($messages)
	{
		$response = '';

	    foreach ($messages as $message)
	    {
	        $response .= $message . '<br>';
	    }

	    return $response;
	}

	/**
	 * Extracts specified season from eager loaded title.
	 * 
	 * @param  Title  $title
	 * @param  string/int $num
	 * @return Season
	 */
	public static function extractSeason(Title $title, $num)
	{
		foreach ($title->season as $k => $v)
		{
			if ($v->number == $num)
			{
				return $v;
			}
		}
	}

    /**
     * Returns current data provider
     * 
     * @return string
     */
    public static function getProvider()
	{
    	if ( ! self::$provider)
    	{
    		$opt = App::make('options');
    		self::$provider = $opt->getDataProvider();
    	}

    	return self::$provider;
    }

	/**
	 * Checks if user has specific privilegies.
	 * 
	 * @param  string $for
	 * @return boolean
	 */
	public static function hasAccess($for)
	{
		$user = self::loggedInUser();

		if ($user && $user->hasAccess($for))
		{
			return true;
		}

		return false;
	}

	public static function hasAnyAccess($permissions = array())
	{
		$user = self::loggedInUser();

		if ($user && $user->hasAnyAccess($permissions))
		{
			return true;
		}

		return false;
	}

	/**
	 * Checks if specified user is currently logged in user.
	 * 
	 * @param  string  $username
	 * @return boolean
	 */
	public static function isUser($username = null)
	{
		$user = self::loggedInUser();

		if ( ! $user || $user->username !== $username)
		{
			return false;
		}

		return true;
	}

	/**
	 * Compile genres from db into a string acceptable
	 * by javascript responsible for filtering titles.
	 * 
	 * @param  string $genres
	 * @return string
	 */
	public static function genreFilter($genres)
	{
		$compiled = '';

		if(strlen($genres) > 3)
		{			
			if (strpos($genres, '|'))
			{
				$gnr = explode(' | ', $genres);

				foreach ($gnr as $k => $v)
				{
					$compiled .= '"' . trim($v) . '", ';
				}
				
				return '[' . rtrim($compiled, ', ') . ', "All"' . ']';
			}
			else
			{
				$gnr = explode(',', $genres);

				foreach ($gnr as $k => $v)
				{
					$compiled .= '"' . trim($v) . '", ';
				}

				return '[' . rtrim($compiled, ', ') . ']';
			}
		}
	}

	/**
	 * Extracts only 4 digit year from string.
	 * 
	 * @param  string $string
	 * @return string
	 */
	public static function extractYear($string)
	{
		preg_match('/\b\d{4}\b/', $string, $matches);

		return (isset($matches[0]) ? $matches[0] : Carbon::now()->addYear()->year);
	}

	/**
	 * Extracts imdb title, actor or char id from url or other strings.
	 * 
	 * @param  string $url
	 * @return string
	 */
	public static function extract($url)
	{
		preg_match("/.*?([a-z]{2}[0-9]+)\/.*?/", $url, $m);

		return (isset($m[1]) ? $m[1] : '');
	}

	/**
	 * Extracts resource id from sites absolute or relative url.
	 * 
	 * @param  string $string 
	 * @return string
	 */
	public static function extractId($string)
	{
		$result = preg_split('/[^a-z0-9]/i', $string);
	
		//check if url structure is valid: id-title
		if ( ! isset($result[0]) || ! preg_match('/[0-9]+/', $result[0]))
		{
			App::abort(404, 'Not valid url');
		}

		return $result[0];
	}

	/**
	 * Check if user has super access (all privilegies)
	 * 
	 * @return boolean
	 */
	public static function hasSuperAccess()
	{
		$user = Sentry::getUser();

		if (isset($user) && ! empty($user) && $user->hasAccess('superuser'))
		{
			return true;
		}
	}

	/**
	 * Gets the currently logged in user
	 * 
	 * @return User
	 */
	public static function loggedInUser()
	{
		$user = Sentry::getUser();

		if ( isset($user) )
		{
			return $user;
		}
	}

	
	/**
	 * Returns ids of all titles current user has added to watchlist.
	 * 
	 * @return array
	 */
	public static function getUserLists($name = 'watchlist')
	{
		if ($user = self::loggedInUser())
		{
			return User::fetchLists($user, $name);
		}		
	}

	/**
	 * Constructs url to current logged in users profile.
	 * 
	 * @return string
	 */
	public static function profileUrl()
	{
		$user = self::loggedInUser();

		return self::url($user->username, $user->id, 'users');
	}

	/**
	 * Shorthens the string to the specified lenght.
	 * 
	 * @param  string  $string 
	 * @param  integer $lenght
	 * @return string 
	 */
	public static function shrtString($string, $lenght=20)
	{
		$string = strip_tags($string);

		if (strlen($string) > $lenght)
		{
			$string = substr($string, 0, $lenght) . '...';
		}

		return preg_replace('/<img.*?\/>/', '', $string);
	}

	/**
	 * Makes a fully qualified urs from provider params.
	 * 
	 * @param  string $title 
	 * @return array
	 */
	public static function url($resource, $id, $controller = 'movies')
	{
		if ($controller == 'movie')
		{
			$controller = 'movies';
		}

		$opt = App::make('options');

		$s = $opt->getUriSeparator();
		$case = $opt->getUriCase();

		//remove all non alpha numeric characters and replace all spaces
		//and double spaces with uri separator
		$resource = preg_replace('~[^\p{L}\p{N} ]++~u', '', $resource);
		$resource = str_replace('  ', $s, trim($resource));
		$resource = str_replace(' ', $s, trim($resource));

		$controller = Str::slug(trans("main.$controller"));

		if ($case && $case == 'lowercase')
		{
			return url(strtolower( $controller . '/' . $id . $s . $resource));
		}		

		try {
			return url($controller . '/' . $id . $s . Str::slug($resource));
		} catch (Exception $e) {
			return url($controller . '/' . $id . $s . strtolower($resource));
		}
	}

	/**
	 * Generates url to given season.
	 *
	 * @param  model $season
	 * @param  string $title
	 * @return string
	 */
	public static function season($title, $season, $base = false)
	{
		$opt = App::make('options');

		$s = $opt->getUriSeparator();
		$case = $opt->getUriCase();

		$title = preg_replace('~[^\p{L}\p{N} ]++~u', '', $title);
		$title = str_replace('  ', $s, $title);
		$title = str_replace(' ', $s, $title);

		if ( ! $base)
		{
			$url = Str::slug(trans('main.series')) . '/' . $season->title_id . "-$title" . "/seasons/{$season->number}";
		}

		//if true is passed as 3rd argument we'll generate url to series base seasons page
		else
		{
			$url = 'series/' . $season->title_id . "-$title" . "/seasons";
		}

		if ($case && $case == 'lowercase')
		{
			$url = strtolower($url);
		}

		return url($url);
	}


	public static function episodeUrl($resource, $id, $controller = 'series', $seasonNum, $episodeNum)
	{
		$base = Helpers::url($resource, $id, $controller);

		return $base . '/seasons/' . $seasonNum . '/episodes/' . $episodeNum;
	}

	/**
	 * Enlarges imdb image while keeping the aspect and crop.
	 *
	 * While the image will be responsive and scale to column lenght, giving
	 * it bigger size will make it sharper but also make it load slower.
	 * 
	 * @param  string $url
	 * @param  integer $s by how much to multiply image size
	 * @return string
	 */
	public static function size($url, $s = 4)
	{
		if ( ! strpos($url, '_V1_')) return $url;

		if ( ! empty($url))
		{
			if ($s === 'original')
			{
				return preg_replace('/_V1_.+?\.[a-zA-Z]{3}/', '.jpg', $url);
			}

			//grab only part of the string that represents img size and crop
			$numbers = explode('V1', $url);

			if ( ! isset($numbers[1]))
			{
				return null;
			}

			//multiply all size and crop numbers by 4
			$size = preg_replace_callback('/([0-9]+)/', function($m) use ($s)
			{
			   return ($m[0] * $s);

			}, $numbers[1]);
			//$size = preg_replace('/\d+/e', "$0 * $s", $numbers[1]);
			
			return $numbers[0] . 'V1' . $size;
		}
	}

	/**
	 * Compiles wikipedia url to a given string.
	 * 
	 * @param  string $string
	 * @return string
	 */
	public static function wikiUrl($string)
	{
		$base = 'http://en.wikipedia.org/wiki/'; //Sandra_Bullock

		return $base . str_replace(' ', '_', $string);
	}
}