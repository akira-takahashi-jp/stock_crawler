<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use App\Issue;
use App\Classes\CalcLeastSquare;

class CalcGradient extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'stock:calcgradient';

	protected $numberOfVectors = 5;
	protected $mailTo = 'akira.t.0702@gmail.com';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Import from Excel to issues.';

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
		$issues = Issue::all();
		$checkIssueList = array();

		foreach($issues as $issue){

			$stockPricelInfos = $issue->stockPricelInfos()
				->where('average_75days', '!=', 0)
				->orderBy('acquire_at', 'DESC')
				->take($this->numberOfVectors)
				->get();

			if(count($stockPricelInfos) < 2) continue;

			$vectors = array();
			$i = 0;
			foreach($stockPricelInfos as $stockPricelInfo){
				$vectors[] = ['x' => count($stockPricelInfos) - $i, 'y' => $stockPricelInfo->average_75days];
				$i++;
			}

			$cls = new CalcLeastSquare($vectors) ;
			$stockPricelInfos[0]->gradient_75days = $cls->getGradient();
			$stockPricelInfos[0]->save();

			if($stockPricelInfos[1]->gradient_75days < 0 && $stockPricelInfos[0]->gradient_75days >= 0){
				$issue->price_up_to_date = $stockPricelInfos[0]->opening_price;
				$checkIssueList[] = $issue;
			}

		}

		if(!count($checkIssueList)) return;

		$mailBody = "";
		foreach($checkIssueList as $issue){
			$mailBody .= $issue->code . " " . $issue->name . " ¥" . number_format($issue->unit * $issue->price_up_to_date);
		}

		mb_send_mail($this->mailTo, '75日トレンド転換面柄', $mailBody);

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
