<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Models\User;
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
    public function home (){
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
    public function contar ($desde,$fins) {

        $labels=['cefire','permis','compensa','curs'];

        $cefire=user::find(auth()->id())->cefire()->whereBetween('data', [$desde, $fins])->get();
        $permis=user::find(auth()->id())->permis()->whereBetween('data', [$desde, $fins])->get();
        $compensa=user::find(auth()->id())->compensa()->whereBetween('data', [$desde, $fins])->get();
        $curs=user::find(auth()->id())->curs()->whereBetween('data', [$desde, $fins])->get();

        $total_cef=0;
        foreach($cefire as $cef){
            $duration = $cef->inici->diffInMinutes($cef->fi);
            $total_cef=$total_cef+$duration;
        }

        $total_per=0;
        foreach($permis as $perm){
            $in = Carbon::parse($perm->inici);
            $fi = Carbon::parse($perm->fi);
            $duration = $in->diffInMinutes($fi);
            $total_per=$total_per+$duration;
        }

        $total_comp=0;
        foreach($compensa as $comp){
            $in = Carbon::parse($comp->inici);
            $fi = Carbon::parse($comp->fi);
            $duration = $in->diffInMinutes($fi);
            $total_comp=$total_comp+$duration;
        }

        $total_curs=0;
        foreach($curs as $cu){
            $in = Carbon::parse($cu->inici);
            $fi = Carbon::parse($cu->fi);
            $duration = $in->diffInMinutes($fi);
            $total_curs=$total_curs+$duration;
        }
        $datos = [round($total_cef/60,2),round($total_per/60,2),round($total_comp/60,2),round($total_curs/60,2)];

        $ret = array('labels' => $labels, 'datos' => $datos);
        return ($ret);
    }

    public function logat () {
        return user::find(auth()->id())->name;
    }

    public function logat_id () {
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
        $message="";
        for ($i=0; $i < count($input); $i++) {
            $user_comp=User::where('email','=',$input[$i]['email'])->first();
            if($user_comp != null){
                $message=$message." ".$user_comp->email;
            } else {
                $dat = new User();
                $dat->name=$input[$i]['name'];
                $dat->email=$input[$i]['email'];
                $dat->Perfil = 0;
                $passwd = Str::random(5);
                $dat->password=Hash::make($passwd);
                $dat->save();
                $emailJob = (new SendPasswordMail($input[$i]['email'],$passwd))->delay(Carbon::now()->addSeconds(120));
                dispatch($emailJob);
            }
        }
        if ($message==""){
            return "Tots els usuaris s'han creat";
        } else {
            return "Els usuaris amb mail: ".$message.", ja estan creats, per tant no s'han tornat a crear";
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
            $pwd=$request->contrasenya;
            $conn = ldap_connect($request->ip, '389');
            $bindDn = "uid=netadmin,ou=Admins,ou=People,dc=ma5,dc=lliurex,dc=net";
            ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);

            $ldaptree="ou=Teachers,ou=People,dc=ma5,dc=lliurex,dc=net";


            if($conn) {

                $ldapbind = ldap_bind($conn, $bindDn, $pwd) or die ("Ha hagut un error: ".ldap_error($conn));

                if ($ldapbind) {

                    $result = ldap_search($conn,$ldaptree, "(cn=*)") or die ("Ha hagut un error: ".ldap_error($conn));
                    $data = ldap_get_entries($conn, $result);

                    $usuaris=array();
                    for ($i=0; $i<$data["count"]; $i++) {
                        $el=['email' => $data[$i]["uid"][0], 'name' =>  $data[$i]["description"][0]];
                        array_push($usuaris,$el);
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
        if ($request->id == auth()->id() || auth()->user()->Perfil == 1){
            $user=User::find($request->id);
            $user->name=$request->nom;
            $user->email=$request->mail;
            $user->Perfil=$request->perfil;
            $user->rfid=$request->rfid;
            if($user->contrasenya != ""){
                $user->password=$request->contrasenya;
            }
            $user->save();
        } else {
            abort(403,"No tens permís per a realitzar aquesta acció");
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
    public function dia_cefire($dia,$mati)
    {
        //
        if ($mati == 'm') $control='<'; else $control='>';
        $cefire = User::find(auth()->id())->cefire()->where('data','=',$dia)->where('fi',$control,'15:00:00')->get();
        $ret2=array();
        foreach ($cefire as  $value) {
            $ret1 = array("id" => $value->id, "user_id" => $value->user_id,"inici" => $value->inici->format('H:i:s'),"fi" => $value->fi->format('H:i:s'));
            array_push($ret2,$ret1);

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
    public function dia_guardia($dia,$mati)
    {
        //
        if ($mati == 'm') $control='<'; else $control='>';
        $cefire = User::find(auth()->id())->guardia()->where('data','=',$dia)->where('fi',$control,'15:00:00')->get();
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
    public function dia_incidencies($dia,$mati)
    {
        //
        if ($mati == 'm') $control='<'; else $control='>';
        $incidencies = User::find(auth()->id())->incidencies()->where('data','=',$dia)->where('fi',$control,'15:00:00')->get();
        return $incidencies;
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
    public function dia_curs($dia,$mati)
    {
        //
        if ($mati == 'm') $control='<'; else $control='>';
        $cefire = User::find(auth()->id())->curs()->where('data','=',$dia)->where('fi',$control,'15:00:00')->get();
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
    public function dia_compensa($dia,$mati)
    {
        //
        if ($mati == 'm') $control='<'; else $control='>';
        $cefire = User::find(auth()->id())->compensa()->where('data','=',$dia)->where('fi',$control,'15:00:00')->get();
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
    public function dia_visita($dia,$mati)
    {
        //
        if ($mati == 'm') $control='<'; else $control='>';
        $cefire = User::find(auth()->id())->visita()->where('data','=',$dia)->where('fi',$control,'15:00:00')->get();
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
    public function dia_permis($dia,$mati)
    {
        //
        if ($mati == 'm') $control='<'; else $control='>';
        $cefire = User::find(auth()->id())->permis()->where('data','=',$dia)->where('fi',$control,'15:00:00')->get();
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
    public function get_cefire(Request $request, $num,$any,$mes)
    {
        //
        $cefire=User::find($num)->cefire()->whereMonth('data', '=', date($mes))->whereYear('data', '=', date($any))->get();
        $ret2=array();
        foreach ($cefire as  $value) {
            # code...
            $ret1 = array("data"=>$value->data ,"id" => $value->id, "user_id" => $value->user_id,"inici" => $value->inici->format('H:i:s'),"fi" => $value->fi->format('H:i:s'));
            array_push($ret2,$ret1);
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

    public function get_guardia(Request $request, $num,$any,$mes)
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

    public function get_curs(Request $request, $num,$any,$mes)
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

    public function get_compensa(Request $request, $num,$any,$mes)
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

    public function get_visita(Request $request, $num,$any,$mes)
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

    public function get_permis(Request $request, $num,$any,$mes)
    {
        //
        return User::find($num)->permis()->whereMonth('data', '=', date($mes))->whereYear('data', '=', date($any))->get();
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
    public function get_all($de,$fins)
    {
        //
        $cefire = User::find(auth()->id())->cefire()->where('data','>',$de)->where('data','<',$fins)->get();
        $compensa = User::find(auth()->id())->compensa()->where('data','>',$de)->where('data','<',$fins)->get();
        $curs = User::find(auth()->id())->curs()->where('data','>',$de)->where('data','<',$fins)->get();
        $guardia = User::find(auth()->id())->guardia()->where('data','>',$de)->where('data','<',$fins)->get();
        $permis = User::find(auth()->id())->permis()->where('data','>',$de)->where('data','<',$fins)->get();
        $visita = User::find(auth()->id())->visita()->where('data','>',$de)->where('data','<',$fins)->get();
        $ret=[
            'cefire' => $cefire,
            'compensa' => $compensa,
            'curs' => $curs,
            'guardia' => $guardia,
            'permis' => $permis,
            'visita' => $visita
        ];
        return $ret;



    }
}
