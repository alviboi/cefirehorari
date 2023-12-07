<?php

//use App\Http\Controllers\BorsaSolicitudsController;
//use App\Models\BorsaSolicituds;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use App\Models\control;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Comprovem si el registre està habilitat per a mostrar-se o no, comprovem també si la taula existeix ja que quan corres
//l'artisan per primera vegada llig el web.php i dona un error si control no existeix.
//View::share('title', 'My Title Here');


if (Schema::hasTable('control')) {
    if (control::where("registra", "=", 1)->exists()) {
        Auth::routes();
    } else {
        $registra = false;
        Auth::routes([
            'register' => false,
        ]);
    }
}

// Route::get('/login', function () {
//     return view('login');
// })->name('login');

$data = array(
    'title' => 'Home',
    'otherData' => 'Data Here'
);


Route::get('/', function () {
    return view('welcome');
})->name('entrada');


Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    return "Cache is cleared";
});


Route::view('/consultacentres', 'centres')->name('centresconsulta');


Route::get('/home', 'UserController@home')->middleware('auth');

Route::get('/logat', 'UserController@logat')->middleware('auth');
Route::get('/logat_id', 'UserController@logat_id')->middleware('auth');

Route::get('/user_cefire/{num}/{any}/{mes}', 'UserController@get_cefire')->middleware('auth');
Route::get('/user_compensa/{num}/{any}/{mes}', 'UserController@get_compensa')->middleware('auth');
Route::get('/user_curs/{num}/{any}/{mes}', 'UserController@get_curs')->middleware('auth');
Route::get('/user_guardia/{num}/{any}/{mes}', 'UserController@get_guardia')->middleware('auth');
Route::get('/user_permis/{num}/{any}/{mes}', 'UserController@get_permis')->middleware('auth');
Route::get('/user_visita/{num}/{any}/{mes}', 'UserController@get_visita')->middleware('auth');
Route::get('/user_vacances/{num}/{any}/{mes}', 'UserController@get_vacances')->middleware('auth');
Route::get('/user_moscosos/{num}/{any}/{mes}', 'UserController@get_moscosos')->middleware('auth');
Route::get('/user_all/{de}/{fins}', 'UserController@get_all')->middleware('auth');
Route::get('/user_get', 'UserController@get_user')->middleware('auth');
Route::get('/user_statistics', 'UserController@get_statistics')->middleware('auth');
Route::get('/ultims_dies_estadistica', 'cefireController@ultims_dies_estadistica')->middleware('auth');

Route::post('/get_usuaris_ldap', 'UserController@get_usuaris_ldap')->middleware('auth');

Route::prefix('complet')->group(function () {
    Route::get('/cefire/{any}/{mes}', 'cefireController@get_data_index')->middleware('auth');
    Route::get('/compensa/{any}/{mes}', 'compensaController@get_data_index')->middleware('auth');
    Route::get('/curs/{any}/{mes}', 'cursController@get_data_index')->middleware('auth');
    Route::get('/guardia/{any}/{mes}', 'guardiaController@get_data_index')->middleware('auth');
    Route::get('/permis/{any}/{mes}', 'permisController@get_data_index')->middleware('auth');
    Route::get('/visita/{any}/{mes}', 'visitaController@get_data_index')->middleware('auth');
    Route::get('/incidencies/{any}/{mes}', 'IncidenciesController@get_data_index')->middleware('auth');
    Route::get('/vacances/{any}/{mes}', 'VacancesController@get_data_index')->middleware('auth');
    Route::get('/moscosos/{any}/{mes}', 'MoscosoController@get_data_index')->middleware('auth');
});

Route::get('/dia_cefire/{dia}', 'UserController@dia_cefire')->name('dia_cefire')->middleware('auth');
Route::get('/dia_compensa/{dia}', 'UserController@dia_compensa')->name('dia_compensa')->middleware('auth');
Route::get('/dia_curs/{dia}', 'UserController@dia_curs')->name('dia_curs')->middleware('auth');
Route::get('/dia_visita/{dia}', 'UserController@dia_visita')->name('dia_vista')->middleware('auth');
Route::get('/dia_guardia/{dia}', 'UserController@dia_guardia')->name('dia_guardia')->middleware('auth');
Route::get('/dia_permis/{dia}', 'UserController@dia_permis')->name('dia_permis')->middleware('auth');
Route::get('/dia_incidencies/{dia}', 'UserController@dia_incidencies')->name('dia_incidencies')->middleware('auth');
Route::get('/dia_moscosos/{dia}', 'UserController@dia_moscoso')->name('dia_moscoso')->middleware('auth');
Route::get('/dia_vacances/{dia}', 'UserController@dia_vacances')->name('dia_vacances')->middleware('auth');

Route::get('/dia_tot/{dia}', 'UserController@dia_tot')->name('dia_tot')->middleware('auth');

Route::get('/guardia/totes/{mes}/{any}', 'guardiaController@get_data_index2')->name('guardia_totes')->middleware('auth');
Route::get('/guardia/totes_les_guardies', 'guardiaController@get_numero_de_guardies')->name('guardia_totals')->middleware('auth');

Route::get('/contar/{desde}/{fins}', 'cefireController@contar_cefires')->name('guardia_totes_conta')->middleware('auth');

Route::get('/contar_tot/{desde}/{fins}', 'UserController@contar')->name('contar_tot')->middleware('auth');

Route::post('guardia/insert', 'guardiaController@put_guardia_id')->name('put_guardia_id')->middleware('auth');

Route::post('upload_permis', 'permisController@upload')->middleware('auth');
Route::post('download_permis', 'permisController@download')->middleware('auth');
Route::post('permis_desde', 'permisController@permis_desde')->middleware('can:esAdmin');
Route::post('permis_sense_arxiu', 'permisController@permis_sense_arxiu')->middleware('can:esAdmin');

Route::post('compensacions_no_validades', 'compensaController@compensacionsnovalidades')->middleware('can:esAdmin');
Route::post('moscosos_no_validades', 'MoscosoController@moscososnovalidades')->middleware('can:esAdmin');
Route::post('vacances_no_validades', 'VacancesController@vacancesnovalidades')->middleware('can:esAdmin');
Route::post('visita_no_validades', 'visitaController@visitanovalidades')->middleware('can:esAdmin'); //NOU

Route::post('validacompensacio', 'compensaController@validacompensacio')->middleware('can:esAdmin');
Route::post('validamoscosos', 'MoscosoController@validamoscosos')->middleware('can:esAdmin');
Route::post('validavacances', 'VacancesController@validavacances')->middleware('can:esAdmin');
Route::post('validavisita', 'visitaController@validavisita')->middleware('can:esAdmin'); //NOU
Route::post('borsasolicitudsvalida', 'BorsaSolicitudsController@borsasolicitudsvalida')->middleware('can:esAdmin'); //NOU


Route::post('afegix_minuts_admin', 'DeutesmesController@afegix_minuts_admin')->middleware('can:esAdmin'); //NOU



Route::post('cefire_fitxa_oblit', 'cefireController@cefire_fitxa_oblit')->middleware('can:esAdmin'); //NOU

Route::get('/usuaris_oblit_fitxatge', 'cefireController@usuaris_oblit_fitxatge')->name('usuaris_oblit_fitxatge')->middleware('can:esAdmin');
Route::post('/validaoblidat', 'cefireController@validaoblidat')->name('validaoblidat')->middleware('can:esAdmin');
Route::get('/tots_els_dies_mes/{any}/{mes}', 'UserController@tots_els_dies_mes')->name('tots_els_dies_mes')->middleware('can:esAdmin');
//Route::get('/vacancesoficials/{from}/{to}', 'VacancesOficialsController@agafavacancescurs')->name('agafavacancescurs')->middleware('can:esAdmin');
Route::get('/calcula_deutes_mes_un_usuari', 'UserController@calcula_deutes_mes_un_usuari')->name('calcula_deutes_mes_un_usuari')->middleware('auth');
Route::post('/minuts_a_compensar_solicitud', 'DeutesmesController@minuts_a_compensar_solicitud')->name('minuts_a_compensar_solicitud')->middleware('auth');


Route::resource('control', ControlController::class)->middleware('can:esAdmin');

Route::resource('centres', centresController::class);

Route::group(['middleware' => 'auth'], function () {
    Route::resources([
        'cefire' => cefireController::class,
        'compensa' => compensaController::class,
        'curs' => cursController::class,
        'guardia' => guardiaController::class,
        'lectura_rfid' => lectura_rfidController::class,
        'notificacions' => notificacionsController::class,
        'permis' => permisController::class,
        'user' => UserController::class,
        'visita' => visitaController::class,
        'avisos' => avisosController::class,
        'incidencies' => IncidenciesController::class,
        'moscosos' => MoscosoController::class,
        'vacances' => VacancesController::class,
        'vacancesoficials' => VacancesOficialsController::class,
        'borsahores' => BorsaHoresController::class,
        'borsasolicituds' => BorsaSolicitudsController::class,
        'deutemes' => DeutesmesController::class,
        'horariespecial' => HorariespecialController::class,
    ]);
});
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
