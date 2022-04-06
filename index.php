<?php
$log_file = "./my-errors.log";
// setting error logging to be active
ini_set("log_errors", TRUE); 
  
// setting the logging file in php.ini
ini_set('error_log', $log_file);

include_once("public/index.php");

?>