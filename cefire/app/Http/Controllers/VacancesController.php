<?php

namespace App\Http\Controllers;

use App\Models\Vacances;
use App\Models\User;
use Illuminate\Http\Request;

class VacancesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $els = Vacances::where('user_id','=',auth()->id())->get();
        return $els;
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

    function isWeekend($date) {
        return (date('N', strtotime($date)) >= 6);
    }

    public function getWorkingDays($startDate, $endDate)
    {
        // do strtotime calculations just once
        $endDatea = strtotime($endDate);
        $startDatea = strtotime($startDate);

        //$days = ($endDate)->diff($startDate);
        //The total number of days between the two dates. We compute the no. of seconds and divide it to 60*60*24
        //We add one to inlude both dates in the interval.
        $days = ($endDatea - $startDatea) / 86400 + 1;

        $tots_els_dies = array();

        for ($i=0; $i < $days; $i++) { 
            # code...
            $dia = date('Y-m-d', strtotime($startDate. ' + '.$i.' days'));
            if (!$this->isWeekend($dia)){
                array_push($tots_els_dies,$dia);
            }       
        }
        

        return $tots_els_dies;
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
        $year = date("Y");        
        $dies_a_afegir = $this->getWorkingDays($request->dia_inici,$request->dia_fi);
        $vacances_total=User::where("id","=",auth()->id())->first();
        $str = $year . "-1-1";
        $inici = date_create($str);
        $final = date_create(($year + 1) . "-12-31");
        $total = Vacances::where('data', '>', $inici)->where('data', '<', $final)->where("user_id","=",auth()->id())->count();

        foreach ($dies_a_afegir as $key => $value) {
            # code...
            $exist_vacances = Vacances::where('data', '=',$value)->first();
            if ($exist_vacances) {
                abort(403, "Estàs demanant un dia que ja tens");
            }
        }

        if (($total+ count($dies_a_afegir)) > $vacances_total->vacances){
            abort(403, "Estàs passant-te dels dies que tens de vacances".($total+ count($dies_a_afegir))."  ".$vacances_total);
        } 
               
        foreach ($dies_a_afegir as $key => $value) {
            # code...
            $dat = new Vacances();
            $dat->data = $value;
            $dat->user_id = auth()->id();
            $dat->save();
        }
        return $dies_a_afegir;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Vacances  $vacances
     * @return \Illuminate\Http\Response
     */
    public function show(Vacances $vacances)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Vacances  $vacances
     * @return \Illuminate\Http\Response
     */
    public function edit(Vacances $vacances)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vacances  $vacances
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vacances $vacances)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Vacances  $vacances
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $vac = Vacances::find($id)->first();
        
        $data_hui = date('Y-m-d');
        if ($vac->data < $data_hui){
            abort(403,"No pots borrar aquestes vacances");
            //return "No pots borrar aquest moscós";
        } else {
            Vacances::find($id)->delete();
        }
    }
}