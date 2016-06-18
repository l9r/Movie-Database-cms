<?php

use Illuminate\Database\Migrations\Migration;

class AddColumnsToSliderTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('slides', function($table)
		{
    		$table->string('genre')->nullable();
    		$table->string('director')->nullable();
    		$table->string('stars')->nullable();
    		$table->string('trailer')->nullable();
    		$table->string('trailer_image')->nullable();
		});
		
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$table->dropColumn('genre');
		$table->dropColumn('director');
		$table->dropColumn('stars');
		$table->dropColumn('trailer');
		$table->dropColumn('trailer_image');
	}

}