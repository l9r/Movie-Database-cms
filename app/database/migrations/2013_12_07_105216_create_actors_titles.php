<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActorsTitles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('actors_titles', function(Blueprint $table)
		{
			$table->Bigincrements('id');
			$table->bigInteger('actor_id')->unsigned();
			$table->bigInteger('title_id')->unsigned();
			$table->string('char_name', 255)->default('Unknown');
			$table->tinyInteger('known_for')->default(0)->unsigned();
			$table->timestamp('created_at')->default( DB::raw('CURRENT_TIMESTAMP') );
			$table->timestamp('updated_at')->nullable();

			$table->engine = 'InnoDB';
			$table->unique(array('actor_id','title_id'), 'actor_title_unique');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('actors_titles');
	}

}
