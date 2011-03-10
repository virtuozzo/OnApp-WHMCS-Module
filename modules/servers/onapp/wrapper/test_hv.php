<?
require_once 'ONAPP.php';
require_once 'Hypervisor.php';


$hv = new ONAPP_Hypervisor();

$hv->auth(
    "http://109.123.105.194",
    "admin",
    "changeme"
);

//var_dump($hv);
$list = $hv->getList();
//var_dump($hv);
var_dump($list[0]);
?>
