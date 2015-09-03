<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class StockPriceInfo extends Model {

	public function issue(){
		return $this->belongsTo('App\Issue');
	}

}
