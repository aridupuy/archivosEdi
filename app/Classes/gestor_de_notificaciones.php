<?php

class Gestor_de_notificaciones {

    const ACTIVAR_DEBUG = true;
    const URL_GCM = "https://gcm-http.googleapis.com/gcm/send";
    const URL_FCM = "https://fcm.googleapis.com/fcm/send";
    const URL_FCM_GROUP = "https://fcm.googleapis.com/fcm/notification";
//  const KEY_WEB = '';
    const KEY = PROJECT_KEY;
    const PROJECT_ID = PROJECT_ID;
    const ID_AUTHSTAT_ACTIVO = 990;
    const ID_AUTH = 30;
    const VERDADERO = 1;
    const FALSO = 0;
    const DEFAULT_LEIDO = 0;
    const DEFAULT_NIVEL = 0;
    CONST ACTIVAR_TEST = false;

    protected static $cuerpo = '{ 
                                    "priority" : "high",
                                    "delay_while_idle" : true,
                                    "data": {
                                            "titulo": "",
                                            "cuerpo": "",
                                            "activity" : ""
                                    },
                                    "to" : ""
                                }';
    protected static $cuerpo_web = '{ 
                                    "to" : "",
                                    priority:10,
                                    "collapse_key" : "notificacion",
                                    "notification" : {
                                        "title": "",
                                        "body":""
	                            },
                                    "data": {
                                        "titulo": "",
                                        "cuerpo":"",
                                        "nivel":0,
                                        "activity":"",
                                        "importante":0,
                                        "archivado":0,
                                        "destacado":0
                                    },
                                    "android":{
                                        "priority":"normal"
                                    },
                                    "apns":{
                                        "headers":{
                                          "apns-priority":"5"
                                        }
                                    },
                                    "webpush": {
                                        "headers": {
                                          "Urgency": "high"
                                        }
                                    }
                                }';

    public static function notificar($id_cuenta, $mensaje, $titulo = APP_NAME, $actividad = "home", $params = [], $importante = 0, $destacado = 0) {

        if (self::ACTIVAR_DEBUG)
            developer_log("Notificando a la cuenta: " . $id_cuenta);
//    AAAAW8mw8B0:APA91bEKYAVlvpOPEJ3-wkChHnR0fAh-ZimU9pinfkEWWPaKCu0zg_LPjM74bk4zz0UyZmCSsz65aCDvEQwPDQgQXnAKtlRNM5nRfCntS2_KF2YnjnZvR8fXn-A9k9QYr4L9oc7RkHk1    self::guardar_notificacion($id_marchand, $mensaje,$titulo,$actividad,$importante,$destacado);
        $notificacion = new \Notificacion_cuenta();
        if (!$notificacion->set_id_cuenta($id_cuenta)->set_titulo($titulo)->set_mensaje($mensaje)->set_activity($actividad)->set()) {
            developer_log("No se pudo guardar la notificacion");
            return false;
        }
        $recordset = Fcm_token::select(array("id_cuenta" => $id_cuenta));
        $postdata["operation"] = "create";
        $fecha=new DateTime("now");
        $postdata["notification_key_name"] = "appCuenta_$id_cuenta";
        $activar_web=false;
        if ($recordset->rowCount() > 1) {
            foreach ($recordset as $row) {
                 if ($row["device"] == "web"){
                    $activar_web =true;
                 }
                $postdata["registration_ids"][] = $row["token"];
            }
            $key_group = self::crear_notificaction_group($postdata);
            unset($postdata);
            $postdata["data"]["titulo"] = $titulo;
            $postdata["data"]["cuerpo"] = $mensaje;
            $postdata["notification"]["title"] = $titulo;
            $postdata["notification"]["body"] = $mensaje;
            $postdata["data"]["nivel"] = 0;
            $postdata["data"]["activity"] = $actividad;
            $postdata["data"]["importante"] = $importante;
            $postdata["data"]["destacado"] = $destacado;
            $postdata["data"]["archivado"] = 0;
            $postdata["data"]["params"] = json_encode($params);
            $postdata["to"] = $key_group;
            if (self::enviar_a_grupo($postdata) != false) {
                if(!$activar_web)
                    return true;
            }
        }
        foreach ($recordset as $row) {
            $fcm = new Fcm_token($row);
            developer_log(json_encode($row));
            if ($row["device"] == "web"){
                $postdata = json_decode(self::$cuerpo_web, true);
                if (self::ACTIVAR_DEBUG)
                    developer_log("Dispositivo recuperado IDC:" . $id_cuenta . " | TIPO:" . $row['device']);
                $postdata["data"]["titulo"] = $titulo;
                $postdata["data"]["cuerpo"] = $mensaje;
                $postdata["notification"]["title"] = $titulo;
                $postdata["notification"]["body"] = $mensaje;
                $postdata["data"]["nivel"] = 0;
                $postdata["data"]["activity"] = $actividad;
                $postdata["data"]["importante"] = $importante;
                $postdata["data"]["destacado"] = $destacado;
                $postdata["data"]["archivado"] = 0;
                $postdata["data"]["params"] = json_encode($params);
                $postdata["to"] = $row["token"];
                if ($row["device"] == "web") {
                    $result = self::enviar_web($postdata);
                } else {
                    $result = self::enviar($postdata);
                }
                if (!$result) {
                    developer_log("Ah ocurrido un error al enviar la notificacion.");
                    Model::delete($fcm->get_id(), $fcm, get_class($fcm));
                }
            }
            if (self::ACTIVAR_DEBUG)
                developer_log("La notificacion ha sido enviada.");
        }
        return true;
    }

    public static function crear_notificaction_group($postdata) {

        $result = self::verificar_grupo($postdata["notification_key_name"]);
        if (!$result) {
            $json_envio = json_encode($postdata);
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => self::URL_FCM_GROUP,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $json_envio,
                CURLOPT_HTTPHEADER => array(
                    'PROJECT_ID: ' . self::PROJECT_ID,
                    'Authorization: ' . self::KEY,
                    'Content-Type: application/json'
                ),
            ));

            $result = curl_exec($curl);

            developer_log($result);
            $result = json_decode($result, true);

            if (isset($result["notification_key"])) {
                developer_log("Grupo creado en google.");
                return $postdata["notification_key"];
            }
            developer_log("Grupo no creado");
//            self::verificar_grupo($postdata["notification_key_name"]);
            return false;
        } else {
            $postdata["operation"]="add";
            $postdata["notification_key"]=$result;
            $post ["operation"]="add";
            $post ["notification_key"]=$result;
            $post ["notification_key_name"]=$postdata["notification_key_name"];
            $post ["registration_ids"]=$postdata["registration_ids"];
            
            $json_envio = json_encode($post);
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => self::URL_FCM_GROUP,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $json_envio,
                CURLOPT_HTTPHEADER => array(
                    'PROJECT_ID: ' . self::PROJECT_ID,
                    'Authorization: ' . self::KEY,
                    'Content-Type: application/json'
                ),
            ));

            $result = curl_exec($curl);

            developer_log($result);
            $result = json_decode($result, true);

            if (isset($result["notification_key"])) {
                developer_log("Grupo actualizado en google.");
                return $postdata["notification_key"];
            }
            developer_log("Grupo no actualizado");
//            self::verificar_grupo($postdata["notification_key_name"]);
            return $result;
        }
    }

    public static function verificar_grupo($nombre) {
        $url = "https://fcm.googleapis.com/fcm/notification?notification_key_name=$nombre";
        developer_log($url );
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => '',
            CURLOPT_HTTPHEADER => array(
                'PROJECT_ID: ' . self::PROJECT_ID,
                'Authorization: ' . self::KEY,
                'Content-Type: application/json',
                'Content-length: 0'
            ),
        ));

        $result = curl_exec($curl);
        $err = curl_error($curl);
        developer_log(curl_getinfo($curl,CURLINFO_HTTP_CODE));
        developer_log($result);
        developer_log($err);
        curl_close($curl);
        if (!$result) {
            return false;
        }
        $result = json_decode($result, true);
        if (isset($result["notification_key"])) {
            return $result["notification_key"];
        }
        return false;
    }

    public static function enviar_a_grupo($postdata) {
        $json_envio = json_encode($postdata);
        $headers = array(
            'Content-Type' => 'application/JSON',
            'Authorization' => self::KEY
        );
        $opts = array('http' =>
            array(
                'method' => 'POST',
                'header' => self::preparar_cabeceras($headers),
                'content' => $json_envio
            )
        );
        $context = stream_context_create($opts);
        $result = file_get_contents(self::URL_FCM, false, $context);
//        var_dump($result);
        developer_log($result);
        $result = json_decode($result, true);

        if ($result['success'] >= self::VERDADERO) {
            developer_log("Notificacion Correctamente enviada a google.");
            return true;
        }
        developer_log("Notificacion no pudo ser enviada a google.");
        return false;
    }

    public static function notificar_y_guardar($id_cuenta, $mensaje, $titulo = APP_NAME, $actividad = "Main.Main", $importante = 0, $destacado = 0) {
        self::notificar($id_cuenta, $mensaje, $titulo, $actividad, $importante, $destacado);
    }

    private static function enviar($postdata) {
        developer_log("ENVIO POR APP");

        if (self::ACTIVAR_TEST) {
            return true;
        }
        $json_envio = json_encode($postdata);
        $headers = array(
            'Content-Type' => 'application/JSON',
            'Authorization' => self::KEY
        );
        $opts = array('http' =>
            array(
                'method' => 'POST',
                'header' => self::preparar_cabeceras($headers),
                'content' => $json_envio
            )
        );
        $context = stream_context_create($opts);
        $result = file_get_contents(self::URL_FCM, false, $context);
//        var_dump($result);
        $result = json_decode($result, true);

        if ($result['success'] === self::VERDADERO AND $result['failure'] === self::FALSO) {
            developer_log("Notificacion Correctamente enviada a google.");
            return true;
        }
        developer_log("Notificacion no pudo ser enviada a google.");
        return false;
    }

    private static function enviar_web($postdata) {
        developer_log("ENVIO POR WEB");
        var_dump("web");
        $json_envio = json_encode($postdata);
        $headers = array(
            'Content-Type' => 'application/JSON',
            'Authorization' => self::KEY
        );
        $opts = array('http' =>
            array(
                'method' => 'POST',
                'header' => self::preparar_cabeceras($headers),
                'content' => $json_envio
            )
        );
        developer_log(json_encode($opts));
        $context = stream_context_create($opts);
        $result = file_get_contents(self::URL_FCM, false, $context);
        $result = json_decode($result, true);

        if ($result['success'] === self::VERDADERO AND $result['failure'] === self::FALSO) {
            developer_log("Notificacion web Correctamente enviada a google.");
            return true;
        }
        developer_log("Notificacion web no pudo ser enviada a google.");
        return false;
    }

    private static function preparar_cabeceras($headers) {
        $array = array();
        foreach ($headers as $key => $header) {
            if (is_int($key)) {
                $array[] = $header;
            } else {
                $array[] = $key . ': ' . $header;
            }
        }

        return implode("\r\n", $array);
    }

}
