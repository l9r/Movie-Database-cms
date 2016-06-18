<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSlides extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('slides', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->string('title', 255);
			$table->text('body')->nullable();
			$table->string('link', 255);
			$table->string('image', 255);
			
			$table->engine = 'InnoDB';
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('slides');
	}

}
