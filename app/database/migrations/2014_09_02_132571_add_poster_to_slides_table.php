<?php

use Illuminate\Database\Migrations\Migration;

class AddPosterToSlidesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('slides', function($table)
		{
    		$table->string('poster')->nullable();
		});
		
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$table->dropColumn('poster');
	}

}