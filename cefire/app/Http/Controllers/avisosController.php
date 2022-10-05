<?php

namespace App\Http\Controllers;

use App\Models\avisos;
use Illuminate\Http\Request;

class avisosController extends Controller
{
    /**
     * Mostra un llistat de tot el recurs
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $avisos=avisos::orderby('id','DESC')->get();
        $ret=array();
        foreach ($avisos as $avis) {
            # code...
            $el = ["nom"=>$avis->user['name'],"cap"=>$avis->cap,"data"=>$avis->data,"avis"=>$avis->avis,"id"=>$avis->id];
            array_push($ret,$el);
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
     * Guarda l'elememt creat.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validem en el component, no seria necessari esta part
        $this->validate($request, [
            'cap' => 'required',
            'avis' => 'required',
         ]);
        $avis = new avisos();
        $avis->cap = $request->cap;
        $avis->avis = $request->avis;
        $avis->data = date("Y-m-d");
        $avis->user_id = auth()->id();
        //  Store data in database
        $avis->save();
        //
        return 'Avís gravat correctament ';

    }


    /**
     * Elimina l'element  del recurs de la base de dades
     *
     * @param  \App\Models\avisos  $avisos
     * @return \Illuminate\Http\Response
     */
    public function destroy($avisos)
    {
        //

        $avis=avisos::find($avisos);
        if ($avis->id == auth()->id() || auth()->user()->Perfil == 1){
            $avis->delete();
            return "Borrat correctament";
        } else {
            abort(403,"No tens permís per a borrar aquest avís");
        }
    }
}
