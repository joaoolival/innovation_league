<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Scopes\TeamScope;


class Team extends Model
{
    use HasFactory;

    public function relPoint(){
      return $this->hasMany('App\Models\Point');
    }

}
