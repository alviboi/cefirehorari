<?php

namespace App\Http\Controllers;

use App\Events\MailEntrada;
use App\Models\notificacions;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Jobs\SendMissatgeMail;
use Carbon\Carbon;

use App\Mail\EnviarMissatge;


class notificacionsController extends Controller
{
    /**
     * Mostra un llistat de tot el recurs
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        // return notificacions::where('user_id','=',auth()->id())->get();
        $ret = array();
        $els = notificacions::where('user_id','=',auth()->id())->get();
        foreach ($els as $el) {
            $user=user::where('id','=',$el->From)->first();
            $item=array("id"=>$el->id, "name"=>$user['name'], "From"=>$el->From, "user_id"=>$el->user_id, "cap"=>$el->cap, "missatge"=>$el->missatge);
            array_push($ret, $item);
        }
        return $ret;

    }


    /**
     * Guarda l'element creat i envia una notificació al websocket i un mail
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //id 	From 	user_id 	missatge
        $notificacio = new notificacions();
        $notificacio->From=auth()->id();
        $notificacio->cap=$request->cap;
        $notificacio->user_id=$request->user_id;
        $notificacio->missatge=$request->avis;
        $err=$notificacio->save();

        $datos = [
            'De' => Auth::user()->name,
            'Cap' => $notificacio->cap,
            'missatge' => $notificacio->missatge
        ];

        $user=user::find($notificacio->From);

        $item=array("id"=>$notificacio->id, "name"=>$user->name, "From"=>$notificacio->From, "user_id"=>$notificacio->user_id, "cap"=>$notificacio->cap, "missatge"=>$notificacio->missatge);
        broadcast(new MailEntrada($notificacio->user_id, $item))->toOthers();

        $emailJob = (new SendMissatgeMail($notificacio->user['email'],$datos))->delay(Carbon::now()->addSeconds(120));
   		dispatch($emailJob);



        if ($err && $emailJob){
            return "Missatge enviat  correctament";
        } else {
            return "Alguna cosa ha anar malament";
        }

    }


    /**
     * Elimina l'element  del recurs de la base de dades
     *
     * @param  \App\Models\notificacions  $notificacions
     * @return \Illuminate\Http\Response
     */
    public function destroy($notificacions)
    {
        //
        notificacions::find($notificacions)->delete();

    }
}
