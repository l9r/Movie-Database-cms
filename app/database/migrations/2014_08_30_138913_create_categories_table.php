<?php

use Illuminate\Database\Migrations\Migration;

class CreateCategoriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('categories', function($table)
		{
    		$table->increments('id');
    		$table->string('name')->default('Generic Name');
    		$table->string('icon')->default('fa fa-fire');
    		$table->string('query')->nullable();
    		$table->boolean('auto_update')->default(0);
    		$table->boolean('show_trailer')->default(0);
    		$table->boolean('show_rating')->default(0);
    		$table->boolean('active')->default(1);
    		$table->integer('weight')->default(1);
    		$table->integer('limit')->default(8);
    		$table->timestamp('created_at')->default( DB::raw('CURRENT_TIMESTAMP') );
			$table->timestamp('updated_at')->nullable();

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
		$table->dropColumn('name');
	}

}