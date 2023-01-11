<?php

namespace App\Http\Controllers;

use App\Models\BorsaHores;
use App\Models\cefire;
use App\Models\deutesmes;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ControlController;
use App\Events\AfegitCefire;
use App\Events\BorratCefire;


class cefireController extends Controller
{

    protected $aparell;

    /**
     * __construct
     *
     * Extraguem la informació de configuració de l'aplicació
     *
     * aparell = 1 fitxa per temps i l'aparell funciona
     * aparell = 0 el fitxatge es per dia
     *
     * @param  mixed $Control_data
     * @return void
     */
    public function __construct(ControlController $Control_data)
    {
        $this->aparell = $Control_data->index()->aparell;
    }
    /**
     * Mostra un llistat de tot el recurs
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $ret = cefire::get();
        return $ret->toArray();

    }

    /**
     * Extrau totes les dades de fitxar del cefire amb el nom
     *
     * @return \Illuminate\Http\Response
     */
    public function get_data_index($any, $mes)
    {
        $ret = array();
        $els = cefire::whereMonth('data', '=', date($mes))->whereYear('data', '=', date($any))->get();
        foreach ($els as $el) {
            $item=array("id"=>$el->id, "name"=>$el->user['name'], "data"=>$el->data, "inici"=>$el->inici->format('H:i:s'), "fi"=>$el->fi->format('H:i:s'));
            array_push($ret, $item);
        }
        return $ret;
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
        $data_hui = date('Y-m-d');
        $en4dies = date('Y-m-d', strtotime($data_hui. ' + 4 days'));
        $any = date('Y');
        $data_15_oct = date($any."-10-15");
        $data_15_mai = date($any."-05-15");
        $hora_Actual = date('H');  //Per a modificar l'hora a la que es pot fitxar com a màxim
        if ($hora_Actual >= 22){
            abort(403,"No pots fitxar a estes hores");
        }


        if ($this->aparell == 1){
            if ($request->data != $data_hui){
                return response("Està habilitat el fitxatge per dies. Has de fitxar cada dia", 403);
                //abort(403, 'Està habilitat el fitxatge per dies. Has de fitxar cada dia');
            } else {
                $hora = date('H:i:s');
                if ($request->id == 0){
                    $dat = new cefire();
                    $dat->data = $request->data;
                    $dat->inici = $hora;
                    $dat->fi = "00:00:00";
                    $dat->user_id = auth()->id();
                    $dat->save();

                    $broad= array("data" => $dat->data,"nom" => auth()->user()->name,"id" => $dat->id, "inici" => $dat->inici->format('H:i:s'), "fi" => $dat->fi->format('H:i:s'));
                    $ret = array("id" => $dat->id,"inici" => $dat->inici->format('H:i:s'),"fi" => $dat->fi->format('H:i:s'));
                    // return cefire::where('data','=',$request->data)->where('user_id','=',auth()->id())->get();
                } else {
                    $cef=cefire::where('id','=',$request->id)->first();

                    $a = strtotime($hora)+480;
                    $b = strtotime($cef->inici);
                    $interval = $a - $b;

                    
                    $hora = date("H:i:s", $a);
                    /**
                     * Calculem una nova hora si excideixes de les hores que has fet. I et registra la nova data
                     */
                    //abort(403,$hora . "    " .  $cef->inici . "       " .$interval);

                    //S'eliminen les hores màximes per a fitxar

                    // if ($data_hui >= $data_15_oct || $data_hui <= $data_15_mai) {
                    //     if ($interval > 27900){//27900
                    //         //Calcular nova data $hora
                    //         $nova_data = $a + 27900;
                    //         $hora = date("H:i:s", $nova_data);
                    //     }               
                    // } else {
                    //     if ($interval > 26100){
                    //         $nova_data = $a + 26100;
                    //         $hora = date("H:i:s", $nova_data);
                    //         //Calcular nova data $hora
                    //     }    
                    // }

                    $cef->fi = $hora;
                    $cef->save();
                    $broad = array("data" => $cef->data, "nom" => auth()->user()->name, "id" => $cef->id, "inici" => $cef->inici->format('H:i:s'), "fi" => $cef->fi->format('H:i:s'));
                    $ret = array("id"=>$cef->id,"inici" => $cef->inici->format('H:i:s'),"fi" => $cef->fi->format('H:i:s'));
                    // return cefire::where('data','=',$request->data)->where('user_id','=',auth()->id())->get();
                }
                broadcast(new AfegitCefire($broad))->toOthers();
                return $ret;

            }
        } else {
            $hi_ha=cefire::where('user_id','=',auth()->id())->where('data','=',$request->data)->where('inici','=',$request->inici)->first();
            if ($hi_ha) {
                //abort(403, "Ja has fitxat el dia de hui");
                return response("Ja has fitxat", 403);
            } elseif ($request->data > $en4dies) {
                return response("Has de planificar la teua estància al CEFIRE cada setmana", 403);
            }else {
                $dat = new cefire();
                $dat->data = $request->data;
                $dat->inici = $request->inici;
                $dat->fi = $request->fi;
                $dat->user_id = auth()->id();
                $dat->save();
                $broad = array("data" => $dat->data, "nom" => auth()->user()->name, "id" => $dat->id, "inici" => $dat->inici->format('H:i:s'), "fi" => $dat->fi->format('H:i:s'));
                $ret = array("id" => $dat->id,"inici" => $dat->inici->format('H:i:s'),"fi" => $dat->fi->format('H:i:s'));
                broadcast(new AfegitCefire($broad))->toOthers();
                return $ret;

                // return cefire::where('data','=',$request->data)->where('user_id','=',auth()->id())->get();

            }
        }
    }

    /**
     * Mostra l'element del curs
     *
     * @param  \App\Models\cefire  $cefire
     * @return \Illuminate\Http\Response
     */
    public function show(cefire $cefire)
    {
        //
    }


    /**
     * Elimina l'element  del recurs de la base de dades
     *
     * @param  \App\Models\cefire  $cefire
     * @return \Illuminate\Http\Response
     */
    public function destroy(cefire $cefire)
    {
        //
        broadcast(new BorratCefire($cefire->toArray()))->toOthers();

        $cefire->delete();
    }


    /**
     * contar_cefires
     *
     * Calcula el temps que s'ha realitzat al Cefire entre $desde fins a $fins, seccionat per dies. Aquesta funció torna les dades per a poder generar la gràfica de
     * estadística de l'aplicació.
     *
     * @param  mixed $desde
     * @param  mixed $fins
     * @return void
     */
    public function contar_cefires($desde,$fins)
    {
        //
        $cefire=cefire::where('user_id','=',auth()->id())->whereBetween('data', [$desde, $fins])->orderBy('data','ASC')->get();
        $total=0;
        $labels=array();
        $data=array();
        $data_ant=new DateTime();
        $comp = new DateTime("00:00:00");
        foreach($cefire as $cef){
            if($cef->fi == $comp) {

            } else {
                if ($cef->data == $data_ant) {
                    $duration = $cef->inici->diffInMinutes($cef->fi);
                    $valor = array_pop($data);
                    array_push($data,($valor+$duration));
                } else {
                    $duration = $cef->inici->diffInMinutes($cef->fi);
                    array_push($labels,$cef->data);
                    array_push($data,$duration);
                }
                $total=$total+$duration;
                $data_ant=$cef->data;
            }

        }
        $ret=array('labels' => $labels, 'data' => $data, 'total' => $total);
        return ($ret);
    }
    
    public function usuaris_oblit_fitxatge() {
        $data_hui = date('Y-m-d');
        $cefires = cefire::where("fi","=","00:00:00")->where('data','<',$data_hui)->get();
        $dias=array("Diumenge","Dilluns","Dimarts","Dimecres","Dijous","Divendres","Dissabte");
        $ret = array();
        foreach ($cefires as $el) {
            $da=date("d-m-Y", strtotime($el->data));
            $da2=$dias[date("w", strtotime($el->data))];
            
            $item=array("id"=>$el->id, "name"=>$el->user['name'], "data"=>$da2.", ".$da, "inici"=>$el->inici->format("H:i:s"), "fi"=>$el->inici->format("H:i:s"));
            array_push($ret, $item);
        }
        return $ret;

    }


    public function ultims_dies_estadistica() {


        $data_hui = date('Y-m-d');
        $any = date('Y');
        $data_15_oct = date($any."-10-15");
        $data_15_mai = date($any."-05-15");
        if ($data_hui >= $data_15_oct || $data_hui <= $data_15_mai) {
            $interval_comp = 27900/60;       
        } else {
            $interval_comp = 26100/60;
        }



        $cefires = cefire::where('user_id','=',auth()->id())->orderBy('id', 'desc')->take(5)->get();
        $ret =array();
        foreach ($cefires as $key => $el) {
            # code...
            $a = strtotime($el->fi);
            $b = strtotime($el->inici);
            $interval = round(($a - $b)/60,0,PHP_ROUND_HALF_DOWN);
            $item=array("id"=>$el->id, "name"=>$el->user['name'], "data"=>$el->data, "inici"=>$el->inici->format("H:i:s"), "fi"=>$el->fi->format("H:i:s"), "diferencia"=>$interval, "compara"=>$interval_comp);
            array_push($ret, $item);
        }
        return $ret;
    }

    public function tots_els_dies_mes($any,$mes) {
        $inici = date($any."-".$mes."-01");
        $fi = $inici->format('Y-m-t');

        $dates = cefire::select('data')->distinct()->whereBetween('data',[$inici,$fi])->get();
        
        
        return $dates;

        // $data_hui = date('Y-m-d');
        // $any = date('Y');
        // $data_15_oct = date($any."-10-15");
        // $data_15_mai = date($any."-05-15");
        // if ($data_hui >= $data_15_oct || $data_hui <= $data_15_mai) {
        //     $interval_comp = 27900/60;       
        // } else {
        //     $interval_comp = 26100/60;
        // }



        // $cefires = cefire::where('user_id','=',auth()->id())->orderBy('id', 'desc')->take(5)->get();
        // $ret =array();
        // foreach ($cefires as $key => $el) {
        //     # code...
        //     $a = strtotime($el->fi);
        //     $b = strtotime($el->inici);
        //     $interval = round(($a - $b)/60,0,PHP_ROUND_HALF_DOWN);
        //     $item=array("id"=>$el->id, "name"=>$el->user['name'], "data"=>$el->data, "inici"=>$el->inici->format("H:i:s"), "fi"=>$el->fi->format("H:i:s"), "diferencia"=>$interval, "compara"=>$interval_comp);
        //     array_push($ret, $item);
        // }
        // return $ret;
    }

    public function cefire_fitxa_oblit(Request $request) {
        $dat = new cefire();
        $dat->data = $request->data;
        $dat->inici = $request->inici;
        $dat->fi = $request->fi;
        $dat->user_id = $request->id;
        $ok = $dat->save();
        $duration = $dat->inici->diffInMinutes($dat->fi);
        $data_hui_mes = date('m');
        $data_hui_a = explode('-',$dat->data);
        $data_hui = $data_hui_a[1];
        if ($data_hui_mes != $data_hui) {
            $deute = new DeutesmesController();
            $deute->afegix_a_mes_anterior($dat->user_id,$duration);
        } 
        if ($ok) {
            return "Fitxat correctament";
        } else {
            abort(403, "Alguna cosa ha anat malament");
        }
    }

    public function validaoblidat(Request $request) {
        $cefire = cefire::find($request->id);
        $cefire->fi = $request->fi;
        $duration = $cefire->inici->diffInMinutes($cefire->fi);
        $cefire->save();
        
        $data_hui_mes = date('m');
        $data_hui_a = explode('-',$cefire->data);
        $data_hui = $data_hui_a[1];
        if ($data_hui_mes != $data_hui) {
            $deute = new DeutesmesController();
            $deute->afegix_a_mes_anterior($cefire->user_id,$duration);
        } 
        return;
    }

}
