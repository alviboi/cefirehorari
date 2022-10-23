<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Get the comments cefire data.
     */
    public function cefire()
    {
        return $this->hasMany('App\Models\cefire');
    }
    /**
     * Get the comments centres data.
     */
    public function centres()
    {
        return $this->hasMany('App\Models\centres');
    }
    /**
     * Get the comments compensa data.
     */
    public function compensa()
    {
        return $this->hasMany('App\Models\compensa');
    }
    /**
     * Get the comments curs data.
     */
    public function curs()
    {
        return $this->hasMany('App\Models\curs');
    }
    /**
     * Get the comments lectura_rfid data.
     */
    public function lectura_rfid()
    {
        return $this->hasMany('App\Models\lectura_rfid');
    }
    /**
     * Get the comments notificacions data.
     */
    public function notificacions()
    {
        return $this->hasMany('App\Models\notificacions');
    }
      /**
     * Get the comments permis data.
     */
    public function permis()
    {
        return $this->hasMany('App\Models\permis');
    }
    /**
     * Get the comments visita data.
     */
    public function visita()
    {
        return $this->hasMany('App\Models\visita');
    }
    /**
     * Get the comments guardia data.
     */
    public function guardia()
    {
        return $this->hasMany('App\Models\guardia');
    }
    /**
     * Get the comments guardia data.
     */
    public function incidencies()
    {
        return $this->hasMany('App\Models\Incidencies');
    }
    /**
     * Get the comments guardia data.
     */
    public function moscoso()
    {
        return $this->hasMany('App\Models\moscoso');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    // public function setiniciAttribute($value)
    // {
    //     $this->attributes['inici'] = $value;
    // }
    // public function setfiAttribute($value)
    // {
    //     $this->attributes['fi'] = $value;
    // }


}
