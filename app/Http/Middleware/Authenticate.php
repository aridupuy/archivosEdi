<?php

namespace App\Http\Middleware;

use Closure;
use Model;

//require_once 'database/factories/inicializador_db.php';

class Authenticate {

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    public function handle(\Illuminate\Http\Request $request, Closure $next) {
        
        Model::singleton();
        //var_dump("aca no se verifica autenticacion previa");
        return $next($request)->header("Access-Control-Allow-Origin", "*")
                        //Métodos que a los que se da acceso
                        ->header("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE")
                        //Headers de la petición
                        ->header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization");
    }

}
