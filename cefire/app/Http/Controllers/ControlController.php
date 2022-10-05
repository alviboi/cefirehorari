<?php

namespace App\Http\Controllers;
use App\Models\control;

use Illuminate\Http\Request;


use Illuminate\Support\Facades\Route;




class ControlController extends Controller
{

    /**
     * Mostra un llistat de tot el recurs
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return control::first();

    }


    /**
     * Actualitza l'element del recurs a la base de dades
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $con = control::first();
        if (isset($request->aparell)){
            $con->aparell = $request->aparell;
        }
        if (isset($request->registra)){
            $con->registra = $request->registra;
        }
        $con->save();
        return "S'han actualitzat les dades";
    }

    /**
     * Elimina l'element  del recurs de la base de dades
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
