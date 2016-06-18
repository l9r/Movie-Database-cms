<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductionCompaniesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('production_companies', function($table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('logo')->nullable();
			$table->string('description')->nullable();
			$table->timestamps();

			$table->engine = 'InnoDB';
			$table->unique('name');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('production_companies');
	}

}
