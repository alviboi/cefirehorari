<?php

namespace App\Http\Controllers;
use Validator;
use App\Models\permis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class permisController extends Controller
{
    /**
     * Mostra un llistat de tot el recurs
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return permis::get();
    }
    /**
     * Extrau totes les dades del permis entre dades determinades
     *
     * @return \Illuminate\Http\Response
     */
    public function get_data_index($any, $mes)
    {
        $ret = array();
        $els = permis::whereMonth('data', '=', date($mes))->whereYear('data', '=', date($any))->get();
        foreach ($els as $el) {
            $item=array("id"=>$el->id, "name"=>$el->user['name'], "data"=>$el->data, "inici"=>$el->inici, "fi"=>$el->fi, "motiu"=>$el->motiu);
            array_push($ret, $item);
        }
        return $ret;
    }

    /**
     * Guarda l'element creat.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $dat = new permis();
        $dat->data = $request->data;
        $dat->inici = $request->inici;
        $dat->fi = $request->fi;
        $dat->user_id = auth()->id();
        $dat->motiu = $request->motiu;
        $dat->arxiu= $request->arxiu;
        $dat->save();
        return $dat->toArray();
    }

    /**
     * Mostra l'element del curs entre dos dades donades pel request
     *
     * @param  \App\Models\permis  $permis
     * @return \Illuminate\Http\Response
     */
    public function permis_desde(Request $request)
    {
        //
        $per = permis::where('user_id','=',$request->id)->whereBetween('data', [$request->desde, $request->fins])->get();
        if ($per->isEmpty()){
            abort(403,"No hi ha resultats");
        } else {
            $ret = array();
            foreach ($per as $el) {
                $item=array("id"=>$el->id, "name"=>$el->user['name'], "data"=>$el->data, "inici"=>$el->inici, "fi"=>$el->fi, "motiu"=>$el->motiu, "arxiu"=>$el->arxiu);
                array_push($ret, $item);
            }
            return $ret;
        }

    }
    /**
     * Busca tots els permisos del sistema que no tinguen l'arxiu pujat
     *
     * @param  \App\Models\permis  $permis
     * @return \Illuminate\Http\Response
     */
    public function permis_sense_arxiu(Request $request)
    {
        //
        $per = permis::where('arxiu','=',null)->get();
        if ($per->isEmpty()){
            abort(403,"No hi ha resultats");
        } else {
            $ret = array();
            foreach ($per as $el) {
                $item=array("id"=>$el->id, "name"=>$el->user['name'], "data"=>$el->data, "inici"=>$el->inici, "fi"=>$el->fi, "motiu"=>$el->motiu, "arxiu"=>$el->arxiu);
                array_push($ret, $item);
            }
            return $ret;
        }

    }
    /**
     * Elimina l'element  del recurs de la base de dades
     *
     * @param  \App\Models\permis  $permis
     * @return \Illuminate\Http\Response
     */
    public function destroy($permis)
    {
        //
        permis::find($permis)->delete();
    }
    /**
     * Puja l'arxiu del permís
     *
     * @param  \App\Models\permis  $permis
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'arxiu' => 'required|mimes:pdf|max:10024'
        ]);

        if ($validator->fails()) {
            abort(404,"L'arxiu no és pdf");
        } else {
            $arxiu=$request->file('arxiu')->store('arxius');
            return $arxiu;
        }
    }
    /**
     * Descarrega l'arxiu demanat
     *
     * @param  \App\Models\permis  $permis
     * @return \Illuminate\Http\Response
     */
    public function download(Request $request)
    {
        $arx = response()->download(storage_path("app/".$request->arxiu), 'permis.pdf', [], 'inline');
        return $arx;
    }


}
