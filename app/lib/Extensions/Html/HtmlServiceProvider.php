<?php namespace Lib\Extensions\Html;

use Illuminate\Html\HtmlServiceProvider as LaravelHtmlProvider;

class HtmlServiceProvider extends LaravelHtmlProvider {

	/**
	 * Register the HTML builder instance.
	 *
	 * @return void
	 */
	protected function registerHtmlBuilder()
	{
		$this->app->bindShared('html', function($app)
		{
			return new HtmlBuilder($app['url']);
		});
	}
}
