<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRanking75LowDiremptionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ranking75_low_diremptions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->date('acquire_at');
			$table->integer('order');
			$table->integer('issue_id')->index();
			$table->integer('price');

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
		Schema::drop('ranking75_low_diremptions');
	}

}
