<?php

use Illuminate\Database\Migrations\Migration;

class IncreaseCustomFieldLen extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('titles', function($table)
		{
    		DB::statement('ALTER TABLE titles MODIFY COLUMN custom_field TEXT');
		});
		
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::statement('ALTER TABLE titles MODIFY COLUMN custom_field TEXT');
	}

}