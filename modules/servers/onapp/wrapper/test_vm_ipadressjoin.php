<?
require_once 'ONAPP.php';
require_once 'VirtualMachine/IpAddressJoin.php';


$ipaddressjoin = new ONAPP_VirtualMachine_IpAddressJoin();

$ipaddressjoin->_virtual_machine_id = 239;

$ipaddressjoin->auth(
    "http://109.123.105.194",
    "admin",
    "changeme"
);

//var_dump($ipaddressjoin);
$list = $ipaddressjoin->getList();
//var_dump($ipaddressjoin);
var_dump($list[0]);
?>
