<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class FundamentalInfo extends Model {

	public function issue(){
		return $this->belongsTo('App\Issue');
	}

}
