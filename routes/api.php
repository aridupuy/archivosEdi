<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backoffice;
use App\Http\Controllers\EfectivoDigital;

/*
  |--------------------------------------------------------------------------
  | API Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register API routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | is assigned the "api" middleware group. Enjoy building your API!
  |
 */

//error_log("registrando");
//var_dump($request);

Route::post("login", 'AuthController@login')->middleware('auth');
Route::post("loginBo", 'Auth_BO_Controller@login')->middleware('auth');
Route::post("checkToken", 'AuthController@checkToken')->middleware('auth');
//Route::post("loginwithtoken", 'AuthController@loginwithtoken')->middleware('auth');
//Route::get("usuario", 'UsuarioController@obtener_filtrado')->middleware('efectivo');
//Route::post("alta", 'AltaController@crear')->middleware('efectivo');
//$archivos = scandir(__DIR__ . "/../app/Http/Controllers");
iterate(false);

function iterate($middleware = false) {
    //busca activamente las rutas disponibles
    if ($middleware == "Events") {
        return;
    }
    if ($middleware == false) {
        $archivos = scandir(__DIR__ . "/../app/Http/Controllers/");
    } else {
        $archivos = scandir(__DIR__ . "/../app/Http/Controllers/$middleware");
    }
    foreach ($archivos as $archivo) {
        if ($archivo == "." or $archivo == "..") {
            continue;
        } else if ($archivo != "." and $archivo != ".." and is_dir(__DIR__ . "/../app/Http/Controllers/$archivo/")) {
            iterate($archivo);
            continue;
        }
        if ($archivo == '.' OR $archivo == '..' OR $archivo == 'Controller.php' /* OR $archivo == 'log_in.php' */ or $archivo == "AuthController.php" or $archivo == "Auth_BO_Controller.php") {
            continue;
        }
        $ruta = explode("Controller.php", $archivo);
        $controller = explode(".php", $archivo);
        $ruta = $ruta[0];
        $controller_class = $controller[0];
        if ($controller_class == "Log_in_app") {
            continue;
        }

        $controller = '\App\Http\Controllers\\' . $middleware . '\\' . $controller[0];
        //solo se van a leer metodos publicos
        $metodos = get_class_methods($controller);
        if ($parent_class = get_parent_class($controller)) {
            $metodos_heredados = get_class_methods($parent_class);
            $metodos = array_diff($metodos, $metodos_heredados);
        }
        if ($metodos and count($metodos) > 0)
            foreach ($metodos as $metodo) {
                //añado metodos
                //ver como diferenciar get de post
                $metodo_post = explode("_post", $metodo);

                if (count($metodo_post) > 1) {
//            "si el metodo es _post la consulta se resuelve por post";
                    Route::post(strtolower($ruta . "/" . $metodo_post[0]), "$middleware\\" . $controller_class . '@' . $metodo)->middleware($middleware);
                } else {
                    Route::get(strtolower($ruta . "/" . $metodo), "$middleware\\" . $controller_class . '@' . $metodo)->middleware($middleware);
                    //    var_dump("get:: ".strtolower($ruta . "/" . $metodo));
                }
            }
    }

    return;
}
