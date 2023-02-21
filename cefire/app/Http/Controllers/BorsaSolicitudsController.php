<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreBorsaSolicitudsRequest;
use App\Http\Requests\UpdateBorsaSolicitudsRequest;
use App\Models\BorsaHores;
use App\Models\BorsaSolicituds;

class BorsaSolicitudsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        //return BorsaSolicituds::get();

        $borsol = BorsaSolicituds::orderby('id', 'DESC')->where("aprobada", "=", 0)->get();
        $ret = array();
        foreach ($borsol as $bors) {
            # code...
            $el = ["nom" => $bors->user['name'], "any" => $bors->any, "mes" => $bors->mes, "minuts" => $bors->minuts, "justificacio" => $bors->justificacio, "id" => $bors->id, "user_id" => $bors->user_id];
            array_push($ret, $el);
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
     * @param  \App\Http\Requests\StoreBorsaSolicitudsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBorsaSolicitudsRequest $request)
    {
        //
        //Validem en el component, no seria necessari esta part
        $this->validate($request, [
            'minuts_a_afegir' => 'required',
            'justificacio' => 'required',
        ]);
        $check = BorsaSolicituds::where("user_id", "=", auth()->id())->where("mes", "=", date("m") - 1)->where("any", "=", date("Y"))->first();
        if ($check) {
            abort(403, "Ja existeix una sol·licitud per a aquest mes");
        }

        $borssol = new BorsaSolicituds();
        $borssol->user_id = auth()->id();
        $borssol->minuts = $request->minuts_a_afegir;
        $borssol->mes = date("m") - 1;
        $borssol->any = date("Y");
        $borssol->justificacio = $request->justificacio;

        //  Store data in database
        $borssol->save();
        //
        return 'Sol·licitud resgistrada correctament';
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BorsaSolicituds  $borsaSolicituds
     * @return \Illuminate\Http\Response
     */
    public function show(BorsaSolicituds $borsaSolicituds)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BorsaSolicituds  $borsaSolicituds
     * @return \Illuminate\Http\Response
     */
    public function edit(BorsaSolicituds $borsaSolicituds)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBorsaSolicitudsRequest  $request
     * @param  \App\Models\BorsaSolicituds  $borsaSolicituds
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBorsaSolicitudsRequest $request, BorsaSolicituds $borsaSolicituds)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BorsaSolicituds  $borsaSolicituds
     * @return \Illuminate\Http\Response
     */
    public function destroy($borsaSolicituds)
    {
        $ret = BorsaSolicituds::find($borsaSolicituds)->delete();
        //$a = $borsaSolicituds->id;
        return $ret;
        //return "sfsdaf";
    }


    public function borsasolicitudsvalida(Request $request)
    {

        $user_controller = new UserController();
        $este = $user_controller->calcula_deutes_mes_usuari($request->user_id);

        $deutes_mes_controller = new DeutesmesController();
        $deutes_mes_controller->afegix_deutes_mes($request->user_id, -$request->minuts);

        //abort(413, $este['diferència']);

        $bs = BorsaSolicituds::find($request->id);
        $BorsaHores = new BorsaHoresController();
        $ret = $BorsaHores->crea($request->user_id, $request->minuts);
        if ($ret > 0) {
            $bs->aprobada = 1;
            $bs->save();
            return "La borsa ara és de " . $ret . " minuts.";
        } else {
            abort(403, "Ha hagut un error.");
        }


    }
}