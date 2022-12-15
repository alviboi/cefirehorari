<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBorsaSolicitudsRequest;
use App\Http\Requests\UpdateBorsaSolicitudsRequest;
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
        $check = BorsaSolicituds::where("user_id", "=", auth()->id())->where("mes", "=", date("m")-1)->where("any", "=", date("Y"))->first();
        if ($check) {
            abort(403, "Ja existeix una sol·licitud per a aquest mes");
        }

        $borssol = new BorsaSolicituds();
        $borssol->user_id = auth()->id();
        $borssol->minuts = $request->minuts_a_afegir;
        $borssol->mes = date("m")-1;
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
    public function destroy(BorsaSolicituds $borsaSolicituds)
    {
        //
    }
}