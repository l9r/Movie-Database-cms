<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImages extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('images', function(Blueprint $table)
		{
			$table->BigIncrements('id');
			$table->string('local', 255)->nullable();
			$table->string('web', 255)->nullable();
			$table->bigInteger('title_id')->unsigned()->nullable();
			$table->timestamp('created_at')->default( DB::raw('CURRENT_TIMESTAMP') );
			$table->timestamp('updated_at')->nullable();

			$table->engine = 'InnoDB';
			$table->unique('local');
			$table->unique('web');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('images');
	}

}
