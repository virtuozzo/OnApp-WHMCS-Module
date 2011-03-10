<?
require_once 'ONAPP.php';
require_once 'VirtualMachine.php';


$vm = new ONAPP_VirtualMachine();

$vm->auth(
    "http://109.123.105.194",
    "admin",
    "changeme"
);

//var_dump($vm);
$list = $vm->getList();
//var_dump($vm);
var_dump($list[0]);
?>
