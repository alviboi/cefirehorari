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
}
