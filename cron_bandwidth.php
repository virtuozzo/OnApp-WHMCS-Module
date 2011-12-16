<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

define("ROOT", "../../../");

require_once ROOT . "dbconnect.php";
require_once ROOT . "includes/functions.php";
require_once ROOT . "includes/clientareafunctions.php";
require_once ROOT . "includes/wrapper/OnAppInit.php";

$query =  "SELECT
               tblonappservices.service_id,
               tblonappservices.vm_id,
               tblservers.ipaddress,
               tblservers.hostname,
               tblhosting.id as hosting_id,
               tblhosting.domain,
               tblhosting.server,
               tblhosting.bwusage,
               tblhosting.lastupdate,
               MONTH ( tblhosting.lastupdate ) as lastupdate_month,
               tblonappclients.email,
               tblonappclients.password
           FROM
               tblonappservices
           LEFT JOIN
               tblhosting ON tblonappservices.service_id = tblhosting.id
           LEFT JOIN
               tblservers ON tblhosting.server = tblservers.id
           LEFT JOIN
               tblonappclients ON tblhosting.server = tblonappclients.server_id";

$products_query = full_query( $query );

if ( ! $products_query ) die('Cron error: No OnApp products');

$monthBegin = date( 'Y-m-1 00:00:00');
$enddate    = date( 'Y-m-d H:00:00' );

while( $products = mysql_fetch_assoc( $products_query ) ) {

    if ( $products['lastupdate_month'] < date('m') ) {
        $products['bwusage'] = 0;
    }

    $onapp = new OnApp_Factory(
        ( $products['hostname'] ) ? $products['hostname'] : $products['ipaddress'],
        $products['email'],
        decrypt( $products['password'])
    );

    $network_interface  = $onapp->factory('VirtualMachine_NetworkInterface');
    $network_interfaces = $network_interface->getList( $products['vm_id']);

    $usage = $onapp->factory('VirtualMachine_NetworkInterface_Usage', true );

    $startdate = ( $products['lastupdate'] === '0000-00-00 00:00:00' || 
                 $products['bwusage'] == '0' )  
                     ? $monthBegin
                     : $products['lastupdate'];

    $url_args = array(
        'period[startdate]' => $startdate,
        'period[enddate]'   => $enddate,
    );

    foreach ( $network_interfaces as $interface ) {
        $usage_stats[ $interface->_id ] = $usage->getList( $interface->_virtual_machine_id, $interface->_id, $url_args );
    }

    $traffic = 0;

    foreach ( $usage_stats as $interface ) {
        foreach ( $interface as $bandwidth ) {
           $traffic  += $bandwidth->_data_sent;
           $traffic  += $bandwidth->_data_received;
        }
    }
    
    $traffic =  $traffic / 1024;
    $traffic += $products['bwusage'];

    $params = array(
        'bwusage'    => $traffic,
        'lastupdate' => $enddate,
        'hosting_id' => $products['hosting_id'],
     );
    
    onapp_UsageUpdate($params); 
}

print('<pre>');print_r($usage);die();

function onapp_UsageUpdate($params) {
    $query = "
        UPDATE
            tblhosting
        SET
            bwusage = '$params[bwusage]',
            lastupdate = '$params[lastupdate]'
        WHERE
            id = '$params[hosting_id]'";

    $result = full_query( $query );
}
