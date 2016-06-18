<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTitles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('titles', function(Blueprint $table)
		{
			$table->bigIncrements('id')->unsigned();
			$table->string('title', 255);		
			$table->string('type', 15)->default('movie');
			$table->string('imdb_rating', 3)->nullable();
			$table->string('tmdb_rating', 3)->nullable();
			$table->string('mc_user_score', 3)->nullable();
			$table->smallInteger('mc_critic_score')->nullable()->unsigned();
			$table->integer('mc_num_of_votes')->nullable()->unsigned();
			$table->bigInteger('imdb_votes_num')->nullable()->unsigned();
			$table->string('release_date', 255)->nullable();
			$table->smallInteger('year')->nullable()->unsigned();
			$table->text('plot')->nullable();
			$table->string('genre', 255)->nullable();
			$table->string('tagline', 255)->nullable();
			$table->string('poster', 255)->nullable();
			$table->string('background', 255)->nullable();
			$table->string('awards', 255)->nullable();
			$table->string('runtime', 255)->nullable();
			$table->string('trailer', 255)->nullable();
			$table->string('budget', 255)->nullable();
			$table->string('revenue', 255)->nullable();
			$table->bigInteger('views')->default(1);
			$table->float('tmdb_popularity', 50)->unsigned()->nullable();
			$table->string('imdb_id', 255)->nullable();
			$table->bigInteger('tmdb_id')->unsigned()->nullable();
			$table->tinyInteger('season_number')->nullable()->unsigned();
			$table->tinyInteger('fully_scraped')->default(0)->unsigned();
			$table->tinyInteger('allow_update')->default(1)->unsigned();
			$table->tinyInteger('featured')->default(0)->unsigned();
			$table->tinyInteger('now_playing')->default(0)->unsigned();
			$table->timestamp('created_at')->default( DB::raw('CURRENT_TIMESTAMP') );
			$table->timestamp('updated_at')->default('0000-00-00 00:00:00');
			$table->string('temp_id', 255)->nullable();

			$table->engine = 'InnoDB';
			$table->unique('imdb_id');
			$table->unique(array('tmdb_id', 'type'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('titles');
	}

}
