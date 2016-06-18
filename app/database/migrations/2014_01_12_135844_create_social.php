<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocial extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('social', function(Blueprint $table)
		{
			$table->bigIncrements('id')->unsigned();
			$table->string('service', 25);		
			$table->string('service_user_identifier', 255)->default('movie');
			$table->bigInteger('user_id')->unsigned();
			$table->timestamp('created_at')->default( DB::raw('CURRENT_TIMESTAMP') );
			$table->timestamp('updated_at')->default('0000-00-00 00:00:00');

			$table->engine = 'InnoDB';
			$table->unique(array('user_id'), 'user_id');
			$table->unique(array('service_user_identifier'), 'service_user_identifier');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('social');
	}

}
