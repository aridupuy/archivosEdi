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
use Token;
use Exception;
/**
 * Description of AuthController
 *
 * @author ariel
 */
class Auth_BO_Controller extends Log_in {

    public function login(Request $request) {
        try {
            return response()->json(
                            $this->loginAction($request->json()->all())
            );
        } catch (Exception $e) {
            return response()->json(["log" => $e->getMessage()]);
        }
    }
    public function checkToken(Request $request){
        $variables = $request->json()->all();
//        var_dump($variables);

        return response()->json(["check"=>Token::checktoken($variables["token"])]);
//        var_dump($rs_usuario->rowCount());
    }
    public function loginAction($variables) {
        if(is_array($variables) and isset($variables["ct"]) and isset($variables["iv"]) and isset($variables["s"])){
            $hash=new \Gestor_de_hash(self::CLAVE_CIFRADO);
            $variables=$hash->cryptoJsAesDecrypt(json_encode($variables));

        }
        if(!isset($variables["jwl"])){
            throw new Exception("Error en credenciales.");
        }

        if($variables["jwl"]==env("PASSBO")){
            $array["token"] = bin2hex(random_bytes(25));
            $token = new Token();
            $token->set_id_cuenta_usuario(50);
            $token->set_token($array["token"]);
            $token->set_fecha_gen("now()");
            $token->set_ultimo_uso("now()");
        }
        else{
            throw new Exception("Error en credenciales.");
        }
        if ($token->set()) {
            $array["valido_hasta"]="Tras ".INTERVALO_SESION." minutos de inactividad.";
            return $array;
        }






        throw new Exception("Error al autenticar. Verifique credenciales");
    }

    //put your code here
}
