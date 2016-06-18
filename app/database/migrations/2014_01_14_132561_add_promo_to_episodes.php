<?php

use Illuminate\Database\Migrations\Migration;

class AddPromoToEpisodes extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('episodes', function($table)
		{
    		$table->text('promo')->nullable();
		});
		
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$table->dropColumn('promo');
	}

}