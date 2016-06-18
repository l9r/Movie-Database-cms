<?php

use Illuminate\Database\Migrations\Migration;

class AddColumnsToNews extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('news', function($table)
		{
    		$table->tinyInteger('fully_scraped')->default(0)->unsigned();
		});
		
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$table->dropColumn('fully_scraped');
	}

}