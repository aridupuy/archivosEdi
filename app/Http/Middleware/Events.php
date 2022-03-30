<?php

namespace App\Http\Middleware;
use App\Http\Controllers\Controller;
use Closure;
use Model;
//use Token;
require_once '../database/factories/inicializador_db_efectivo.php';
class Events
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(\Illuminate\Http\Request $request, Closure $next)
    {
        
        Model::singleton();
        Controller::cargar_parametros($request->json()->all());
        if($request)
            return $next($request)->header("Access-Control-Allow-Origin", "*")
            ->header("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS")
            ->header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization");
        else return $next()->header("Access-Control-Allow-Origin", "*")
            ->header("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE, OPTIONS")
            ->header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization");
    }
}

