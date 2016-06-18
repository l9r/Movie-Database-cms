<?php namespace Lib\Composers;

use Route, File;
use Lib\Page\PageRepository as PageRepo;

class DashMenusComposer {

	/**
     * Page repository implementation.
     * 
     * @var Lib\Repositories\Page\PageRepositoryInterface
     */
    private $repo;

    /**
     * Create new DashMenusComposer instance.
     * 
     * @param PageRepo $repo
     */
    public function __construct(PageRepo $repo)
    {
        $this->repo = $repo;
    }

    /**
     * Attach required data to Dashboard Menus view.
     * 
     * @param  View $view
     * @return void
     */
    public function compose($view)
    {
        $routes   = $this->getAllRoutes();
        $partials = $this->getDropdownPartials();
        $pages    = $this->getPages();

        $view->with('routes', $routes)->with('partials', $partials)->with('pages', $pages);
    }

    /**
     * Return array of available pages titles.
     * 
     * @return array
     */
    private function getPages()
    {
        return $this->repo->getPagesList();
    }

    /**
     * Returns an array of available dropdown
     * partial views names.
     * 
     * @return array
     */
    private function getDropdownPartials()
    {
        $paths = File::files(app_path('views/Partials/Menus/Dropdowns'));

        $names = array();
        
        foreach ($paths as $path)
        {
           $names[] = str_replace('.blade.php', '', basename($path));
        }
 
        return $names;
    }

    /**
     * Return an array of all registered get routes.
     * 
     * @param  string $method
     * @return array
     */
    private function getAllRoutes($method = 'GET')
    {
    	$compiled = array();
    	$routes = Route::getRoutes();

    	foreach ($routes->getRoutes() as $route)
    	{
				$methods = $route->getMethods();
				$action  = $route->getAction();
				
				if ($methods[0] === $method && isset($action['as']) && strpos($route->getURI(), '{') === false)
                {
					array_push($compiled, $action['as']);		
                }
		}

		return $compiled;
    }
}