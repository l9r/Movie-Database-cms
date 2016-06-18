<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReviews extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('reviews', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->string('author', 255);
			$table->string('source', 255)->nullable();
			$table->text('body')->nullable();
			$table->integer('score')->nullable();
			$table->string('link', 255)->nullable();
			$table->integer('title_id')->unsigned();
			$table->integer('user_id')->unsigned()->nullable();
			$table->timestamp('created_at')->default( DB::raw('CURRENT_TIMESTAMP') );
			$table->timestamp('updated_at')->nullable();			
			$table->string('temp_id', 255)->nullable();

			$table->engine = 'InnoDB';
			$table->unique(array('title_id','author'), 'author_title_unique');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('reviews');
	}

}
