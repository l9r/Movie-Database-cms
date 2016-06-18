<?php namespace Lib\Services\Trailers;

use App;

class Youtube
{
	/**
	 * Compiles url for youtube api video query.
	 * 
	 * @param  string $title 
	 * @param  string release date
	 * @return string
	 */
	public function compileUrl($title = null, $release)
	{
		$key = App::make('options')->getYoutubeKey();

		if ($title)
		{
			$title = strtolower(str_replace(' ', '+', $title));
			$year  = \Helpers::extractYear($release);
		
			return "https://www.googleapis.com/youtube/v3/search?part=snippet&q=$title+$year+trailer&videoEmbeddable=true&order=relevance&type=video&key=$key";
		}

		return '';			
	}


	/**
	 * Parse out title trailer from youtube api response.
	 * 
	 * @param  string $json
	 * @return string
	 */
	public function parseTrailers($json)
	{		
		try
		{
			$array = json_decode($json, true);
			$videoId = $array['items'][0]['id']['videoId'];
		}

		//return empty string in case youtube changes the json format and we get some errors.
		catch (\Exception $e)
		{
			return '';
		}

		return $videoId ? "//www.youtube.com/embed/$videoId" : '';
	}
}