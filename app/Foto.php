<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Foto extends Model
{
    protected $table = 'fotos';

    public function user(){
        return $this->belongsTo('App/User', 'user_id');
    }
}
