<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSeasons extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('seasons', function(Blueprint $table)
		{
			$table->BigIncrements('id');
			$table->string('title', 255)->nullable();
			$table->string('release_date', 255)->nullable();
			$table->string('poster', 255)->nullable();
			$table->text('overview')->nullable();
			$table->integer('number')->default(1);
			$table->bigInteger('title_id')->unsigned();
			$table->string('title_imdb_id', 255)->nullable();
			$table->bigInteger('title_tmdb_id')->unsigned()->nullable();
			$table->tinyInteger('fully_scraped')->default(0)->unsigned();
			$table->tinyInteger('allow_update')->default(1)->unsigned();
			$table->timestamp('created_at')->default( DB::raw('CURRENT_TIMESTAMP') );
			$table->timestamp('updated_at')->nullable();
			$table->string('temp_id', 255)->nullable();

			$table->engine = 'InnoDB';
			$table->unique(array('title_id','number'), 'tile_number_unique');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('seasons');
	}

}
