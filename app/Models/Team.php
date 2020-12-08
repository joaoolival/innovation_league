<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    public function relPoint(){
      return $this->hasMany('App\Models\Point');
    }

    public function scopeActive($query, $group)
{   
    return $query->where('id_type', $group);
}

}
