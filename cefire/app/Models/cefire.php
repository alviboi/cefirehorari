<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class cefire extends Model
{
    public $table = 'cefire';
    use HasFactory;
    // Per a que eloquent ens mostre les dades en format temps per a facilitar el càlcul
    protected $casts = [
        'inici' => 'datetime',
        'fi' => 'datetime',
    ];
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
    // public function setiniciAttribute($value)
    // {
    //     $this->attributes['inici'] = $value;
    // }
    // public function setfiAttribute($value)
    // {
    //     $this->attributes['fi'] = $value;
    // }
}
