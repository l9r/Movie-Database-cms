<?php namespace Lib\Composers;

use Lib\Repository;

class DashStatsbarComposer {

	/**
     * Game Repository instance.
     * 
     * @var  Lib\Repositories\Game\GameRepositoryInterface
     */
    private $repo;
    
    /**
     * Create new DashStatsbarComposer instance.
     * 
     * @param Lib\Repository $repo 
     */
    public function __construct(Repository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Attach db statistics to settings page.
     * 
     * @param  View $view
     * @return void
     */
    public function compose($view)
    {
        $count = array(
            'titles'   => $this->repo->count('Titles'),
            'news'     => $this->repo->count('News'),
            'reviews'  => $this->repo->count('Reviews'),
            'actors'   => $this->repo->count('Actors'),
            'users'    => $this->repo->count('Users'),
        );

        $view->with('count', $count);
    }
}