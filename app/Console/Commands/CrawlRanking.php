<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpSpec\Exception\Exception;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use App\Issue;
use App\StockPriceInfo;
use App\Ranking75LowDiremption;
use Illuminate\Support\Facades\DB;

class CrawlRanking extends Command
{

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'stock:ranking';

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

		DB::table('ranking75_low_diremptions')->truncate();
		$this->crawlYahoo('http://info.finance.yahoo.co.jp/ranking/?kd=26&tm=d&vl=a&mk=1&p=', '75');
		$this->crawlYahoo('http://info.finance.yahoo.co.jp/ranking/?kd=24&tm=d&vl=a&mk=1&p=', '25');

	}

	private function crawlYahoo($baseUrl, $kind)
	{
		$today = date('Y-m-d');
		$yesterday = date('Y-m-d', strtotime((date('N') == 1 ? '-3' : '-1') . ' day'));

		//max page
		$html = $this->getHtmlContent($baseUrl . '1');
		preg_match('#<ul class="ymuiPagingBottom.*?</ul>#ms', $html, $matches);
		preg_match_all('#<a.*?>([0-9]+)</a>#ms', $matches[0], $matches);
		$maxPage = $matches[1][count($matches[1])-1];
		$order = 0;

		for($i=1; $i<=$maxPage; $i++){

			$html = $this->getHtmlContent($baseUrl . $i);

			preg_match_all('#<tr class="rankingTabledata yjM">.*?</tr>#ms', $html, $matches);

			foreach($matches[0] as $row){

				$order++;

				preg_match('#<a.*?>(.*?)</a>#ms', $row, $matchesTemp);
				$issue = Issue::where('code', $matchesTemp[1])->first();
				if(!$issue) continue;
				$stockPriceInfo = StockPriceInfo::where('issue_id', $issue->id)->where('acquire_at', $yesterday)->first();

				preg_match('#<td class="txtright">(.*?)</td>#ms', $row, $matchesTemp);
				if($kind == '75'){
					$stockPriceInfo->average_75days = str_replace(',', '', $matchesTemp[1]);
				}elseif($kind == '25'){
					$stockPriceInfo->average_25days = str_replace(',', '', $matchesTemp[1]);
				}
				$stockPriceInfo->save();

				if($kind == '75'){
					preg_match('#<td class="txtright bold">(.*?)</td>#ms', $row, $matchesTemp);
					$ranking75LowDiremption = new Ranking75LowDiremption();
					$ranking75LowDiremption->acquire_at = $today;
					$ranking75LowDiremption->order = $order;
					$ranking75LowDiremption->issue_id = $issue->id;
					$ranking75LowDiremption->price = str_replace(',', '', $matchesTemp[1]);
					$ranking75LowDiremption->save();

				}


			}

		}

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
