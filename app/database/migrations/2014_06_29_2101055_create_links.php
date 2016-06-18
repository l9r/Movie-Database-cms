<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLinks extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('links', function(Blueprint $table)
		{
			$table->bigIncrements('id')->unsigned();
			$table->string('url', 255);
			$table->string('type', 255)->default('embed');
			$table->string('label', 255)->nullable();
			$table->bigInteger('title_id')->unsigned()->nullable();
			$table->integer('season')->unsigned()->nullable();
			$table->integer('episode')->unsigned()->nullable();
			$table->integer('reports')->unsigned()->default(0);
			$table->timestamp('created_at')->default( DB::raw('CURRENT_TIMESTAMP') );
			$table->timestamp('updated_at')->default('0000-00-00 00:00:00');
			$table->string('temp_id', 255)->nullable();

			$table->engine = 'InnoDB';
			$table->unique('url');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('links');
	}

}
