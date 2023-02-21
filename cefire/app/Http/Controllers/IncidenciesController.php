<?php

namespace App\Http\Controllers;

use App\Jobs\SendIncidenciaresolta;
use Validator;
use Illuminate\Http\Request;
use App\Models\Incidencies;
use Carbon\Carbon;

class IncidenciesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return Incidencies::get();
    }
    /**
     * Extrau totes les dades de visita amb el nom
     *
     * @return \Illuminate\Http\Response
     */
    public function get_data_index($any, $mes)
    {
        $ret = array();
        $els = Incidencies::whereMonth('data', '=', date($mes))->whereYear('data', '=', date($any))->get();
        foreach ($els as $el) {
            $item = array("id" => $el->id, "name" => $el->user['name'], "data" => $el->data, "inici" => $el->inici, "fi" => $el->fi, "incidencia" => $el->incidencia, "corregida" => $el->corregida);
            array_push($ret, $item);
        }
        return $ret;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $dat = new Incidencies();
        $dat->data = $request->data;
        $dat->inici = $request->inici;
        $dat->fi = $request->fi;
        $dat->user_id = auth()->id();
        $dat->incidencia = $request->incidencia;
        $dat->save();
        return $dat->toArray();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        return Incidencies::get()->where('ass_id', '=', $id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $inci = Incidencies::where("id", "=", $id)->first();
        $inci->corregida = $request->estat;
        $inci->save();


        $datos2 = [
            'nombre' => $inci->user['name'],
            'data' => date("d/m/Y", strtotime($inci->data)),
            'incidencia' => $inci->incidencia,
            'estat' => $inci->corregida ? "Resolta" : "No resolta",

        ];

        $emailJob2 = (new SendIncidenciaresolta($inci->user['email'], $datos2))->delay(Carbon::now()->addSeconds(120));
        dispatch($emailJob2);



        return "Canviat estat d'incidència";
        //return $inci->corregida;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($incidencia)
    {
        //
        Incidencies::find($incidencia)->delete();
    }
}