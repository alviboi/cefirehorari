<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoredeutesmesRequest;
use App\Http\Requests\UpdatedeutesmesRequest;
use App\Models\deutesmes;

class DeutesmesController extends Controller
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
     * @param  \App\Http\Requests\StoredeutesmesRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoredeutesmesRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\deutesmes  $deutesmes
     * @return \Illuminate\Http\Response
     */
    public function show(deutesmes $deutesmes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\deutesmes  $deutesmes
     * @return \Illuminate\Http\Response
     */
    public function edit(deutesmes $deutesmes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdatedeutesmesRequest  $request
     * @param  \App\Models\deutesmes  $deutesmes
     * @return \Illuminate\Http\Response
     */
    public function update(UpdatedeutesmesRequest $request, deutesmes $deutesmes)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\deutesmes  $deutesmes
     * @return \Illuminate\Http\Response
     */
    public function destroy(deutesmes $deutesmes)
    {
        //
    }

    public function calcula_deutes_mes()
    {

        $user_controller = new UserController();

        $usuaris = $user_controller->index();

        foreach ($usuaris as $key => $value) {
            # code...
            $este = $user_controller->calcula_deutes_mes_usuari($value->id);
            //Tant positiu com negatiu
            $this->afegix_deutes_mes($value->id, $este['difer??ncia']);
            // if ($este['difer??ncia'] < 0) {
            //     $this->afegix_deutes_mes($value->id, $este['difer??ncia']);
            // } else {
            //     $this->afegix_deutes_mes($value->id, 0); //No sobrecarrega ja que este valor s'utilitzar?? bastant i este c??lcul es fa una vegada al mes
            // }
        }
    }

    public function afegix_deutes_mes($user_id, $minuts_a_afegir)
    {

        $existeix = deutesmes::where("user_id", "=", $user_id)->first();
        if (!$existeix) {
            $dat = new deutesmes();
            $dat->user_id = $user_id;
            $dat->minuts = $minuts_a_afegir;
            $dat->save();
            return $minuts_a_afegir;
        } else {
            $add = $existeix->minuts + $minuts_a_afegir;
            $existeix->minuts = $add;
            $existeix->save();
            return $add;
        }
        return 0;

    }

    public function afegix_a_mes_anterior($user_id, $minuts) {

        $deute = deutesmes::where("user_id","=",$user_id)->first();
        if ($deute){
            $afig = $deute->minuts + $minuts;
            if ($afig > 0) {
                $deute->minuts = 0;
            } else {
                $deute->minuts = $afig;
            }
            $deute->save();
        }
        return;

    }   
    

}