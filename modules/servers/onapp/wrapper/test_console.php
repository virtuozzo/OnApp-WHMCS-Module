<?
require_once 'ONAPP.php';
require_once 'Console.php';


$console = new ONAPP_Console();

$console->auth(
    "http://109.123.105.194",
    "admin",
    "changeme"
);

$console->load(280);
//var_dump($console);
var_dump($console);
?>
