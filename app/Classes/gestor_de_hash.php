<?php

// namespace Classes;
class Gestor_de_hash {

    private $clave_de_cifrado;
    public static $clave_de_cifrado_estatica = false;

    public function __construct($clave_de_cifrado) {
        $this->clave_de_cifrado = hash('md5', $clave_de_cifrado);
        self::$clave_de_cifrado_estatica = $clave_de_cifrado;
        return $this;
    }

    public function mask($nodos) {
        # Recorre un domdocument y codifica todos los ids y names
        # Mejorar esta funcion y quitar recursividad, usar getElementsByTagName

        if (!ACTIVAR_HASH)
            return $nodos;
        if ($nodos === false)
            return false;

        # Posiblemente este if no tenga ningun sentido
        if ((is_object($nodos) AND get_class($nodos) == 'View')AND ( $nodos->childNodes->length == 1))
            $items = $nodos->documentElement;
        else
            $items = $nodos;

        //print_r($nodos->childNodes->length);
        if (!isset($items) OR ! $items->hasChildNodes())
            return $items;# No se que hacia esta linea ¿?
        foreach ($items->childNodes as $item):

            if (get_class($item) == 'DOMElement') {
                if (($item->hasAttribute('id') AND $item->hasAttribute('type'))
                        AND ( $item->getAttribute('type') == 'button' OR $item->getAttribute('type') == 'submit')) {
                    $cifrado = $this->cifrar($item->getAttribute('id'));
                    $item->setAttribute('id', $cifrado);
                }

                if (($item->hasAttribute('name') AND $item->tagName != 'select')AND ( $item->tagName != 'meta')) {
                    if ($item->getAttribute('name') != 'login_post' AND $item->getAttribute('name') != 'logout_post') {
                        $cifrado = $this->cifrar($item->getAttribute('name'));
                        if (ACTIVAR_LOG_APACHE_DE_HASH)
                            developer_log('Cifrando atributo name: ' . $item->getAttribute('name') . ' : ' . $cifrado);
                        $item->setAttribute('name', $cifrado);
                    }
                }
                if ($item->tagName == 'select') {
                    if (substr($item->getAttribute('name'), 0, 6) != PREFIJO_PARA_ELEMENTOS_CIFRRADOS) {
                        $cifrado = $this->cifrar($item->getAttribute('name'));
                        if (ACTIVAR_LOG_APACHE_DE_HASH)
                            developer_log('Cifrando atributo name: ' . $item->getAttribute('name') . ' : ' . $cifrado);
                        $item->setAttribute('name', PREFIJO_PARA_ELEMENTOS_CIFRRADOS . $cifrado);
                    }
                }

                if ($item->tagName == 'option') {
                    # Todos los selects se cifran con un prefijo
                    if ($item->hasAttribute('value')) {
                        $cifrado = $this->cifrar($item->getAttribute('value'));
                        if (ACTIVAR_LOG_APACHE_DE_HASH)
                            developer_log('Cifrando atributo value: ' . $item->getAttribute('value') . ' : ' . $cifrado);
                        $item->setAttribute('value', $cifrado);
                    }
                }

                if ($item->hasAttribute('nav')) {
                    $cifrado = $this->cifrar($item->getAttribute('nav'));
                    if (ACTIVAR_LOG_APACHE_DE_HASH)
                        developer_log('Cifrando atributo nav: ' . $item->getAttribute('nav') . ' : ' . $cifrado);
                    $item->setAttribute('nav', $cifrado);
                }

                if ($item->tagName == 'input' AND $item->getAttribute('type') == 'radio') {
                    # Todos los selects se cifran con un prefijo
                    if ($item->hasAttribute('value')) {
                        $item->setAttribute('name', PREFIJO_PARA_ELEMENTOS_CIFRRADOS . $item->getAttribute('name'));
                        $cifrado = $this->cifrar($item->getAttribute('value'));
                        $item->setAttribute('value', $cifrado);
                    }
                }

                if ($item->hasChildNodes())
                    $this->mask($item);#llamado recursivo
            }

        endforeach;

        return $nodos;
    }

    public function unmask($array) {
        # Recorre un array y decodifica todos los ids y names
        if (!ACTIVAR_HASH)
            return $array;
        $nuevo_array = array();
        foreach ($array as $clave => $valor):

            # Descifrado de claves
            if (($clave != 'id' AND $clave != 'pagina') AND ( PREFIJO_PARA_ELEMENTOS_CIFRRADOS != substr($clave, 0, 6))) {

                if (($clave != 'login_post' AND $clave != 'logout_post')AND ( $clave != NOMBRE_HIDDEN_INSTANCIA)) {
                    if ($clave != 'undefined') {
                        $clave_temp = $clave;
                        $clave = $this->descifrar(utf8_decode(rawurldecode($clave)));
                        if (ACTIVAR_LOG_APACHE_DE_HASH)
                            developer_log('Descifrando atributo name: ' . $clave_temp . ' : ' . $clave);
                        $nuevo_array[$clave] = $valor;
                    }
                }
            }

            if (PREFIJO_PARA_ELEMENTOS_CIFRRADOS == substr($clave, 0, 6)) {
                # Es un select que trae cifrado el value
                $clave_temp = substr($clave, 6);
                $clave = $this->descifrar(utf8_decode(rawurldecode(substr($clave, 6))));
                $valor_temp = $valor;
                $valor = $this->descifrar(utf8_decode(rawurldecode($valor)));
                if (ACTIVAR_LOG_APACHE_DE_HASH)
                    developer_log('Descifrando ' . strtolower(PREFIJO_PARA_ELEMENTOS_CIFRRADOS) . ' @clave:' . $clave_temp . ' : ' . $clave . ' @Valor:' . $valor_temp . ' : ' . $valor);
                $nuevo_array[$clave] = $valor;
            }
            if ($clave == 'id') {
                $nuevo_array[$clave] = $this->descifrar(utf8_decode(rawurldecode($valor)));
            }
            # Prefijo especial para que no descifre un valor especifico del id
            if ($clave == 'id' and $valor[0] == '@') {
                $nuevo_array[$clave] = $valor;
            }
            if ($clave == 'pagina')
                $nuevo_array[$clave] = $valor;
            if ($clave == NOMBRE_HIDDEN_INSTANCIA)
                $nuevo_array[NOMBRE_HIDDEN_INSTANCIA] = $valor;
            unset($array[$clave]);

        endforeach;
        return $nuevo_array;
    }

 public function cifrar($texto, $clave = null, $force = false) {
        if (!ACTIVAR_HASH and!$force)
            return $texto;
        
        if ($clave == null)
            $clave = $this->clave_de_cifrado;
        if ($texto === '')
            return '';
//        developer_log("Cifrando dato. $texto");
//        $ident = mcrypt_module_open(ALGORITMO_HASH, '', 'ecb', '');
//        $long_iniciador = mcrypt_enc_get_iv_size($ident);
//        $inicializador = mcrypt_create_iv($long_iniciador, MCRYPT_RAND);
//        mcrypt_generic_init($ident, $clave, $inicializador);
//        $texto_encriptado = mcrypt_generic($ident, $texto);
//        mcrypt_generic_deinit($ident);
//        mcrypt_module_close($ident);
        $cipher = 'aes-256-ctr';
        $digest = 'sha256';
        $key = openssl_digest($clave, $digest, true);
        $iv_length = openssl_cipher_iv_length($cipher);
//        $iv_length = openssl_cipher_iv_length($cipher);
//        $iv = openssl_random_pseudo_bytes($iv_length);
//        $iv = 'w]?\r????L???%?\\';
        $iv = 'teganamoscon9gat';
//        developer_log($iv);
        $cifrado = base64_encode($iv . openssl_encrypt($texto, $cipher, $key, 0, $iv));
        return trim($cifrado);
    }

    public function descifrar($texto_encriptado, $clave = null, $force = false) {
        if (!ACTIVAR_HASH and!$force) {
            developer_log("HASH DESACTIVADO");
            return $texto_encriptado;
        }
        if ($clave == null)
            $clave = $this->clave_de_cifrado;
        if ($texto_encriptado === '')
            return '';
//        developer_log("Descifrando dato.");
        $cipher = 'aes-256-ctr';
        $digest = 'sha256';
        $txt_encriptado = $texto_encriptado;
        
        $texto_encriptado = base64_decode($texto_encriptado,true);
//        var_dump($texto_encriptado);
        if ($texto_encriptado == false) {
            return $txt_encriptado;
        }
//        $ident = mcrypt_module_open(ALGORITMO_HASH, '', 'ecb', '');
//        $long_iniciador = mcrypt_enc_get_iv_size($ident);
//        $inicializador = mcrypt_create_iv($long_iniciador, MCRYPT_RAND);
//        mcrypt_generic_init($ident, $clave, $inicializador);
     
        $key = openssl_digest($clave, $digest, true);
        $iv_length = openssl_cipher_iv_length($cipher);
//        $value = base64_decode($value);
        $iv = substr($texto_encriptado, 0, $iv_length);
        $value = substr($texto_encriptado, $iv_length);
        $desencriptado = @openssl_decrypt($value, $cipher, $key, 0, $iv);
      
        return trim($desencriptado);
    }
    
    public function esta_hasheado($dato){
        return(!is_numeric($dato) and base64_encode(base64_decode($dato, true)) === $dato);
    }
    
    /**
     * Decrypt data from a CryptoJS json encoding string
     *
     * @param mixed $passphrase
     * @param mixed $jsonString
     * @return mixed
     */
    function cryptoJsAesDecrypt( $jsonString) {
        $jsondata = json_decode($jsonString, true);
        if(count($jsondata)==0){
            return $jsondata;
        }
        $salt = hex2bin($jsondata["s"]);
        $ct = base64_decode($jsondata["ct"]);
        $iv = hex2bin($jsondata["iv"]);
        $concatedPassphrase = self::$clave_de_cifrado_estatica . $salt;
        $md5 = array();
        $md5[0] = md5($concatedPassphrase, true);
        $result = $md5[0];
        for ($i = 1; $i < 3; $i++) {
            $md5[$i] = md5($md5[$i - 1] . $concatedPassphrase, true);
            $result .= $md5[$i];
        }
        $key = substr($result, 0, 32);
        $data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
        return json_decode($data, true);
    }

    /**
     * Encrypt value to a cryptojs compatiable json encoding string
     *
     * @param mixed $passphrase
     * @param mixed $value
     * @return string
     */
    function cryptoJsAesEncrypt($passphrase, $value) {
        $salt = openssl_random_pseudo_bytes(8);
        $salted = '';
        $dx = '';
        while (strlen($salted) < 48) {
            $dx = md5($dx . $passphrase . $salt, true);
            $salted .= $dx;
        }
        
        $key = substr($salted, 0, 32);
        $iv = substr($salted, 32, 16);
        $encrypted_data = openssl_encrypt(json_encode($value), 'aes-256-cbc', $key, true, $iv);
        $data = array("ct" => base64_encode($encrypted_data), "iv" => bin2hex($iv), "s" => bin2hex($salt));
        return json_encode($data);
    }

}

?>