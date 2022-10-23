<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoremoscosoRequest;
use App\Http\Requests\UpdatemoscosoRequest;
use App\Models\moscoso;
use App\Models\User;

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
}
