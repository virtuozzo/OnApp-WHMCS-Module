<?
require_once 'ONAPP.php';
require_once 'Template.php';


$tmp = new ONAPP_Template();

$tmp->auth(
    "http://109.123.105.194",
    "admin",
    "changeme"
);

//var_dump($tmp);
$list = $tmp->getList();
//var_dump($tmp);
var_dump($list[0]);
?>
