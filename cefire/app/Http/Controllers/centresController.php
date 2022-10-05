<?php

namespace App\Http\Controllers;

use App\Models\centres;
use Illuminate\Http\Request;

class centresController extends Controller
{
    /**
     * Mostra un llistat de tot el recurs
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $centres=centres::get()->values();
        $ret=array();
        foreach ($centres as $el) {
            $item=array("id" => $el->id,"assessor" => $el->user['name'],"nom" => $el->nom,"codi"=>$el->codi,"situacio"=>$el->situacio,"CP"=>$el->CP,"ciutat"=>$el->ciutat,"contacte"=>$el->contacte,"mail_contacte"=>$el->mail_contacte,"tlf_contacte"=>$el->tlf_contacte,"Observacions"=>$el->Observacions);
            array_push($ret, $item);
        }
        return $ret;
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
     * Guarda l'element creat. L'element centre no es deu de crear d'un en un l'administrador fa un bolcat a principi de curs des d'un full de calc.
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
     * @param  \App\Models\centres  $centres
     * @return \Illuminate\Http\Response
     */
    public function show(centres $centres)
    {
        //
    }


    /**
     * Actualitza l'element del recurs a la base de dades
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\centres  $centres
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $centres)
    {
        //
        if (auth()->check()) {
            $centre = centres::find($centres);
            $centre->user_id = $request->user_id;
            $centre->nom = $request->nom;
            $centre->codi = $request->codi;
            $centre->situacio = $request->situacio;
            $centre->CP = $request->CP;
            $centre->ciutat = $request->ciutat;
            $centre->contacte = $request->contacte;
            $centre->mail_contacte = $request->mail_contacte;
            $centre->tlf_contacte = $request->tlf_contacte;
            $centre->Observacions = $request->Observacions;
            $centre->save();
            return $centre;
        } else {
            return "No estàs logat";
        }

    }

    /**
     * Elimina l'element  del recurs de la base de dades
     *
     * @param  \App\Models\centres  $centres
     * @return \Illuminate\Http\Response
     */
    public function destroy($centres)
    {
        //
        centres::find($centres)->delete();

    }
}
