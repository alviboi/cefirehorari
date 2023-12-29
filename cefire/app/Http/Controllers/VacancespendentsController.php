<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVacancespendentsRequest;
use App\Http\Requests\UpdateVacancespendentsRequest;
use App\Models\Vacancespendents;
use App\Models\User;
use App\Models\Vacances;


class VacancespendentsController extends Controller
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
     * @param  \App\Http\Requests\StoreVacancespendentsRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreVacancespendentsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Vacancespendents  $vacancespendents
     * @return \Illuminate\Http\Response
     */
    public function show(Vacancespendents $vacancespendents)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Vacancespendents  $vacancespendents
     * @return \Illuminate\Http\Response
     */
    public function edit(Vacancespendents $vacancespendents)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateVacancespendentsRequest  $request
     * @param  \App\Models\Vacancespendents  $vacancespendents
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateVacancespendentsRequest $request, Vacancespendents $vacancespendents)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Vacancespendents  $vacancespendents
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vacancespendents $vacancespendents)
    {
        //
    }

    public function gener() {
        //Vacancespendents::truncate();
        $any = date("Y")-1;
        $inici = date($any . "-1-1");
        $fi = date($any . "-12-31");
        $user = User::get();
        foreach ($user as $key => $value) {
            # code...

            $control_vacances = Vacancespendents::where("user_id","=",$value->id)->first();
            if ($control_vacances === null){
                $control_vacances = new Vacancespendents();
                $control_vacances->dies_sobrants_vacances = 0;
                $control_vacances->dies_sobrants_moscosos = 0;
            }

            $control_vacances->user_id = $value->id;

            //Canviem el $fi fins a març i demanades en l'any anterior
            //$vacances_consumides = $value->vacances()->whereBetween('data', [$inici, $fi])->count();
            $fins_any2 = date_create(($any + 1) . "-03-01");
            $vacances_consumides = $value->vacances()->where('created_at','>',$inici)->where('created_at','<',$fi)->whereBetween('data', [$inici, $fins_any2])->count();
            //$moscosos_consumits = $value->moscoso()->whereBetween('data', [$inici, $fi])->count();
            $moscosos_consumits = $value->moscoso()->where('created_at','>',$inici)->where('created_at','<',$fi)->whereBetween('data', [$inici, $fins_any2])->count();

            $vacances_restants = ($value->vacances+$control_vacances->dies_sobrants_vacances) - $vacances_consumides;
            $moscosos_restants = ($value->moscosos+$control_vacances->dies_sobrants_moscosos) - $moscosos_consumits;

            
            
            $control_vacances->dies_sobrants_vacances = $vacances_restants;
            $control_vacances->dies_sobrants_moscosos = $moscosos_restants;

            $control_vacances->save();
        }

    }

    public function marc() {

        $any = date("Y");
        $inici = date($any . "-1-1");
        $fi = date($any . "-12-31");
        $user = User::get();
        foreach ($user as $key => $value) {
            # code...
            $control_vacances = Vacancespendents::where("user_id","=",$value->id)->first();

            if ($control_vacances !== null){

                $vacances_consumides = $value->vacances()->whereBetween('data', [$inici, $fi])->count();
                $moscosos_consumits = $value->moscoso()->whereBetween('data', [$inici, $fi])->count();
    
    
                if ($vacances_consumides<$control_vacances->dies_sobrants_vacances) {
                    $control_vacances->dies_sobrants_vacances = $vacances_consumides;
                } else if ($vacances_consumides>$control_vacances->dies_sobrants_vacances) {
                    $control_vacances->dies_sobrants_vacances = 0;
                }
    
                if ($moscosos_consumits<$control_vacances->dies_sobrants_moscosos) {
                    $control_vacances->dies_sobrants_moscosos = $moscosos_consumits;
                } else if ($moscosos_consumits>$control_vacances->dies_sobrants_moscosos){
                    $control_vacances->dies_sobrants_moscosos = 0;
                }
    
                $control_vacances->save();

            }
            


        }
        
    }
}
