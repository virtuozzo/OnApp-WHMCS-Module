<?php

define( 'LIB_WRAPPER_DIR', dirname(__FILE__).'/wrapper' );

require_once LIB_WRAPPER_DIR.'/Disk.php';
require_once LIB_WRAPPER_DIR.'/User.php';
require_once LIB_WRAPPER_DIR.'/Template.php';
require_once LIB_WRAPPER_DIR.'/IpAddress.php';

require_once LIB_WRAPPER_DIR.'/VirtualMachine.php';
require_once LIB_WRAPPER_DIR.'/VirtualMachine/Backup.php';
require_once LIB_WRAPPER_DIR.'/VirtualMachine/CpuUsage.php';
require_once LIB_WRAPPER_DIR.'/VirtualMachine/IpAddressJoin.php';
require_once LIB_WRAPPER_DIR.'/VirtualMachine/NetworkInterface.php';

$ONAPP_DEFAULT_ROLE  = 2;
$ONAPP_DEFAULT_GROUP = 1;

/**
 * Load $_LANG from language file
 */
function load_language() {
    global $_LANG;
    $dh = opendir (dirname(__FILE__).'/lang/');

    while (false !== $file2 = readdir ($dh)) {
        if (!is_dir ('' . 'lang/' . $file2) ) {
            $pieces = explode ('.', $file2);
            if ($pieces[1] == 'txt') {
                $arrayoflanguagefiles[] = $pieces[0];
                continue;
            }
            continue;
        }
    };

    closedir ($dh);

    $language = $_SESSION['Language'];

    if ( ! in_array ($language, $arrayoflanguagefiles) )
        $language =  "English";

    ob_start ();
    include dirname(__FILE__) . "/lang/$language.txt";
    $templang = ob_get_contents ();
    ob_end_clean ();
    eval ($templang);
}

/**
 * Get server configuration options
 *
 * @param integer $id server id
 */
function get_onapp_config($id){
    if ($id == '')
        return false;

    $sql = sprintf(
        "SELECT 
            id, 
            name, 
            ipaddress, 
            hostname, 
            username, 
            password 
        FROM tblservers 
        WHERE id = '%s'",
        addslashes( $id )
    );

    $onapp_config = mysql_fetch_array(
        full_query($sql)
    );

    // Error if server not found in DB
    if ( $onapp_config ) {
        $onapp_config["password"] = decrypt($onapp_config["password"]);
        $onapp_config["adress"] = $onapp_config["ipaddress"] != "" ? 
            "http://" . $onapp_config["ipaddress"] : 
            $onapp_config["hostname"];
        $onapp_config[] = $onapp_config["adress"];
    } else
//        return array( "error" => "Can't found active OnApp server #".addslashes($id)." in Data Base");
        return false;

    //Error if server adress (IP and hostname) not set
    if ( ! $onapp_config["adress"] )
//        return array( "error" => "OnApp server adress (IP and hostname) not set for #".$onapp_config["id"]." '".$onapp_config["name"]."'" );
        return false;

    return $onapp_config;
}

/**
 * Get service data form DB using global variable $id
 *
 * @return array
 */
function get_service($service_id) {

    $select_service = "SELECT
        tblhosting.id as id,
        userid,
        tblproducts.configoption1 as serverid,
        tblonappservices.vm_id as vmid,
        tblhosting.password,
        tblhosting.domain as domain,
        tblhosting.orderid as orderid,
        tblproducts.name as product,
        tblproducts.configoptionsupgrade,
        tblproducts.configoption1,
        tblproducts.configoption2,
        tblproducts.configoption3,
        tblproducts.configoption4,
        tblproducts.configoption5,
        tblproducts.configoption6,
        tblproducts.configoption7,
        tblproducts.configoption8,
        tblproducts.configoption9,
        tblproducts.configoption10,
        tblproducts.configoption11,
        tblproducts.configoption12,
        tblproducts.configoption13,
        tblproducts.configoption14,
        tblproducts.configoption15,
        tblproducts.configoption16,
        tblproducts.configoption17,
        tblproducts.configoption18,
        0 as additionalram,
        0 as additionalcpus,
        0 as additionalcpushares,
        0 as additionaldisksize,
        0 as additionalips
    FROM
        tblhosting
        LEFT JOIN tblproducts ON tblproducts.id = packageid
        LEFT JOIN tblonappservices ON service_id = tblhosting.id
    WHERE
        servertype = 'onapp'
        AND tblhosting.id = '$service_id'";

    $service_rows = full_query($select_service);

    if ( ! $service_rows )
        return false;

    $service = mysql_fetch_assoc( $service_rows );

    $select_config ="
    SELECT 
        optionssub.id,
        optionssub.optionname,
        options.configid,
        tblproductconfigoptions.optionname as configoptionname, 
        tblproductconfigoptions.optiontype,
        tblproductconfigoptions.qtymaximum AS max,
        tblproductconfigoptions.qtyminimum AS min,
        options.qty, 
        optionssub.sortorder, 
        options.optionid as active
    FROM 
        tblhostingconfigoptions AS options 
        LEFT JOIN tblproductconfigoptionssub AS sub 
            ON options.configid = sub.configid 
            AND optionid = sub.id 
        LEFT JOIN tblproductconfigoptions 
            ON tblproductconfigoptions.id = options.configid 
        LEFT JOIN tblproductconfigoptionssub AS optionssub 
            ON optionssub.configid = tblproductconfigoptions.id
    WHERE
        relid = '$service_id';";

    $config_rows = full_query($select_config);

    if ( ! $config_rows )
        return false;

    $onappconfigoptions = array(
        $service["configoption12"], // additional ram
        $service["configoption13"], // additional cpus
        $service["configoption14"], // additional cpu shares
        $service["configoption15"], // additional disk size
        $service["configoption16"]  // additional ips
    );

    $service["configoptions"] = array();

    while ( $row = mysql_fetch_assoc($config_rows) )
        if ( in_array($row["configid"], $onappconfigoptions ) ) {
            switch ( $row['optiontype'] ) {
                case '1': // Dropdown
                    $row['order'] = $row['sortorder'];
                    break;
                case '2': // Radio
                    $row['order'] = $row['sortorder'];
                    break;
                case '3': // Yes/No
                    $row['order'] = 0;
                    break;
                case '4': // Quantity
                    $row['order'] = $row['qty'] * $row['sortorder'];
                    break;
            };

            if(!isset($service["configoptions"][$row['configid']]))
                $service["configoptions"][$row['configid']] = array(
                    'name'       => $row['configoptionname'],
                    'active'     => $row['active'],
                    'optiontype' => $row['optiontype'],
                    'step'       => 1
                );

            if ( $row["id"] == $row["active"]) {
                if ($service["configoption12"] == $row["configid"]) {
                    $service["additionalram"] = $row["order"];
                    $service["configoptions"][$row['configid']]['order'] = $service['configoption3'];
                    $service["configoptions"][$row['configid']]['step'] = 4;
                    $service["configoptions"][$row['configid']]['prefix'] = 'MB';
                } elseif ($service["configoption13"] == $row["configid"]) {
                    $service["additionalcpus"] = $row["order"];
                    $service["configoptions"][$row['configid']]['order'] = $service['configoption5'];
                    $service["configoptions"][$row['configid']]['prefix'] = '';
                } elseif ($service["configoption14"] == $row["configid"]) {
                    $service["additionalcpushares"] = $row["order"];
                    $service["configoptions"][$row['configid']]['order'] = $service['configoption7'];
                    $service["configoptions"][$row['configid']]['prefix'] = '%';
                } elseif ($service["configoption15"] == $row["configid"]) {
                    $service["additionaldisksize"] = $row["order"];
                    $service["configoptions"][$row['configid']]['order'] = $service['configoption11'];
                    $service["configoptions"][$row['configid']]['prefix'] = 'GB';
                } elseif ($service["configoption16"] == $row["configid"]) {
                    $service["additionalips"] = $row["order"];
                    $service["configoptions"][$row['configid']]['order'] = $service['configoption18'];
                    $service["configoptions"][$row['configid']]['prefix'] = '';
                };

                $service["configoptions"][$row['configid']]['value'] = $row['qty'];
            };

            $service["configoptions"][$row['configid']]['options'][$row['sortorder']] = array(
                'id'   => $row['id'],
                'name' => $row['optionname'],
                'max'  => $row['max'],
                'min'  => $row['min']
            );
            
        };

    return $service;
}

/**
 * Get onapp user data
 *
 * TODO check this
 */
function get_onapp_client( $service_id ) {
    global $ONAPP_DEFAULT_GROUP, $ONAPP_DEFAULT_ROLE;

    $service = get_service($service_id);

    $sql_select = sprintf(
        "SELECT 
            onapp_user_id, 
            email, 
            password 
        FROM 
            tblonappclients 
        WHERE 
            client_id = '%s' AND 
            server_id = '%s';",
        $service['userid'],
        $service['serverid']);

    $user = mysql_fetch_array( full_query($sql_select) );

    if ( $user ) {
        return $user;
    } else {
        $user = new ONAPP_User();

        $onapp_config = get_onapp_config($service['serverid']);

        if ( $service['serverid'] == "" )
            return array( 
                "error" => "Can't create OnApp User 'server id for plan not found'"
            );

        $user->auth(
            $onapp_config["adress"],
            $onapp_config['username'],
            $onapp_config['password']
        );

        $sql_select_client = sprintf(
            "SELECT * FROM tblclients WHERE id = '%s'",
            $service['userid']
        );

        $clientsdetails= mysql_fetch_array( full_query($sql_select_client) );

        $user->_email      = $clientsdetails['email'];
        $user->_password   = $clientsdetails['password'];
        $user->_login      = $clientsdetails['email'];
        $user->_first_name = $clientsdetails['firstname'];
        $user->_last_name  = $clientsdetails['lastname'];

        $user->_group_id   = $ONAPP_DEFAULT_GROUP;

        $user->_role_ids   = array(
            'attributesArray' => array(
                'type' => 'array'
            ),
            'role-id' => $ONAPP_DEFAULT_ROLE
        );

        $user->save();

        if ( ! is_null($user->_obj->error) )
            return array('error' => is_array($user->_obj->error) ?
                "Can't create OnApp User<br/>\n " . implode('.<br/>', $user->_obj->error) :
                "Can't create OnApp User<br/>\n " . $user->_obj->error);
        elseif ( is_null($user->_obj->_id) )
            return array( "error" => "Can't create OnApp User");

        $sql_replace = "REPLACE tblonappclients SET
          server_id = '".$service['serverid']."' ,
          client_id = '".$service["userid"]."' ,
          onapp_user_id = '".$user->_obj->_id."' ,
          password = '".$clientsdetails['password']."' ,
          email = '".$clientsdetails['email']."';";

        if ( full_query($sql_replace) ) {
            return array(
                "onapp_user_id" => $user->_obj->_id,
                "email"         => $clientsdetails["email"],
                "password"      => $clientsdetails['password']
            );
        } else {
            return array( "error" => "Can't update user data in Data Base");
        };
    };
}

/**
 * Get vitual machine data
 *
 * return object ONAPP_VirtualMachine
 */
function get_vm( $service_id ) {
    $user    = get_onapp_client( $service_id );

    $vm = new ONAPP_VirtualMachine();

    if ( isset($user['error']) ) {
        $vm->error = $user['error'];
        return $vm;
    };

    $service = get_service($service_id);

    $onapp_config = get_onapp_config($service['serverid']);

    if (isset($service["vmid"]) && ! is_null($service["vmid"]) ) {

        $vm->auth(
            $onapp_config["adress"],
            $user["email"],
            $user["password"]
        );

        $vm->_id = $service["vmid"];

        $vm->load();
    } else {
        $vm->error = "Cant load Virtual machine";
    };

    return $vm;
}

/**
 * Get service OS templates list
 * 
 * @param integer $serverid service id
 * @param string $templatesid list of templates CSV in format
 *
 * return array list of templates
 */
function get_templates($serverid, $templatesid) {
    $onapp_config = get_onapp_config( $serverid );

    $template = new ONAPP_Template();

    $template->auth(
        $onapp_config["adress"],
        $onapp_config['username'],
        $onapp_config['password']
    );

    $templates_id = explode(',',$templatesid);

    $templates = array();

    foreach( $template->getList() as $template ) {
        if ( in_array( $template->_id, $templates_id) )
            $templates[$template->_id] = $template;
    };

    return $templates;
}

/**
 * Get VM IPs
 */
function get_vm_ips($service_id) {
    $vm             = get_vm($service_id);
    $service        = get_service($service_id);
    $base_ips       = array();
    $additional_ips = array();

    $ips = array();

    if (is_array($vm->_obj->_ip_addresses) )
        foreach( $vm->_obj->_ip_addresses as $ip )
            $ips[$ip->_id] = $ip;

    $select_ips = sprintf("
        SELECT
            serviceid,
            ipid,
            isbase
        FROM tblonappips
        WHERE serviceid = '%s'",
        addslashes($service_id)
    );

    $ips_rows = full_query($select_ips);

    while ( $row = mysql_fetch_assoc($ips_rows) ) {
        if ( $row['isbase'] == 1 ) {
            $base_ips[$row['ipid']] = $ips[$row['ipid']];
            unset($ips[$row['ipid']]);
        } else {
            $additional_ips[$row['ipid']] = $ips[$row['ipid']];
            unset($ips[$row['ipid']]);      
        }
    };

    return array(
        'notresolved' => $ips,
        'base'        => $base_ips,
        'additional'  => $additional_ips,
    );
}

/**
 * Action resolve IP set base
 */
function _action_ip_setbase($service_id, $ipid) {
    $vm      = get_vm($service_id);
    $service = get_service($service_id);

    $ips = get_vm_ips($service_id);

    if( $ipid == "" || ! isset($ips['notresolved'][$ipid]) )
        return array(
            'error' => "Can't found not resolved IP with id #".$ipid,
        );

    if ( $service['configoption18'] - count($ips['base']) < 1 )
        return array(
            'error' => "Can't found not not assigned base IPs ",
        );

    $sql_insert_ip = "REPLACE tblonappips SET
        serviceid  = '$service_id' ,
        ipid       = '$ipid',
        isbase     = 1";

    if( ! full_query($sql_insert_ip) )
        return array('error' => "Can't resolve IP address");

    return array('success' => true);
}

/**
 * Action resolve IP set additional
 */
function _action_ip_setadditional($service_id, $ipid) {
    $vm      = get_vm($service_id);
    $service = get_service($service_id);

    $ips = get_vm_ips($service_id);

    if( $ipid == "" || ! isset($ips['notresolved'][$ipid]) )
        return array(
            'error' => "Can't found not resolved IP with id #".$ipid,
        );

    if ( $service['additionalips']  - count($ips['additional']) < 1 )
        return array(
            'error' => "Can't found not not assigned additional IPs ",
        );

    $sql_insert_ip = "REPLACE tblonappips SET
        serviceid  = '$service_id' ,
        ipid       = '$ipid',
        isbase     = 0";

    if( ! full_query($sql_insert_ip) )
        return array('error' => "Can't resolve IP address");

    return array('success' => true);
}

/**
 * Action assign IP
 */
function _action_ip_add($service_id, $isbase) {
    $service = get_service($service_id);
    $vm      = get_vm($service_id);
    $ips = get_vm_ips($service_id);

    if ( $isbase == 1 && $service['configoption18'] - count($ips['base']) < 1 )
        return array(
            'error' => "Can't found not not assigned base IPs ",
        );
    elseif ( $isbase != 1 && $service['additionalips'] - count($ips['additional']) < 1 )
        return array(
            'error' => "Can't found not not assigned additional IPs ",
        );

    $onapp_config = get_onapp_config($service['serverid']);

    $ipaddress = new ONAPP_IpAddress();

    $ipaddress->auth(
        $onapp_config["adress"],
        $onapp_config['username'],
        $onapp_config['password']
    );

    $ips = $ipaddress->getList($service["configoption6"]);

    if ($ips)
        foreach( $ips as $ip)
            if( $ip->_free == "true" && ! isset($free_ip) )
                $free_ip = $ip;

    if ( ! isset($free_ip) || is_null($free_ip) )
        return array('error' => "Can't found free IP");
    else {
        $networkinterface = new ONAPP_VirtualMachine_NetworkInterface();

        $networkinterface->_virtual_machine_id = $vm->_id;

        $networkinterface->auth(
            $onapp_config["adress"],
            $onapp_config['username'],
            $onapp_config['password']
        );

        $firstnetworkinterface = array_shift( $networkinterface->getList() );
    };

    if ( ! isset($firstnetworkinterface) || is_null($firstnetworkinterface) )
        return array(
            'error' => "Can't found Virtual Machine network interface"
        );

    $ipaddressjoin = new ONAPP_VirtualMachine_IpAddressJoin();

    $ipaddressjoin->_virtual_machine_id   = $vm->_id;
    $ipaddressjoin->_network_interface_id = $firstnetworkinterface->_id;
    $ipaddressjoin->_ip_address_id        = $free_ip->_id;

    $ipaddressjoin->auth(
        $onapp_config["adress"],
        $onapp_config['username'],
        $onapp_config['password']
    );

    $ipaddressjoin->save();

    if ( ! isset($ipaddressjoin->_ip_address_id) )
        return array('error' => "Can't save IP address");

    if ( $isbase == 1 )
        return _action_ip_setbase($service_id, $ipaddressjoin->_ip_address_id);
    else 
        return _action_ip_setadditional($service_id, $ipaddressjoin->_ip_address_id);
}


function _ips_resolve_all() {
////////
};

function _ips_unassign_all() {
////////
}

/**
 * Action delete IP
 */
/*
function _action_ip_delete($id, $ipid) {
    if ( is_null($ipid) )
        return array('error' => 'IP ID not set');

    $vm      = get_vm($id);
    $vm_ips  = get_vm_ips($id);
    $service = get_service($id);
    $onapp_config = get_onapp_config($service['serverid']);

    if ( ! isset( $vm_ips[$ipid] ) )
        return array('error' => "IP adress with id #$ipid does not exist");
    elseif ( $vm_ips[$ipid]['resolved'] )
        return array('error' => "IP adress #$ipid is resolved");

    $ipaddressjoin = new ONAPP_VirtualMachine_IpAddressJoin();

    $ipaddressjoin->auth(
        $onapp_config["adress"],
        $onapp_config['username'],
        $onapp_config['password']
    );

    foreach( $ipaddressjoin->getList($vm->_id) as $ip )
        if ( ! $ip_exist && $ip->_ip_address_id == $ipid ) {
            $ip_join= $ip;
            break;
        };

    $ip_join->_virtual_machine_id = $vm->_id;

    $ip_join->auth(
        $onapp_config["adress"],
        $onapp_config['username'],
        $onapp_config['password']
    );

    $ip_join->delete();

    if ( ! is_null($ip_join->_obj->error) ) {
        return array('error' => is_array($ip_join->_obj->error) ?
            "Can't delete IP Address<br/>\n " . implode('.<br>', $ip_join->_obj->error) :
            "Can't delete IP Address " . $ip_join->_obj->error);
    };

    return true;
}
*/
function create_vm( $service_id, $hostname, $template_id) {

    $vm = new ONAPP_VirtualMachine();

    $service = get_service($service_id);

    $user = get_onapp_client( $service_id );
    if ( isset($user['error']) ) {
        $vm->error = $user['error'];
        return $vm;
    };

    $onapp_config = get_onapp_config( $service['serverid'] );

    $vm->auth(
        $onapp_config["adress"],
        $user["email"],
        $user["password"]
    );

    $memory            = $service['configoption3']  + $service['additionalram'];
    $cpus              = $service['configoption5']  + $service['additionalcpus'];
    $cpu_shares        = $service['configoption7']  + $service['additionalcpushares'];
    $primary_disk_size = $service['configoption11'] + $service['additionaldisksize'];

    $vm->_template_id                    = $template_id;
    $vm->_hypervisor_id                  = $service['configoption4'];
    $vm->_primary_network_id             = $service['configoption6'];
    $vm->_required_virtual_machine_build = 'false';
    $vm->_hostname                       = $hostname;
    $vm->_memory                         = $memory;
    $vm->_cpus                           = $cpus;
    $vm->_cpu_shares                     = $cpu_shares;
    $vm->_primary_disk_size              = $primary_disk_size;
    $vm->_swap_disk_size                 = $service['configoption9'];
    $vm->_label                          = $hostname;
    $vm->_remote_access_password         = decrypt( $service['password'] );
    $vm->_initial_root_password          = decrypt( $service['password'] );
    $vm->_required_ip_address_assignment = '1';
    $vm->_required_automatic_backup      = '0';
    $vm->_rate_limit                     = $service['configoption8'];

    $vm->save();

    if ( ! is_null($vm->_obj->error) ) {
        $vm->error = $vm->_obj->error;
        return $vm;
    } elseif ( is_null($vm->_obj->_id) ) {
        $vm->error = "Can't create virtual machine for service #".$service_id;
        return $vm;
    } else {

        $sql_replace = "REPLACE tblonappservices SET
            service_id = '$service_id',
            vm_id      = '".$vm->_obj->_id."',
            memory     = '$memory',
            cpus       = '$cpus',
            cpu_shares = '$cpu_shares',
            disk_size  = '$primary_disk_size';";

        switch ($vm->_obj->_operating_system) {
            case 'linux':
                $username = "root";
                break;
            case 'windows':
                $username = "administrator";
                break;
        };

        $sql_username_update = "UPDATE tblhosting SET
            username = '$username',
            server = '".$service['serverid']."'
        WHERE
            id = '".$service['id']."';";

        if ( $username != "" )
            full_query($sql_username_update);

        if ( full_query($sql_replace) ) {
            if ( $service['configoption10'] == 'on' )
                $vm->build();

            sendmessage('Virtual Machine Created', $service_id);

//        action_resolveall_ips();
//        action_resolve all_backups();
        } else {
            $vm->error = "Can't add virtual machine in DB";
            return $vm;
        };
    };

    return $vm;
}

function delete_vm( $service_id ) {

    $vm = get_vm( $service_id );

    if( ! isset($vm->_id)) {
        $vm->error = "Can't Load Virtual Machine";
        return $vm;
    };

    $vm->delete();

    if( $vm->error ) {
       $vm->error = "Can't Delete Virtual Machine";
       return $vm;
    };

    $sql_delete_service = sprintf(
        "DELETE FROM tblonappservices WHERE service_id = '%s'",
        $service_id
    );

    if ( ! full_query($sql_delete_service) ) {
        $vm->error = "Can't delete data from tblonappservices";
        return $vm;
    };

//    $sql_delete_ip = "DELETE FROM tblonappaddonips WHERE orderid = '" . $_ONAPPVARS['service']['orderid'] . "';";
//    if ( ! full_query($sql_delete_ip) ) {
//        $_ONAPPVARS['error'] = "Can't delete data from tblonappips";
//        return false;
//    };

    sendmessage('Virtual Machine Deleted', $service_id );

    return $vm;
}

?>
