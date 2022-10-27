<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoremoscosoRequest;
use App\Http\Requests\UpdatemoscosoRequest;
use Illuminate\Http\Request;

use App\Models\moscoso;
use App\Models\User;
use App\Jobs\SendAvismoscosos;
use Carbon\Carbon;

class MoscosoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return moscoso::get();
    }

    public function get_data_index($any, $mes)
    {
        $ret = array();
        $els = moscoso::whereMonth('data', '=', date($mes))->whereYear('data', '=', date($any))->get();
        foreach ($els as $el) {
            $item=array("id"=>$el->id, "name"=>$el->user['name'], "data"=>$el->data, "inici"=>"00:00:00", "fi"=>"23:00:00","concepte"=>"Moscosos");
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
     * @param  \App\Http\Requests\StoremoscosoRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoremoscosoRequest $request)
    {
        //
        $exist_mocosos = moscoso::where('data', '=',$request->data)->first();
        if ($exist_mocosos){
            abort(403,"No te'n pots anar dos dies de festa en un sol dia");
        }

        $year = date("Y");
        $str = $year."-1-1";
        
        $inici = date_create($str);
        $final = date_create(($year+1)."-12-31");
        $moscosos=User::where("id","=",auth()->id())->first();
        $total = moscoso::where('data', '>', $inici)->where('data', '<', $final)->where("user_id","=",auth()->id())->count();


        if ($moscosos->moscosos <= $total) {
            abort(403,"Ja has consumit tots els moscosos");
        } else {
            $dat = new moscoso();
            $dat->data = $request->data;
            $dat->user_id = auth()->id();
            $dat->save();
        }
        
        return $total;
        //return $dat->toArray();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\moscoso  $moscoso
     * @return \Illuminate\Http\Response
     */
    public function show(moscoso $moscoso)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\moscoso  $moscoso
     * @return \Illuminate\Http\Response
     */
    public function edit(moscoso $moscoso)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatemoscosoRequest  $request
     * @param  \App\Models\moscoso  $moscoso
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatemoscosoRequest $request, moscoso $moscoso)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\moscoso  $moscoso
     * @return \Illuminate\Http\Response
     */
    public function destroy(moscoso $moscoso)
    {
        //
        $data_hui = date('Y-m-d');
        if ($moscoso->data < $data_hui){
            abort(403,"No pots borrar aquest moscós");
            //return "No pots borrar aquest moscós";
        } else {
            $moscoso->delete();
        }
        
    }

    public function moscososnovalidades()
    {
        //
        $ret = array();
        $els = moscoso::where('aprobada','=',false)->get();
        $dias=array("Diumenge","Dilluns","Dimarts","Dimecres","Dijous","Divendres","Dissabte");

        foreach ($els as $el) {
            $da=date("d-m-Y", strtotime($el->data));
            $da2=$dias[date("w", strtotime($el->data))];
            
            $item=array("id"=>$el->id, "name"=>$el->user['name'], "data"=>$da2.", ".$da);
            array_push($ret, $item);
        }
        return $ret;
    }

    public function validamoscosos(Request $request)
    {
        //
        
        $el = moscoso::where('id',$request->id)->update(['aprobada'=>true]);

        $moscoso = moscoso::find($request->id);

        $link2 = "https://calendar.google.com/calendar/u/0/r/eventedit?text=MOSCOSO+CEFIRE&dates=" . str_replace("-", "", $moscoso->data) . "&details=compensa+del+Cefire+de+Valencia+ELIMINADA&location=Valencia&trp=false#eventpage_6";

        $datos2 = [
            'nombre' => $moscoso->user['name'],
            'fecha' => date("d/m/Y", strtotime($moscoso->data)),
            'link' => $link2,
            'estat' => 'Aprovada'
        ];


        $emailJob3 = (new SendAvismoscosos($moscoso->user['email'], $datos2))->delay(Carbon::now()->addSeconds(120));
        dispatch($emailJob3);

        //Mail::to($compensa->user['email'])->send(new Eliminarcompensa($datos2));

    }
}
