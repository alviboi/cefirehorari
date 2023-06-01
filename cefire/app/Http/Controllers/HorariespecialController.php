<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorehorariespecialRequest;
use App\Http\Requests\UpdatehorariespecialRequest;
use App\Models\horariespecial;
use Illuminate\Http\Request;


class HorariespecialController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $ret = horariespecial::get();
        return $ret->toArray();
    }

    public function index_en_dif()
    {
        //
        $ret_a = array();
        $ret = horariespecial::get();

        foreach ($ret as $key => $value) {
            $fi = strtotime($value->fi);
            $inici = strtotime($value->inici);
            $dif = $fi - $inici;
            $add = ['dia' => $value['data'], 'total' => $dif/60 ];
            array_push($ret_a,$add);
            # code...
        }

        return $ret_a;
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
     * @param  \App\Http\Requests\StorehorariespecialRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $dat = new horariespecial();
        $dat->data = $request->dia;
        $dat->inici = $request->inici;
        $dat->fi = $request->fi;
        $dat->save();
        return $dat->toArray();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\horariespecial  $horariespecial
     * @return \Illuminate\Http\Response
     */
    public function show(horariespecial $horariespecial)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\horariespecial  $horariespecial
     * @return \Illuminate\Http\Response
     */
    public function edit(horariespecial $horariespecial)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatehorariespecialRequest  $request
     * @param  \App\Models\horariespecial  $horariespecial
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatehorariespecialRequest $request, horariespecial $horariespecial)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\horariespecial  $horariespecial
     * @return \Illuminate\Http\Response
     */
    public function destroy($horariespecial)
    {
        //
        horariespecial::find($horariespecial)->delete();
        return "Borrat";

    }
}
