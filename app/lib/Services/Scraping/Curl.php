<?php namespace Lib\Services\Scraping;

use Lib\Services\Trailers\Youtube;

class Curl
{
	/**
	 * Youtube service instance.
	 * 
	 * @var Lib\Services\Trailers\Youtube
	 */
	private $youtube;

	public function __construct(Youtube $youtube)
	{
		$this->youtube = $youtube;
	}

	/**
	 * Php curl wrapper.
	 * 
	 * @param  string $url
	 * @return string
	 */
	public function curl($url)
	{
		$handle = curl_init();
		curl_setopt($handle, CURLOPT_HTTPHEADER, array('Accept-Language:en;q=0.8,en-US;q=0.6'));
		curl_setopt($handle, CURLOPT_URL, str_replace(' ', '%20', $url));
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);

		$html = curl_exec($handle);
		
		curl_close($handle);

		return $html;
	}
	

	/**
	 * Php multi curl wrapper
	 * @param  array $urls
	 * @return array
	 */
	public function multiCurl(array $urls)
	{
	  // array of curl handles
	  $handles = array();
	  // data to be returned
	  $result = array();
	 
	  // multi handle
	  $mh = curl_multi_init();
	 
	  // loop through $data and create curl handles
	  // then add them to the multi-handle
	  foreach ($urls as $k => $u)
	  {
	 
	  	$handles[$k] = curl_init();
	 
	    curl_setopt($handles[$k], CURLOPT_URL, $u);
	    curl_setopt($handles[$k], CURLOPT_HTTPHEADER, array('Accept-Language:en;q=0.8,en-US;q=0.6'));
	    curl_setopt($handles[$k], CURLOPT_RETURNTRANSFER, 1);
	 
	    curl_multi_add_handle($mh, $handles[$k]);
	  }
	 
	  // execute the handles
	  $running = null;
	  do {
	    curl_multi_exec($mh, $running);
	  } while($running > 0);
	 
	 
	  // get content and remove handles
	  foreach($handles as $id => $content)
	  {
	    $results[$id] = curl_multi_getcontent($content);
	    curl_multi_remove_handle($mh, $content);
	  }
	 
	  // all done
	  curl_multi_close($mh);

	  return $results;
	}


	/**
	 * Get title trailer from youtube api.
	 * 
	 * @param  mixed $title
	 * @param  string release date
	 * @return string
	 */
	public function getTrailer($title = null, $release = '')
	{
		$url = $this->youtube->compileUrl($title, $release);
	
		$json = $this->curl($url);
		
		return $this->youtube->parseTrailers($json);
	}	
}