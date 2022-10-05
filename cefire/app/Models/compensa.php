<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class compensa extends Model
{
    public $table = 'compensa';
    use HasFactory;
    /**
     * user
     *
     * Per a crear la relació de molts a un hem afegit aquesta funció
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
