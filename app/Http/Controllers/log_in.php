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
abstract class Log_in extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    const RESPUESTA_CORRECTA=1;
    const RESPUESTA_INCORRECTA=0;
    const CLAVE_CIFRADO="teganamoscon9";
    public static $CUENTA;
    public static $USUARIO;
    private $metodo_actual;
    private $request_actual;

    public  function despachar(\Illuminate\Http\Request $cosa = null){
        $this->request_actual=$cosa;
//        return call_user_func_array($this->metodo_actual, $request->json()->all());
        return call_user_func_array([$this, $this->metodo_actual],array($cosa));

    }

    public function callAction($method, $parameters)
    {
        $this->metodo_actual=$method;
        return call_user_func_array([$this, "despachar"], $parameters);
//        return call_user_func_array([$this, $method], $parameters);
    }
}
