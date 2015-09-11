<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Ranking75LowDiremption extends Model {

	public function issue(){
		return $this->belongsTo('App\Issue');
	}


}
