<?php

use Illuminate\Support\Facades\Route;

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
include __DIR__.'/../database/factories/inicializador_db.php';
//error_log("registrando eventos web");

Route::get('/', function () {
    return view('index');
});

////Route::get("validaMail/{params}",'Web\ValidaMailAfterWeb@validar');
//Route::get("validaMail/",'Web\ValidaMailAfterWeb@validar');
//Route::post("events/debinacred",'Events\EventsController@debinAcred')->middleware("Events");
//Route::post("events/transfcvurecibida",'Events\EventsController@transferenciaCVURecibida')->middleware("Events");
//Route::get("events/transfcvurecibida",'Events\EventsController@transferenciaCVURecibidaGet')->middleware("Events");
//Route::get("events/debinacred",'Events\EventsController@debinAcredGet')->middleware("Events");
//Route::post("/notificaciones/depositosCVU",'Events\EventsController@pei_deposito')->middleware("Events");
//Route::post("/notificaciones/creditosCVU",'Events\EventsController@pei_transferencia')->middleware("Events");
////Route::get("/{any}",function () {
////    return view('index');
////})->where("any",".*");
//
//Route::get("/{any}",function () {
//    return view('index');
//})->where("any",".*");

