<?php

namespace App\Events;

// use Illuminate\Broadcasting\Channel;
// use Illuminate\Broadcasting\InteractsWithSockets;
// use Illuminate\Broadcasting\PresenceChannel;
// use Illuminate\Broadcasting\PrivateChannel;
// use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
// use Illuminate\Foundation\Events\Dispatchable;
// use Illuminate\Queue\SerializesModels;

use Illuminate\Broadcasting\PrivateChannel;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;


class BorratCefire implements ShouldBroadcast
{

    public $cefire = array();


    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($cefire)
    {
        $this->cefire = $cefire;
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
    public function broadcastAs()
    {
        return 'CefireBorratGeneral';
    }
}
















// namespace App\Events;

// use Illuminate\Broadcasting\Channel;
// use Illuminate\Broadcasting\InteractsWithSockets;
// use Illuminate\Broadcasting\PresenceChannel;
// use Illuminate\Broadcasting\PrivateChannel;
// use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
// use Illuminate\Foundation\Events\Dispatchable;
// use Illuminate\Queue\SerializesModels;

// class AfegitCefire
// {
//     use Dispatchable, InteractsWithSockets, SerializesModels;

//     public $message;

//     /**
//      * Create a new event instance.
//      *
//      * @return void
//      */
//     public function __construct($message)
//     {
//         //
//         $this->message = $message;
//     }

//     /**
//      * Get the channels the event should broadcast on.
//      *
//      * @return \Illuminate\Broadcasting\Channel|array
//      */
//     public function broadcastOn()
//     {
//         return ['my-channel'];
//         // return new PrivateChannel('my-channel');
//     }
//     // public function broadcastAs()
//     // {
//     //     return 'server.my-event';
//     // }
// }
