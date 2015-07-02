<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateModemsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('modems', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('hostname');
			$table->integer('contract_id')->unsigned();
			$table->string('mac')->sizeof(17);
			$table->integer('status');
			$table->boolean('network_access');
			$table->string('serial_num');
			$table->string('inventar_num');
			$table->text('description');
			$table->integer('parent');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('modems');
	}

}