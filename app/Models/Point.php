<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Point extends Model
{
  protected $fillable = ['id_game','id_team','points'];

  public function relTeam(){
    return $this->belongsTo('App\Models\Team','id_team');
  }

  public function relGame(){
    return $this->belongsTo('App\Models\Game','id_game');
  }

}
