<?php

/**
 * Aquest classe tot i que no s'utilitza es deixa predefinida per a futures ampliacions o actualitzacions
 * que permitin compaginat les cridades des d'Arduino amb laravel
 */

namespace App\Http\Controllers;

use App\Models\lectura_rfid;
use Illuminate\Http\Request;

class lectura_rfidController extends Controller
{
    /**
     * Mostra un llistat de tot el recurs
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return lectura_rfid::get();
    }

    /**
     * Crea un element del recurs
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Guarda l'elememt creat.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Mostra l'element del curs
     *
     * @param  \App\Models\lectura_rfid  $lectura_rfid
     * @return \Illuminate\Http\Response
     */
    public function show(lectura_rfid $lectura_rfid)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\lectura_rfid  $lectura_rfid
     * @return \Illuminate\Http\Response
     */
    public function edit(lectura_rfid $lectura_rfid)
    {
        //
    }

    /**
     * Actualitza l'element del recurs a la base de dades
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\lectura_rfid  $lectura_rfid
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, lectura_rfid $lectura_rfid)
    {
        //
    }

    /**
     * Elimina l'element  del recurs de la base de dades
     *
     * @param  \App\Models\lectura_rfid  $lectura_rfid
     * @return \Illuminate\Http\Response
     */
    public function destroy(lectura_rfid $lectura_rfid)
    {
        //
    }
}
