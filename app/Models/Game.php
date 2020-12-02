<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Game extends Model
{
  protected $fillable = ['id_team_home','id_team_away','goals_home','goals_away','id_type','id_group'];

  public function relGameType(){
    return $this->belongsTo('App\Models\GameType','id_type');
  }

  public function relTeamHome(){
    return $this->belongsTo('App\Models\Team','id_team_home');
  }

  public function relTeamAway(){
    return $this->belongsTo('App\Models\Team','id_team_away');
  }

  public function relGroup(){
    return $this->belongsTo('App\Models\Group','id_group');
  }




  public function getAllGamesWithType() {
    return $this->belongsTo('App\Models\GameType','id','id_type')
  ->join('games', 'games.id_type', '=', 'game_types.id')
  ->select('games.*', 'game_types.display_name')
  ->get();
}

}
