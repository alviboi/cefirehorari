<?php

namespace App\Http\Controllers;

use App\Models\compensa;
use App\Models\control;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

use App\Jobs\SendAviscompensacio;
use App\Mail\Aviscompensacio;
use Carbon\Carbon;

class compensaController extends Controller
{
    /**
     * Mostra un llistat de tot el recurs
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return compensa::get();
    }

    /**
     * Extrau totes les dades de fitxar del compensa amb el nom
     *
     * @return \Illuminate\Http\Response
     */
    public function get_data_index($any, $mes)
    {
        $ret = array();
        $els = compensa::whereMonth('data', '=', date($mes))->whereYear('data', '=', date($any))->get();
        foreach ($els as $el) {
            $item=array("id"=>$el->id, "name"=>$el->user['name'], "data"=>$el->data, "inici"=>$el->inici, "fi"=>$el->fi, "motiu"=>$el->motiu);
            array_push($ret, $item);
        }
        return $ret;
    }



    /**
     * Crea un element del recurs
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Guarda l'elememt creat.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
               
        $aux2 = compensa::where('user_id','=',auth()->id())->where('data','=',$request->data)->where('inici','=',$request->inici)->get();
        if (!$aux2->isEmpty()) {
            abort(403,"Ja tens una compensació este dia");
        } 
        $max_compensacions = control::first();

        $aux = compensa::where('data','=',$request->data)->where('inici','=',$request->inici)->count();
        if ($aux>=$max_compensacions->max_compensacions) {
            abort(403,"El màxim de persones que poden compensar per dia són ".$max_compensacions->max_compensacions." i ja s'ha superat");
        } 
        
        

        $dat = new compensa();
        $dat->data = $request->data;
        $dat->inici = $request->inici;
        $dat->fi = $request->fi;
        $dat->user_id = auth()->id();
        $dat->motiu = $request->motiu;
        $dat->save();
        return $dat->toArray();
    }



    /**
     * Actualitza l'element del recurs a la base de dades
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\compensa  $compensa
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, compensa $compensa)
    {
        //
    }

    /**
     * Elimina l'element  del recurs de la base de dades
     *
     * @param  \App\Models\compensa  $compensa
     * @return \Illuminate\Http\Response
     */
    public function destroy(compensa $compensa)
    {
        //
        
        if($compensa->inici == "09:00:00"){
            $m='matí';
        }else{
            $m='vesprada';
        }

        $link2 = "https://calendar.google.com/calendar/u/0/r/eventedit?text=COMPENSACIÓ+CEFIRE+ELIMINADA&dates=" . str_replace("-", "", $compensa->data) . "T" . str_replace(":", "", $compensa->inici) . "/" . str_replace("-", "", $compensa->data) . "T" . str_replace(":", "", $compensa->fi) . "&details=compensa+del+Cefire+de+Valencia+ELIMINADA&location=Valencia&trp=false#eventpage_6";

        $datos2 = [
            'nombre' => $compensa->user['name'],
            'fecha' => date("d/m/Y", strtotime($compensa->data)),
            'rato' => $m,
            'link' => $link2,
            'estat' => 'Eliminada'
        ];


        $emailJob2 = (new SendAviscompensacio($compensa->user['email'], $datos2))->delay(Carbon::now()->addSeconds(120));
        dispatch($emailJob2);

        $compensa->delete();
    }

    public function compensacionsnovalidades()
    {
        //
        $ret = array();
        $els = compensa::where('confirmat','=',false)->get();
        $dias=array("Diumenge","Dilluns","Dimarts","Dimecres","Dijous","Divendres","Dissabte");

        foreach ($els as $el) {
            $da=date("d-m-Y", strtotime($el->data));
            $da2=$dias[date("w", strtotime($el->data))];
            
            $item=array("id"=>$el->id, "name"=>$el->user['name'], "data"=>$da2.", ".$da, "inici"=>$el->inici, "fi"=>$el->fi, "motiu"=>$el->motiu);
            array_push($ret, $item);
        }
        return $ret;
    }
    public function validacompensacio(Request $request)
    {
        //
        $el = compensa::where('id',$request->id)->update(['confirmat'=>true]);

        $compensa = compensa::find($request->id);
        if($compensa->inici == "09:00:00"){
            $m='matí';
        }else{
            $m='vesprada';
        }

        $link2 = "https://calendar.google.com/calendar/u/0/r/eventedit?text=COMPENSACIÓ+CEFIRE&dates=" . str_replace("-", "", $compensa->data) . "T" . str_replace(":", "", $compensa->inici) . "/" . str_replace("-", "", $compensa->data) . "T" . str_replace(":", "", $compensa->fi) . "&details=compensa+del+Cefire+de+Valencia+ELIMINADA&location=Valencia&trp=false#eventpage_6";

        $datos2 = [
            'nombre' => $compensa->user['name'],
            'fecha' => date("d/m/Y", strtotime($compensa->data)),
            'rato' => $m,
            'link' => $link2,
            'estat' => 'Aprovada'
        ];


        $emailJob3 = (new SendAviscompensacio($compensa->user['email'], $datos2))->delay(Carbon::now()->addSeconds(120));
        dispatch($emailJob3);

        //Mail::to($compensa->user['email'])->send(new Eliminarcompensa($datos2));

    }
    
}
