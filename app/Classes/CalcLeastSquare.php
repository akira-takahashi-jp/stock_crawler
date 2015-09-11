<?php
/**
 * Created by IntelliJ IDEA.
 * User: takahashi
 * Date: 2015/09/11
 * Time: 11:48
 * To change this template use File | Settings | File Templates.
 */

namespace App\Classes;


class CalcLeastSquare {

	private $vectors = array();
	private $gradient = null;
	private $intercept = null;

	public function __construct($vectors){
		$this->vectors = $vectors;
	}

	private function calculate(){

		$a=0;
		$b=0;
		$c=0;
		$d=0;

		foreach($this->vectors as $vector){
			$a += ($vector['x'] * $vector['y']);
			$b += $vector['x'];
			$c += $vector['y'];
			$d += ($vector['x'] * $vector['x']);
		}
		$this->gradient = ($a - ($b * $c)/count($this->vectors)) / ($d - ($b * $b)/count($this->vectors));
		$this->intercept = ($c - $this->gradient * $b) / count($this->vectors);

	}

	public function  getGradient(){
		if($this->gradient === null) $this->calculate();
		return $this->gradient;
	}

	public function  getIntercept(){
		if($this->intercept === null) $this->calculate();
		return $this->intercept;
	}
}


