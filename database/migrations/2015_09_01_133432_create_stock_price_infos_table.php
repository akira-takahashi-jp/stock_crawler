<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStockPriceInfosTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('stock_price_infos', function(Blueprint $table)
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
			$table->integer('opening_price');
			$table->integer('closing_price');
			$table->integer('high_price');
			$table->integer('low_price');
			$table->integer('traded_volume');
			$table->integer('average_25days');
			$table->integer('average_75days');

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
		Schema::drop('stock_price_infos');
	}

}
