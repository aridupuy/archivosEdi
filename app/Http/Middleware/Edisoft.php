<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Controller;
use Closure;
use Model;
use Token;

class Edisoft {

    const CLAVE_CIFRADO = "teganamoscon9";

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(\Illuminate\Http\Request $request, Closure $next) {
        ///inicializo la base de datos para #APP_NAME.
        Model::singleton();
        $token = $request->header("token");
        if (!$token) {
            $token = $request->post()["token"];
        }
        if (isset($request->all()["developers"])) {
            Controller::$HASH = false;
        }
        if (!$token) {
            return response(false, "Falta el parametro token para la autenticacion", [])->header("Access-Control-Allow-Origin", "*");
        }
        try{
            if (!$this->verificar_autenticacion($token)) {
                return $this->retornar(false, "El Token no es válido", [])->header("Access-Control-Allow-Origin", "*")
                                //Métodos que a los que se da acceso
                                ->header("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE")
                                //Headers de la petición
                                ->header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization,Access-Control-Allow-Headers,content-type");
                ;
            }
        }catch(\Exception $e){
            return $this->retornar(false, $e->getMessage(), [])->header("Access-Control-Allow-Origin", "*")
                                //Métodos que a los que se da acceso
                                ->header("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE")
                                //Headers de la petición
                                ->header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization,Access-Control-Allow-Headers,content-type");
                ;
        }
        $developers = false;
//        var_dump($request->all());
        if (Controller::$HASH) {
            if (isset($request->all()["developers"]))
                $developers = $request->all()["developers"];
            if (isset($request->json()->all()["developers"]))
                $developers = $request->json()->all()["developers"];
            if ($developers) {
                Controller::$HASH = false;
                $vars = $request->json()->all();
                Controller::cargar_parametros($vars);
            } else {
                /* aca se decifra la informacion */
                $hash = new \Gestor_de_hash(self::CLAVE_CIFRADO);

                if ($request->json()->all() != null and is_array($request->json()->all())) {
                    $vars = $hash->cryptoJsAesDecrypt(json_encode($request->json()->all()));
                    Controller::cargar_parametros($vars);
                }
            }
        } else {
            Controller::cargar_parametros($request->all());
        }
        if ($request->getQueryString() != null) {
            parse_str($request->getQueryString(), $vars);
            Controller::cargar_parametros($vars);
        }
        if ($request->file("File") != false and $request->file("File")->getPathname() != false) {
            $vars = $request->post();
            $file = $request->file('File');
            $path = PATH_CDEXPORTS . "tmp";
            $file->move($path, $file->getClientOriginalName());
            $name = $request->file("File")->getFilename();
            $vars["archivo"] = $path . "/" . $file->getClientOriginalName();
//            $request->s

            Controller::cargar_parametros($vars);
        }

        try {
            if (!Controller::set_cuenta($token)) {
                return $this->retornar(false, "Error en la autenticacion, La cuenta no existe ", [])->header("Access-Control-Allow-Origin", "*");
            }
        } catch (\Exception $e) {
            return $this->retornar(false, "Error en la autenticacion, La cuenta no existe ", [])->header("Access-Control-Allow-Origin", "*");
        }
        \Logger::log("Entrada " . APP_NAME . " (App) id_usuario -->" . Controller::$USUARIO->get_id(), isset($vars) ? $vars : "", $request->getRequestUri());
        if ($request)
            return $next($request)->header("Access-Control-Allow-Origin", "*")
                            //Métodos que a los que se da acceso
                            ->header("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE")
                            //Headers de la petición
                            ->header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization,Access-Control-Allow-Headers");
        else
            return $next()->header("Access-Control-Allow-Origin", "*")
                            //Métodos que a los que se da acceso
                            ->header("Access-Control-Allow-Methods", "GET, POST, PUT, DELETE")
                            //Headers de la petición
                            ->header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization,Access-Control-Allow-Headers");
    }

    private function verificar_autenticacion($token) {
        $tok = new \Gestor_de_tokens();

        $lectura = $tok->leer($token);
        $usuario = new \Usuario();
        if ($lectura == null) {
            return false;
        }
        $usuario->get($lectura->claims->get("roles")["usuario"]);
//        var_dump($usuario);
        if ($usuario->get_id() == null) {
            Controller::set_cuenta($token);
        } else {
            developer_log("false");
        }
        return ($usuario);
    }

    public function retornar($resultado, $log, ...$param) {
        /* Encriptamos todas las respuestas */
        if (isset(Controller::$variables["developers"])) {
            Controller::$HASH = false;
        }
        if (!Controller::$HASH) {
            $response = json_encode([
                "resultado" => $resultado,
                "log" => $log,
                "extras" => $param
            ]);
        } else {
            $hash = new \Gestor_de_hash(Controller::CLAVE_DE_ENCRIPTACION);
            $response = $hash->cryptoJsAesEncrypt(Controller::CLAVE_DE_ENCRIPTACION, json_encode([
                "resultado" => $resultado,
                "log" => $log,
                "extras" => $param
            ]));
        }

        return response($response);
        /* ->json(
          [
          "resultado"=>$resultado,
          "log"=>$log,
          "extras"=>$param
          ]); */
    }

}
