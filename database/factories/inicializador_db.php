<?php
date_default_timezone_set("America/Argentina/Buenos_Aires");
require_once __DIR__.'/../../app/lib.php';

define('PATH_PUBLIC', __DIR__.'/../../storage/app/public/');
define('PATH_MODELS',__DIR__."/../../app/models/");
define('PATH_VENDOR',__DIR__."/../../vendor/");
define('PATH_CLASSES',__DIR__."/../../app/Classes/");
define('PATH_EXCEPTIONS',__DIR__."/../../app/Exceptions/");
define('PATH_TRAITS',__DIR__."/../../app/traits/");
 
//hay que buscar una mejor forma para esto
define('DB_CONNECTION',"mysqli");
define('DATABASE_NAME','EDI');
define('DATABASE_USERNAME','mobul_usr');
define('DATABASE_USERPASS','.t&]7+tBU7!R');
define('NOMBRE_HIDDEN_INSTANCIA',getenv('NOMBRE_HIDDEN_INSTANCIA'));
define('ALGORITMO_HASH',getenv('ALGORITMO_HASH'));
define('PATH_VENDOR_HANDLE_ERROR',PATH_VENDOR.'/adodb/adodb-php/adodb-errorhandler.inc.php');

define('COOKIE_PATH','/');
define('ACTIVAR_HASH',true);
define('PATH_CDEXPORTS',"/home/cdexports/");

define('COOKIE_DOMAIN','');
define('COOKIE_SECURE',getenv('COOKIE_SECURE'));
define('COOKIE_HTTPONLY',getenv('COOKIE_HTTPONLY'));
define('LOGIN_DE_USUARIO_INTERNO','login_de_usuario_interno');
define('LOGIN_DE_USUARIO_EXTERNO','login_de_usuario_externo');
define('GENERAR_ID_ALEATORIO','GENERAR_ID_ALEATORIO');
define('GENERAR_ID_MAXIMO','GENERAR_ID_MAXIMO');
define('PREFIJO_PARA_ELEMENTOS_CIFRRADOS',getenv('PREFIJO_PARA_ELEMENTOS_CIFRRADOS'));
define('EXTENSION_ARCHIVO_DE_SISTEMA', getenv('EXTENSION_ARCHIVO_DE_SISTEMA'));
define('DEVELOPER',"1");
define('ACTIVAR_LOG_APACHE',"1"); 
$GLOBALS['ACTIVAR_LOG_NAVEGADOR']="0";
define('ACTIVAR_LOG_CONSOLA_NAVEGADOR',"0"); 
define('ACTIVAR_LOG_APACHE_LOGIN',"1"); 
define('ACTIVAR_LOG_APACHE_DEV_LOG',"0"); 
define('ACTIVAR_LOG_APACHE_DE_IDS',"0");
define('ACTIVAR_LOG_APACHE_DE_COOKIES',"0");
define('ACTIVAR_LOG_APACHE_DE_HASH',"0");
define('ACTIVAR_LOG_APACHE_DE_PERMISOS',"0");
define('ACTIVAR_LOG_EXT_APACHE_DE_PERMISOS',"0");
define('ACTIVAR_LOG_APACHE_DE_CORREO',"1");
define('ACTIVAR_LOG_APACHE_DE_CONSULTAS_SQL',"0");
define('ACTIVAR_LOG_INSTANCIAS',"0");
define('ACTIVAR_LOG_TRANSACCIONES',"0");
define('ACTIVAR_LOG_EXT_APACHE_DE_CONSULTAS_SQL',"0"); # Solo luego del log en sql

define('ACTIVAR_LOG_SQL_CONTROLLERS',getenv('ACTIVAR_LOG_SQL_CONTROLLERS'));
define('ACTIVAR_LOG_SQL_SELECT',getenv('ACTIVAR_LOG_SQL_SELECT'));
define('ACTIVAR_LOG_SQL_UPDATE',getenv('ACTIVAR_LOG_SQL_UPDATE'));
define('ACTIVAR_LOG_SQL_INSERT',getenv('ACTIVAR_LOG_SQL_INSERT'));
$GLOBALS['REGISTROS_POR_PAGINA']=getenv('REGISTROS_POR_PAGINA');
$GLOBALS['MAXIMO_REGISTROS_POR_CONSULTA']=getenv('MAXIMO_REGISTROS_POR_CONSULTA');
define('MAXIMO_CARACTERES_CELDA',"70");
define('FORMATO_FECHA_POST', '!Y-m-d');
define('FORMATO_FECHA_POSTGRES','Y-m-d H:i:s.ue');
define('FORMATO_FECHA_POSTGRES_SIN_TIMESTAMP','Y-m-d H:i:s');
define('FORMATO_TIEMPO_POSTGRES','H:i:s.u');
define('FORMATO_FECHA_POSTGRES_WITHOUT_TZ','Y-m-d H:i:s.u');
define('INTERVALO_SESION_EXTENDIDO', "20");
define('INTERVALO_SESION', "200");
define('TIEMPO_EXTENDIDO', "day");
define('TIEMPO', "minute");
define('SOLO_REPLICA', false);
define('DATABASE_HOST','localhost');
define('DATABASE_PORT','3306');
define('PROJECT_KEY','');
define('PROJECT_ID','');
define('EMAIL_USER_INFO','noresponder@unmail.com');
define('EMAIL_USER_NOREPPLY','noresponder@unmail.com');
define('EMAIL_USER_ATC','noresponder@unmail.com');
define('EMAIL_USER_DESARROLLO','noresponder@unmail.com');
define('EMAIL_HOST','smtp.gmail.com');
define('EMAIL_PORT','587');
define('EMAIL_USER','noresponder@unmail.com');
define('EMAIL_PASS','');
define('APP_NAME','EDISOFT');

require_once PATH_MODELS."model.php";
function mi_autoload($clase) {
    developer_log($clase);
  if($clase===ucfirst(strtolower($clase))){
    $clase=strtolower($clase);
//    developer_log($clase); 
    $archivo=PATH_TRAITS.$clase. '.php';
      
      if (file_exists($archivo)) {
        require $archivo;
        return true;
      }  
    $archivo=PATH_MODELS.$clase. '.php';
//    developer_log($archivo); 
      if (file_exists($archivo) and  $archivo!=PATH_MODELS."model.php") {
        require $archivo;
        return true;
      }
      $archivo=PATH_CLASSES.$clase. '.php';
      if (file_exists($archivo)) {
        require $archivo;
        return true;
      }
      $archivo=PATH_EXCEPTIONS.$clase. '.php';
      if (file_exists($archivo)) {
        require $archivo;
        return true;
      }
//      $archivo=getenv('PATH_TRAITS').$clase. '.php';
//      if (file_exists($archivo)) {
//       	error_log($archivo);
//	 require $archivo;
//        return true;
//      }
//      if(!isset($directorio)){
//        return false;
//      }
//      else{
//        $archivo=$directorio.'controllers/'.$clase. '.php';
//        if (file_exists($archivo)) {
//          require $archivo;
//          return true;
//        }
//      }
  }
    
    return false;
}
# Registro de autoload
spl_autoload_register('mi_autoload');


# Registro de manejo de errores catcheables
set_error_handler('mi_manejador_de_errores',E_ALL);
function mi_manejador_de_errores($errno, $errstr, $errfile, $errline) {
  if ( E_RECOVERABLE_ERROR===$errno ) {
      Gestor_de_log::set_exception($errstr,0);
      developer_log($errstr);
    return true;
  }

  return false;
}
#Includes
try {
   // var_dump(PATH_VENDOR);
  if(!include_once PATH_VENDOR.'adodb/adodb-php/adodb.inc.php') 
    throw new Exception('Fallo al abrir la libreria AdoDB.');
  /*if(!include_once PATH_CORE.'lib.php') 
    throw new Exception('Fallo al abrir la libreria de funciones lib.php');
  if(ACTIVAR_IDS) if(!@include_once PATH_PUBLIC.'ids.php') 
    throw new Exception('Fallo al abrir la extension de IDS.');
  */
 \Model::singleton();
} catch (Exception $e) {
    error_log($e->getMessage());
    exit();
}
