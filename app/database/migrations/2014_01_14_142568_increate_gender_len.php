<?php

use Illuminate\Database\Migrations\Migration;

class IncreateGenderLen extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('users', function($table)
		{
    		DB::statement('ALTER TABLE users MODIFY COLUMN gender VARCHAR(10)');
		});
		
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement('ALTER TABLE users MODIFY COLUMN gender VARCHAR(5)');
	}

}