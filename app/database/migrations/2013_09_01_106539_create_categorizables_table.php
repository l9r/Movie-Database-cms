<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCategorizablesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('categorizables', function(Blueprint $table)
		{
			$table->Bigincrements('id');
			$table->bigInteger('category_id')->unsigned();
			$table->bigInteger('categorizable_id')->unsigned();
			$table->string('categorizable_type')->default('title');
			$table->timestamp('created_at')->default( DB::raw('CURRENT_TIMESTAMP') );
			$table->timestamp('updated_at')->nullable();

			$table->engine = 'InnoDB';
			$table->unique(array('category_id', 'categorizable_id', 'categorizable_type'), 'ccc_unique');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('categories_titles');
	}

}
