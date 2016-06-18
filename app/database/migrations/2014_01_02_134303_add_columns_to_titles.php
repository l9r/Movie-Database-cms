<?php

use Illuminate\Database\Migrations\Migration;

class AddColumnsToTitles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('titles', function($table)
		{
    		$table->string('language', 255)->nullable();
			$table->string('country', 255)->nullable();
			$table->string('original_title', 255)->nullable();
			$table->string('affiliate_link', 255)->nullable();
			$table->string('custom_field', 255)->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('titles', function($table)
		{
		    $table->dropColumn('language');
		    $table->dropColumn('country');
		    $table->dropColumn('original_title');
		    $table->dropColumn('affiliate_link');
		    $table->dropColumn('custom_field');
		});
	}

}