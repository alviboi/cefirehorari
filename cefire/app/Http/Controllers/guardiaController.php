<?php

namespace App\Http\Controllers;
use App\Events\GuardiaBorrada;

use App\Events\GuardiaAfegida;
use App\Events\GuardiaBorradaGeneral;
use App\Mail\EliminarGuardia;
use App\Events\GuardiaAfegidaGeneral;
use App\Mail\EnviarGuardia;

use App\Models\guardia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Jobs\SendGuardiaMail;

use App\Jobs\SendGuardiaBorrarMail;
use Carbon\Carbon;


class guardiaController extends Controller
{
    /**
     * Mostra un llistat de tot el recurs
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return guardia::get();
    }
    /**
     * Extrau totes les dades de guardia amb el nom
     *
     * @return \Illuminate\Http\Response
     */
    public function get_data_index($any, $mes)
    {
        $ret = array();
        $els = guardia::whereMonth('data', '=', date($mes))->whereYear('data', '=', date($any))->get();
        foreach ($els as $el) {
            $item=array("id"=>$el->id, "name"=>$el->user['name'], "data"=>$el->data, "inici"=>$el->inici, "fi"=>$el->fi);
            array_push($ret, $item);
        }
        return $ret;
    }
    /**
     * Mostra un llistat de totes les guardies d'un mes i un any concret
     *
     * @return array $ret
     */
    public function get_data_index2($mes, $any)
    {

        $ret = array();
        $guardies = guardia::whereMonth('data', '=', date($mes))->whereYear('data', '=', date($any))->get();
        foreach ($guardies as $guardia) {
            array_push($ret, [$guardia->user['name'],$guardia->data,$guardia->inici,$guardia->id]);
        }
        return $ret;
    }

    /**
     *
     * Guardia la guardia d'un moment determinat, després envia una notificació per correu electrònic i pel canal de GuardiaAfegida per a que s'actualitze d'inmediat
     *
     * @return \Illuminate\Http\Response
     */
    public function put_guardia_id(Request $request)
    {
        $guardia = new guardia();
        $guardia->user_id=$request->id;
        $guardia->data=$request->data;
        if ($request->mati=="m"){
            $guardia->inici="09:00:00";
            $guardia->fi="14:00:00";
            $txt_rato="MATÍ";
        } else {
            $guardia->inici="16:00:00";
            $guardia->fi="20:00:00";
            $txt_rato="VESPRADA";
        }

        $guardia->save();

        $env=$guardia->toArray();
        $env['nom']=$guardia->user['name'];

        broadcast(new GuardiaAfegida(auth()->id(), $guardia->toArray(),$request->mati))->toOthers();
        broadcast(new GuardiaAfegidaGeneral($env))->toOthers();

        //https://calendar.google.com/calendar/u/0/r/eventedit?text=Example+Event&dates=20131124T010000Z/20131124T020000Z&details=Event+Details+Here&location=123+Main+St,+Example,+NY&sf=true

        $link= "https://calendar.google.com/calendar/u/0/r/eventedit?text=GUARDIA+CEFIRE&dates=". str_replace("-","",$guardia->data)."T". str_replace(":", "", $guardia->inici)."/". str_replace("-", "", $guardia->data)."T". str_replace(":", "", $guardia->fi)."&details=Guardia+del+Cefire+de+Valencia&location=Valencia&trp=false#eventpage_6";

        $datos = [
            'nombre' => $guardia->user['name'],
            'fecha' => date("d/m/Y", strtotime($guardia->data)),
            'rato' => $txt_rato,
            'link' => $link
        ];

        $emailJob = (new SendGuardiaMail($guardia->user['email'],$datos))->delay(Carbon::now()->addSeconds(120));
   		dispatch($emailJob);
        return $guardia;



    }

    /**
     * Guarda l'element creat.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $dat = new guardia();
        $dat->data = $request->data;
        $dat->inici = $request->inici;
        $dat->fi = $request->fi;
        $dat->user_id = auth()->id();
        $dat->save();
        $env = $dat->toArray();
        $env['nom'] = auth()->user()->name;
        broadcast(new GuardiaAfegidaGeneral($env))->toOthers();
        return $dat->toArray();

    }



    /**
     * Elimina l'element  del recurs de la base de dades i després envia a través del websocket una notificació
     *
     * @param  \App\Models\cefire  $cefire
     * @return \Illuminate\Http\Response
     */
    public function destroy($guardia)
    {
        //
        $guardia = guardia::find($guardia);
        if($guardia->inici == "09:00:00"){
            $m='matí';
        }else{
            $m='vesprada';
        }
        broadcast(new GuardiaBorrada(auth()->id(), $guardia->toArray(),$m))->toOthers();
        broadcast(new GuardiaBorradaGeneral($guardia->toArray()))->toOthers();

        $link2 = "https://calendar.google.com/calendar/u/0/r/eventedit?text=GUARDIA+CEFIRE+ELIMINADA&dates=" . str_replace("-", "", $guardia->data) . "T" . str_replace(":", "", $guardia->inici) . "/" . str_replace("-", "", $guardia->data) . "T" . str_replace(":", "", $guardia->fi) . "&details=Guardia+del+Cefire+de+Valencia+ELIMINADA&location=Valencia&trp=false#eventpage_6";

        $datos2 = [
            'nombre' => $guardia->user['name'],
            'fecha' => date("d/m/Y", strtotime($guardia->data)),
            'rato' => $m,
            'link' => $link2
        ];


        $emailJob2 = (new SendGuardiaBorrarMail($guardia->user['email'], $datos2))->delay(Carbon::now()->addSeconds(120));
        dispatch($emailJob2);

        //Mail::to($guardia->user['email'])->send(new EliminarGuardia($datos2));



        $guardia->delete();

    }

    /**
     * Mostra un llistat de totes les guardies d'un mes i un any concret
     *
     * @return array $ret
     */
    public function get_numero_de_guardies()
    {
        $year = date("Y");
        $mes = date("m");
        if ($mes > 8) {
            $any = $year;
        } else {
            $any = $year-1;
        }
        $str = $any."-9-1";
        $inici = date_create($str);
        $final = date_create(($any+1)."-08-31");
        $ret = array();
        $guardies = guardia::selectRaw('user_id, count(*) total')->where('data', '>', $inici)->where('data', '<', $final)->groupby("user_id")->orderBy('total','desc')->get();
        foreach ($guardies as $guardia) {
            array_push($ret, [$guardia->user_id,$guardia->user['name'],$guardia->total]);
        }
        return $ret;
    }
}
