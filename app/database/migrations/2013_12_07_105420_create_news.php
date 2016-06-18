<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNews extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('news', function(Blueprint $table)
		{
			$table->BigIncrements('id');
			$table->string('title', 255);
			$table->string('image', 255)->nullable();
			$table->text('body')->nullable();
			$table->string('source', 255)->nullable();
			$table->string('full_url', 255)->nullable();
			$table->string('author', 255)->nullable();
			$table->timestamp('created_at')->default( DB::raw('CURRENT_TIMESTAMP') );
			$table->timestamp('updated_at')->nullable();
			$table->string('temp_id', 255)->nullable();

			$table->engine = 'InnoDB';
			$table->unique('title', 'title_unique');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('news');
	}

}
