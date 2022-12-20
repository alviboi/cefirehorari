<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBorsaHoresRequest;
use App\Http\Requests\UpdateBorsaHoresRequest;
use App\Models\BorsaHores;

class BorsaHoresController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return BorsaHores::get();
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
     * @param  \App\Http\Requests\StoreBorsaHoresRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBorsaHoresRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BorsaHores  $borsaHores
     * @return \Illuminate\Http\Response
     */
    public function show(BorsaHores $borsaHores)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BorsaHores  $borsaHores
     * @return \Illuminate\Http\Response
     */
    public function edit(BorsaHores $borsaHores)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBorsaHoresRequest  $request
     * @param  \App\Models\BorsaHores  $borsaHores
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBorsaHoresRequest $request, BorsaHores $borsaHores)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BorsaHores  $borsaHores
     * @return \Illuminate\Http\Response
     */
    public function destroy(BorsaHores $borsaHores)
    {
        //
    }

    public function crea($user_id, $minuts_a_afegir)
    {
        //
        $existeix = BorsaHores::where("user_id", "=", $user_id)->first();
        if (!$existeix) {
            $dat = new BorsaHores();
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

    public function borraborsa() {
        BorsaHores::truncate();
        return 1;
    }
}