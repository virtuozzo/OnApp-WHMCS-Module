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
               tblservers.username,
               tblservers.password,
               tblhosting.id as hosting_id,
               tblhosting.domain,
               tblhosting.server,
               tblhosting.bwusage,
               tblhosting.lastupdate,
               tblhosting.domainstatus,
               tblhosting.userid,
               tblhosting.suspendreason as email_sent,
               MONTH ( tblhosting.lastupdate ) as lastupdate_month,
               tblproducts.overagesbwlimit as bwlimit,
               tblproducts.overagesdisklimit as disklimit,
               tblproducts.overagesenabled as enabled,
               tblproducts.configoption10 as configoption10,
               tblproducts.configoption22 as bandwidthconfigoption,
               tblhostingconfigoptions.optionid,
               tblproductconfigoptionssub.sortorder as additional_bandwidth,
               tblupgrades.status as upgrade_status,
               tblupgrades.paid as upgrade_paid,
               tblupgrades.id as upgrade_id,
               tblclients.email,
               tblclients.firstname,
               tblclients.lastname
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
           LEFT JOIN tblclients
               ON tblhosting.userid = tblclients.id
           WHERE
               tblproducts.overagesenabled = 1
               AND tblproducts.servertype = 'onapp'";
                
$products_query = full_query( $query );

// Adding Email Template if not exists /////////////////////////////////////////
define("SELECT_BANDWIDTH_LIMIT_NOTIFICATION",
      "SELECT * FROM tblemailtemplates WHERE type='product' AND name='Bandwidth Limit Notification';"
);

define("INSERT_BANDWIDTH_LIMIT_NOTIFICATION",
      "INSERT INTO tblemailtemplates ( type, name, subject, message, plaintext)
          VALUES ('product', 'Bandwidth Limit Notification', 'Bandwidth Limit Reached 90%', 'Dear {\$client_name},<br/><br/>This is a notification that bandwidth usage of you domain {\$domain} has reached 90% of your Bandwidth Limit.', 0 );");

if ( mysql_num_rows( full_query( SELECT_BANDWIDTH_LIMIT_NOTIFICATION ) ) < 1 )
    full_query( INSERT_BANDWIDTH_LIMIT_NOTIFICATION );
/////////////////////////////////////////////////////////////////////////////////

if ( ! $products_query ) 
    die('Bandwidth Usage Update Cron Select Error #' . mysql_error() );

if ( mysql_num_rows( $products_query ) < 1 )
    exit;

$monthBegin = date( 'Y-m-1 00:00:00');
$enddate    = date( 'Y-m-d H:00:00' );
$realdate   = date( 'Y-m-d H:i:s' );

$i = 0;

while( $products = mysql_fetch_assoc( $products_query ) ) {
    // new month begins
    if ( $products['lastupdate_month'] != date('m') ) {
        if ( $products['domainstatus'] == 'Active' ) {
            $products['bwusage'] = 0;
        }
    }

    $onapp = new OnApp_Factory(
        ( $products['hostname'] ) ? $products['hostname'] : $products['ipaddress'],
        $products['username'],
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
        $usage_stats[$i][ $interface->_id ] = $usage->getList( $interface->_virtual_machine_id, $interface->_id, $url_args );
    }

    $traffic = 0;

    foreach ( $usage_stats[$i] as $interface ) {
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

    if ( $traffic > $bandwidth_limit && $checkbox_values[2]  ) {
        echo 'Limit Exceeded. Suspending VM!';
        onapp_SuspendAccount( $products, $onapp );
    }
    elseif( $traffic * 100 / $bandwidth_limit > 90     &&
            $products['email_sent'] == ''              &&
            $products['domainstatus'] != 'Suspended' 
    ) {
       echo 'Sending Email Notification <br />';
       sendMessage(
           'Bandwidth Limit Notification',
           $products['userid'],
           array(
               'client_name' => $products['firstname'] . ' ' . $products['lastname'],
               'domain'      => $products['domain']
           )
       );
       
       full_query( "UPDATE tblhosting SET suspendreason = '1' WHERE id = $products[hosting_id]");
    }
    elseif( $traffic * 100 / $bandwidth_limit < 90     &&
            $products['email_sent'] != ''              &&
            $products['domainstatus'] != 'Suspended'
    ) {
        full_query( "UPDATE tblhosting SET suspendreason = '' WHERE id = $products[hosting_id]");
    }

    $params = array(
        'bwusage'    => $traffic,
        'lastupdate' => $realdate,
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

    $i++;
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
                      suspendreason = 'Bandwidth Limit Exceeded',
                      domainstatus  = 'Suspended'
                  WHERE
                      id = '$products[hosting_id]'";

        $result = full_query( $query );

        if ( ! $result )
            die('Bandwidth Usage Suspend Account Query Error #' . mysql_error() );
    }

        $_vm  = $onapp->factory( 'VirtualMachine', true );

        $vm = $_vm->load( $products['vm_id'] );

        if ( ! $vm->_suspended ) {
            $vm->suspend();
        }
}
