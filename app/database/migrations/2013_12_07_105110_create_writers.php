<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWriters extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('writers', function(Blueprint $table)
		{
			$table->BigIncrements('id');
			$table->string('name', 255);
			$table->tinyInteger('allow_update')->default(1)->unsigned();
			$table->timestamp('created_at')->default( DB::raw('CURRENT_TIMESTAMP') );
			$table->timestamp('updated_at')->nullable();
			$table->string('temp_id', 255)->nullable();
			$table->unique('name');

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
		Schema::drop('writers');
	}

}
