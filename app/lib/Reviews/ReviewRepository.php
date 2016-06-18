<?php namespace Lib\Reviews;

use Lib\Repository;
use Title, Review, Sentry;
use Lib\Services\Db\Writer;
use Lib\Services\Scraping\Curl;
use Symfony\Component\DomCrawler\Crawler;

class ReviewRepository extends Repository
{
	/**
	 * Writer instance.
	 * 
	 * @var Lib\Services\Db\Writer
	 */
	private $dbWriter;

	/**
	 * Crawler instance.
	 * 
	 * @var Lib\Parsers\mCriticParser
	 */
	private $crawler;

	/**
	 * Scraped reviews html.
	 * 
	 * @var string
	 */
	private $html;

	/**
	 * Curl instance.
	 * 
	 * @var Lib\Services\Scraping\Curl
	 */
	private $scraper;

	/**
	 * Current parsed reviews if any.
	 * 
	 * @var array
	 */
	private $data;

    /**
     * Title model instance.
     * 
     * @var Title
     */
    private $title;

    /**
     * Review model instance.
     * 
     * @var Review
     */
    protected $model;


	/**
     * Instantiates dependencys.
     */
    public function __construct(Writer $dbWriter, Curl $scraper, Review $review)
    {
        $this->model      = $review;
        $this->dbWriter   = $dbWriter;
        $this->scraper    = $scraper;
    }

	 /**
     * Scrapes the reviews of provided title from metacritic.
     * 
     * @param  string $title
     * @return self
     */
    public function get(Title $title)
    {
        $titleName = $title->original_title ? $title->original_title : $title->title;
        $url = $this->url($titleName, $title->type);
        
        $this->html  = $this->scraper->curl($url);
        $this->title = $title;

        return $this;
    }

    /**
     * Parse the reviews from raw html.
     * 
     * @param  string $html
     * @return self
     */
    public function parse()
    {
        //bail if not valid html
        if ( ! $this->html || ! is_string($this->html)) return $this;

        $this->crawler = New Crawler($this->html);

        //bail if we got 404 page
        if ($this->checkIfNot404()) return $this;

        $this->data['reviews'] = $this->compileReviews();
        $this->data['scores']  = $this->parseScores();
      
        return $this;
    }

    /**
     * Parse our the scores from metacritic.
     * 
     * @return array
     */
    private function parseScores()
    {
        $critic     = head( $this->crawler->filter('span[itemprop="ratingValue"]')->extract('_text') );
        $user       = head( $this->crawler->filter('div.metascore_w.user')->extract('_text') );
        $numOfVotes = head( $this->crawler->filter('span.count > a')->extract('_text') );
        $numOfVotes = preg_replace('/[^0-9]/', '', $numOfVotes);

        if ($user === 'tbd') $user = null;

        return array('mc_user_score' => $user, 'mc_critic_score' => $critic, 'mc_num_of_votes' => $numOfVotes);
    }

    /**
     * Checks if we didn't get passed metacritic 404 page html.
     * 
     * @return void/null
     */
    private function checkIfNot404()
    {
        $notFound =  $this->crawler->filter('span.error_type')->extract(array('_text'));

        if ( ! empty($notFound) && head($notFound) == 'Page Not Found')
        {
            return true;
        }
    }

    /**
     * Compile reviews into save ready array.
     * 
     * @return void/array
     */
    private function compileReviews()
    {
        $allReviews = $this->crawler->filter('ol.critic_reviews > li');

        foreach ($allReviews as $k => $v)
        {
            $cr = new crawler($v);

            $compiled[] = array(
                'source' => head($cr->filter('div.source')->extract(array('_text'))),
                'author' => head($cr->filter('div.author > a')->extract(array('_text'))),
                'body'   => trim(head($cr->filter('div.review_body')->extract(array('_text')))),
                'link'   => head($cr->filter('a.external')->extract(array('href'))),
                'score'  => trim(head($cr->filter('div.review_grade')->extract(array('_text'))))
                );
        }
    
        return (isset($compiled) ? $compiled : null);
    }

    /**
     * Saves reviews and scores to database.
     * 
     * @return void
     */
    public function saveFromMetaCritic()
    {		
        //check if we got back any reviews from metacritic
        //before saving
        if ($this->data && $this->data['reviews'])
        {
            //add title id to reviews before inserting
            $this->addTitleId($this->title->id); 
        
            $this->dbWriter->compileBatchInsert('reviews', $this->data['reviews'])->save();
        }

        //check if we got back any scores back
        if ( $this->data && head($this->data['scores']) )
        {
            $this->saveScores();
        }       
    }

    /**
     * Saves reviews from user input.
     * 
     * @param  array $input
     * @param  array $id title id
     * 
     * @return void
     */
    public function save(array $input, $id)
    {
        if (($user = Sentry::getUser()) && $id)
        {
            $input['source']   = trans('main.brand');
            $input['author']   = $user->username;
            $input['title_id'] = $id;
            $input['user_id']  = $user->id;
        
            $this->dbWriter->compileInsert('reviews', $input)->save();
        } 
    }

    /**
     * Saves metacritic scores into db.
     * 
     * @return void
     */
    private function saveScores()
    {
        foreach ($this->data['scores'] as $k => $v)
        {
            $this->title->$k = $v;
        }

        $this->title->save();
    }

    /**
     * Adds title id to review insert array.
     *
     * @param  int $id
     * @return  self 
     */
    private function addTitleId($id)
    {
        foreach ($this->data['reviews'] as $k => $v)
        {
            $this->data['reviews'][$k]['title_id'] =  $id;
        }

        return $this;
    }

    /**
     * Compiles fully qualified metacritic url for scraper
     * 
     * @return string
     */
    public function url($title, $type)
    {
        $base  = 'http://www.metacritic.com/';
       
        //remove all non alpha numeric characters and replace all spaces
        //and double spaces with -
        $title = preg_replace('~[^\p{L}\p{N} -]++~u', '', $title);
        $title = str_replace('  ', '-', trim($title));
        $title = str_replace(' ', '-', trim($title));

        $url = $base . ($type == 'series' ? 'tv' : 'movie') . '/' . $title . '/critic-reviews?sort-by=most-clicked&num_items=100';

        return strtolower($url);
    }

    /**
     * Deletes specified review from database.
     * 
     * @param  string $id
     * @return void
     */
    public function delete($id)
    {
        $user = Sentry::getUser();
        $review = $this->model->find($id);
        
        if ($user && ($user->id == $review->user_id || \Helpers::hasAccess('superuser')))
        {
            $this->model->destroy($id);
        }
    }
}
