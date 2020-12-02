<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameType extends Model
{
    use HasFactory;

    public function relGame(){
      return $this->hasMany('App\Models\Game', 'id_type');
    }
}
