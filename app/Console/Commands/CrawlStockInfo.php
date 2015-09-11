<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use App\Issue;
use App\StockPriceInfo;

class CrawlStockInfo extends Command
{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'stock:crawl';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Crawl from YahooFinance.';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$today = date('Y-m-d');
		$yesterday = date('Y-m-d', strtotime((date('N') == 1 ? '-3' : '-1') . ' day'));

		$issues = Issue::all();

		foreach ($issues as $issue) {

			$stockPriceInfoToday = new StockPriceInfo();
			$stockPriceInfoToday->acquire_at = $today;
			$stockPriceInfoYesterday = StockPriceInfo::where('issue_id', $issue->id)
				->where('acquire_at', $yesterday)
				->first();
			if (!$stockPriceInfoYesterday) {
				$stockPriceInfoYesterday = new StockPriceInfo();
				$stockPriceInfoYesterday->acquire_at = $yesterday;
			}

			$html = $this->getHtmlContent($this->getUrl($issue->code));
			$ret = preg_match_all('#<dl.*?</dl>#ms', $html, $matches);
			foreach ($matches[0] as $element) {
				if (preg_match('#<strong>([^<]+)</strong>.*<dt class="title">単元株数#ms', $element, $matchesTemp)) {
					$issue->unit = intval(str_replace(',', '', $matchesTemp[1]));
				}

				if (preg_match('#<strong>([^<]+)</strong>.*<dt class="title">前日終値#ms', $element, $matchesTemp)) {
					$stockPriceInfoYesterday->closing_price = intval(str_replace(',', '', $matchesTemp[1]));
				}
				if (preg_match('#<strong>([^<]+)</strong>.*<dt class="title">始値#ms', $element, $matchesTemp)) {
					$stockPriceInfoToday->opening_price = intval(str_replace(',', '', $matchesTemp[1]));
				}
				if (preg_match('#<strong>([^<]+)</strong>.*<dt class="title">高値#ms', $element, $matchesTemp)) {
					$stockPriceInfoToday->high_price = intval(str_replace(',', '', $matchesTemp[1]));
				}
				if (preg_match('#<strong>([^<]+)</strong>.*<dt class="title">安値#ms', $element, $matchesTemp)) {
					$stockPriceInfoToday->low_price = intval(str_replace(',', '', $matchesTemp[1]));
				}
				if (preg_match('#<strong>([^<]+)</strong>.*<dt class="title">出来高#ms', $element, $matchesTemp)) {
					$stockPriceInfoToday->traded_volume = intval(str_replace(',', '', $matchesTemp[1]));
				}

				if (preg_match('#<strong>([^<]+)</strong>.*<dt class="title">発行済株式数#ms', $element, $matchesTemp)) {
					$stockPriceInfoToday->number_of_stocks = intval(str_replace(',', '', $matchesTemp[1]));
				}
				if (preg_match('#<strong>([^<]+)</strong>.*<dt class="title">配当利回り#ms', $element, $matchesTemp)) {
					$stockPriceInfoToday->dividend_yield = floatval(str_replace(',', '', $matchesTemp[1]));
				}
				if (preg_match('#<a[^>]+?>([^<]+)</a>.*1株配当#ms', $element, $matchesTemp)) {
					$stockPriceInfoToday->dividend_per_stock = floatval(str_replace(',', '', $matchesTemp[1]));
				}
				if (preg_match('#<strong>.*?([^<()]+)</strong>.*<dt class="title">PER#ms', $element, $matchesTemp)) {
					$stockPriceInfoToday->per = floatval(str_replace(',', '', $matchesTemp[1]));
				}
				if (preg_match('#<strong>.*?([^<()]+)</strong>.*<dt class="title">PBR#ms', $element, $matchesTemp)) {
					$stockPriceInfoToday->pbr = floatval(str_replace(',', '', $matchesTemp[1]));
				}
				if (preg_match('#<a[^>]+?>.*?([^<()]+)</a>.*EPS#ms', $element, $matchesTemp)) {
					$stockPriceInfoToday->eps = floatval(str_replace(',', '', $matchesTemp[1]));
				}
				if (preg_match('#<a[^>]+?>.*?([^<()]+)</a>.*BPS#ms', $element, $matchesTemp)) {
					$stockPriceInfoToday->bps = floatval(str_replace(',', '', $matchesTemp[1]));
				}

			}

			if ($issue->getOriginal() != $issue->getAttributes()) $issue->save();
			$issue->stockPricelInfos()->save($stockPriceInfoToday);
			$issue->stockPricelInfos()->save($stockPriceInfoYesterday);
		}


	}

	private function getUrl($code)
	{
		return "http://stocks.finance.yahoo.co.jp/stocks/detail/?code={$code}.t";
	}

	private function getHtmlContent($url){
		for($i=0; $i<3; $i++){
			try{
				$content = @file_get_contents($url);
				if($content) return $content;
			}catch (Exception $e){

			}
		}
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			//['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
		];
	}

}
