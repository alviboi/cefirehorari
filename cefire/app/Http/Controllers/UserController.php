<?php

namespace App\Http\Controllers;
use DB;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\cefire;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Jobs\SendPasswordMail;
use DateTime;


/**
 * UserController Class
 *
 * @version 1.0
 *
 * Per a totes aquelles connexions realitzades a la taula User
 *
 */

class UserController extends Controller
{


    /**
     * @return la vista en blade de home
     */
    public function home()
    {
        $conta = User::find(auth()->id())->notificacions()->count();
        return view('home', ['conta' => $conta]);
    }




    /**
     * contar: Conta tots els elements que té un assessor
     *
     * @param  mixed $desde inici del di
     * @param  mixed $fins fi del dia
     * @return array de totes les dades contades
     */
    public function contar($desde, $fins)
    {

        $labels = ['cefire', 'permis', 'compensa', 'curs'];

        $cefire = user::find(auth()->id())->cefire()->whereBetween('data', [$desde, $fins])->get();
        $permis = user::find(auth()->id())->permis()->whereBetween('data', [$desde, $fins])->get();
        $compensa = user::find(auth()->id())->compensa()->whereBetween('data', [$desde, $fins])->get();
        $curs = user::find(auth()->id())->curs()->whereBetween('data', [$desde, $fins])->get();

        $total_cef = 0;
        foreach ($cefire as $cef) {
            $duration = $cef->inici->diffInMinutes($cef->fi);
            $total_cef = $total_cef + $duration;
        }

        $total_per = 0;
        foreach ($permis as $perm) {
            $in = Carbon::parse($perm->inici);
            $fi = Carbon::parse($perm->fi);
            $duration = $in->diffInMinutes($fi);
            $total_per = $total_per + $duration;
        }

        $total_comp = 0;
        foreach ($compensa as $comp) {
            $in = Carbon::parse($comp->inici);
            $fi = Carbon::parse($comp->fi);
            $duration = $in->diffInMinutes($fi);
            $total_comp = $total_comp + $duration;
        }

        $total_curs = 0;
        foreach ($curs as $cu) {
            $in = Carbon::parse($cu->inici);
            $fi = Carbon::parse($cu->fi);
            $duration = $in->diffInMinutes($fi);
            $total_curs = $total_curs + $duration;
        }
        $datos = [round($total_cef / 60, 2), round($total_per / 60, 2), round($total_comp / 60, 2), round($total_curs / 60, 2)];

        $ret = array('labels' => $labels, 'datos' => $datos);
        return ($ret);
    }

    public function logat()
    {
        return user::find(auth()->id())->name;
    }

    public function logat_id()
    {
        return user::find(auth()->id())->id;
    }

    /**
     * Guarda l'elememt creat.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response missatge de tasca feta
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $message = "";
        for ($i = 0; $i < count($input); $i++) {
            $user_comp = User::where('email', '=', $input[$i]['email'])->first();
            if ($user_comp != null) {
                $message = $message . " " . $user_comp->email;
            } else {
                $dat = new User();
                $dat->name = $input[$i]['name'];
                $dat->email = $input[$i]['email'];
                $dat->Perfil = 0;
                $passwd = Str::random(5);
                $dat->password = Hash::make($passwd);
                $dat->save();
                $emailJob = (new SendPasswordMail($input[$i]['email'], $passwd))->delay(Carbon::now()->addSeconds(120));
                dispatch($emailJob);
            }
        }
        if ($message == "") {
            return "Tots els usuaris s'han creat";
        } else {
            return "Els usuaris amb mail: " . $message . ", ja estan creats, per tant no s'han tornat a crear";
        }
    }

    /**
     * get_usuaris_ldap funció per a demanar tots els usuaris LDAP
     *
     * @param  mixed $request es demana la ip i la contrasenya de netadmin per a poder connectar-se a un LliureX
     * @return text Si ha estat satisfactòria
     */
    public function get_usuaris_ldap(Request $request)
    {

        //Cal identificar-se amb l'usuari netadmin
        //uid=alfredo@alfredo.es,ou=Teachers,ou=People,dc=ma5,dc=lliurex,dc=net
        $pwd = $request->contrasenya;
        $conn = ldap_connect($request->ip, '389');
        $bindDn = "uid=netadmin,ou=Admins,ou=People,dc=ma5,dc=lliurex,dc=net";
        ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);

        $ldaptree = "ou=Teachers,ou=People,dc=ma5,dc=lliurex,dc=net";


        if ($conn) {

            $ldapbind = ldap_bind($conn, $bindDn, $pwd) or die("Ha hagut un error: " . ldap_error($conn));

            if ($ldapbind) {

                $result = ldap_search($conn, $ldaptree, "(cn=*)") or die("Ha hagut un error: " . ldap_error($conn));
                $data = ldap_get_entries($conn, $result);

                $usuaris = array();
                for ($i = 0; $i < $data["count"]; $i++) {
                    $el = ['email' => $data[$i]["uid"][0], 'name' => $data[$i]["description"][0]];
                    array_push($usuaris, $el);
                }
                return $usuaris;
                ldap_close($conn);
            } else {
                ldap_close($conn);
                return "El servidor no està ben configurat. Revisa la configuració";
            }


        }
    }

    /**
     * Mostra la informació d'un usuari
     *
     * @return informació
     */
    public function get_user()
    {
        //
        return User::find(auth()->id());
    }
    /**
     * Mostra un llistat de tot el recurs
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return User::orderBy('name', 'ASC')->get();
    }

    /**
     * destroy
     *
     * Elimina el recurs
     *
     * @param  mixed $user
     * @return void
     */
    public function destroy($user)
    {
        //
        User::find($user)->delete();
    }


    /**
     * update
     *
     * Actualitza el recurs
     *
     * @param  mixed $request
     * @return estat de la petició
     */
    public function update(Request $request)
    {
        //

        if ($request->id == auth()->id() || auth()->user()->Perfil == 1) {
            $user = User::find($request->id);
            $user->name = $request->nom;
            $user->email = $request->mail;
            $user->Perfil = $request->perfil;
            $user->rfid = $request->rfid;
            if ($request->moscosos != null && auth()->user()->Perfil == 1){
                $user->moscosos = $request->moscosos;
            }
            if ($request->vacances != null  && auth()->user()->Perfil == 1){
                $user->vacances = $request->vacances;
            }            
            if ($request->contrasenya != "") {
                $user->password = Hash::make($request->contrasenya);
            }
            $user->update();
        } else {
            abort(403, "No tens permís per a realitzar aquesta acció");
        }


    }


    /**
     * dia_cefire
     *
     * Torna la informació sol·licitada d'un moment concret del dia del recurs
     *
     * @param  mixed $dia
     * @param  mixed $mati
     * @return array $ret2 amb tota la informació
     */
    public function dia_cefire($dia)
    {
        //

        $cefire = User::find(auth()->id())->cefire()->where('data', '=', $dia)->get();
        $ret2 = array();
        foreach ($cefire as $value) {
            $ret1 = array("id" => $value->id, "user_id" => $value->user_id, "inici" => $value->inici->format('H:i:s'), "fi" => $value->fi->format('H:i:s'));
            array_push($ret2, $ret1);

        }

        return $ret2;

    }
    /**
     * dia_guardia
     *
     * Torna la informació sol·licitada d'un moment concret del dia del recurs
     *
     * @param  mixed $dia
     * @param  mixed $mati
     * @return array $ret2 amb tota la informació
     */
    public function dia_guardia($dia)
    {
        //

        $cefire = User::find(auth()->id())->guardia()->where('data', '=', $dia)->get();
        return $cefire;
    }
    /**
     * dia_incidencies
     *
     * Torna la informació sol·licitada d'un moment concret del dia del recurs
     *
     * @param  mixed $dia
     * @param  mixed $mati
     * @return array $ret2 amb tota la informació
     */
    public function dia_incidencies($dia)
    {
        //

        $incidencies = User::find(auth()->id())->incidencies()->where('data', '=', $dia)->get();
        return $incidencies;
    }
    /**
     * dia_moscoso
     *
     * Torna la informació sol·licitada d'un moment concret del dia del recurs
     *
     * @param  mixed $dia
     * @param  mixed $mati
     * @return array $ret2 amb tota la informació
     */
    public function dia_moscoso($dia)
    {
        //
        $moscoso = User::find(auth()->id())->moscoso()->where('data', '=', $dia)->get();
        return $moscoso;
    }
    /**
     * dia_moscoso
     *
     * Torna la informació sol·licitada d'un moment concret del dia del recurs
     *
     * @param  mixed $dia
     * @param  mixed $mati
     * @return array $ret2 amb tota la informació
     */
    public function dia_vacances($dia)
    {
        //
        $moscoso = User::find(auth()->id())->vacances()->where('data', '=', $dia)->get();
        return $moscoso;
    }
    /**
     * dia_curs
     *
     * Torna la informació sol·licitada d'un moment concret del dia del recurs
     *
     * @param  mixed $dia
     * @param  mixed $mati
     * @return array $ret2 amb tota la informació
     */
    public function dia_curs($dia)
    {
        //

        $cefire = User::find(auth()->id())->curs()->where('data', '=', $dia)->get();
        return $cefire;
    }
    /**
     * dia_compensa
     *
     * Torna la informació sol·licitada d'un moment concret del dia del recurs
     *
     * @param  mixed $dia
     * @param  mixed $mati
     * @return array $ret2 amb tota la informació
     */
    public function dia_compensa($dia)
    {
        //

        $cefire = User::find(auth()->id())->compensa()->where('data', '=', $dia)->get();
        return $cefire;
    }
    /**
     * dia_visita
     *
     * Torna la informació sol·licitada d'un moment concret del dia del recurs
     *
     * @param  mixed $dia
     * @param  mixed $mati
     * @return array $ret2 amb tota la informació
     */
    public function dia_visita($dia)
    {
        //

        $cefire = User::find(auth()->id())->visita()->where('data', '=', $dia)->get();
        return $cefire;
    }
    /**
     * dia_permis
     *
     * Torna la informació sol·licitada d'un moment concret del dia del recurs
     *
     * @param  mixed $dia
     * @param  mixed $mati
     * @return array $ret2 amb tota la informació
     */
    public function dia_permis($dia)
    {
        //

        $cefire = User::find(auth()->id())->permis()->where('data', '=', $dia)->get();
        return $cefire;
    }

    public function dia_tot($dia)
    {
        //
        $cefire = array();
        $ret2 = array();
        $ret3 = array();

        $cefire['permis'] = User::find(auth()->id())->permis()->where('data', '=', $dia)->get();
        $cefire['visita'] = User::find(auth()->id())->visita()->where('data', '=', $dia)->get();
        $cefire['compensa'] = User::find(auth()->id())->compensa()->where('data', '=', $dia)->get();
        $cefire['curs'] = User::find(auth()->id())->curs()->where('data', '=', $dia)->get();
        $cefire['guardia'] = User::find(auth()->id())->guardia()->where('data', '=', $dia)->get();
        $cefire['vacances'] = User::find(auth()->id())->vacances()->where('data', '=', $dia)->get();
        $ret2 = User::find(auth()->id())->cefire()->where('data', '=', $dia)->get();

        foreach ($ret2 as $value) {
            $ret1 = array("id" => $value->id, "user_id" => $value->user_id, "inici" => $value->inici->format('H:i:s'), "fi" => $value->fi->format('H:i:s'));
            array_push($ret3, $ret1);
        }
        $cefire['cefire'] = $ret3;
        $cefire['moscosos'] = User::find(auth()->id())->moscoso()->where('data', '=', $dia)->get();


        return $cefire;
    }


    /**
     * get_cefire
     *
     * Torna tots el fitxatges fets durant un temps determinat per $any $mes de l'assesor amb el codi $num
     *
     * @param  mixed $request
     * @param  mixed $num
     * @param  mixed $any
     * @param  mixed $mes
     * @return array $ret2 amb les dades sol·licitades
     */
    public function get_cefire(Request $request, $num, $any, $mes)
    {
        //
        $cefire = User::find($num)->cefire()->whereMonth('data', '=', date($mes))->whereYear('data', '=', date($any))->get();
        $ret2 = array();
        foreach ($cefire as $value) {
            # code...
            $ret1 = array("data" => $value->data, "id" => $value->id, "user_id" => $value->user_id, "inici" => $value->inici->format('H:i:s'), "fi" => $value->fi->format('H:i:s'));
            array_push($ret2, $ret1);
        }
        return $ret2;
    }
    /**
     * get_guardia
     *
     * Torna totes les guàrdies durant un temps determinat per $any $mes de l'assesor amb el codi $num
     *
     * @param  mixed $request
     * @param  mixed $num
     * @param  mixed $any
     * @param  mixed $mes
     * @return array $ret2 amb les dades sol·licitades
     */

    public function get_guardia(Request $request, $num, $any, $mes)
    {
        //
        return User::find($num)->guardia()->whereMonth('data', '=', date($mes))->whereYear('data', '=', date($any))->get();
    }

    /**
     * get_curs
     *
     * Torna tots els curs fets per un assessor durant un temps determinat per $any $mes de l'assesor amb el codi $num
     *
     * @param  mixed $request
     * @param  mixed $num
     * @param  mixed $any
     * @param  mixed $mes
     * @return array $ret2 amb les dades sol·licitades
     */

    public function get_curs(Request $request, $num, $any, $mes)
    {
        //
        return User::find($num)->curs()->whereMonth('data', '=', date($mes))->whereYear('data', '=', date($any))->get();
    }

    /**
     * get_compensa
     *
     * Torna totes les compensacions fetes per un assessor durant un temps determinat per $any $mes de l'assesor amb el codi $num
     *
     * @param  mixed $request
     * @param  mixed $num
     * @param  mixed $any
     * @param  mixed $mes
     * @return array $ret2 amb les dades sol·licitades
     */

    public function get_compensa(Request $request, $num, $any, $mes)
    {
        //
        return User::find($num)->compensa()->whereMonth('data', '=', date($mes))->whereYear('data', '=', date($any))->get();
    }

    /**
     * get_visita
     *
     * Torna totes les visites durant un temps determinat per $any $mes de l'assesor amb el codi $num
     *
     * @param  mixed $request
     * @param  mixed $num
     * @param  mixed $any
     * @param  mixed $mes
     * @return array $ret2 amb les dades sol·licitades
     */

    public function get_visita(Request $request, $num, $any, $mes)
    {
        //
        return User::find($num)->visita()->whereMonth('data', '=', date($mes))->whereYear('data', '=', date($any))->get();
    }

    /**
     * get_permis
     *
     * Torna tots els permisos durant un temps determinat per $any $mes de l'assesor amb el codi $num
     *
     * @param  mixed $request
     * @param  mixed $num
     * @param  mixed $any
     * @param  mixed $mes
     * @return array $ret2 amb les dades sol·licitades
     */

    public function get_permis(Request $request, $num, $any, $mes)
    {
        //
        return User::find($num)->permis()->whereMonth('data', '=', date($mes))->whereYear('data', '=', date($any))->get();
    }

    public function get_vacances(Request $request, $num, $any, $mes)
    {
        //
        $ret = User::find($num)->vacances()->whereMonth('data', '=', date($mes))->whereYear('data', '=', date($any))->get();
        for ($i=0; $i < count($ret); $i++) { 
            # code...
            $ret[$i]['inici']="Tot el dia";
            $ret[$i]['fi']="";
            $ret[$i]['concepte']="Vacances";
        }
        
        return $ret;

    }

    public function get_moscosos(Request $request, $num, $any, $mes)
    {
        //
        $ret = User::find($num)->moscoso()->whereMonth('data', '=', date($mes))->whereYear('data', '=', date($any))->get();
        for ($i=0; $i < count($ret); $i++) { 
            # code...
            $ret[$i]['inici']="Tot el dia";
            $ret[$i]['fi']="";
            $ret[$i]['moscoso']="Vacances";
        }
        
        return $ret;
    }

    // public function __invoke(Request $request)
    // {
    //     //
    // }


    /**
     * get_all
     *
     * Torna totes les dades d'un assessor en concret
     *
     * @param  mixed $de
     * @param  mixed $fins
     * @return array $ret
     */
    public function get_all($de, $fins)
    {
        //
        $cefire = User::find(auth()->id())->cefire()->where('data', '>', $de)->where('data', '<', $fins)->get();
        $compensa = User::find(auth()->id())->compensa()->where('data', '>', $de)->where('data', '<', $fins)->get();
        $curs = User::find(auth()->id())->curs()->where('data', '>', $de)->where('data', '<', $fins)->get();
        $guardia = User::find(auth()->id())->guardia()->where('data', '>', $de)->where('data', '<', $fins)->get();
        $permis = User::find(auth()->id())->permis()->where('data', '>', $de)->where('data', '<', $fins)->get();
        $visita = User::find(auth()->id())->visita()->where('data', '>', $de)->where('data', '<', $fins)->get();
        $ret = [
            'cefire' => $cefire,
            'compensa' => $compensa,
            'curs' => $curs,
            'guardia' => $guardia,
            'permis' => $permis,
            'visita' => $visita
        ];
        return $ret;

    }

    public function get_statistics()
    {
        $ret = array();
        //$labels=['cefire','permis','curs','visita'];
        $year = date('Y');
        $mes = date('m');
        $desde = date($year . "-" . $mes . "-1");
        $fins = date($year . "-" . $mes . "-t");

        $desde_any = date($year . "-1-1");
        $fins_any = date($year . "-12-31");


        $cefire = user::find(auth()->id())->cefire()->whereBetween('data', [$desde, $fins])->where('fi','!=','00:00:00')->get();
        $permis = user::find(auth()->id())->permis()->whereBetween('data', [$desde, $fins])->get();
        $compensa = user::find(auth()->id())->compensa()->whereBetween('data', [$desde, $fins])->get();
        $curs = user::find(auth()->id())->curs()->whereBetween('data', [$desde, $fins])->get();
        $visita = user::find(auth()->id())->visita()->whereBetween('data', [$desde, $fins])->get();
        $moscosos = user::find(auth()->id())->moscoso()->whereBetween('data', [$desde_any, $fins_any])->count();
        $vacances = user::find(auth()->id())->vacances()->whereBetween('data', [$desde_any, $fins_any])->count();

        $total_cef = 0;
        foreach ($cefire as $cef) {
            $duration = $cef->inici->diffInMinutes($cef->fi);
            $total_cef = $total_cef + $duration;
        }
        $cefire_count = user::find(auth()->id())->cefire()->where('fi','!=','00:00:00')->whereBetween('data', [$desde, $fins])->count();
        $ret['fitxatges'] = $total_cef . " minuts" . "(" . $cefire_count . " dies)";
        $total_per = 0;
        foreach ($permis as $perm) {
            $in = Carbon::parse($perm->inici);
            $fi = Carbon::parse($perm->fi);
            $duration = $in->diffInMinutes($fi);
            $total_per = $total_per + $duration;
        }
        $ret['permís'] = $total_per . " minuts";
        $total_comp = 0;
        foreach ($compensa as $comp) {
            $in = Carbon::parse($comp->inici);
            $fi = Carbon::parse($comp->fi);
            $duration = $in->diffInMinutes($fi);
            $total_comp = $total_comp + $duration;
        }
        $ret['compensa'] = $total_comp . " minuts";
        $total_curs = 0;
        foreach ($curs as $cu) {
            $in = Carbon::parse($cu->inici);
            $fi = Carbon::parse($cu->fi);
            $duration = $in->diffInMinutes($fi);
            $total_curs = $total_curs + $duration;
        }
        $ret['curs'] = $total_curs . " minuts";
        $total_visita = 0;
        foreach ($visita as $vi) {
            $in = Carbon::parse($vi->inici);
            $fi = Carbon::parse($vi->fi);
            $duration = $in->diffInMinutes($fi);
            $total_visita = $total_visita + $duration;
        }
        $ret['Com. Serv.'] = $total_visita . " minuts";

        $user = User::find(auth()->id());
        $ret['moscosos (any)'] = $moscosos . " de " . $user->moscosos . " consumits";
        $ret['vacances (any)'] = $vacances . " de " . $user->vacances . " consumits";
        $ret['TOTAL TEMPS'] = ($total_visita + $total_curs + $total_cef) . " minuts";
        return $ret;

    }
    public function tots_els_dies_mes($any,$mes)
    {

        $inici = date($any."-".$mes."-01");
        $fi = date("Y-m-t", strtotime($inici));
        
        $dates = cefire::select('data')->distinct()->whereBetween('data',[$inici,$fi])->orderBy('data', 'ASC')->get();
        if ($dates->isEmpty()){
            abort(403,"No hi ha cap resultat");
        }
        $total_dies = $dates->count();
        $usuaris = User::orderBy('name', 'ASC')->get();


        $este = array();
        $a = array();
        
        $data_hui = date('Y-m-d');
        $any = date('Y');
        $data_15_oct = date($any."-11-01");
        $data_15_mai = date($any."-04-30");

        $total_mes = 0;
        foreach ($dates as $key => $value) {
            # code...
            if ($value >= $data_15_oct || $value <= $data_15_mai) {
                $total_mes += (27900/60);    
            } else {
                $total_mes += (26100/60);
            }

        }


        if ($data_hui >= $data_15_oct || $data_hui <= $data_15_mai) {
            $total_dia = (27900/60);    
        } else {
            $total_dia = (26100/60);
        }

       



        foreach ($usuaris as $key => $value) {
            # code...
            // Anar agafant-ho tot per dia
            $este['Nom'] = $value->name;
            $este['fitxatge'] = intval($value->cefire()->select(DB::raw('SUM(TIME_TO_SEC(TIMEDIFF(fi,inici))/60) as total'))->whereBetween('data',[$dates[0]['data'],$dates->last()['data']])->where('fi','!=','00:00:00')->first()['total']);
            
            $este['permís'] = intval($value->permis()->select(DB::raw('SUM(TIME_TO_SEC(TIMEDIFF(fi,inici))/60) as total'))->whereBetween('data',[$dates[0]['data'],$dates->last()['data']])->first()['total']);
            $este['compensa'] = intval($value->compensa()->select(DB::raw('SUM(TIME_TO_SEC(TIMEDIFF(fi,inici))/60) as total'))->whereBetween('data',[$dates[0]['data'],$dates->last()['data']])->first()['total']);
            $este['curs'] = intval($value->curs()->select(DB::raw('SUM(TIME_TO_SEC(TIMEDIFF(fi,inici))/60) as total'))->whereBetween('data',[$dates[0]['data'],$dates->last()['data']])->first()['total']);
            $este['visita'] = intval($value->visita()->select(DB::raw('SUM(TIME_TO_SEC(TIMEDIFF(fi,inici))/60) as total'))->whereBetween('data',[$dates[0]['data'],$dates->last()['data']])->first()['total']);
            
            if ($mes == 5 || $mes == 10){
                $mosc1 = $value->moscoso()->whereBetween('data',[$any."-".$mes."-01",$any."-".$mes."-15"])->count()*(26100/60); 
                $vac1 = $value->vacances()->whereBetween('data',[$any."-".$mes."-01",$any."-".$mes."-15"])->count()*(26100/60);
                $mosc2 = $value->moscoso()->whereBetween('data',[$any."-".$mes."-16",date("Y-m-t", strtotime($any."-".$mes."-16"))])->count()*(27900/60); 
                $vac2 = $value->vacances()->whereBetween('data',[$any."-".$mes."-16",date("Y-m-t", strtotime($any."-".$mes."-16"))])->count()*(27900/60);           
                $este['moscosos'] = $mosc1 + $mosc2; 
                $este['vacances'] = $vac1 + $vac2;
            
            } else {
                $este['moscosos'] = $value->moscoso()->whereBetween('data',[$dates[0]['data'],$dates->last()['data']])->count()*$total_dia; 
                $este['vacances'] = $value->vacances()->whereBetween('data',[$dates[0]['data'],$dates->last()['data']])->count()*$total_dia;
            }
            


            $este['total'] = $este['fitxatge']+ $este['permís']+$este['compensa']+$este['curs']+$este['visita']+$este['moscosos']+$este['vacances'];
            $este['diferència'] = $este['total'] - $total_mes;
            array_push($a,$este);
        }


        return $a;

    }


}