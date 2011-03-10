<?
require_once 'ONAPP.php';
require_once 'DataStore.php';


$datastore = new ONAPP_DataStore();

$datastore->auth(
    "http://109.123.105.194",
    "admin",
    "changeme"
);

//var_dump($datastore);
$list = $datastore->getList();
//var_dump($datastore);
var_dump($list[0]);
?>
