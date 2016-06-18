<?php namespace Lib\Hooks;

use View;

class Hooks {

	/**
	 * Js script paths to include in main template.
	 * 
	 * @var array
	 */
	protected $scripts = array();

	/**
	 * css file paths to include in main template.
	 * 
	 * @var array
	 */
	protected $css = array();

	/**
	 * Array of keyd html to render in the templates.
	 * 
	 * @var array
	 */
	protected $html = array();

	protected $replaceHtml = array();

	/**
	 * Register a new script to include in main template.
	 * 
	 * @param  string $path
	 * @return void
	 */
	public function registerScript($path)
	{
		$this->scripts[] = $path;
	}

	/**
	 * Register a new css file to include in main template.
	 * 
	 * @param  string $path
	 * @return void
	 */
	public function registerCss($path)
	{
		$this->css[] = $path;
	}

	/**
	 * Compiles blade view html and registers it.
	 * 
	 * @param  string $key
	 * @return void
	 */
	public function registerView($key)
	{
		$html = View::make($key)->render();
		
		$this->registerHtml($key, $html);
	}

	public function hasReplace($key)
	{
		return array_key_exists($key, $this->replaceHtml);
	}

	public function registerReplace($key)
	{
		$this->replaceHtml[$key] = $key;
	}

	public function renderReplace($key, $args, $name = 'title')
	{
        return View::make($key)->with($name, $args)->render();
	}

	public function registerHtml($key, $html)
	{
		$this->html[$key] = $html;
	}

	public function renderHtml($key)
	{
		if (isset($this->html[$key])) 
		{
			return $this->html[$key];
		}
	}

	/**
	 * Return rendered html for registered scripts.
	 * 
	 * @return string
	 */
	public function renderScripts()
	{
		$html = '';

		foreach ($this->scripts as $path)
		{
			$html .= '<script src="'.$path.'"></script>';
		}

		return $html;
	}

	/**
	 * Return rendered html for registered css.
	 * 
	 * @return string
	 */
	public function renderCss()
	{
		$html = '';

		foreach ($this->css as $path)
		{
			$html .= '<link media="all" type="text/css" rel="stylesheet" href="'.$path.'">';
		}

		return $html;
	}
}