<?php
namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;


class GuardiaAfegida implements ShouldBroadcast
{

    public $guardia=array();
    public $user;
    public $m;


    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    /**
     * __construct
     *
     * Al crearse l'objecte s'ha de crear l'usuari i el dia concret on va dirigida
     *
     * @param  mixed $user
     * @param  mixed $guardia
     * @param  mixed $m
     * @return void
     */
    public function __construct($user,$guardia,$m)
    {
        //
        $this->user = $user;
        $this->guardia = $guardia;
        $this->m = $m;;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // return new PrivateChannel('cefire');
        return ['cefire'];
    }

    /**
     * broadcastAs
     *
     * El nom amb que volem que es transmeta el event dins del canal cefire
     *
     * @return void
     */
    public function broadcastAs() {
        return 'GuardiaAfegida'.$this->guardia['data'].$this->m;
    }
}
