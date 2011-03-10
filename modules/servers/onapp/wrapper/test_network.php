<?
require_once 'ONAPP.php';
require_once 'Network.php';


$network = new ONAPP_Network();

$network->auth(
    "http://109.123.105.194",
    "admin",
    "changeme"
);

//var_dump($network);
$list = $network->getList();
//var_dump($network);
var_dump($list[0]);
?>
