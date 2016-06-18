<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDirectorsTitles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('directors_titles', function(Blueprint $table)
		{
			$table->Bigincrements('id');
			$table->bigInteger('director_id')->unsigned();
			$table->bigInteger('title_id')->unsigned();
			$table->timestamp('created_at')->default( DB::raw('CURRENT_TIMESTAMP') );
			$table->timestamp('updated_at')->nullable();

			$table->engine = 'InnoDB';
			$table->unique(array('director_id','title_id'), 'director_title_unique');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('directors_titles');
	}

}
