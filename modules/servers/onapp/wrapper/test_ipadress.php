<?
require_once 'ONAPP.php';
require_once 'IpAddress.php';


$ipaddressjoin = new ONAPP_IpAddress();

$ipaddressjoin->auth(
    "http://109.123.105.194",
    "admin",
    "changeme"
);

$ipaddressjoin->_network_id= 1;

//var_dump($ipaddressjoin);
$list = $ipaddressjoin->getList();
//var_dump($ipaddressjoin);
var_dump($list[0]);
?>
