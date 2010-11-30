<?php
// TODO add onapp $_LANG in to file
// error_reporting(E_ALL);

//require("configuration.php");

require("dbconnect.php");
require("includes/functions.php");
require("includes/clientareafunctions.php");

define( 'PAGE_WRAPPER_DIR', dirname(__FILE__).'/modules/servers/onapp/wrapper' );

require_once PAGE_WRAPPER_DIR.'/Disk.php';
require_once PAGE_WRAPPER_DIR.'/VirtualMachine.php';
require_once PAGE_WRAPPER_DIR.'/VirtualMachine/Backup.php';
require_once PAGE_WRAPPER_DIR.'/VirtualMachine/CpuUsage.php';

require_once dirname(__FILE__).'/modules/servers/onapp/lib.php';

define( "CLIENTAREA", true );

load_language();

/**
 * If they are not logged in divert them
 */
$user_id = $_SESSION["uid"];

if ( ! $user_id ) {
    redirect("clientarea.php");
    exit();
};

/**
 * Set global variables
 */
$_ONAPPVARS = array();

foreach ( array('id', 'page', 'action') as $val )
    $_ONAPPVARS[$val] = get_value($val);

/**
 * Set base noavigation bar
 */
$breadcrumbnav  = ' <a href="index.php">'.$_LANG["globalsystemname"].'</a>';
$breadcrumbnav .= ' &gt; <a href="clientarea.php">'.$_LANG["clientareatitle"].'</a>';
$breadcrumbnav .= ' &gt; <a href="onapp.php">'.$_LANG["onappmyvms"].'</a>';
if ( in_array($_ONAPPVARS['page'], array('productdetails', 'disks', 'cpuusage', 'ipaddresses', 'backups', 'upgrade') ) )
    $breadcrumbnav .= ' &gt; <a title="' .$_LANG["clientareaproductdetails"]. '" href="onapp.php?page=productdetails&id='.$id.'">'.$_LANG["clientareaproductdetails"].'</a>';

/**
 * Check if service exist
 **/
if ( $_ONAPPVARS['id'] !== NULL ) {

    $_ONAPPVARS['service'] = get_service($_ONAPPVARS['id']);

    if ( ! $_ONAPPVARS['service'] )
        $_ONAPPVARS['error'] = sprintf($_LANG["onappservicenotfound"], $id);
    elseif (! is_null($_ONAPPVARS['service']['vmid']) && $_ONAPPVARS['service']['userid'] == $user_id )
        $_ONAPPVARS['vm'] = get_vm($_ONAPPVARS['id']);
};

/**
 * Chose page to show
 */
if ( isset($_ONAPPVARS['page']) && $_ONAPPVARS['service'] && $_ONAPPVARS['service']['userid'] == $user_id )
    switch ( $_ONAPPVARS['page'] ) {
        case 'productdetails':
            productdetails();
            break;
        case 'cpuusage':
            $breadcrumbnav .= ' &gt; <a title="' .$_LANG["onappcpuusage"]. '" href="onapp.php?page=cpuusage&id='.$id.'">'.$_LANG["onappcpuusage"].'</a>';
            productcpuusage();
            break;
        case 'ipaddresses':
            $breadcrumbnav .= ' &gt; <a title="' .$_LANG["onappipaddresses"]. '" href="onapp.php?page=ipaddresses&id='.$id.'">'.$_LANG["onappipaddresses"].'</a>';
            productipaddresses();
            break;
        case 'disks':
            $breadcrumbnav .= ' &gt; <a title="' .$_LANG["onappdisks"]. '" href="onapp.php?page=disks&id='.$id.'">'.$_LANG["onappdisks"].'</a>';
            productdisks();
            break;
        case 'backups':
            $breadcrumbnav .= ' &gt; <a title="' .$_LANG["onappbackups"]. '" href="onapp.php?page=backups&id='.$id.'">'.$_LANG["onappbackups"].'</a>';
            productbackups();
            break;
        case 'upgrade':
            $breadcrumbnav .= ' &gt; <a title="' .$_LANG["onappupgradedowngrade"] .'" href="onapp.php?page=upgrade&id='.$id.'">'.$_LANG["onappupgradedowngrade"].'</a>';
            productupgrade();
            break;
        default:
            $_ONAPPVARS['error'] = sprintf( $_LANG["onapppagenotfound"], $_ONAPPVARS['page'] );
            productdetails();
            break;
    }
else
    clientareaproducts();

/**
 * Redirect to another page
 *
 * @param string $url redirection url
 */
function redirect($url) {
    header('Location: ' . $url);
}

/**
 * Get POST or GET value
 *
 * @param string $name value name
 */
function get_value($name) {
    global $_GET, $_POST;

    return isset($_POST[$name])
        ? $_POST[$name]
        : isset($_GET[$name])
            ? $_GET[$name]
            : NULL;
}

/**
 * Show Client area
 *
 * @param string $templatefile template name
 * @param array $values smarty values
 */
function show_template($templatefile, $values) {
    global $_LANG, $breadcrumbnav, $smartyvalues;

    $pagetitle = $_LANG["clientareatitle"];
    $pageicon = "images/support/clientarea.gif";

    initialiseClientArea($pagetitle, $pageicon, $breadcrumbnav);

    $smartyvalues = $values;

    outputClientArea($templatefile);
}

/**
 * Show user Virtual machines list
 */
function clientareaproducts() {
    global $user_id, $_ONAPPVARS, $_LANG;

    $services = array();
    $not_resolved_vms = array();

// Get OnApp VMs
    $select_onapp_users = sprintf(
        "SELECT 
            *
        FROM
            tblonappclients
            LEFT JOIN tblservers ON tblservers.id = server_id
        WHERE client_id = '%s';",
        $user_id
    );

    $onapp_users_query = full_query($select_onapp_users);

    while ($onapp_user = mysql_fetch_assoc( $onapp_users_query ) ) {
        $vm = new ONAPP_VirtualMachine();

        $vm->auth(
            $onapp_user["ipaddress"] != "" ? $onapp_user["ipaddress"] : $onapp_user["hostname"],
            $onapp_user["email"],
            decrypt($onapp_user["password"])
        );

        $tmp_vms = $vm->getList();

        if ( is_array($tmp_vms) )
            foreach($tmp_vms as $tmp_vm)
                $not_resolved_vms[ $onapp_user["server_id"] ][$tmp_vm->_id] = array(
                  'vm' => $tmp_vm,
                  'server' => $onapp_user
                );
    };

// Get services
    $select_services = "SELECT
        tblhosting.id as id,
        tblhosting.domain as domain,
        tblproducts.configoption1 as serverid,
        tblonappservices.vm_id as vmid,
        tblproducts.name as product
    FROM
        tblhosting
        LEFT JOIN tblproducts ON tblproducts.id = packageid
        LEFT JOIN tblonappservices ON service_id = tblhosting.id
    WHERE
        servertype = 'onapp'
        AND tblhosting.domainstatus = 'Active'
        AND userid = '$user_id'
    ORDER BY tblhosting.id ASC";

    $services_rows = full_query($select_services);

    if ($services_rows)

        while ($service = mysql_fetch_assoc( $services_rows ) ) {
            $services[ $service['id'] ] = $service;

            if ( is_null( $service['vmid'] ) )
                $services[ $service['id'] ]['error'] = $_LANG["onappvmnotcreated"];
            elseif( ! isset( $not_resolved_vms[$service['serverid'] ][$service['vmid']] ) )
                $services[ $service['id'] ]['error'] = sprintf(
                    $_LANG["onappvmnotfound"],
                    $service['vmid']
                );
            else {
                $services[ $service['id'] ]['obj'] = $not_resolved_vms[$service['serverid'] ][$service['vmid']]['vm'];
                unset($not_resolved_vms[$service['serverid'] ][$service['vmid']]);
                if (count($not_resolved_vms[$service['serverid'] ]) == 0 )
                    unset($not_resolved_vms[$service['serverid'] ]);
            };
        };

    show_template(
        "onapp/clientareaproducts",
        array(
            'services'         => $services,
            'not_resolved_vms' => $not_resolved_vms,
            'error'            => isset($_ONAPPVARS['error']) ? $_ONAPPVARS['error'] : NULL,
        )
    );
}

/**
 * Show Virtual machine page
 */
function productdetails() {
    global $_ONAPPVARS;

    if (! isset($_ONAPPVARS['service']) )
        clientareaproducts();
    if ( isset($_ONAPPVARS['action']) && ! isset($_ONAPPVARS['error']) && ! isset($_ONAPPVARS['vm']->_obj->error) )
        _actions_vm($_ONAPPVARS['action']);
    elseif( ! is_null($_ONAPPVARS['service']['vmid']) )
        showproduct();
    else
        showcreateproduct();
}

/**
 * Run action for virtual machine
 */
function _actions_vm($action) {
    global $_ONAPPVARS, $_LANG;

    $action = $_ONAPPVARS['action'];

    if ( ! is_null($action) )
        switch ( $action ) {
            case 'create':
                _action_vm_create();
                break;
            case 'unlock':
                $_ONAPPVARS['vm']->unlock();
                break;
            case 'build':
            case 'rebuild':
                _action_update_res();
                $_ONAPPVARS['vm']->build();
                break;
            case 'start':
                _action_update_res();
                $_ONAPPVARS['vm']->startup();
                break;
            case 'stop':
                $_ONAPPVARS['vm']->shutdown();
                break;
            case 'reboot':
                _action_update_res();
                $_ONAPPVARS['vm']->reboot();
                break;
            case 'delete':
                _action_vm_delete();
                break;
            default:
                $_ONAPPVARS['error'] = sprintf($_LANG["onappactionnotfound"], $action);
                break;
        };

    unset($_ONAPPVARS['action']);

    if ( isset($_ONAPPVARS['vm']) && ! is_null($_ONAPPVARS['vm']->error) )
        $_ONAPPVARS['error'] = is_array($_ONAPPVARS['vm']->error) ?
            implode('.<br>', $_ONAPPVARS['vm']->error) :
            $_ONAPPVARS['vm']->error;
    elseif ( isset($_ONAPPVARS['vm']) && ! is_null($_ONAPPVARS['vm']->_obj->error) )
        $_ONAPPVARS['error'] = is_array($_ONAPPVARS['vm']->_obj->error) ?
            implode('.<br>', $_ONAPPVARS['vm']->_obj->error) :
            $_ONAPPVARS['vm']->_obj->error;

    if ( ! isset($_ONAPPVARS['error']) )
        redirect("onapp.php?page=productdetails&id=".$_ONAPPVARS['id']);
    else
        productdetails();
}

/**
 * Action create virtual machine
 */
function _action_vm_create() {
    global $_ONAPPVARS, $_LANG;

    foreach ( array('templateid', 'hostname' ) as $val )
        $_ONAPPVARS[$val] = get_value($val);
/* TODO check template
    $templates = get_templates($_ONAPPVARS['service']['serverid'], $_ONAPPVARS['service']["configoption2"]);
    $os = $_ONAPPVARS['service']['os'];
    
    if (! is_null($os) && isset($templates[$os]) ) {
        $templates = array(
            $os => $templates[$os]
        );
    };
*/
    if( isset($_ONAPPVARS['vm']) )
        $_ONAPPVARS['error'] =  $_LANG["onappvmexist"];
    elseif ( ! isset($_ONAPPVARS['hostname'] ) || $_ONAPPVARS['hostname'] == "" )
        $_ONAPPVARS['error'] =  $_LANG["onapphostnamenotfound"];
    elseif ( ! isset($_ONAPPVARS['templateid']) )
        $_ONAPPVARS['error'] = $_LANG["onapptemplatenotset"];

    if ( isset($_ONAPPVARS['error']) )
        return false;

    $_ONAPPVARS['vm'] = create_vm($_ONAPPVARS['id'], $_ONAPPVARS['hostname'], $_ONAPPVARS['templateid'] );
    _ips_resolve_all($_ONAPPVARS['id']);

    return true;
}

function _action_vm_delete() {
    global $_ONAPPVARS;

    $_ONAPPVARS['vm'] = delete_vm( $_ONAPPVARS['id'] );

    return true;
}

function _action_update_res() {
    global $_ONAPPVARS;

    $vm           = $_ONAPPVARS['vm']->_obj;
    $service      = $_ONAPPVARS['service'];
    $user         = get_onapp_client( $_ONAPPVARS['id'] );
    $onapp_config = get_onapp_config( $service['serverid'] );

    $memory            = $service['configoption3']  + $service['additionalram'];
    $cpus              = $service['configoption5']  + $service['additionalcpus'];
    $cpu_shares        = $service['configoption7']  + $service['additionalcpushares'];
    $primary_disk_size = $service['configoption11'] + $service['additionaldisksize'];

    // Adjust Resource Allocations
    if ( $vm->_memory != $memory ||
         $vm->_cpus != $cpus ||
         $vm->_cpu_shares != $cpu_shares
    ) {
        $_ONAPPVARS['vm']->_memory            = $memory;
        $_ONAPPVARS['vm']->_cpus              = $cpus;
        $_ONAPPVARS['vm']->_cpu_shares        = $cpu_shares;
        $_ONAPPVARS['vm']->_primary_disk_size = $primary_disk_size;

        $_ONAPPVARS['vm']->save();

        if(! is_null($_ONAPPVARS['vm']->_obj->error) || ! is_null($_ONAPPVARS['vm']->error))
            return false;
    };

    // Change Disk size
    $disks = new ONAPP_Disk();

    $disks->auth(
        $onapp_config["adress"],
        $user["email"],
        $user["password"]
    );

    $primary_disk = null;

    foreach($disks->getList( $_ONAPPVARS['vm']->_id ) as $disk )
        if( $disk->_primary == "true" )
            $primary_disk = $disk;

    if ( $primary_disk->_disk_size != $primary_disk_size ) {
        $primary_disk->_disk_size = $primary_disk_size;

        $primary_disk->auth(
            $onapp_config["adress"],
            $user["email"],
            $user["password"]
        );

        $primary_disk->save();

        if(! is_null($primary_disk->_obj->error) || ! is_null($primary_disk->error))
            return false;
    };

    // resolve all IPs
    _ips_resolve_all($_ONAPPVARS['id']);

    return true;
}

/**
 * Show virtual machine details
 */
function showproduct() {
    global $_ONAPPVARS, $_LANG;

    $onapp_config = get_onapp_config( $_ONAPPVARS['service']['serverid'] );

    if ( ! is_null($_ONAPPVARS['vm']->error) ) {
        $_ONAPPVARS['error'] = is_array($_ONAPPVARS['vm']->error) ?
            implode('.<br>', $_ONAPPVARS['vm']->error) :
            $_ONAPPVARS['vm']->error;

        clientareaproducts();
    } elseif ( is_null($_ONAPPVARS['vm']->_id) ) {
        $_ONAPPVARS['error'] = sprintf(
            $_LANG["onappvmnotfoundonserver"],
            $_ONAPPVARS['service']['vmid'],
            $onapp_config["adress"]
        );

        clientareaproducts();
    } else
        show_template(
            "onapp/clientareaoverview",
            array(
                'virtualmachine'       => $_ONAPPVARS['vm']->_obj,
                'id'                   => $_ONAPPVARS['id'],
                'error'                => isset($_ONAPPVARS['error']) ? $_ONAPPVARS['error'] : NULL,
                'configoptionsupgrade' => $_ONAPPVARS['service']['configoptionsupgrade'],
            )
        );
}

/**
 * Show user Virtual machine creation
 */
function showcreateproduct() {
    global $_ONAPPVARS;

    $templates = get_templates($_ONAPPVARS['service']['serverid'], $_ONAPPVARS['service']["configoption2"]);
    $os = $_ONAPPVARS['service']['os'];

    if (! is_null($os) && isset($templates[$os]) ) {
        $templates = array(
            $os => $templates[$os]
        );
    };

    show_template(
        "onapp/clientareacreateproduct",
        array(
            'service'   => $_ONAPPVARS['service'],
            'templates' => $templates,
            'error'     => isset($_ONAPPVARS['error']) ? $_ONAPPVARS['error'] : NULL,
        )
    );
}

/**
 * Show Virtual machine CPU usage
 */
function productcpuusage() {
    global $_ONAPPVARS;

    $onapp_config = get_onapp_config( $_ONAPPVARS['service']['serverid'] );

    $cpuusage = new ONAPP_VirtualMachine_CpuUsage();

    $cpuusage->_virtual_machine_id = $_ONAPPVARS['vm']->_id;

    $user = get_onapp_client( $_ONAPPVARS['id'] );

    $cpuusage->auth(
        $onapp_config["adress"],
        $user["email"],
        $user["password"]
    );

    $list = $cpuusage->getList();

    $xaxis = '';
    $yaxis = '';

    for ($i = 0; $i < count($list); $i++) {
        $created_at = str_replace(array('T', 'Z'), ' ', $list[$i]->_created_at);
        $xaxis .= "<value xid='$i'>".$created_at."</value>";

        $usage = $list[$i]->_cpu_time/($list[$i]->_elapsed_time * 10);
        $yaxis .= "<value xid='$i'>".number_format($usage, 2)."</value>";
    }

    show_template(
        "onapp/clientareacpuusage",
        array(
            'id'                   => $_ONAPPVARS['id'],
            'templates'            => $templates,
            'xaxis'                => $xaxis,
            'yaxis'                => $yaxis,
            'address'              => $onapp_config["adress"],
            'error'                => isset($_ONAPPVARS['error']) ? $_ONAPPVARS['error'] : NULL,
            'configoptionsupgrade' => $_ONAPPVARS['service']['configoptionsupgrade'],
        )
    );
}

/**
 * Show virtual machine addresses
 */
function productipaddresses() {
    global $_ONAPPVARS, $_LANG;

    foreach ( array('ipid' ) as $val )
        $_ONAPPVARS[$val] = get_value($val);

    $action = $_ONAPPVARS['action'];

    if( ! is_null($action) && $action != "" )
        switch ( $action ) {
            case 'setbase':
                $return = _action_ip_setbase($_ONAPPVARS['id'], $_ONAPPVARS['ipid']);
                break;
            case 'setadditional':
                $return = _action_ip_setadditional($_ONAPPVARS['id'], $_ONAPPVARS['ipid']);
                break;
            case 'assignbase':
                $return = _action_ip_add($_ONAPPVARS['id'], 1);
                break;
            case 'assignadditional':
                $return = _action_ip_add($_ONAPPVARS['id'], 0);
                break;
            case 'resolveall':
                $return = _ips_resolve_all($_ONAPPVARS['id']);
                break;
            case 'delete':
                $return = _action_ip_delete($_ONAPPVARS['id'], $_ONAPPVARS['ipid']);
                break;
            default:
                $_ONAPPVARS['error'] = sprintf($_LANG["onappactionnotfound"], $action);
                break;
        };

    if ( isset($return) )
        if ( isset($return['error']) )
            $_ONAPPVARS['error'] = $return['error'];
        else
            redirect("onapp.php?page=ipaddresses&id=" . $_ONAPPVARS['id']);

    clientareaipaddresses();
}

/**
 * Show Virtual machine network adresses
 */
function clientareaipaddresses() {
    global $_ONAPPVARS;

    $service = $_ONAPPVARS['service'];

    $ips = get_vm_ips($_ONAPPVARS['id']);

    show_template(
        "onapp/clientareaipaddresses",
        array(
            'base_ips'                => $ips['base'],
            'additional_ips'          => $ips['additional'],
            'not_resolved_ips'        => $ips['notresolved'],
            'not_resloved_base'       => $service['configoption18'] - count($ips['base']),
            'not_resloved_additional' => $service['additionalips']  - count($ips['additional']),
            'id'                      => $_ONAPPVARS['id'],
            'service'                 => $_ONAPPVARS['service'],
            'error'                   => isset($_ONAPPVARS['error']) ? $_ONAPPVARS['error'] : NULL,
            'configoptionsupgrade'    => $_ONAPPVARS['service']['configoptionsupgrade'],
        )
    );
}

/**
 * Show Virtual machine Disks
 */
function productdisks() {
    global $_ONAPPVARS;

    $onapp_config = get_onapp_config($_ONAPPVARS['service']['serverid']);

    $disks = new ONAPP_Disk();

    $user = get_onapp_client( $_ONAPPVARS['id'] );

    $disks->auth(
        $onapp_config["adress"],
        $user["email"],
        $user["password"]
    );

    show_template(
        "onapp/clientareadisks",
        array(
            'disks'                => $disks->getList( $_ONAPPVARS['vm']->_id ),
            'id'                   => $_ONAPPVARS['id'],
            'error'                => isset($_ONAPPVARS['error']) ? $_ONAPPVARS['error'] : NULL,
            'configoptionsupgrade' => $_ONAPPVARS['service']['configoptionsupgrade'],
        )
    );
}

/**
 * Show Product Backups
 */
function productbackups() {
    global $_ONAPPVARS;

    foreach ( array('diskid', 'backupid' ) as $val )
        $_ONAPPVARS[$val] = get_value($val);

    $action = $_ONAPPVARS['action'];

    if( ! is_null($action) && $action != "" )
        switch ( $action ) {
            case 'add':
                $return = _action_backup_add($_ONAPPVARS['id'], $_ONAPPVARS['diskid']);
                break;
            case 'restore':
                $return = _action_backup_restore($_ONAPPVARS['id'], $_ONAPPVARS['backupid']);
                break;
            default:
                $_ONAPPVARS['error'] = sprintf($_LANG["onappactionnotfound"], $action);
                break;
        };

    if ( isset($return) )
        if ( isset($return['error']) )
            $_ONAPPVARS['error'] = $return['error'];
        else
            redirect("onapp.php?page=backups&id=".$_ONAPPVARS['id']);

    clientareabackups();
}

/**
 * Show Virtual machine Backups
 */
function clientareabackups() {
    global $_ONAPPVARS;

    $onapp_config = get_onapp_config($_ONAPPVARS['service']['serverid']);

    $backups = new ONAPP_VirtualMachine_Backup();

    $backups->_virtual_machine_id = $_ONAPPVARS['vm']->_id;

    $user = get_onapp_client( $_ONAPPVARS['id'] );

    $backups->auth(
        $onapp_config["adress"],
        $user["email"],
        $user["password"]
    );

    show_template(
        "onapp/clientareabackups",
        array(
            'backups'              => $backups->getList(),
            'id'                   => $_ONAPPVARS['id'],
            'error'                => isset($_ONAPPVARS['error']) ? $_ONAPPVARS['error'] : NULL,
            'configoptionsupgrade' => $_ONAPPVARS['service']['configoptionsupgrade'],
        )
    );
}

/**
 * Action create backup
 */
function _action_backup_add( $id, $diskid ) {
    if ( is_null($diskid) )
        return array('error' => 'Disk ID not set');
    
    $vm           = get_vm($id);
    $service      = get_service($id);
    $onapp_config = get_onapp_config($service['serverid']);

    $backup = new ONAPP_VirtualMachine_Backup();

    $backup->_virtual_machine_id = $vm->_id;
    $backup->_disk_id            = $diskid;

    $user = get_onapp_client( $id );

    $backup->auth(
        $onapp_config["adress"],
        $user["email"],
        $user["password"]
    );

    $backup->save();

    if ( ! is_null($backup->_obj->error) )
        return array(
            'error' => is_array($backup->_obj->error) ?
                implode('.<br>', $backup->_obj->error) :
                $backup->_obj->error
        );
    elseif ( is_null($backup->_obj->_id) )
        return array('error' => "Can't create Backup");

    return true;
}

/**
 * Action restore backup
 */
function _action_backup_restore( $id, $backupid ) {
    if ( is_null($backupid) )
        return array('error' => 'Backup ID not set');

    $vm           = get_vm($id);
    $service      = get_service($id);
    $onapp_config = get_onapp_config($service['serverid']);

    $backup = new ONAPP_VirtualMachine_Backup();

    $backup->_id = $backupid;

    $user = get_onapp_client( $id );

    $backup->auth(
        $onapp_config["adress"],
        $user["email"],
        $user["password"]
    );

    $backup->restore();

    if ( ! is_null($backup->_obj->error) )
        return array(
            'error' => is_array($backup->_obj->error) ?
                "Can't create Backup<br/>\n " . implode('.<br>', $backup->_obj->error) :
                "Can't create Backup '" . $backup->_obj->error
        );
}

function productupgrade() {
    global $_ONAPPVARS, $_LANG;

    $onapp_config = get_onapp_config($_ONAPPVARS['service']['serverid']);

    $service = $_ONAPPVARS['service'];

    if ( ! is_null($_ONAPPVARS['vm']->error) ) {
        $_ONAPPVARS['error'] = is_array($_ONAPPVARS['vm']->error) ?
            implode(', ', $_ONAPPVARS['vm']->error) :
            $_ONAPPVARS['vm']->error;

        clientareaproducts();
    } elseif ( is_null($_ONAPPVARS['vm']->_id) ) {
        $_ONAPPVARS['error'] = sprintf(
            $_LANG["onappvmnotfoundonserver"],
            $_ONAPPVARS['service']['vmid'],
            $onapp_config["adress"]
        );

        clientareaproducts();
    } else
        show_template(
            "onapp/clientareaupgrade",
            array(
                'virtualmachine' => $_ONAPPVARS['vm']->_obj,
                'service'        => $service,
                'configoptions'  => $service['configoptions'],
                'id'             => $_ONAPPVARS['id'],
                'error'          => isset($_ONAPPVARS['error']) ? $_ONAPPVARS['error'] : NULL,
            )
        );
}

?>
