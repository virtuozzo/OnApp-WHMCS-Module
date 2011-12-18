<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
date_default_timezone_set('UTC');

define("ROOT", "../../../");

require_once ROOT . "dbconnect.php";
require_once ROOT . "includes/functions.php";
require_once ROOT . "includes/clientareafunctions.php";
require_once ROOT . "includes/wrapper/OnAppInit.php";

$query =  "SELECT
               DISTINCT tblonappservices.service_id,
               tblonappservices.vm_id,
               tblservers.ipaddress,
               tblservers.hostname,
               tblhosting.id as hosting_id,
               tblhosting.domain,
               tblhosting.server,
               tblhosting.bwusage,
               tblhosting.lastupdate,
               tblhosting.domainstatus,
               MONTH ( tblhosting.lastupdate ) as lastupdate_month,
               tblonappclients.email,
               tblonappclients.password,
               tblproducts.overagesbwlimit as bwlimit,
               tblproducts.overagesdisklimit as disklimit,
               tblproducts.overagesenabled as enabled,
               tblproducts.configoption10 as configoption10,
               tblproducts.configoption22 as bandwidthconfigoption,
               tblhostingconfigoptions.optionid,
               tblproductconfigoptionssub.sortorder as additional_bandwidth,
               tblupgrades.status as upgrade_status,
               tblupgrades.paid as upgrade_paid,
               tblupgrades.id as upgrade_id
           FROM
               tblonappservices
           LEFT JOIN
               tblhosting ON tblonappservices.service_id = tblhosting.id
           LEFT JOIN
               tblservers ON tblhosting.server = tblservers.id
           LEFT JOIN
               tblonappclients ON tblhosting.server = tblonappclients.server_id
           LEFT JOIN
               tblproducts ON tblhosting.packageid = tblproducts.id
           LEFT JOIN 
               tblhostingconfigoptions
               ON tblhostingconfigoptions.relid = tblhosting.id
               AND tblhostingconfigoptions.configid = tblproducts.configoption22
           LEFT JOIN
               tblproductconfigoptionssub
               ON
               tblhostingconfigoptions.optionid = tblproductconfigoptionssub.id
           LEFT JOIN
               tblupgrades 
               ON tblupgrades.newvalue = tblhostingconfigoptions.optionid
               AND tblupgrades.id = (SELECT MAX( id ) FROM tblupgrades WHERE
               newvalue = tblhostingconfigoptions.optionid )
           WHERE
               tblproducts.overagesenabled = 1
               AND tblproducts.servertype = 'onapp'";
                
$products_query = full_query( $query );

if ( ! $products_query ) 
    die('Bandwidth Usage Update Cron Select Error #' . mysql_error() );

if ( mysql_num_rows( $products_query ) < 1 )
    exit;

$monthBegin = date( 'Y-m-1 00:00:00');
$enddate    = date( 'Y-m-d H:00:00' );

while( $products = mysql_fetch_assoc( $products_query ) ) {
    // new month begins
    if ( $products['lastupdate_month'] != date('m') ) {
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
        'period[startdate]'      => $startdate,
        'period[enddate]'        => $enddate,
//        'period[use_local_time]' => '1'
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
    
    // Count bandwidth limit + upgrades if needed
    $bandwidth_limit = (
        $products['optionid']                        &&
        $products['additional_bandwidth']            &&
        $products['upgrade_status']  == 'Completed'  &&
        $products['upgrade_paid'] == 'Y'
    )
    ? $products['bwlimit'] + $products['additional_bandwidth']
    : $products['bwlimit'];

    // Checking whether suspend account
    $checkbox_values = explode(',', $products['configoption10'] );

    if ( $traffic > $products['bwlimit'] && $checkbox_values[2]  ) {
        onapp_SuspendAccount( $products, $onapp );
    }
    
//    elseif( $traffic * 100 / $bandwidth_limit > 90 ) {
//        // send email notification
//    }

    $params = array(
        'bwusage'    => $traffic,
        'lastupdate' => $enddate,
        'hosting_id' => $products['hosting_id'],
        'disklimit'  => $products['disklimit'],
        'bwlimit'    => $bandwidth_limit,
     );
    
//********************************** Debug block BEGIN *************************
//******************************************************************************
//    print('<pre>'); print_r($products); echo '<br />';
//    echo 'bwlimit => ' .$products['bwlimit'] . ' + ';
//    echo 'additional bw => ' . $products['additional_bandwidth'] . ' = ';
//    echo $bandwidth_limit, '<br /><br />';
//    echo 'Updating bwusage => <br />';
//    print('<pre>'); print_r($params);
//    echo '<hr />';
//********************************** Debug block END ***************************
//******************************************************************************

    onapp_UsageUpdate($params);
}

function onapp_UsageUpdate($params) {
    $query = "
        UPDATE
            tblhosting
        SET
            bwusage    = '$params[bwusage]',
            lastupdate = '$params[lastupdate]',
            bwlimit    = '$params[bwlimit]',
            disklimit  = '$params[disklimit]'
        WHERE
            id = '$params[hosting_id]'";

    $result = full_query( $query );
    
    if ( ! $result )
        die('Bandwidth Usage Update Query Error #' . mysql_error() );
}

function onapp_SuspendAccount( $products, $onapp ){
    if ( $products['domainstatus'] == 'Active' ) {
        $query = "UPDATE
                      tblhosting
                  SET
                      suspendreason = 'Bandwidth Limit Exceeded'
                      domainstatus  = 'Suspended'
                  WHERE
                      id = '$params[hosting_id]'";

        $result = full_query( $query );
        
        if ( ! $result )
            die('Bandwidth Usage Suspend Account Query Error #' . mysql_error() );

        $_vm  = $onapp->factory( 'VirtualMachine', true );

        $vm = $_vm->load( $products['vm_id'] );

        if ( ! $vm->_suspended ) {
            $vm->suspend();
        }
    }
}
