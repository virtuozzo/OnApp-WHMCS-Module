<?php

define( 'LIB_WRAPPER_DIR', dirname(__FILE__).'/wrapper' );

require_once LIB_WRAPPER_DIR.'/OnAppInit.php';

function onapp_Config( $id ) {
    global $_LANG;

    $sql = "SELECT id, name, ipaddress, hostname, username, password FROM tblservers WHERE id = " . addslashes( $id );

    $onapp_config = mysql_fetch_array(
        full_query($sql)
    );

    // Error if server not found in DB

    if ( $onapp_config ) {
        $onapp_config["password"] = decrypt($onapp_config["password"]);
        $onapp_config["adress"] = $onapp_config["ipaddress"] != "" ?
            $onapp_config["ipaddress"] :
            $onapp_config["hostname"];
        $onapp_config[] = $onapp_config["adress"];
   } else
        return array(
            "error" => sprintf( $_LANG["onapperrcantfoundserver"], $id)
        );

    //Error if server adress (IP and hostname) not set

    if ( ! $onapp_config["adress"] )
        return array(
            "error" => sprintf(
                $_LANG["onapperrcantfoundadress"],
                $onapp_config["id"],
                $onapp_config["name"]
            ) );

    return $onapp_config;
}

#hug to change service status when admin Create service
function serviceStatus($id, $status = NULL) {
    $select = "select * FROM tblhosting WHERE id = '$id'";
    $rows = full_query($select);
    if ( ! $rows )
        return false;

    $service = mysql_fetch_assoc( $rows );

    $old_status = $service["domainstatus"];

    if ( is_null($status) )
        return $old_status;

    $update = "UPDATE tblhosting SET domainstatus = '$status' WHERE id = '$id'";
    return full_query($update);
}

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

    if( ! in_array ($language, $arrayoflanguagefiles) ) {
		$language =  "English";
	}

	if( file_exists( dirname(__FILE__) . "/lang/$language.txt" ) ) {
        ob_start ();
        include dirname(__FILE__) . "/lang/$language.txt";
        $templang = ob_get_contents ();
		ob_end_clean ();
        eval ($templang);
    }
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
        tblproducts.id as productid,
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
        tblproducts.configoption19,
        tblproducts.configoption20,
        tblproducts.configoption21,
        0 as additionalram,
        0 as additionalcpus,
        0 as additionalcpushares,
        0 as additionaldisksize,
        0 as additionalips,
        0 as additionalportspead
    FROM
        tblhosting
        LEFT JOIN tblproducts ON tblproducts.id = packageid
        LEFT JOIN tblonappservices ON service_id = tblhosting.id
    WHERE
        servertype = 'onapp'
        AND tblhosting.id = '$service_id'
        AND tblhosting.domainstatus = 'Active'";

    $service_rows = full_query($select_service);

    if ( ! $service_rows )
        return false;

    $service = mysql_fetch_assoc( $service_rows );

    $productid =  $service["productid"];

    $select_config ="
    SELECT
        optionssub.id,
        optionssub.optionname,
        sub.configid,
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
        relid = '$service_id'
    ORDER BY optionssub.id ASC;";

    $select_config = "
    SELECT
        optionssub.id,
        optionssub.optionname,
        tblproductconfigoptions.id as configid,
        tblproductconfigoptions.optionname as configoptionname,
        tblproductconfigoptions.optiontype,
        tblproductconfigoptions.qtymaximum AS max,
        tblproductconfigoptions.qtyminimum AS min,
        options.qty,
        optionssub.sortorder,
        IF(options.optionid, options.optionid, optionssub.id) as active
    FROM
        tblproductconfiglinks
        LEFT JOIN tblproductconfigoptions
            ON tblproductconfigoptions.gid = tblproductconfiglinks.gid
        LEFT JOIN tblhostingconfigoptions AS options
            ON options.configid = tblproductconfigoptions.id AND relid = $service_id
        LEFT JOIN tblproductconfigoptionssub AS sub
            ON options.configid = sub.configid
            AND optionid = sub.id
        LEFT JOIN tblproductconfigoptionssub AS optionssub
            ON optionssub.configid = tblproductconfigoptions.id
    WHERE
        tblproductconfiglinks.pid = $productid
    ORDER BY optionssub.id ASC;";
    $config_rows = full_query($select_config);

    if ( ! $config_rows )
        return false;

    $onappconfigoptions = array(
        $service["configoption12"], // additional ram
        $service["configoption13"], // additional cpus
        $service["configoption14"], // additional cpu shares
        $service["configoption15"], // additional disk size
        $service["configoption16"], // additional ips
        $service["configoption19"], // operation system
        $service["configoption20"],  // port spead
        $service["configoption21"],  // user role
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
                    'sortorder'  => $row['sortorder']
                );

            if ( $row["id"] == $row["active"]) {
                if ($service["configoption12"] == $row["configid"]) {
                    $service["additionalram"] = $row["order"];
                    $service["configoptions"][$row['configid']]['order'] = $service['configoption3'];
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
                } elseif ($service["configoption20"] == $row["configid"]) {
                    $service["additionalportspead"] = $row["order"];
                    $service["configoptions"][$row['configid']]['order'] = $service['configoption8'];
                    $service["configoptions"][$row['configid']]['prefix'] = 'Mbps';
                } elseif ($service["configoption19"] == $row["configid"]) {
                    $service["os"] = $row["order"];
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
 */
function get_onapp_client( $service_id, $ONAPP_DEFAULT_BILLING_PLAN = 1 ) {
    global $_LANG;

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

    if ( $user ) 
//  {     $user_obj = new OnApp_User();
//
//        $onapp_config = get_onapp_config($service['serverid']);
//
//        if ( $service['serverid'] == "" )
//            return array(
//                "error" => $_LANG['onappcantcreateuser']
//            );
//
//        $user_obj->auth(
//            $onapp_config["adress"],
//            $onapp_config['username'],
//            $onapp_config['password']
//        );
//
//        $user_obj->load( $user['onapp_user_id'] );
//
//        if ( $service['configoption21'] != $user_obj->_obj->_roles[0]->_id ) {
//            $user_obj->_id = $user['onapp_user_id'];
//            $user_obj->_role_ids = array( $service['configoption21'] );
//            $user_obj->save();
//        }
//  }
        $user["password"] = decrypt( $user["password"] );
    else { 
        $user = new OnApp_User();

        $onapp_config = get_onapp_config($service['serverid']);

        if ( $service['serverid'] == "" )
            return array(
                "error" => $_LANG['onappcantcreateuser']
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

        $clientsdetails = mysql_fetch_array( full_query($sql_select_client) );

        $user->_email      = $clientsdetails['email'];
        $user->_password   = $clientsdetails['password'];
        $user->_login      = $clientsdetails['email'];
        $user->_first_name = $clientsdetails['firstname'];
        $user->_last_name  = $clientsdetails['lastname'];
        $user->_billing_plan_id = $ONAPP_DEFAULT_BILLING_PLAN;
        $user->_role_ids   = ( $service['configoption21'] != '') ? 
            array( $service['configoption21'] ) :
            array ( 2 ) ;
        $user->save();

##TODO LOCALIZE
        if ( ! is_null($user->getErrorsAsArray()) ) {
            return array('error' => $user->getErrorsAsString('<br>'));
		}
        if ( ! is_null($user->_obj->getErrorsAsArray()) )
            return array('error' => $user->_obj->getErrorsAsString('<br>'));
        elseif ( is_null($user->_obj->_id) )
            return array( "error" => "Can't create OnApp User");

        $sql_replace = "REPLACE tblonappclients SET
          server_id = '".$service['serverid']."' ,
          client_id = '".$service["userid"]."' ,
          onapp_user_id = '".$user->_obj->_id."' ,
          password = '".encrypt($clientsdetails['password'])."' ,
          email = '".$clientsdetails['email']."';";

        if ( full_query($sql_replace) ) {
			update_user_limits( $service['serverid'], $service["userid"] );
            $user = array(
                "onapp_user_id" => $user->_obj->_id,
                "email"         => $clientsdetails["email"],
                "password"      => $clientsdetails['password']
            );
        } else {
            return array( "error" => "Can't update user data in Data Base");
        };
    };
    return $user;
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
        $vm->setErrors( $user['error'] );
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

        $vms = $vm->getList();
        $vm_ids = array();

        if ($vms)
            foreach( $vms as $vm_fromlist)
                array_push($vm_ids, $vm_fromlist->_id);

        if (in_array($service["vmid"], $vm_ids)) {
            $vm->_id = $service["vmid"];

            $vm->load();
        }
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
    $ips            = array();
    $base_ips       = array();
    $additional_ips = array();

    if (is_null($vm->_id) )
        return array(
            'notresolved' => $ips,
            'base'        => $base_ips,
            'additional'  => $additional_ips,
        );

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

    if ($ips_rows)
        while ( $row = mysql_fetch_assoc($ips_rows) ) {
            if ( ! isset($ips[$row['ipid']]) ) {
                full_query( "DELETE FROM tblonappips WHERE
                  serviceid  = '$service_id'
                  AND ipid = '" . $row['ipid'] . "'"
                );
               // continue;
            } elseif ( $row['isbase'] == 1 ) {
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

    update_service_ips($service_id);

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

    update_service_ips($service_id);

    return array('success' => true);
}

/**
 * Action assign IP
 */
function _action_ip_add($service_id, $isbase) {
    $service = get_service($service_id);
    $vm      = get_vm($service_id);
    $ips     = get_vm_ips($service_id);

    $user    = get_onapp_client( $service_id );

    if (is_null($vm->_id) )
        return array('error' => "Can't save IP address");

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
            $user["email"],
            $user["password"]
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
        $return = _action_ip_setbase($service_id, $ipaddressjoin->_ip_address_id);
    else
        $return = _action_ip_setadditional($service_id, $ipaddressjoin->_ip_address_id);

    update_service_ips($service_id);

    return $return;
}


function _ips_resolve_all($service_id) {
    $service = get_service($service_id);
    $ips     = get_vm_ips($service_id);

    // resolve base ips after upgrade
    $ips_count = $service['configoption18'] - count($ips['base']);
    for ( $i=0; $i < $ips_count; $i++ ) {
        if (count($ips['notresolved']) > 0) {
            $notresolvedip = array_shift($ips['notresolved']);
            _action_ip_setbase($service_id, $notresolvedip->_id);
        } else {
            _action_ip_add($service_id, 1);
        };

        $ips = get_vm_ips($service_id);
    };

    // resolve base ips after downgrade
    if ( count($ips['base']) > $service['configoption18'] ) {
        $ips_count = count($ips['base']) - $service['configoption18'];
        $remove = array();
        for ( $i = 0; $i < $ips_count; $i++) {
            $ip = array_pop($ips['base']);
            $remove[] = $ip->_id;
        };

        $sql_delete_ips_base = "DELETE FROM tblonappips WHERE
            serviceid  = '$service_id'
            and ipid in (" . implode(',',$remove)  . ")";

        full_query($sql_delete_ips_base);
        $ips = get_vm_ips($service_id);
    };

    // resolve additional ips after upgrade
    $ips_count = $service['additionalips'] - count($ips['additional']);
    for ( $i=0; $i < $ips_count; $i++ ) {
        if (count($ips['notresolved']) > 0) {
            $notresolvedip = array_shift($ips['notresolved']);
            _action_ip_setadditional($service_id, $notresolvedip->_id);
        } else {
            _action_ip_add($service_id, 0);
        };

        $ips = get_vm_ips($service_id);
    };

    // resolve additional ips after downgrade
    if ( count($ips['additional'] ) > $service['additionalips'] ){
        $ips_count = count($ips['additional']) - $service['additionalips'];
        $remove = array();
        for ( $i = 0; $i < $ips_count; $i++) {
            $ip = array_pop($ips['additional']);
            $remove[] = $ip->_id;
        };

        $sql_delete_ips_base = "DELETE FROM tblonappips WHERE
            serviceid  = '$service_id'
            and ipid in (" . implode(',',$remove)  . ")";

        full_query($sql_delete_ips_base);
        $ips = get_vm_ips($service_id);
    };

    // remove not resolved IPs
    foreach($ips['notresolved'] as $ip) {
        _action_ip_delete($service_id, $ip->_id);
    };

    update_service_ips($service_id);
};

function _ips_unassign_all($service_id) {
    $delete_ips = "DELETE FROM tblonappips WHERE
        serviceid  = '$service_id'";

    if( ! full_query($delete_ips) )
        return array('error' => "Can't delete IP addresses");

    update_service_ips($service_id);

    return array('success' => true);
}

/**
 * Action delete IP
 */

function _action_ip_delete($service_id, $ipid) {
    $service      = get_service($service_id);
    $vm           = get_vm($service_id);
    $ips          = get_vm_ips($service_id);
    $onapp_config = get_onapp_config($service['serverid']);

    if ( ! isset( $ips['notresolved'][$ipid] ) )
        return array('error' => "IP adress #$ipid is resolved or does not exist");

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
    } else {
        update_service_ips($service_id);
        return true;
    };
}

function update_service_ips($service_id) {
    $vm = get_vm($service_id);

    $ips = "";
    if (is_array($vm->_obj->_ip_addresses) )
        foreach( $vm->_obj->_ip_addresses as $ip )
            $ips .= $ip->_address.'\n';

    $sql_update = "UPDATE  tblhosting SET assignedips = '$ips' WHERE id = '$service_id'";

    return full_query($sql_update);
}

function create_vm( $service_id, $hostname, $template_id) {

    $vm  = new ONAPP_VirtualMachine();
    $tpl = new ONAPP_Template();

    $service = get_service($service_id);
    $user = get_onapp_client( $service_id );

    if ( isset($user['error']) ) {
        $vm->setErrors( $user['error'] );
        return $vm;
    };

    $onapp_config = get_onapp_config( $service['serverid'] );
                                                                                    
    $vm->auth(
        $onapp_config["adress"],
        $user["email"],
        $user["password"]
    );

    $tpl->auth(
        $onapp_config["adress"],
        $user["email"],
        $user["password"]
    );

    $option = explode(",", $service['configoption4']);
    if ( count($option) > 1 ) {
        $vm->_hypervisor_group_id = $option[1];
    }
    else {
        $vm->_hypervisor_id = $option[0];
    }

    $option = explode(",", $service['configoption11']);
    if ( count($option) > 1 ) {
        $vm->_data_store_group_primary_id = $option[1];
    }

    $option = explode(",", $service['configoption9']);
    if ( count($option) > 1 ) {
        $vm->_data_store_group_swap_id = $option[1];
    }

    $memory            = $service['configoption3']  + $service['additionalram'];
    $cpus              = $service['configoption5']  + $service['additionalcpus'];
    $cpu_shares        = $service['configoption7']  + $service['additionalcpushares'];
    $primary_disk_size = $service['configoption11'] + $service['additionaldisksize'];
    $rate_limit        = $service['configoption8']  + $service['additionalportspead'];

    $vm->_template_id                    = isset($service['os']) ? $service['os'] : $template_id;
    $vm->_primary_network_id             = $service['configoption6'];
    $vm->_required_virtual_machine_build = '0';
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
    $vm->_rate_limit                     = $rate_limit;

    $tpl->load( $vm->_template_id );
    if ( $tpl->_obj->_operating_system == 'windows' ) {
        $vm->_swap_disk_size = NULL;
    }

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
            if ( $service['configoption10'] == 'on' ) {
                $vm->_required_startup = 1;
                $vm->build();
            };

            sendmessage('Virtual Machine Created', $service_id);

//        action_resolveall_ips();
//        action_resolve all_backups();
        } else {
            $vm->error = "Can't add virtual machine in DB";
            return $vm;
        };
    };

    update_service_ips($service_id);

    return $vm;
}

function delete_vm( $service_id ) {

    $vm = get_vm( $service_id );

    if( ! isset($vm->_id)) {
        $vm->setErrors( "Can't Load Virtual Machine" );
        return $vm;
    };

    $vm->delete();

    if( $vm->error ) {
       $vm->setErrors( "Can't Delete Virtual Machine" );
       return $vm;
    };

    $sql_delete_service = sprintf(
        "DELETE FROM tblonappservices WHERE service_id = '%s'",
        $service_id
    );

    if ( ! full_query($sql_delete_service) ) {
        $vm->setErrors( "Can't delete data from tblonappservices" );
        return $vm;
    };

    _ips_unassign_all($service_id);

    sendmessage('Virtual Machine Deleted', $service_id );

    return $vm;
}

function get_vm_interface( $service_id ) {

    $vm = get_vm($service_id);
    $service = get_service($service_id);


    $network = new ONAPP_VirtualMachine_NetworkInterface();

    $onapp_config = get_onapp_config($service['serverid']);

    $network->auth(
        $onapp_config["adress"],
        $onapp_config['username'],
        $onapp_config['password']
    );

    $network->_virtual_machine_id = $vm->_id;

    $networks = $network->getList();

    foreach( $networks as $net )
        if($net->_primary == "true")
            $result = $net;

    $result->auth(
        $onapp_config["adress"],
        $onapp_config['username'],
        $onapp_config['password']
    );

    return $result;
}

function update_user_limits( $server_id, $client_id ) {

    $sql_select = "SELECT
            configoption2 + sub.sortorder as value,

            onapp_user_id as serveruserid,
            tblservers.ipaddress as serveripaddres,
            tblservers.hostname  as serverhostname,
            tblservers.username  as serverusername,
            tblservers.password  as serverpassword
        FROM
            tblhosting
            LEFT JOIN tblproducts
                ON tblproducts.id = packageid
            LEFT JOIN tblhostingconfigoptions AS options ON
                relid = tblhosting.id
            LEFT JOIN tblproductconfigoptionssub AS sub
                ON options.configid = sub.configid
                AND optionid = sub.id
            LEFT JOIN tblproductconfigoptions
                ON tblproductconfigoptions.id = options.configid
            LEFT JOIN tblonappclients
                ON tblhosting.userid = client_id
                AND configoption1 = server_id
            LEFT JOIN tblservers ON tblproducts.configoption1 = tblservers.id
        WHERE
            servertype            = 'onappbackupspace'
            AND domainstatus      = 'Active'
            AND tblhosting.userid = $client_id
            AND configoption1     = $server_id";

    $rows = full_query($sql_select);

    $storage_disk_size_limit = 0;

    $sql_select_user ="SELECT
            onapp_user_id as serveruserid,
            tblservers.ipaddress as serveripaddres,
            tblservers.hostname  as serverhostname,
            tblservers.username  as serverusername,
            tblservers.password  as serverpassword
        FROM
            tblonappclients
            LEFT JOIN tblservers ON tblservers.id = server_id
        WHERE
            tblonappclients.client_id = $client_id
            AND server_id = $server_id";

    $user_rows = full_query($sql_select_user);
    if ($user_rows)
        while ( $user_row = mysql_fetch_assoc($user_rows) )
            $user = array(
                'userid'   => $user_row['serveruserid'],
                'username' => $user_row['serverusername'],
                'password' => decrypt($user_row['serverpassword']),
                'hostanme' => $user_row["serveripaddres"] != "" ?
                    'http://' . $user_row["serveripaddres"] :
                    $row['serverhostname'],
            );

    if ($rows)
        while ( $row = mysql_fetch_assoc($rows) )
            $storage_disk_size_limit += $row['value'];

    if ( ! is_null( $user ) ) {
        $limits = new ONAPP_ResourceLimit();

        $limits->auth(
            $user["hostanme"],
            $user['username'],
            $user['password']
        );

        $limits->load( $user['userid'] );

        $limits->_storage_disk_size = $storage_disk_size_limit;

        $limits->save();
    };

}


function update_user_storagedisksize( $params, $action = 'Active' ) {
    $serviceid = $params["serviceid"];

    $status = serviceStatus($serviceid);
    serviceStatus($serviceid, $action);

    $sql_select = "
        SELECT
            configoption1 as server_id,
            userid
        FROM
            tblhosting
            LEFT JOIN tblproducts
                ON tblproducts.id = packageid
        WHERE
            tblproducts.servertype = 'onappbackupspace' AND
            tblhosting.id = $serviceid";

    $rows = full_query($sql_select);

    if ($rows)
        while ( $row = mysql_fetch_assoc($rows) )
            update_user_limits( $row['server_id'], $row['userid'] );

    serviceStatus($serviceid, $status);
}