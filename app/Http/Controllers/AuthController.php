<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;

require 'log_in.php';

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
class AuthController extends Log_in {

    public function login(Request $request) {
        try {
            error_log(json_encode($request->json()->all()));
            return response()->json(
//				    error_log($request->json()->all());
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
        if (is_array($variables) and isset($variables["ct"]) and isset($variables["iv"]) and isset($variables["s"])) {
            $hash = new \Gestor_de_hash(self::CLAVE_CIFRADO);
            $variables = $hash->cryptoJsAesDecrypt(json_encode($variables));
        } else if (count($variables) == 1) {
            $hash = new \Gestor_de_hash(self::CLAVE_CIFRADO);
            $variables = $hash->cryptoJsAesDecrypt($variables[0]);
        }
        $cuentas = $this->obtener_cuentas($variables["token"]);
        $response = $hash->cryptoJsAesEncrypt(self::CLAVE_DE_ENCRIPTACION, json_encode(["check" => Token::checktoken($variables["token"]), "cuentas" => $cuentas]));
//        $response = $hash->cryptoJsAesEncrypt(self::CLAVE_DE_ENCRIPTACION, json_encode(["check" => Token::checktoken($variables["token"])]));
//        $response = json_encode(["check" => Token::checktoken($variables["token"]),"cuenta"=>$cuenta]);
        return response($response)->header("Access-Control-Allow-Origin", "*")
                        //Métodos que a los que se da acceso
                        ->header("Access-Control-Allow-Methods", "GET, POST, PUT,OPTIONS, DELETE")
                        //Headers de la petición
                        ->header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization");
//        var_dump($rs_usuario->rowCount());
    }

    public function obtener_cuentas($token) {
        $token = Token::select_token($token);
        if (!$token) {
            throw new Exception("Error no hay token ");
        }
        $cuenta_usuario = new Cuenta_usuario();
        $cuenta_usuario->get($token->get_id_cuenta_usuario());
        $rs_cuenta_usuario = Cuenta_usuario::select_activas($cuenta_usuario->get_id_usuario());
        $i = 0;
        foreach ($rs_cuenta_usuario as $row) {
            $cuenta_usuario = new Cuenta_usuario($row);
            if ($cuenta_usuario->get_id() == false) {
                throw new Exception("Error en credenciales.");
            }
            developer_log($cuenta_usuario->get_id());
            $rs = Token::select_token_activo($cuenta_usuario->get_id());

            if ($rs->rowCount() > 0) {

                $token = new Token($rs->fetchRow());
                $array[$i]["token"] = $token->get_token();
                $array[$i]["info"] = "reutilizado";
                $token->set_ultimo_uso("now()");
            } else {

                $array[$i]["token"] = bin2hex(random_bytes(25));
                $array[$i]["info"] = "Creado nuevo";
                ;
                $token = new Token();
                $token->set_id_cuenta_usuario($cuenta_usuario->get_id());
                $token->set_token($array[$i]["token"]);
                $token->set_fecha_gen("now()");
                $token->set_ultimo_uso("now()");
            }

            if ($token->set()) {
                $array[$i]["valido_hasta"] = "Tras " . INTERVALO_SESION . " minutos de inactividad.";
                $cuenta = new \Cuenta();
                $cuenta->get($cuenta_usuario->get_id_cuenta());
                $explode = explode(" ", $cuenta->get_titular());
                $array[$i]["dato"] = $cuenta->get_cuil();
                $array[$i]["iniciales"] = substr($explode[0], 0, 1) . substr($explode[count($explode) - 1], 0, 1);
                $array[$i]["titular"] = $explode;
                $array[$i]["cuenta"] = $cuenta_usuario->get_id_cuenta_usuario();
            } else {
                throw new Exception("Error al guardar token ");
            }

            $i++;
        }

        return $array;
    }

    public function loginAction($variables) {

        if (is_array($variables) and isset($variables["ct"]) and isset($variables["iv"]) and isset($variables["s"])) {
            $hash = new \Gestor_de_hash(self::CLAVE_CIFRADO);
            $variables = $hash->cryptoJsAesDecrypt(json_encode($variables));
        } else if (count($variables) == 1) {
            $hash = new \Gestor_de_hash(self::CLAVE_CIFRADO);
            $variables = $hash->cryptoJsAesDecrypt($variables[0]);
        }
//       var_dump(json_encode($variables));
        if (!isset($variables["usuario"]) and!isset($variables["clave"])) {
            throw new Exception("Error en credenciales.");
        }
        $rs_usuario = \Usuario::select_login($variables["usuario"], $variables["clave"]);
        $usuario = new Usuario($rs_usuario->fetchRow());
        developer_log("Usuarios encontradas " . $rs_usuario->rowCount());
        $rs_cuenta_usuario = Cuenta_usuario::select_activas($usuario->get_id());
        $i = 0;
        developer_log("Cuentas encontradas " . $rs_cuenta_usuario->rowCount());
        foreach ($rs_cuenta_usuario as $row) {
            developer_log(json_encode($row));
            $cuenta_usuario = new Cuenta_usuario($row);
            if ($cuenta_usuario->get_id() == false) {

                throw new Exception("Error en credenciales.");
            }
            developer_log($cuenta_usuario->get_id());
            $rs = Token::select_token_activo($cuenta_usuario->get_id());

            if ($rs->rowCount() > 0) {

                $token = new \Token($rs->fetchRow());
                $array[$i]["token"] = $token->get_token();
                $array[$i]["info"] = "reutilizado";
                $token->set_ultimo_uso("now()");
            } else {
                $array[$i]["token"] = bin2hex(random_bytes(25));
                $array[$i]["info"] = "Creado nuevo";
                ;
                $rs_token = \Token::select(["id_cuenta_usuario" => $cuenta_usuario->get_id()]);
                if ($rs_token->rowCount() > 0) {
                    $token = new Token($rs_token->fetchRow());
                } else
                    $token = new Token();
                $token->set_id_cuenta_usuario($cuenta_usuario->get_id());
                $token->set_token($array[$i]["token"]);
                $token->set_fecha_gen("now()");
                $token->set_ultimo_uso("now()");
            }

            if ($token->set()) {
                $array[$i]["valido_hasta"] = "Tras " . INTERVALO_SESION . " minutos de inactividad.";
                $cuenta = new \Cuenta();
                $cuenta->get($cuenta_usuario->get_id_cuenta());
                $explode = explode(" ", $cuenta->get_titular());
                $array[$i]["dato"] = $cuenta->get_cuil();
                $array[$i]["iniciales"] = substr($explode[0], 0, 1) . substr($explode[count($explode) - 1], 0, 1);
                $array[$i]["titular"] = $explode;
                $array[$i]["cuenta"] = $cuenta_usuario->get_id_cuenta_usuario();
            } else {
                throw new Exception("Error al guardar token ");
            }

            $i++;
        }
//        developer_log(json_encode($array));
        if (!empty($array)) {
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
        $token = Token::select_token_with_token($variables["token"]);
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
