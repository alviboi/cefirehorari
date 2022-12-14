<?php

namespace App\Http\Controllers;

use App\Models\Vacances;
use App\Models\vacances_oficials;
use App\Models\User;
use Illuminate\Http\Request;
use App\Jobs\SendAvisvacances;
use Carbon\Carbon;
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

    public function get_data_index($any, $mes)
    {
        $ret = array();
        $els = Vacances::whereMonth('data', '=', date($mes))->whereYear('data', '=', date($any))->get();
        foreach ($els as $el) {
            $item=array("id"=>$el->id, "name"=>$el->user['name'], "data"=>$el->data, "inici"=>"00:00:00", "fi"=>"23:00:00","concepte"=>"Vacances");
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

    function isWeekend($date) {
        return (date('N', strtotime($date)) >= 6);
    }

    public function getWorkingDays($startDate, $endDate)
    {
        $vacancesoficials = vacances_oficials::get('data')->toArray();
        $vacaofcarr = array();
        foreach ($vacancesoficials as $key => $value) {
            # code...
            array_push($vacaofcarr,$value['data']);
        }
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
                if (!in_array($dia, $vacaofcarr)){
                    array_push($tots_els_dies,$dia);
                }
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
                abort(403, "Est??s demanant un dia que ja tens");
            }
        }

        if (($total+ count($dies_a_afegir)) > $vacances_total->vacances){
            abort(403, "Est??s passant-te dels dies que tens de vacances");
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
        $vac = Vacances::where("id","=",$id)->first();
        $data = date($vac->data);
        $data_hui = date("Y-m-d");
        if ($data_hui > $data && $vac->user['Perfil'] != 1){
            abort(403,"No pots borrar aquestes vacances");
            //return "No pots borrar aquest mosc??s";
        } else {
            Vacances::find($id)->delete();
        }
    }
    public function vacancesnovalidades()
    {
        //
        $ret = array();
        $els = vacances::where('aprobada','=',false)->get();
        $dias=array("Diumenge","Dilluns","Dimarts","Dimecres","Dijous","Divendres","Dissabte");

        foreach ($els as $el) {
            $da=date("d-m-Y", strtotime($el->data));
            $da2=$dias[date("w", strtotime($el->data))];
            
            $item=array("id"=>$el->id, "name"=>$el->user['name'], "data"=>$da2.", ".$da);
            array_push($ret, $item);
        }
        return $ret;
    }

    public function validavacances(Request $request)
    {
        //
        $el = vacances::where('id',$request->id)->update(['aprobada'=>true]);

        $compensa = vacances::find($request->id);

        $link2 = "https://calendar.google.com/calendar/u/0/r/eventedit?text=VACANCES+CEFIRE&dates=" . str_replace("-", "", $compensa->data) . "&details=compensa+del+Cefire+de+Valencia+ELIMINADA&location=Valencia&trp=false#eventpage_6";

        $datos2 = [
            'nombre' => $compensa->user['name'],
            'fecha' => date("d/m/Y", strtotime($compensa->data)),
            'link' => $link2,
            'estat' => 'Aprovada'
        ];


        $emailJob3 = (new SendAvisvacances($compensa->user['email'], $datos2))->delay(Carbon::now()->addSeconds(120));
        dispatch($emailJob3);

        //Mail::to($compensa->user['email'])->send(new Eliminarcompensa($datos2));

    }
}