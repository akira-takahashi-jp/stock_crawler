<?php namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {

	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		'App\Console\Commands\Inspire',
		'App\Console\Commands\ImportIssue',
		'App\Console\Commands\CrawlStockInfo',
		'App\Console\Commands\CrawlRanking',
		'App\Console\Commands\CalcGradient',
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		/*
		$schedule->command('stock:crawl')->dailyAt('12:33')
		->sendOutputTo(storage_path('logs/schedule.log'));
		$schedule->command('stock:import http://www.jpx.co.jp/markets/statistics-equities/misc/tvdivq0000001vg2-att/first-d-j.xls T1D')->dailyAt('12:20');
		$schedule->command('stock:import http://www.jpx.co.jp/markets/statistics-equities/misc/tvdivq0000001vg2-att/first-f-j.xls T1F')->dailyAt('12:29')
			->sendOutputTo(storage_path('logs/schedule.log'));
		*/
	}

}
