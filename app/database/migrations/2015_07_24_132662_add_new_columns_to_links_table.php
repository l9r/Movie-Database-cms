<?php

use Illuminate\Database\Migrations\Migration;

class AddNewColumnsToLinksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('links', function($table)
		{
    		$table->integer('positive_votes')->default(0);
    		$table->integer('negative_votes')->default(0);
    		$table->string('quality')->default('SD');
		});
		
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$table->dropColumn('positive_votes');
		$table->dropColumn('negative_votes');
		$table->dropColumn('quality');
	}

}