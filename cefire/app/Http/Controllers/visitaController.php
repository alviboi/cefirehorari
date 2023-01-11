<?php

namespace App\Http\Controllers;

use App\Models\visita;
use Illuminate\Http\Request;

class visitaController extends Controller
{
    /**
     * Mostra un llistat de tot el recurs
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return visita::get();
    }
    /**
     * Extrau totes les dades de visita amb el nom
     *
     * @return \Illuminate\Http\Response
     */
    public function get_data_index($any, $mes)
    {
        $ret = array();
        $els = visita::whereMonth('data', '=', date($mes))->whereYear('data', '=', date($any))->get();
        foreach ($els as $el) {
            $item=array("id"=>$el->id, "name"=>$el->user['name'], "data"=>$el->data, "inici"=>$el->inici, "fi"=>$el->fi, "centre"=>$el->centre);
            array_push($ret, $item);
        }
        return $ret;
    }


    /**
     * Guarda l'elememt creat.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $dat = new visita();
        $dat->data = $request->data;
        $dat->inici = $request->inici;
        $dat->fi = $request->fi;
        $dat->user_id = auth()->id();
        $dat->centre = $request->motiu;
        $dat->save();
        return $dat->toArray();
    }


    /**
     * Elimina l'element  del recurs de la base de dades
     *
     * @param  \App\Models\visita  $visita
     * @return \Illuminate\Http\Response
     */
    public function destroy($visita)
    {
        //
        visita::find($visita)->delete();
    }

    public function visitanovalidades()
    {
        //
        $ret = array();
        $els = visita::where('aprobada','=',false)->get();
        $dias=array("Diumenge","Dilluns","Dimarts","Dimecres","Dijous","Divendres","Dissabte");

        foreach ($els as $el) {
            $da=date("d-m-Y", strtotime($el->data));
            $da2=$dias[date("w", strtotime($el->data))];
            
            $item=array("id"=>$el->id, "name"=>$el->user['name'], "data"=>$da2.", ".$da,"motiu"=>$el->centre, "inici"=>$el->inici,"fi"=>$el->fi);
            //$item=array("id"=>$el->id, "name"=>"", "data"=>$da2.", ".$da);
            array_push($ret, $item);
        }
        return $ret;
    }

    public function validavisita(Request $request)
    {
        //
        $el = visita::where('id',$request->id)->update(['aprobada'=>true]);

        $compensa = visita::find($request->id);

        $link2 = "https://calendar.google.com/calendar/u/0/r/eventedit?text=COM.+SERVEIS+CEFIRE&dates=" . str_replace("-", "", $compensa->data) . "&details=compensa+del+Cefire+de+Valencia+ELIMINADA&location=Valencia&trp=false#eventpage_6";

        $datos2 = [
            'nombre' => $compensa->user['name'],
            'fecha' => date("d/m/Y", strtotime($compensa->data)),
            'link' => $link2,
            'estat' => 'Aprovada'
        ];


        //$emailJob3 = (new SendAvisvisita($compensa->user['email'], $datos2))->delay(Carbon::now()->addSeconds(120));
        //dispatch($emailJob3);

        //Mail::to($compensa->user['email'])->send(new Eliminarcompensa($datos2));

    }
}
