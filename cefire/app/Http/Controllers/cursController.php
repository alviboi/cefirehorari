<?php

namespace App\Http\Controllers;

use App\Models\curs;
use Illuminate\Http\Request;

class cursController extends Controller
{
    /**
     * Mostra un llistat de tot el recurs
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return curs::get();
    }

    /**
     * Extrau totes les dades de fitxar del cefire amb el nom
     *
     * @return \Illuminate\Http\Response
     */
    public function get_data_index($any, $mes)
    {
        $ret = array();
        $els = curs::whereMonth('data', '=', date($mes))->whereYear('data', '=', date($any))->get();
        foreach ($els as $el) {
            $item=array("id"=>$el->id, "name"=>$el->user['name'], "data"=>$el->data, "inici"=>$el->inici, "fi"=>$el->fi, "curs"=>$el->curs);
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
        //
        $dat = new curs();
        $dat->data = $request->data;
        $dat->inici = $request->inici;
        $dat->fi = $request->fi;
        $dat->user_id = auth()->id();
        $dat->curs = $request->motiu;
        $dat->save();
        return $dat->toArray();
    }


    /**
     * Actualitza l'element del recurs a la base de dades
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\curs  $curs
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, curs $curs)
    {
        //
    }

    /**
     * Elimina l'element  del recurs de la base de dades
     *
     * @param  \App\Models\curs  $curs
     * @return \Illuminate\Http\Response
     */
    public function destroy($curs)
    {
        //
        curs::find($curs)->delete();
    }
}
