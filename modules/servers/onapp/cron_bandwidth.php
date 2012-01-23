<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
date_default_timezone_set('UTC');

define("ROOT", realpath( dirname(__FILE__) .'/../../../' ). '/' );

require_once ROOT . "dbconnect.php";
require_once ROOT . "includes/functions.php";
require_once ROOT . "includes/clientareafunctions.php";
require_once ROOT . "includes/wrapper/OnAppInit.php";
require_once ROOT . "modules/servers/onapp/onapp.php";

$query = "
    SELECT 
        id
    FROM
        tblservers;
";

$result = full_query( $query );

if ( ! $result || mysql_num_rows( $result ) < 1 ) {
    exit;
}

while ( $row = mysql_fetch_assoc( $result ) ) {
    onapp_UsageUpdate( $params = array(
        'serverid'  => $row['id'],
        'extracall' => true,
    ) );
}





