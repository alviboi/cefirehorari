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
//use App\Controllers\VacancesController;
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

        $labels = ['fitxatges', 'permís', 'compensa', 'curs', 'com. serv.'];

        $cefire = user::find(auth()->id())->cefire()->whereBetween('data', [$desde, $fins])->get();
        $permis = user::find(auth()->id())->permis()->whereBetween('data', [$desde, $fins])->get();
        $compensa = user::find(auth()->id())->compensa()->whereBetween('data', [$desde, $fins])->get();
        $curs = user::find(auth()->id())->curs()->whereBetween('data', [$desde, $fins])->get();
        $visita = user::find(auth()->id())->visita()->whereBetween('data', [$desde, $fins])->get();

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
        $total_vis = 0;
        foreach ($visita as $cu) {
            $in = Carbon::parse($cu->inici);
            $fi = Carbon::parse($cu->fi);
            $duration = $in->diffInMinutes($fi);
            $total_vis = $total_vis + $duration;
        }
        $datos = [round($total_cef / 60, 2), round($total_per / 60, 2), round($total_comp / 60, 2), round($total_curs / 60, 2), round($total_vis / 60, 2)];

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

        //Control de usuatis perfil


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
        //abort(403, gettype(auth()->id()));



        if ($request->id == auth()->id() || auth()->user()->Perfil == 1) {
            $user = User::find($request->id);
            $user->name = $request->nom;
            $user->email = $request->mail;
            if (auth()->user()->Perfil != 1 && ($request->perfil != $user->Perfil)) {
                abort(403, "No tens permisos per a canviar eixos paràmetres");
            } else {
                $user->Perfil = $request->perfil;
                $user->reduccio = $request->reduccio ? 1 : 0;
                $user->rfid = $request->rfid;
            }

            if ($request->moscosos != null && auth()->user()->Perfil == 1) {
                $user->moscosos = $request->moscosos;
            }
            if ($request->vacances != null && auth()->user()->Perfil == 1) {
                $user->vacances = $request->vacances;
            }
            if ($request->contrasenya != "") {
                $user->password = Hash::make($request->contrasenya);
            }
            $user->update();
            return "Dades actualitzades";
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
        $cefire['incidencies'] = User::find(auth()->id())->incidencies()->where('data', '=', $dia)->get();
        $ret2 = User::find(auth()->id())->cefire()->where('data', '=', $dia)->get();

        foreach ($ret2 as $value) {
            $ret1 = array("id" => $value->id, "user_id" => $value->user_id, "inici" => $value->inici->format('H:i:s'), "fi" => $value->fi->format('H:i:s'));
            array_push($ret3, $ret1);
        }
        $cefire['cefire'] = $ret3;
        $cefire['moscosos'] = User::find(auth()->id())->moscoso()->where('data', '=', $dia)->get();

        $vac = new VacancesOficialsController();

        $cefire['vac_oficials'] = $vac->es_vacances($dia);


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
        for ($i = 0; $i < count($ret); $i++) {
            # code...
            $ret[$i]['inici'] = "Tot el dia";
            $ret[$i]['fi'] = "";
            $ret[$i]['concepte'] = "Vacances";
        }

        return $ret;

    }

    public function get_moscosos(Request $request, $num, $any, $mes)
    {
        //
        $ret = User::find($num)->moscoso()->whereMonth('data', '=', date($mes))->whereYear('data', '=', date($any))->get();
        for ($i = 0; $i < count($ret); $i++) {
            # code...
            $ret[$i]['inici'] = "Tot el dia";
            $ret[$i]['fi'] = "";
            $ret[$i]['moscoso'] = "Vacances";
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
        
        $vacances_con = new VacancesController();
        //VacancesController

        $year = date('Y');
        $mes = date('m');
        $inici = date($year . "-" . $mes . "-1");
        $fi = date($year . "-" . $mes . "-t");

        $dates = $vacances_con->getWorkingDays($inici, $fi);

        //$dates = cefire::select('data')->distinct()->whereBetween('data',[$inici,$fi])->orderBy('data', 'ASC')->get();

        $total_dies = count($dates);
        $usuari = user::where("id", "=", auth()->id())->first();

        $data_hui = date('Y-m-d');
        $any = date('Y');
        $data_15_oct = date($any . "-10-15");
        $data_15_mai = date($any . "-05-15");


        $total_mes = 0;
       
        $ix=1;
        $total_dia=0;
        $horari_especial = new HorariespecialController();
        $dates_especials_arr = $horari_especial->index_en_dif();
        foreach ($dates as $key => $value) {
            # code...
                foreach ($dates_especials_arr as $key2 => $value2) {
                    $ix = 1;
                    # code...
                    if ($value2["dia"] == $value){
                        //$total_dia = $value2['total'];
                        $total_mes += $value2['total'];
                        $ix = 0;
                        break;
                    } else if ($data_hui >= $data_15_oct || $data_hui <= $data_15_mai) {
                        $total_dia = (27900 / 60);
                    } else {
                        $total_dia = (26100 / 60);
                    }
                }
            if ($ix){
                if ($value >= $data_15_oct || $value <= $data_15_mai) {
                    $total_dia = (27900 / 60);
                    $total_mes += (27900 / 60);
                } else {
                    $total_dia = (26100 / 60);
                    $total_mes += (26100 / 60);
                }

            }
 
        }

        $este = $this->agafa_dades_suma($usuari, $mes, $any, $inici, $fi, $total_mes, $total_dia, $total_dies);
        unset($este["Nom"]);



        return $este;

    }
    public function tots_els_dies_mes($any, $mes)
    {

        $vacances = new VacancesController();
        //VacancesController
        

        $inici = date($any . "-" . $mes . "-01");
        $fi = date("Y-m-t", strtotime($inici));

        $dates = $vacances->getWorkingDays($inici, $fi);

        //$dates = cefire::select('data')->distinct()->whereBetween('data',[$inici,$fi])->orderBy('data', 'ASC')->get();

        if (count($dates) == 0) {
            abort(403, "No hi ha cap resultat");
        }
        $total_dies = count($dates);
        $usuaris = User::orderBy('name', 'ASC')->get();

        $ix=1;
        $este = array();
        $a = array();

        $data_hui = date('Y-m-d');
        $any = date('Y');
        $data_15_oct = date($any . "-10-15");
        $data_15_mai = date($any . "-05-15");

        $total_mes = 0;
        // foreach ($dates as $key => $value) {
        //     # code...
        //     if ($value >= $data_15_oct || $value <= $data_15_mai) {
        //         $total_mes += (27900 / 60);
        //     } else {
        //         $total_mes += (26100 / 60);
        //     }

        // }

        // if ($data_hui >= $data_15_oct || $data_hui <= $data_15_mai) {
        //     $total_dia = (27900 / 60);
        // } else {
        //     $total_dia = (26100 / 60);
        // }

        $horari_especial = new HorariespecialController();
        $dates_especials_arr = $horari_especial->index_en_dif();
            foreach ($dates as $key => $value) {
            # code...
                foreach ($dates_especials_arr as $key2 => $value2) {
                    $ix = 1;
                    # code...
                    if ($value2["dia"] == $value){
                        $total_dia = $value2['total'];
                        $total_mes += $value2['total'];
                        $ix = 0;
                        break;
                    } else if ($data_hui >= $data_15_oct || $data_hui <= $data_15_mai) {
                        $total_dia = (27900 / 60);
                    } else {
                        $total_dia = (26100 / 60);
                    }
                }
                if ($ix){
                    if ($value >= $data_15_oct || $value <= $data_15_mai) {
                        $total_dia = (27900 / 60);
                        $total_mes += (27900 / 60);
                    } else {
                        $total_dia = (26100 / 60);
                        $total_mes += (26100 / 60);
                    }

                }
 
        }

        

        foreach ($usuaris as $key => $value) {
            # code...
            // Anar agafant-ho tot per dia
            $este = $this->agafa_dades_suma($value, $mes, $any, $inici, $fi, $total_mes, $total_dia, $total_dies);
            array_push($a, $este);
        }


        return $a;

    }

    function agafa_dades_suma($value, $mes, $any, $inici, $fi, $total_mes, $total_dia, $total_dies)
    {
        $este = array();
        $desde_any = date($any . "-1-1");
        $fins_any = date($any . "-12-31");
        $este['Nom'] = $value->name;
        $este['fitxatge'] = intval($value->cefire()->select(DB::raw('SUM(TIME_TO_SEC(TIMEDIFF(fi,inici))/60) as total'))->whereBetween('data', [$inici, $fi])->where('fi', '!=', '00:00:00')->first()['total']);

        $este['permís'] = intval($value->permis()->select(DB::raw('SUM(TIME_TO_SEC(TIMEDIFF(fi,inici))/60) as total'))->whereBetween('data', [$inici, $fi])->first()['total']);
        $este['compensa'] = intval($value->compensa()->select(DB::raw('SUM(TIME_TO_SEC(TIMEDIFF(fi,inici))/60) as total'))->whereBetween('data', [$inici, $fi])->first()['total']);
        $este['curs'] = intval($value->curs()->select(DB::raw('SUM(TIME_TO_SEC(TIMEDIFF(fi,inici))/60) as total'))->whereBetween('data', [$inici, $fi])->first()['total']);
        $este['com.serv.'] = intval($value->visita()->select(DB::raw('SUM(TIME_TO_SEC(TIMEDIFF(fi,inici))/60) as total'))->whereBetween('data', [$inici, $fi])->first()['total']);


        
        if ($mes == 5 || $mes == 10) {
            $mosc1 = $value->moscoso()->whereBetween('data', [$any . "-" . $mes . "-01", $any . "-" . $mes . "-15"])->count() * ((26100 / 60) - $value->reduccio * 60);
            $vac1 = $value->vacances()->whereBetween('data', [$any . "-" . $mes . "-01", $any . "-" . $mes . "-15"])->count() * ((26100 / 60) - $value->reduccio * 60);
            $mosc2 = $value->moscoso()->whereBetween('data', [$any . "-" . $mes . "-16", date("Y-m-t", strtotime($any . "-" . $mes . "-16"))])->count() * ((27900 / 60) - $value->reduccio * 60);
            $vac2 = $value->vacances()->whereBetween('data', [$any . "-" . $mes . "-16", date("Y-m-t", strtotime($any . "-" . $mes . "-16"))])->count() * ((27900 / 60) - $value->reduccio * 60);
            $este['moscosos'] = $mosc1 + $mosc2;
            $este['vacances'] = $vac1 + $vac2;

        } else {
            $este['moscosos'] = $value->moscoso()->whereBetween('data', [$inici, $fi])->count() * ($total_dia - $value->reduccio * 60);
            
            $este['vacances'] = $value->vacances()->whereBetween('data', [$inici, $fi])->count() * ($total_dia - $value->reduccio * 60);
            
        }


        $horari_especial = new HorariespecialController();
        $dates_especials_arr = $horari_especial->get_data();
        $horari_especial_dif = $horari_especial->index_en_dif();

        $esp = array();
        


        $esp['fitxatge'] = $value->cefire()->whereBetween('data', [$inici, $fi])->whereIn('data',$dates_especials_arr)->where('fi', '!=', '00:00:00')->get();
        $esp['permís'] = $value->permis()->whereBetween('data', [$inici, $fi])->whereIn('data',$dates_especials_arr)->get();
        $esp['compensa'] = $value->compensa()->whereBetween('data', [$inici, $fi])->whereIn('data',$dates_especials_arr)->get();
        $esp['curs'] = $value->curs()->whereBetween('data', [$inici, $fi])->whereIn('data',$dates_especials_arr)->get();
        $esp['com.serv.'] = $value->visita()->whereBetween('data', [$inici, $fi])->whereIn('data',$dates_especials_arr)->get();
        $esp['moscosos'] = $value->moscoso()->whereBetween('data', [$inici, $fi])->whereIn('data',$dates_especials_arr)->get();
        $esp['vacances'] = $value->vacances()->whereBetween('data', [$inici, $fi])->whereIn('data', $dates_especials_arr)->get();
        
        $canvi_vacances=0;
        $canvi_moscosos=0;
        $canvi_com=0;
        $canvi_curs=0;
        $canvi_compensa=0;
        $canvi_permis=0;
        $canvi_fitxatge=0;

        # Ajusta els valors de la estadística dels tags que són dies sencers i en permís (per si algú posa més) a l'horari especials

        foreach ($horari_especial_dif as $key => $value1) {
            # code...
            for ($i=0; $i < sizeof($esp['vacances']); $i++) { 
                # code...
                if($esp['vacances'][$i]['data'] == $value1['dia']){
                    $canvi_vacances += $total_dia-$value1['total'];
                }
            }

            for ($i=0; $i < sizeof($esp['moscosos']); $i++) { 
                # code...
                if($esp['moscosos'][$i]['data'] == $value1['dia']){
                    $canvi_moscosos += $total_dia-$value1['total'];
                }
            }

            // for ($i=0; $i < sizeof($esp['com.serv.']); $i++) { 
            //     # code...
            //     if($esp['com.serv.'][$i]['data'] == $value1['dia']){
            //         $canvi_com += $total_dia-$value1['total'];
            //     }
            // }

            // for ($i=0; $i < sizeof($esp['curs']); $i++) { 
            //     # code...
            //     if($esp['curs'][$i]['data'] == $value1['dia']){
            //         $canvi_curs += $total_dia-$value1['total'];
            //     }
            // }

            // for ($i=0; $i < sizeof($esp['compensa']); $i++) { 
            //     # code...
            //     if($esp['compensa'][$i]['data'] == $value1['dia']){
            //         $canvi_compensa += $total_dia-$value1['total'];
            //     }
            // }

            for ($i=0; $i < sizeof($esp['permís']); $i++) { 
                # code...
                if($esp['permís'][$i]['data'] == $value1['dia']){
                    $canvi_permis += $total_dia-$value1['total'];
                }
            }

            // for ($i=0; $i < sizeof($esp['fitxatge']); $i++) { 
            //     # code...
            //     if($esp['fitxatge'][$i]['data'] == $value1['dia']){
            //         $canvi_fitxatge += $total_dia-$value1['total'];
            //     }
            // }
        }

        //abort(403,"Dia ".$canvi_vacances);

        $este['vacances'] -= $canvi_vacances;
        $este['moscosos'] -= $canvi_moscosos;
        $este['com.serv.'] -= $canvi_com;
        $este['curs'] -= $canvi_curs;
        $este['compensa'] -= $canvi_compensa;
        $este['permís'] -= $canvi_permis;
        $este['fitxatge'] -= $canvi_fitxatge;




        $deutesmes = $value->deutesmes()->first();
        if ($deutesmes) {
            $a = $deutesmes->minuts;
            $este['recompte mesos anteriors'] = $a . " min";
        } else {
            $este['recompte mesos anteriors'] = "0 min";
        }
        
        //$este['deute mes concret'] = $this->calcula_deutes_mes_usuari_i_mes($value->id, $mes, $any);


        //$vacances = $value->vacances()->whereBetween('data', [$desde_any, $fins_any])->count();
        //        $moscosos = $value->moscoso()->whereBetween('data', [$desde_any, $fins_any])->count();

        //tema càlcul de estadística, ja que conten fins 3 mesos després

        $fins_any2 = date('Y-m-d', strtotime('+3 month' , strtotime(date($fins_any))));
        $vacances = $value->vacances()->where('created_at','>',$desde_any)->where('created_at','<',$fins_any)->whereBetween('data', [$desde_any, $fins_any2])->count();
        $moscosos = $value->moscoso()->where('created_at','>',$desde_any)->where('created_at','<',$fins_any)->whereBetween('data', [$desde_any, $fins_any2])->count();

        
        
        
        
        //2023-11-19 10:18:48
        $borsahores = $value->borsahores()->first();
        if ($borsahores) {
            $este['borsa hores'] = $borsahores->minuts . " min";
        } else {
            $este['borsa hores'] = 0;
        }


        $moscosos_pendents = $value->vacancespendents()->first();
        $vacances_pendents = $value->vacancespendents()->first();
        //abort(413,$moscosos_pendents)->toArray();
        if ($moscosos_pendents === null) { $moscosos_pendents_num=0;} else { $moscosos_pendents_num=$moscosos_pendents->dies_sobrants_moscosos; }
        if ($vacances_pendents === null) { $vacances_pendents_num=0;} else { $vacances_pendents_num=$vacances_pendents->dies_sobrants_vacances; }



        $este['moscosos (any)'] = $moscosos . " de " . $value->moscosos . " + (".$moscosos_pendents_num .") cons";
        $este['vacances (any)'] = $vacances . " de " . $value->vacances . " + (".$vacances_pendents_num .") cons";


        $este['total'] = $este['fitxatge'] + $este['permís'] + $este['compensa'] /*Es suma perquè les està gaudint d'un excés que ha fet altre mes*/+ $este['curs'] + $este['com.serv.'] + $este['moscosos'] + $este['vacances'];


        $este['diferència'] = ($este['total']) - $total_mes + ($value->reduccio * 60 * $total_dies); //El total dels dies del mes multiplicat per 60
        return $este;
    }


    function calcula_deutes_mes_tots_els_usuaris()
    {

    }
    /**
     * Aquesta funció, pot ser que no s'estiga utilitzant, es pot utilitzat per a la de dalt
     * TODO: Funció de dalt
     * @param mixed $fi_opt
     * @return array
     */

    function calcula_deutes_mes_un_usuari($fi_opt = null)
    {
        //$any, $mes
        $vacances = new VacancesController();
        //VacancesController
        $any = date("Y", strtotime("-1 months"));
        $mes = date("m", strtotime("-30 days"));


        $inici = date($any . "-" . $mes . "-01");
        if ($fi_opt === null) {
            $fi = date("Y-m-t", strtotime($inici));
        } else {
            $fi = date($fi_opt);
        }

        $dates = $vacances->getWorkingDays($inici, $fi);

        //$dates = cefire::select('data')->distinct()->whereBetween('data',[$inici,$fi])->orderBy('data', 'ASC')->get();

        if (count($dates) == 0) {
            abort(403, "No hi ha cap resultat");
        }
        $total_dies = count($dates);
        $usuari = User::where('id', "=", auth()->id())->first();


        $este = array();
        //$a = array();

        $data_hui = date('Y-m-d');
        $any = date('Y');
        $data_15_oct = date($any . "-10-15");
        $data_15_mai = date($any . "-05-15");

        $total_mes = 0;
        foreach ($dates as $key => $value) {
            # code...
            if ($value >= $data_15_oct || $value <= $data_15_mai) {
                $total_mes += (27900 / 60);
            } else {
                $total_mes += (26100 / 60);
            }

        }
        if ($data_hui >= $data_15_oct || $data_hui <= $data_15_mai) {
            $total_dia = (27900 / 60);
        } else {
            $total_dia = (26100 / 60);
        }

        $este = $this->agafa_dades_suma($usuari, $mes, $any, $inici, $fi, $total_mes, $total_dia, $total_dies);
        //$este['diferència'] = 100;
        //$este["proves"]=$mes;
        $este["proves2"]=date("Y-m-d");
        return $este;

    }

    /**
     * 
     * Aquesta funció és la mateixa que la de dalt, però hem modificat que accepte el id de l'usuari
     * @param mixed $user_id
     * @param mixed $fi_opt
     * @return array
     */
    function calcula_deutes_mes_usuari($user_id, $fi_opt = null)
    {
        //$any, $mes
        $vacances = new VacancesController();
        //VacancesController
        $any = date("Y", strtotime("-1 months"));
        $mes = date("m", strtotime("-1 months"));


        $inici = date($any . "-" . $mes . "-01");
        if ($fi_opt === null) {
            $fi = date("Y-m-t", strtotime($inici));
        } else {
            $fi = date($fi_opt);
        }

        $dates = $vacances->getWorkingDays($inici, $fi);

        //$dates = cefire::select('data')->distinct()->whereBetween('data',[$inici,$fi])->orderBy('data', 'ASC')->get();

        if (count($dates) == 0) {
            abort(403, "No hi ha cap resultat");
        }
        $total_dies = count($dates);
        $usuari = User::where('id', "=", $user_id)->first();


        $este = array();
        //$a = array();

        $data_hui = date('Y-m-d');
        $any = date('Y');
        $data_15_oct = date($any . "-10-15");
        $data_15_mai = date($any . "-05-15");

        // $total_mes = 0;
        // foreach ($dates as $key => $value) {
        //     # code...
        //     if ($value >= $data_15_oct || $value <= $data_15_mai) {
        //         $total_mes += (27900 / 60);
        //     } else {
        //         $total_mes += (26100 / 60);
        //     }

        // }
        // if ($data_hui >= $data_15_oct || $data_hui <= $data_15_mai) {
        //     $total_dia = (27900 / 60);
        // } else {
        //     $total_dia = (26100 / 60);
        // }
        $total_mes = 0;
        $ix=1;
        $total_dia=0;
        $horari_especial = new HorariespecialController();
        $dates_especials_arr = $horari_especial->index_en_dif();
            foreach ($dates as $key => $value) {
            # code...
                foreach ($dates_especials_arr as $key2 => $value2) {
                    $ix = 1;
                    # code...
                    if ($value2["dia"] == $value){
                        $total_dia = $value2['total'];
                        $total_mes += $value2['total'];
                        $ix = 0;
                        break;
                    } else if ($data_hui >= $data_15_oct || $data_hui <= $data_15_mai) {
                        $total_dia = (27900 / 60);
                    } else {
                        $total_dia = (26100 / 60);
                    }
                }
            if ($ix){
                if ($value >= $data_15_oct || $value <= $data_15_mai) {
                    $total_dia = (27900 / 60);
                    $total_mes += (27900 / 60);
                } else {
                    $total_dia = (26100 / 60);
                    $total_mes += (26100 / 60);
                }

            }
 
        }

        $este = $this->agafa_dades_suma($usuari, $mes, $any, $inici, $fi, $total_mes, $total_dia, $total_dies);
        //$este['diferència'] = 100;
        return $este;

    }


    /**
     * 
     * Aquesta funció és la mateixa que la de dalt, però hem modificat que accepte el id de l'usuari
     * @param mixed $user_id
     * @param mixed $fi_opt
     * @return array
     */
    function calcula_deutes_mes_usuari_i_mes($user_id, $mes, $any)
    {
        //$any, $mes
        $vacances = new VacancesController();

        $inici = date($any . "-" . $mes . "-01");
        $fi = date("Y-m-t", strtotime($inici));

        $dates = $vacances->getWorkingDays($inici, $fi);

        //$dates = cefire::select('data')->distinct()->whereBetween('data',[$inici,$fi])->orderBy('data', 'ASC')->get();

        if (count($dates) == 0) {
            abort(403, "No hi ha cap resultat");
        }
        $total_dies = count($dates);
        $usuari = User::where('id', "=", $user_id)->first();


        $este = array();
        //$a = array();

        $data_15_oct = date($any . "-10-15");
        $data_15_mai = date($any . "-05-15");

        $total_mes = 0;
        $ix=1;
        $total_dia=0;
        $horari_especial = new HorariespecialController();
        $dates_especials_arr = $horari_especial->index_en_dif();
            foreach ($dates as $key => $value) {
            # code...
                foreach ($dates_especials_arr as $key2 => $value2) {
                    $ix = 1;
                    # code...
                    if ($value2["dia"] == $value){
                        $total_dia = $value2['total'];
                        $total_mes += $value2['total'];
                        $ix = 0;
                        break;
                    } else if ($value >= $data_15_oct || $value <= $data_15_mai) {
                        $total_dia = (27900 / 60);
                    } else {
                        $total_dia = (26100 / 60);
                    }
                }
            if ($ix){
                if ($value >= $data_15_oct || $value <= $data_15_mai) {
                    $total_mes += (27900 / 60);
                } else {
                    $total_mes += (26100 / 60);
                }

            }
 
        }

        $este = $this->agafa_dades_suma2($usuari, $mes, $any, $inici, $fi, $total_mes, $total_dia, $total_dies);
        return $este['diferència'];

    }

    function agafa_dades_suma2($value, $mes, $any, $inici, $fi, $total_mes, $total_dia, $total_dies)
    {
        $este = array();
        $desde_any = date($any . "-1-1");
        $fins_any = date($any . "-12-31");
        $este['Nom'] = $value->name;
        $este['fitxatge'] = intval($value->cefire()->select(DB::raw('SUM(TIME_TO_SEC(TIMEDIFF(fi,inici))/60) as total'))->whereBetween('data', [$inici, $fi])->where('fi', '!=', '00:00:00')->first()['total']);

        $este['permís'] = intval($value->permis()->select(DB::raw('SUM(TIME_TO_SEC(TIMEDIFF(fi,inici))/60) as total'))->whereBetween('data', [$inici, $fi])->first()['total']);
        $este['compensa'] = intval($value->compensa()->select(DB::raw('SUM(TIME_TO_SEC(TIMEDIFF(fi,inici))/60) as total'))->whereBetween('data', [$inici, $fi])->first()['total']);
        $este['curs'] = intval($value->curs()->select(DB::raw('SUM(TIME_TO_SEC(TIMEDIFF(fi,inici))/60) as total'))->whereBetween('data', [$inici, $fi])->first()['total']);
        $este['com.serv.'] = intval($value->visita()->select(DB::raw('SUM(TIME_TO_SEC(TIMEDIFF(fi,inici))/60) as total'))->whereBetween('data', [$inici, $fi])->first()['total']);

        if ($mes == 5 || $mes == 10) {
            $mosc1 = $value->moscoso()->whereBetween('data', [$any . "-" . $mes . "-01", $any . "-" . $mes . "-15"])->count() * (26100 / 60);
            $vac1 = $value->vacances()->whereBetween('data', [$any . "-" . $mes . "-01", $any . "-" . $mes . "-15"])->count() * (26100 / 60);
            $mosc2 = $value->moscoso()->whereBetween('data', [$any . "-" . $mes . "-16", date("Y-m-t", strtotime($any . "-" . $mes . "-16"))])->count() * (27900 / 60);
            $vac2 = $value->vacances()->whereBetween('data', [$any . "-" . $mes . "-16", date("Y-m-t", strtotime($any . "-" . $mes . "-16"))])->count() * (27900 / 60);
            $este['moscosos'] = $mosc1 + $mosc2;
            $este['vacances'] = $vac1 + $vac2;

        } else {
            $este['moscosos'] = $value->moscoso()->whereBetween('data', [$inici, $fi])->count() * $total_dia;
            $este['vacances'] = $value->vacances()->whereBetween('data', [$inici, $fi])->count() * $total_dia;
        }
        
        $este['total'] = $este['fitxatge'] + $este['permís'] + $este['compensa'] /*Es suma perquè les està gaudint d'un excés que ha fet altre mes*/+ $este['curs'] + $este['com.serv.'] + $este['moscosos'] + $este['vacances'];


        $este['diferència'] = ($este['total']) - $total_mes + ($value->reduccio * 60 * $total_dies); //El total dels dies del mes multiplicat per 60
        return $este;
    }



}