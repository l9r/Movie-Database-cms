<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTitles extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users_titles', function(Blueprint $table)
		{
			$table->bigIncrements('id');
			$table->integer('user_id')->unsigned();
			$table->integer('title_id')->unsigned();
			$table->tinyInteger('watchlist')->default(0)->unsigned();
			$table->tinyInteger('favorite')->default(0)->unsigned();
			$table->timestamp('created_at')->default( DB::raw('CURRENT_TIMESTAMP') );
			$table->timestamp('updated_at')->nullable();

			$table->engine = 'InnoDB';
			$table->unique(array('title_id','user_id', 'watchlist'), 't_u_w_unique');
			$table->unique(array('title_id','user_id', 'favorite'), 't_u_f_unique');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users_titles');
	}

}
