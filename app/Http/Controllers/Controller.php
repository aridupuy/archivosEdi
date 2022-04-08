<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Usuario;
use Token;
use Exception;

abstract class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    const RESPUESTA_CORRECTA=1;
    const RESPUESTA_INCORRECTA=0;
    const CLAVE_DE_ENCRIPTACION="teganamoscon9";
    public static $USUARIO;
    private $metodo_actual;
    public static $HASH=false;
    protected static $variables;

    public function retornar($resultado,$log,...$param){
        /* Encriptamos todas las respuestas */
//        var_dump($resultado,$log,$param);
        if(isset(self::$variables["developers"])){
            self::$HASH = false;
        }
        if(!self::$HASH){
          $response=json_encode([
            "resultado"=> boolval($resultado),
            "log"=>$log,
            "extras"=>$param
            ]);
        }
        else{
//            developer_log("aca");
            $hash=new \Gestor_de_hash(self::CLAVE_DE_ENCRIPTACION);
//            developer_log(json_encode([
//                "resultado"=>boolval($resultado),
//                "log"=>$log,
//                "extras"=>$param
//            ]));
            $response=$hash->cryptoJsAesEncrypt(self::CLAVE_DE_ENCRIPTACION, json_encode([
                "resultado"=>boolval($resultado),
                "log"=>$log,
                "extras"=>$param
            ]));
        }
//	developer_log(json_encode([
//                "resultado"=>boolval($resultado),
//                "log"=>$log,
//                "extras"=>$param
//            ]));
        return response($response)->header("Access-Control-Allow-Origin", "*")
                            //Métodos que a los que se da acceso
                            ->header("Access-Control-Allow-Methods", "GET, POST, PUT,OPTIONS, DELETE")
                            //Headers de la petición
                            ->header("Access-Control-Allow-Headers", "X-Requested-With, Content-Type, X-Token-Auth, Authorization")
                            ->header('Content-Type', 'application/json');
                            
                /*->json(
        [
            "resultado"=>$resultado,
            "log"=>$log,
            "extras"=>$param
        ]);*/
    }
    public  function despachar(\Illuminate\Http\Request $cosa = null){
//        developer_log("aca");
        return call_user_func_array([$this, $this->metodo_actual],array($cosa));

    }

    public function callAction($method, $parameters)
    {
        $this->metodo_actual=$method;
//        var_dump($parameters);
        try{
        return call_user_func_array([$this, "despachar"], $parameters);

        } catch (\Exception $e){
            developer_log($e);
            return $this->retornar(false, $e->getMessage());
        }
//        return call_user_func_array([$this, $method], $parameters);
    }
    public static function cargar_parametros($parametros){
//        var_dump($parametros);
//        developer_log(json_encode(self::$variables));
        if(self::$variables==null)
            self::$variables=$parametros;
        else
            self::$variables=array_merge(self::$variables,$parametros);
        
        self::validar_datos();
//            developer_log("ACA------".\GuzzleHttp\json_encode(self::$variables));
    }
    public static function validar_datos(){
        $variables =[];
        foreach (self::$variables as $key=>$vars){
            if($vars!="" and $vars!=null){
                $variables[$key]=$vars;
            }
        }
        self::$variables=$variables;
    }
    public static function set_cuenta($token){
//        developer_log("TOKEN:".$token);
        $gestor_de_tok = new \Gestor_de_tokens();
        $lectura = $gestor_de_tok->leer($token);
        if($lectura == null){
            throw new \Exception("No autorizado");
        }
        
        self::$USUARIO = new \Usuario();
        self::$USUARIO->get($lectura->claims->get("roles")["usuario"]);
        if(self::$USUARIO->get_id()== null or self::$USUARIO->get_id_authstat()!=1){
            throw new \Exception("No autorizado");
        }
//        developer_log("USUARIO:".self::$USUARIO->get_id());
        if(self::$USUARIO->get_id_usuario()==null){
            return false;
        }
        return true;
    }
}
