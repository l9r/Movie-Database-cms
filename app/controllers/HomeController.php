<?php

use Carbon\Carbon;
use Lib\Services\Mail\Mailer;
use Lib\Services\Validation\ContactValidator;
use Lib\Services\Rendering\HomepageRenderer;

class HomeController extends BaseController
{
	/**
	 * Validator instance.
	 * 
	 * @var Lib\Services\Validation\ContactValidator
	 */
	private $validator;

	/**
	 * Options instance.
	 * 
	 * @var Lib\Services\Options\Options
	 */
	private $options;

	/**
	 * Mailer instance.
	 * 
	 * @var Lib\Services\Mail\Mailer;
	 */
	private $mailer;


	public function __construct(ContactValidator $validator, Mailer $mailer, HomepageRenderer $renderer)
	{
		$this->mailer = $mailer;
		$this->renderer = $renderer;
		$this->validator = $validator;
		$this->options = App::make('options');
	}

	/**
	 * Show homepage.
	 * 
	 * @return View
	 */
	public function index()
	{	
		return $this->renderer->render('Home.Home');	  
	}

	/**
	 * Show contact us page.
	 * 
	 * @return View
	 */
	public function contact()
	{
		return View::make('Main.Contact');
	}

	/**
	 * Sends an email message from contact us form.
	 * 
	 * @return View
	 */
	public function submitContact()
	{
		$input = Input::all();

		if ( ! $this->validator->with($input)->passes())
		{
			return Redirect::back()->withErrors($this->validator->errors())->withInput($input);
		}

		$this->mailer->sendContactUs($input);

		return Redirect::to('/')->withSuccess( trans('main.contact succes') );
	}
}