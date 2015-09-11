<?php namespace App;

	use Illuminate\Database\Eloquent\Model;

class Issue extends Model {

	public function fundamentalInfos(){
		return $this->hasMany('App\FundamentalInfo');
	}

	public function stockPricelInfos(){
		return $this->hasMany('App\StockPriceInfo');
	}

	public function ranking75LowDiremptions(){
		return $this->hasMany('App\Ranking75LowDiremption');
	}
}
