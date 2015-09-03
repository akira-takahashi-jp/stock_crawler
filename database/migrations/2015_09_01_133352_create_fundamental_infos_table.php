<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFundamentalInfosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('fundamental_infos', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('issue_id');
			$table->date('acquire_at');
			$table->integer('number_of_stocks');
			$table->decimal('dividend_yield');
			$table->decimal('dividend_per_stock');
			$table->decimal('per');
			$table->decimal('pbr');
			$table->decimal('eps');
			$table->decimal('bps');
			$table->timestamps();

			$table->index(['issue_id', 'acquire_at']);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('fundamental_infos');
	}

}
