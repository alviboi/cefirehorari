<?php

namespace App\Http\Controllers;

use App\Http\Requests\Storevacances_oficialsRequest;
use App\Http\Requests\Updatevacances_oficialsRequest;
use App\Models\vacances_oficials;

class VacancesOficialsController extends Controller
{
    // public function agafavacancescurs ($from, $to) {
    //     return vacances_oficials::whereBetween('data', [$from, $to])->get();
    // }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        //
        //$data_hui = date('Y-m-d');
        $any = date('Y');
        $mes = date('m');
        //abort(403,$mes);
        // $data_1_set = date();
        // $data_31_agost = date();

        if ($mes >= 9) {
            $data_1_set = date($any."-09-01");
            $data_31_agost = date(($any+1)."-08-31");    
        } else {
            $data_1_set = date(($any-1)."-09-01");
            $data_31_agost = date(($any)."-08-31");
        }
        return vacances_oficials::whereBetween('data', [$data_1_set, $data_31_agost])->get();
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
     * @param  \App\Http\Requests\Storevacances_oficialsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Storevacances_oficialsRequest $request)
    {
        //
        vacances_oficials::truncate();
        foreach ($request->toArray() as $key => $value) {
            # code...
            $vac = vacances_oficials::firstOrCreate(["data" => $value['id']]);
            if (!$vac){
                abort(403,"Ha hagut un error");
            }
        }
        return "Sembla que tot ha anat correctament";
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\vacances_oficials  $vacances_oficials
     * @return \Illuminate\Http\Response
     */
    public function show(vacances_oficials $vacances_oficials)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\vacances_oficials  $vacances_oficials
     * @return \Illuminate\Http\Response
     */
    public function edit(vacances_oficials $vacances_oficials)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\Updatevacances_oficialsRequest  $request
     * @param  \App\Models\vacances_oficials  $vacances_oficials
     * @return \Illuminate\Http\Response
     */
    public function update(Updatevacances_oficialsRequest $request, vacances_oficials $vacances_oficials)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\vacances_oficials  $vacances_oficials
     * @return \Illuminate\Http\Response
     */
    public function destroy(vacances_oficials $vacances_oficials)
    {
        //
    }
}
