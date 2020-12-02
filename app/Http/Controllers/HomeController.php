<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GameType;
use App\Models\Game;
use App\Models\Team;
use App\Models\Point;
use DB;

class HomeController extends Controller
{

    private $objGameType;
    private $objGame;
    private $objPoint;
    private $objTeam;

    public function __construct(){
      $this->objGameType = new GameType();
      $this->objGame = new Game();
      $this->objPoint = new Point();
      $this->objTeam = new Team();
    }

    private function getGamesByRound($round){
      $games = $this->objGame->whereHas('relGameType', function($query) use($round) {
      $query->where('name', $round);
      })->get();

      $gamesArray = array();
      foreach ($games as $game) {
        $gameArray = array(
        "teamHome" => $game->relTeamHome->name,
        "goalsHome" => $game->goals_home,
        "goalsAway" => $game->goals_away,
        "teamAway" => $game->relTeamAway->name
        );
        array_push($gamesArray, $gameArray);
      }
      return $gamesArray;
    }

    private function getPointsByGroup($group){
      $points = DB::select(" SELECT @r := @r+1 as pos,z.*
      FROM ( SELECT `groups`.`name_group` as Group_name, `teams`.`id` as id, `teams`.`name` as Name, IFNULL(SUM(`points`.points), 0) as Points, COUNT(`points`.`id_team`) as Matches,
      COUNT(if(`points`.points = 3, 1, NULL)) as Won,  COUNT(if(`points`.points = 1, 1, NULL)) as Drawn,  COUNT(if(`points`.points = 0, 1, NULL)) as Lost,
      IFNULL(SUM(case when `teams`.`id` = `games`.`id_team_home` then `games`.`goals_home` else `games`.`goals_away` end), 0) as GF,
      IFNULL(SUM(case when `teams`.`id` = `games`.`id_team_home` then `games`.`goals_away` else `games`.`goals_home` end), 0) as GA,
      IFNULL(SUM((case when `teams`.`id` = `games`.`id_team_home` then `games`.`goals_home` else `games`.`goals_away` end) -
                       (case when `teams`.`id` = `games`.`id_team_home` then `games`.`goals_away` else `games`.`goals_home` end)), 0) as GD
      FROM `teams`
      LEFT JOIN `points`
      ON `points`.`id_team` = `teams`.`id`
      LEFT JOIN `games`
      ON `games`.`id` = `points`.`id_game`
      LEFT JOIN `groups`
      ON `groups`.`id` = `teams`.`id_type`
      LEFT JOIN `game_types`
      ON `game_types`.`id` = 1
      WHERE `groups`.`name_group` = '$group'
      GROUP BY `teams`.`id`, `groups`.`id`
      ORDER BY `points` DESC, GD DESC, GF DESC, Matches DESC) z, (SELECT @r:=0)y;");


      return $points;
    }

    private function getGameById($id){
      $game = DB::select("SELECT *
      FROM `games`
      WHERE `games`.`id` = '$id'");

    }

    public function getIdFromTeams($teams){
      $teamsId = array();
      foreach ($teams as $team){
        array_push($teamsId, $team->id);
      }
      return $teamsId;
    }

    private function removeUnique($arr) {
      $hist = array();
    	for($i = 0; $i < count($arr); $i++) {
        if($i == 0){
          $hist[$arr[$i]->Points]=1;
        }else if (array_key_exists($arr[$i]->Points, $hist)) {
          $hist[$arr[$i]->Points] = $hist[$arr[$i]->Points] + 1;
        }else{
          $hist[$arr[$i]->Points]= 1;
        }
    	}

      $count = count($arr);
      for($i = 0; $i < $count; $i++){
        if($hist[$arr[$i]->Points] == 1){
          unset($arr[$i]);
        }
      }

      return $arr;
    }

    private function removeUniqueGoals($arr) {
      $hist = array();
    	for($i = 0; $i < count($arr); $i++) {
        if($i == 0){
          $hist[$arr[$i]->GD]=1;
        }else if (array_key_exists($arr[$i]->GD, $hist)) {
          $hist[$arr[$i]->GD] = $hist[$arr[$i]->GD] + 1;
        }else{
          $hist[$arr[$i]->GD]= 1;
        }
    	}

      $count = count($arr);
      for($i = 0; $i < $count; $i++){
        if($hist[$arr[$i]->GD] == 1){
          unset($arr[$i]);
        }
      }

      return $arr;
    }

    private function removeUniqueGoalsScored($arr) {
      $hist = array();
    	for($i = 0; $i < count($arr); $i++) {
        if($i == 0){
          $hist[$arr[$i]->GF]=1;
        }else if (array_key_exists($arr[$i]->GF, $hist)) {
          $hist[$arr[$i]->GF] = $hist[$arr[$i]->GF] + 1;
        }else{
          $hist[$arr[$i]->GF]= 1;
        }
    	}

      $count = count($arr);
      for($i = 0; $i < $count; $i++){
        if($hist[$arr[$i]->GF] == 1){
          unset($arr[$i]);
        }
      }

      return $arr;
    }

    private function getLeagueFromIds($arrayTeams, $group){
      $arrayToUnset = array_slice($arrayTeams,1);
      $addQuery = "";
      $i = 0;
      foreach ($arrayTeams as &$value0){
        foreach ($arrayToUnset as &$value1){
          if($i!=0){
              $addQuery.='OR';
          }
          $addQuery.='(`games`.`id_team_home` = '.$value0['id'].' AND `games`.`id_team_away` = '.$value1['id'].')
                    OR (`games`.`id_team_home` = '.$value1['id'].' AND `games`.`id_team_away` = '.$value0['id'].')';
                    $i++;
        }
        $arrayToUnset = array_slice($arrayToUnset,1);
      }

      $league = DB::select("SELECT `teams`.`id` as id, `teams`.`name` as Name, IFNULL(SUM(`points`.points), 0) as Points
             FROM `teams`
             LEFT JOIN `points`
             ON `points`.`id_team` = `teams`.`id`
             LEFT JOIN `games`
             ON `games`.`id` = `points`.`id_game`
             LEFT JOIN `groups`
             ON `groups`.`id` = `games`.`id_group`
             LEFT JOIN `game_types`
             ON `game_types`.`id` = `games`.`id_type`
             WHERE `groups`.`name_group` = '$group' AND `game_types`.`name` = 'groups'
             AND (".$addQuery.")
             GROUP BY `teams`.`id`, `groups`.`id`
             ORDER BY `points`  DESC");

      for ($i = 0; $i < count($league); $i++){
        foreach ($arrayTeams as $teams_array){
          if($league[$i]->id == $teams_array['id']){
            $league[$i]->pos = $teams_array['pos'];
          }
        }
      }

      return $league;
    }

    private function getLeagueFromIds_Goals($arrayTeams, $group){
      $arrayToUnset = array_slice($arrayTeams,1);
      $addQuery = "";
      $i = 0;
      foreach ($arrayTeams as &$value0){
        foreach ($arrayToUnset as &$value1){
          if($i!=0){
              $addQuery.='OR';
          }
          $addQuery.='(`games`.`id_team_home` = '.$value0->id.' AND `games`.`id_team_away` = '.$value1->id.')
                    OR (`games`.`id_team_home` = '.$value1->id.' AND `games`.`id_team_away` = '.$value0->id.')';
                    $i++;
        }
        $arrayToUnset = array_slice($arrayToUnset,1);
      }

      $league = DB::select("SELECT `teams`.`id` as id, `teams`.`name` as Name, IFNULL(SUM(`points`.points), 0) as Points,
			       IFNULL(SUM((case when `teams`.`id` = `games`.`id_team_home` then `games`.`goals_home` else `games`.`goals_away` end) -
             (case when `teams`.`id` = `games`.`id_team_home` then `games`.`goals_away` else `games`.`goals_home` end)), 0) as GD
             FROM `teams`
             LEFT JOIN `points`
             ON `points`.`id_team` = `teams`.`id`
             LEFT JOIN `games`
             ON `games`.`id` = `points`.`id_game`
             LEFT JOIN `groups`
             ON `groups`.`id` = `games`.`id_group`
             LEFT JOIN `game_types`
             ON `game_types`.`id` = `games`.`id_type`
             WHERE `groups`.`name_group` = '$group' AND `game_types`.`name` = 'groups'
             AND (".$addQuery.")
             GROUP BY `teams`.`id`, `groups`.`id`
             ORDER BY GD  DESC");

      for ($i = 0; $i < count($league); $i++){
        foreach ($arrayTeams as $teams_array){
          if($league[$i]->id == $teams_array->id){
            $league[$i]->pos = $teams_array->pos;
          }
        }
      }
      return $league;
    }

    private function getLeagueFromIds_GoalsScored($arrayTeams, $group){
      $arrayToUnset = array_slice($arrayTeams,1);
      $addQuery = "";
      $i = 0;
      foreach ($arrayTeams as &$value0){
        foreach ($arrayToUnset as &$value1){
          if($i!=0){
              $addQuery.='OR';
          }
          $addQuery.='(`games`.`id_team_home` = '.$value0->id.' AND `games`.`id_team_away` = '.$value1->id.')
                    OR (`games`.`id_team_home` = '.$value1->id.' AND `games`.`id_team_away` = '.$value0->id.')';
                    $i++;
        }
        $arrayToUnset = array_slice($arrayToUnset,1);
      }

      $league = DB::select("SELECT `teams`.`id` as id, `teams`.`name` as Name, IFNULL(SUM(`points`.points), 0) as Points,
             IFNULL(SUM(case when `teams`.`id` = `games`.`id_team_home` then `games`.`goals_home` else `games`.`goals_away` end), 0) as GF
             FROM `teams`
             LEFT JOIN `points`
             ON `points`.`id_team` = `teams`.`id`
             LEFT JOIN `games`
             ON `games`.`id` = `points`.`id_game`
             LEFT JOIN `groups`
             ON `groups`.`id` = `games`.`id_group`
             LEFT JOIN `game_types`
             ON `game_types`.`id` = `games`.`id_type`
             WHERE `groups`.`name_group` = '$group' AND `game_types`.`name` = 'groups'
             AND (".$addQuery.")
             GROUP BY `teams`.`id`, `groups`.`id`
             ORDER BY GF  DESC");

      for ($i = 0; $i < count($league); $i++){
        foreach ($arrayTeams as $teams_array){
          if($league[$i]->id == $teams_array->id){
            $league[$i]->pos = $teams_array->pos;
          }
        }
      }
      return $league;
    }

    public function headToHeadPoints($league, $tiedTeams, $group){
      $unTiedTeam = array();
      for ($i = 0; $i < count($tiedTeams); $i++){
        $unTied = $this->getLeagueFromIds($tiedTeams[$i], $group);
        $teamsToKeep = $this->removeUnique($unTied);
        if(count($unTied) == count($teamsToKeep)){
          continue;
        }
        array_push($unTiedTeam, $unTied);
        $tiedTeams[$i] = $teamsToKeep;
        }


      $arrayLeagueToSort = array();
      foreach ($unTiedTeam as $teams){
        $arrayTeamsToSort = array();
        foreach ($teams as $team){
          array_push($arrayTeamsToSort, $league[intval($team->pos)-1]);
          }
        array_push($arrayLeagueToSort, $arrayTeamsToSort);
        }


        foreach($arrayLeagueToSort as $teams){
          $minorPos = 1000;
            foreach ($teams as $team){
              if($team->pos < $minorPos){
                $minorPos = $team->pos;
              }
            }

            for ($i = 0; $i < count($teams); $i++) {
              $league[$minorPos-1 + $i] = $teams[$i];
              $league[$minorPos-1 + $i]->pos = $minorPos + $i;
            }
        }
        return array($league, $tiedTeams);
    }

    public function headToHeadGoals($league, $tiedTeams, $group){
      $unTiedTeam = array();
      for ($i = 0; $i < count($tiedTeams); $i++){
        $unTied = $this->getLeagueFromIds_Goals($tiedTeams[$i], $group);
        $teamsToKeep = $this->removeUniqueGoals($unTied);
        if(count($unTied) == count($teamsToKeep)){
          continue;
        }
        array_push($unTiedTeam, $unTied);
        $tiedTeams[$i] = $teamsToKeep;
        }


      $arrayLeagueToSort = array();
      foreach ($unTiedTeam as $teams){

        $arrayTeamsToSort = array();
        foreach ($teams as $team){
          array_push($arrayTeamsToSort, $league[intval($team->pos)-1]);
          }
        array_push($arrayLeagueToSort, $arrayTeamsToSort);
        }


        foreach($arrayLeagueToSort as $teams){
          $minorPos = 1000;
            foreach ($teams as $team){
              if($team->pos < $minorPos){
                $minorPos = $team->pos;
              }
            }

            for ($i = 0; $i < count($teams); $i++) {
              $league[$minorPos-1 + $i] = $teams[$i];
              $league[$minorPos-1 + $i]->pos = $minorPos + $i;
            }
        }
        return array($league, $tiedTeams);
    }

    public function headToHeadGoalsScored($league, $tiedTeams, $group){
      $unTiedTeam = array();
      for ($i = 0; $i < count($tiedTeams); $i++){
        $unTied = $this->getLeagueFromIds_GoalsScored($tiedTeams[$i], $group);
        $teamsToKeep = $this->removeUniqueGoalsScored($unTied);
        if(count($unTied) == count($teamsToKeep)){
          continue;
        }
        array_push($unTiedTeam, $unTied);
        $tiedTeams[$i] = $teamsToKeep;
        }


      $arrayLeagueToSort = array();
      foreach ($unTiedTeam as $teams){
        $arrayTeamsToSort = array();
        foreach ($teams as $team){
          array_push($arrayTeamsToSort, $league[intval($team->pos)-1]);
          }
        array_push($arrayLeagueToSort, $arrayTeamsToSort);
        }


        foreach($arrayLeagueToSort as $teams){
          $minorPos = 1000;
            foreach ($teams as $team){
              if($team->pos < $minorPos){
                $minorPos = $team->pos;
              }
            }

            for ($i = 0; $i < count($teams); $i++) {
              $league[$minorPos-1 + $i] = $teams[$i];
              $league[$minorPos-1 + $i]->pos = $minorPos + $i;
            }
        }
        return array($league, $tiedTeams);
    }

    function getIdsTiedTeams($league){
      $teamsArray = array();
      $tiedTeams = array();
      $pivot = $league[0];
      $pivotIn = false;
      $pivotDif = false;

      for ($i = 1; $i < count($league); $i++) {
        if($pivot->Points == 0){
          return $teamsArray;
        }
        if(($pivot->Points == $league[$i]->Points)){
          if(!$pivotIn){
            array_push($tiedTeams, array("id" => $pivot->id, "pos" => $i, "Points" => $pivot->Points));
            $pivotIn = true;
          }
          array_push($tiedTeams, array("id" => $league[$i]->id,"pos" => $i+1, "Points" => $league[$i]->Points));
        }else if($pivotIn){
            array_push($teamsArray, $tiedTeams);
            $tiedTeams = array();
            $pivotIn = false;
            $pivot = $league[$i];
          }else{
            $pivot = $league[$i];
          }
      }
      if(count($tiedTeams)>1){
        array_push($teamsArray, $tiedTeams);
      }
      return $teamsArray;
    }

    public function tiebreakLeague($league, $tiedTeams, $group){
      $leagueAndTiedTeams = $this->headToHeadPoints($league, $tiedTeams, $group);


      //IF HA MAIS PARA DESEMPATAR RUN - headToHeadGoals
      if(!array_filter($leagueAndTiedTeams[1])){
        $leagueAndTiedTeams = $this->headToHeadGoals($leagueAndTiedTeams[0], $leagueAndTiedTeams[1], $group);
      }

      //IF HA MAIS PARA DESEMPATAR RUN - headToHeadGoalsScored
      if(!array_filter($leagueAndTiedTeams[1])){
        $leagueAndTiedTeams = $this->headToHeadGoalsScored($leagueAndTiedTeams[0], $leagueAndTiedTeams[1], $group);
      }

      return $leagueAndTiedTeams[0];

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {


      $leagues = array();
      $groups = DB::select("SELECT `groups`.`name_group` as Group_name FROM `groups`");

      foreach ($groups as $group) {
        $league = $this->getPointsByGroup($group->Group_name);
        $tiedTeams = $this->getIdsTiedTeams($league);

        //IF há equipas empatadas
        if(array_filter($tiedTeams)){
          $unTiedLeague = $this->tiebreakLeague($league, $tiedTeams, $group->Group_name);
          array_push($leagues, $unTiedLeague);
        }else{
          array_push($leagues, $league);
        }

      }

      
      //MANDAR LEAGUES PARA HTML + isto abaixo
      $round_16 = $this->getGamesByRound('round_16');
      $quarter_final = $this->getGamesByRound('quarter_final');
      $semi_final = $this->getGamesByRound('semi_final');
      $final = $this->getGamesByRound('final');

      return view('home', compact('leagues','round_16','quarter_final','semi_final','final'));


    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
