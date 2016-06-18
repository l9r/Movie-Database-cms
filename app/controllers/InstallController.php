<?php

use Lib\Services\Installer;

class InstallController extends BaseController {

    /**
     * Installer instance.
     * 
     * @var Lib\Services\Installer
     */
    private $installer;

    /**
     * Create new InstallController instance.
     *
     * @param Installer $installer
     */
    public function __construct(Installer $installer)
    {
    	$this->beforeFilter('installed');
        $this->installer = $installer;

        ini_set('max_execution_time', 0);
    }

    public function getIndex()
    {
        return View::make('Install.Index');
    }

    /**
     * Check for any compatability issues between
     * server and this app.
     * 
     * @return array
     */
    public function postCheckCompat()
    {
        return $this->installer->checkForIssues();
    }

    /**
     * Store basic site information and admin account
     * details to database.
     * 
     * @return array
     */
    public function postStoreBasics()
    {
        $validator = App::make('Lib\Services\Validation\UserValidator');

        if ( ! $validator->with(Input::all())->passes())
        {
            return Response::json($validator->errors(), 400);
        }

        try {
            $this->installer->storeBasics(Input::except('_token', 'password_confirmation'));
        } catch (Exception $e) {
            return Response::json($e->getMessage(), 500);
        }

        return Response::json('', 201);
    }

    /**
     * Create database schema.
     * 
     * @return array
     */
    public function postPrepareDb()
    {
        $input = Input::except('_token');
        
        if ( ! Input::get('filledManually'))
        {
            $db =  'mysql:host='.$input['host'].';dbname='.$input['database'];
        
            //test db connection with user supplied credentials
            try {
                $conn = new PDO($db, $input['username'], $input['password']);
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch(PDOException $e) {
                return Response::json($e->getMessage(), 403);
            }
        }

        //create database schema
        try {
            $this->installer->createSchema($input);
        } catch (Exception $e) {
            return Response::json($e->getMessage(), 500);
        }

        return Response::json('', 201);   
    }

    /**
     * Finalize the installation.
     * 
     * @return Response
     */
    public function postFinalize()
    {
        $this->installer->finalize(Input::all());
        
        return Response::json('', 201); 
    }
}