<?php

use Illuminate\Database\Migrations\Migration;

class AddApprovedToLinksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('links', function($table)
		{
    		$table->boolean('approved')->default(1);
		});
		
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$table->dropColumn('approved');
	}

}