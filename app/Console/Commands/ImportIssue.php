<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use PHPExcel;
use PHPExcel_IOFactory;
use App\Issue;

class ImportIssue extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'stock:import';

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
		$market = $this->argument('market');

		$fileName = storage_path() . '/app/' . time() . '.xls';
		file_put_contents($fileName, $this->getHtmlContent($this->argument('url')));

		$reader = PHPExcel_IOFactory::createReader('Excel5');
		$excel = $reader->load($fileName);
		$sheet = $excel->getSheet();

		$i = 2;
		while($sheet->getCell("A{$i}") != ""){

			$code = $sheet->getCell("B{$i}");
			$name = $sheet->getCell("C{$i}");

			$issue = Issue::where('code', $code)->first();
			if(!$issue) $issue = new Issue();

			$issue->code = $code;
			$issue->name = $name;
			$issue->market = $market;

			if($issue->getOriginal() != $issue->getAttributes()){
				$issue->save();
			}

			$i++;
		}

		unlink($fileName);

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
			['url', InputArgument::REQUIRED, 'Excel file url.'],
			['market', InputArgument::REQUIRED, 'Market code.ex) T1D(東証一部国内）, T1O, T2D, T2O, MD, MO, JG, JS, JO'],
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
