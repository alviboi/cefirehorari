<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class curs extends Model
{
    public $table = 'curs';

    use HasFactory;
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
