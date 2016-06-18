<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersGroups extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users_groups', function(Blueprint $table)
		{
			$table->integer('user_id')->unsigned();
			$table->integer('group_id')->unsigned();

			$table->engine = 'InnoDB';
			$table->primary(array('user_id', 'group_id'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('users_groups');
	}

}
