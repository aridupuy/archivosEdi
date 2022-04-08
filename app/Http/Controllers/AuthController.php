<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;
use App\Http\Controllers\Log_in_app;
//require 'log_in.php';

use Illuminate\Http\Request;
use Usuario;
use Cuenta_usuario;
use Token;
use Exception;

/**
 * Description of AuthController
 *
 * @author ariel
 */
class AuthController extends Log_in_app {

    public function login(Request $request) {
        try {
            //var_dump($request->json()->all());
//            error_log(json_encode($request->json()->all()));
            return response()->json(
				    
                                    $this->loginAction($request->json()->all())
                            )->header("Access-Control-Allow-Origin", "*")
                            //Métodos que a los que se da acceso
                            ->header("Access-Control-Allow-Methods", "GET, POST, PUT,OPTIONS, DELETE")
                            //Headers de la petición
                            ->header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization");
            ;
        } catch (Exception $e) {
            return response()->json(["resultado" => false, "log" => $e->getMessage(), "tokenError" => ""]);
        }
    }

    public function checkToken(Request $request) {
        $variables = $request->json()->all();
//        var_dump($variables);
        $token=$variables["token"];
        if (is_array($variables) and isset($variables["ct"]) and isset($variables["iv"]) and isset($variables["s"])) {
            $hash = new \Gestor_de_hash(self::CLAVE_CIFRADO);
            $variables = $hash->cryptoJsAesDecrypt(json_encode($variables));
            $token=$variables["token"];
//            var_dump("aca1");
        } else if (count($variables) == 1 and isset($variables["ct"])) {
            $hash = new \Gestor_de_hash(self::CLAVE_CIFRADO);
            $variables = $hash->cryptoJsAesDecrypt($variables[0]);
            $token=$variables["token"];
//            var_dump("aca2");
        }
        $gestor_tok=new \Gestor_de_tokens();
        $lectura= $gestor_tok->leer($token);
        if($lectura==null){
            return response("Token expirado");
        }
        $usuario = new \Usuario();
        $usuario->get($lectura->claims->get("roles")["usuario"]);
        if($usuario->getId()==null or $usuario->getId()!= \Authstat::ACTIVO){
//            return response("No autorizado");
            $response = json_encode(["check" => false,"log"=> "No autorizado"]);
            return response($response);
            
        }
        $usu["nombre_completo"]=$usuario->get_nombre_completo();
        $usu["nombre_usuario"]=$usuario->get_nombre_usuario();
        $usu["id_usuario"]=$usuario->get_id_usuario();
//        $usu["email"]=$usuario->get_email();
//        $usu["celular"]=$usuario->get_celular();
//        $usu["cod_area"]=$usuario->get_cod_area();
        $response = json_encode(["check" => boolval($lectura),"cuenta"=> $usu]);
        return response($response)->header("Access-Control-Allow-Origin", "*")
                        //Métodos que a los que se da acceso
                        ->header("Access-Control-Allow-Methods", "GET, POST, PUT,OPTIONS, DELETE")
                        //Headers de la petición
                        ->header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization");

    }

    

    public function loginAction($variables) {

        if (is_array($variables) and isset($variables["ct"]) and isset($variables["iv"]) and isset($variables["s"])) {
            $hash = new \Gestor_de_hash(self::CLAVE_CIFRADO);
            $variables = $hash->cryptoJsAesDecrypt(json_encode($variables));
        } else if (count($variables) == 1) {
            $hash = new \Gestor_de_hash(self::CLAVE_CIFRADO);
            $variables = $hash->cryptoJsAesDecrypt($variables[0]);
        }
       
        if (!isset($variables["usuario"]) and!isset($variables["clave"])) {
            throw new \Exception("Error en credenciales.");
        }
        $rs_usuario = \Usuario::select_login($variables["usuario"], $variables["clave"]);
        $usuario = new \Usuario($rs_usuario->fetchRow());
        if($rs_usuario->rowCount()==0){
            throw new Exception("Falla en la autenticacion, revise credenciales");    
        }
        $gestor_tok=new \Gestor_de_tokens();
        $array["token"] = $gestor_tok->crear($usuario);
        $array["info"] = "Creado nuevo";
        $array["valido_hasta"] = "Tras " . INTERVALO_SESION . " minutos de inactividad.";
        $array["id_usuario"] = $usuario->get_id();
        $array["nombre_usuario"] = $usuario->get_nombre_usuario();
        $array["nombre_completo"] = $usuario->get_nombre_completo();
        $fecha = new \DateTime("now");
        $usuario->set_last_login($fecha->format("Y-m-d H:i:s"));
        self::$USUARIO=$usuario;
//        developer_log(json_encode($array));
        if (!empty($array) and $usuario->set()) {
//            $response = $hash->cryptoJsAesEncrypt(self::CLAVE_DE_ENCRIPTACION, json_encode($array));
            return $array;
        }
        throw new Exception("Error al autenticar. Verifique credenciales");
    }

    public function loginwithtoken(Request $request) {
//        var_dump($request);
//        developer_log("aca loginwithtoken");
        $variables = $request->json()->all();
        if (is_array($variables) and isset($variables["ct"]) and isset($variables["iv"]) and isset($variables["s"])) {
            $hash = new \Gestor_de_hash(self::CLAVE_CIFRADO);
            $variables = $hash->cryptoJsAesDecrypt(json_encode($variables));
        } else if (count($variables) == 1) {
            $hash = new \Gestor_de_hash(self::CLAVE_CIFRADO);
            $variables = $hash->cryptoJsAesDecrypt($variables[0]);
        }
//        developer_log(json_encode($variables));
        $token = ($variables["token"]);
        $gestor_tok = new \Gestor_de_tokens();
        $lectura = $gestor_tok->leer($token);
        
        if (!$token) {
            $array["token"] = false;
            $array["log"] = "Error al autenticar. Verifique credenciales";
            $array["resultado"] = false;
            $response = $hash->cryptoJsAesEncrypt(self::CLAVE_DE_ENCRIPTACION, json_encode($array));
            return response($response);
        }
        $token_anterior = $token;
        $array["token"] = bin2hex(random_bytes(25));
        $token_anterior->set_token($array["token"]);
        $token_anterior->set_fecha_gen("now()");
        $token_anterior->set_ultimo_uso("now()");
        developer_log("IDTOKENANTERIOR");
        developer_log($token_anterior->get_id());
        if ($token_anterior->set()) {
            $array["valido_hasta"] = "Tras " . INTERVALO_SESION_EXTENDIDO . " dias de inactividad.";
            $response = $hash->cryptoJsAesEncrypt(self::CLAVE_DE_ENCRIPTACION, json_encode($array));
            return response($response)->header("Access-Control-Allow-Origin", "*")
                            //Métodos que a los que se da acceso
                            ->header("Access-Control-Allow-Methods", "GET, POST, PUT,OPTIONS, DELETE")
                            //Headers de la petición
                            ->header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization");
            ;
        }

        return response(false)->header("Access-Control-Allow-Origin", "*")
                        //Métodos que a los que se da acceso
                        ->header("Access-Control-Allow-Methods", "GET, POST, PUT,OPTIONS, DELETE")
                        //Headers de la petición
                        ->header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization");
        ;
    }

    //put your code here
}
