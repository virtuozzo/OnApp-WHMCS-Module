<?php

if( ! defined( 'ONAPP_WRAPPER_INIT' ) ) {
	define( 'ONAPP_WRAPPER_INIT', dirname( dirname( __FILE__ ) ) . '/includes/wrapper/OnAppInit.php' );
}

if( file_exists( ONAPP_WRAPPER_INIT ) ) {
	require_once ONAPP_WRAPPER_INIT;
}

/**
 * TODO Add description
 */
function onapp_Config( $id ) {
	global $_LANG;

	$sql = "SELECT id, name, ipaddress, hostname, username, password FROM tblservers WHERE id = " . addslashes( $id );

	$onapp_config = mysql_fetch_array(
		full_query( $sql )
	);

	// Error if server not found in DB

	if( $onapp_config ) {
		$onapp_config[ "password" ] = decrypt( $onapp_config[ "password" ] );
		$onapp_config[ "adress" ]   = $onapp_config[ "ipaddress" ] != "" ?
			$onapp_config[ "ipaddress" ] :
			$onapp_config[ "hostname" ];

		if( strpos( $onapp_config[ "adress" ], 'http' ) === false ) {
			$onapp_config[ "adress" ] = 'http://' . $onapp_config[ "adress" ];
		}

		$onapp_config[ ] = $onapp_config[ "adress" ];
	}
	else {
		return array(
			"error" => sprintf( $_LANG[ "onapperrcantfoundserver" ], $id )
		);
	}

	//Error if server adress (IP and hostname) not set
	if( ! $onapp_config[ "adress" ] ) {
		return array(
			"error" => sprintf(
				$_LANG[ "onapperrcantfoundadress" ],
				$onapp_config[ "id" ],
				$onapp_config[ "name" ]
			)
		);
	}

	return $onapp_config;
}

/**
 * hook to change service status when admin Create service
 */
function serviceStatus( $id, $status = null ) {
	$select = "SELECT * FROM tblhosting WHERE id = '$id'";
	$rows   = full_query( $select );
	if( ! $rows ) {
		return false;
	}

	$service = mysql_fetch_assoc( $rows );

	$old_status = $service[ "domainstatus" ];

	if( is_null( $status ) ) {
		return $old_status;
	}

	$update = "UPDATE tblhosting SET domainstatus = '$status' WHERE id = '$id'";
	return full_query( $update );
}

/**
 * Load $_LANG from language file
 */
function load_language() {
	global $_LANG;
	$dh = opendir( dirname( __FILE__ ) . '/lang/' );

	$arrayoflanguagefiles = array();
	while( false !== $file2 = readdir( $dh ) ) {
		if( ! is_dir( '' . 'lang/' . $file2 ) ) {
			$pieces = explode( '.', $file2 );
			if( $pieces[ 1 ] == 'txt' ) {
				$arrayoflanguagefiles[ ] = $pieces[ 0 ];
				continue;
			}
			continue;
		}
	}

	closedir( $dh );

	$language = 'English';

	if( isset( $GLOBALS[ 'CONFIG' ][ 'Language' ] ) ) {
		$_SESSION[ 'Language' ] = ucfirst( $GLOBALS[ 'CONFIG' ][ 'Language' ] );
	}

	if( isset( $_SESSION[ 'Language' ] ) ) {
		$language = $_SESSION[ 'Language' ];
	}

	if( ! in_array( $language, $arrayoflanguagefiles ) ) {
		$language = "English";
	}

	if( file_exists( dirname( __FILE__ ) . "/lang/$language.txt" ) ) {
		ob_start();
		include dirname( __FILE__ ) . "/lang/$language.txt";
		$templang = ob_get_contents();
		ob_end_clean();
		eval ( $templang );
	}
}

/**
 * Get server configuration options
 *
 * @param integer $id server id
 */
function get_onapp_config( $id ) {
	if( $id == '' ) {
		return false;
	}

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
		full_query( $sql )
	);

	// Error if server not found in DB
	if( $onapp_config ) {
		$onapp_config[ "password" ] = decrypt( $onapp_config[ "password" ] );
		$onapp_config[ "adress" ]   = $onapp_config[ "ipaddress" ] != "" ?
			$onapp_config[ "ipaddress" ] :
			$onapp_config[ "hostname" ];

		if( strpos( $onapp_config[ "adress" ], 'http' ) === false ) {
			$onapp_config[ "adress" ] = 'http://' . $onapp_config[ "adress" ];
		}

		$onapp_config[ ] = $onapp_config[ "adress" ];
	}
	else //        return array( "error" => "Can't found active OnApp server #".addslashes($id)." in Data Base");
	{
		return false;
	}

	//Error if server adress (IP and hostname) not set
	if( ! $onapp_config[ "adress" ] ) //        return array( "error" => "OnApp server adress (IP and hostname) not set for #".$onapp_config["id"]." '".$onapp_config["name"]."'" );
	{
		return false;
	}

	return $onapp_config;
}

/**
 * Get service data form DB using global variable $id
 *
 * @return array
 */
function get_service( $service_id ) {

	$select_service = "SELECT
        tblproducts.id AS productid,
        tblhosting.id AS id,
        userid,
        tblhosting.server AS serverid,
        tblproducts.configoption1 AS productserverid,
        tblonappservices.vm_id AS vmid,
        tblhosting.password,
        tblhosting.domain AS domain,
        tblhosting.orderid AS orderid,
        tblproducts.name AS product,
        tblproducts.overagesbwlimit AS bwlimit,
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
        tblproducts.configoption22,
        tblproducts.configoption23,
        tblproducts.overagesenabled,
        0 AS additionalram,
        0 AS additionalcpus,
        0 AS additionalcpushares,
        0 AS additionaldisksize,
        0 AS additionalips,
        0 AS additionalportspead,
        0 AS addbandwidth
    FROM
        tblhosting
        LEFT JOIN tblproducts ON tblproducts.id = packageid
        LEFT JOIN tblonappservices ON service_id = tblhosting.id
    WHERE
        servertype = 'onapp'
        AND tblhosting.id = '$service_id'
        AND tblhosting.domainstatus = 'Active'";

	$service_rows = full_query( $select_service );

	if( ! $service_rows ) {
		return false;
	}

	$service = mysql_fetch_assoc( $service_rows );

	$productid = $service[ "productid" ];

	$select_config = "
    SELECT
        CASE LCASE(hosting.billingcycle)
            WHEN 'monthly'      THEN tblpricing.monthly
            WHEN 'quarterly'    THEN tblpricing.quarterly
            WHEN 'semiannually' THEN tblpricing.semiannually
            WHEN 'annually'     THEN tblpricing.annually
            WHEN 'biennially'   THEN tblpricing.biennially
            WHEN 'triennially'  THEN tblpricing.triennially
        ELSE
            '0.00'
        END AS price,
        currencies.prefix,
        currencies.suffix,
        optionssub.id,
        optionssub.optionname,
        tblproductconfigoptions.id AS configid,
        tblproductconfigoptions.optionname AS configoptionname,
        tblproductconfigoptions.optiontype,
        tblproductconfigoptions.qtymaximum AS max,
        tblproductconfigoptions.qtyminimum AS min,
        options.qty,
        optionssub.sortorder,
        IF(options.optionid, options.optionid, 0) AS active
    FROM
        tblproductconfiglinks
        LEFT JOIN tblproductconfigoptions
           ON tblproductconfigoptions.gid = tblproductconfiglinks.gid
        LEFT JOIN tblhostingconfigoptions AS options
            ON options.configid = tblproductconfigoptions.id AND options.relid = $service_id
        LEFT JOIN tblproductconfigoptionssub AS sub
            ON options.configid = sub.configid
            AND optionid = sub.id
        LEFT JOIN tblproductconfigoptionssub AS optionssub
            ON optionssub.configid = tblproductconfigoptions.id
        LEFT JOIN tblhosting AS hosting
            ON hosting.id = $service_id
        LEFT JOIN tblclients AS clients
            ON clients.id = hosting.userid
        LEFT JOIN tblpricing
            ON tblpricing.relid = optionssub.id
            AND tblpricing.currency = clients.currency
            AND tblpricing.type = 'configoptions'
        LEFT JOIN tblcurrencies AS currencies
            ON currencies.id = clients.currency
    WHERE
        tblproductconfiglinks.pid = $productid
    GROUP BY
        optionssub.id
    ORDER BY optionssub.id ASC";

	$config_rows = full_query( $select_config );

	if( ! $config_rows ) {
		return false;
	}

	$onappconfigoptions = array(
		$service[ "configoption12" ], // additional ram
		$service[ "configoption13" ], // additional cpus
		$service[ "configoption14" ], // additional cpu shares
		$service[ "configoption15" ], // additional disk size
		$service[ "configoption16" ], // additional ips
		$service[ "configoption19" ], // operation system
		$service[ "configoption20" ], // port spead
		$service[ "configoption22" ], // bandwidth
	);

	if( $option = (array)json_decode( htmlspecialchars_decode( $service[ 'configoption23' ] ) ) ) {
		$sec_net_ips                    = $option[ 'sec_net_ips' ];
		$sec_net_configurable_option_id = $option[ 'sec_net_configurable_option_id' ];
		array_push( $onappconfigoptions, $sec_net_configurable_option_id );
	}

	$service[ "configoptions" ] = array();

	while( $row = mysql_fetch_assoc( $config_rows ) ) {
		if( in_array( $row[ "configid" ], $onappconfigoptions ) ) {
			switch( $row[ 'optiontype' ] ) {
				case '1': // Dropdown
					$row[ 'order' ] = $row[ 'sortorder' ];
					break;
				case '2': // Radio
					$row[ 'order' ] = $row[ 'sortorder' ];
					break;
				case '3': // Yes/No
					$row[ 'order' ] = 0;
					break;
				case '4': // Quantity
					$row[ 'order' ] = $row[ 'qty' ] * $row[ 'sortorder' ];
					break;
			}
			;

			if( ! isset( $service[ "configoptions" ][ $row[ 'configid' ] ] ) ) {
				$service[ "configoptions" ][ $row[ 'configid' ] ] = array(
					'name'       => $row[ 'configoptionname' ],
					'active'     => $row[ 'active' ],
					'optiontype' => $row[ 'optiontype' ],
					'sortorder'  => $row[ 'sortorder' ]
				);
			}

			if( $row[ "id" ] == $row[ "active" ] ) {
				if( $service[ "configoption12" ] == $row[ "configid" ] ) {
					$service[ "additionalram" ]                                   = $row[ "order" ];
					$service[ "configoptions" ][ $row[ 'configid' ] ][ 'order' ]  = $service[ 'configoption3' ];
					$service[ "configoptions" ][ $row[ 'configid' ] ][ 'prefix' ] = 'MB';
				}
				elseif( $service[ "configoption13" ] == $row[ "configid" ] ) {
					$service[ "additionalcpus" ]                                  = $row[ "order" ];
					$service[ "configoptions" ][ $row[ 'configid' ] ][ 'order' ]  = $service[ 'configoption5' ];
					$service[ "configoptions" ][ $row[ 'configid' ] ][ 'prefix' ] = '';
				}
				elseif( $service[ "configoption14" ] == $row[ "configid" ] ) {
					$service[ "additionalcpushares" ]                             = $row[ "order" ];
					$service[ "configoptions" ][ $row[ 'configid' ] ][ 'order' ]  = $service[ 'configoption7' ];
					$service[ "configoptions" ][ $row[ 'configid' ] ][ 'prefix' ] = '%';
				}
				elseif( $service[ "configoption15" ] == $row[ "configid" ] ) {
					$service[ "additionaldisksize" ]                              = $row[ "order" ];
					$service[ "configoptions" ][ $row[ 'configid' ] ][ 'order' ]  = $service[ 'configoption11' ];
					$service[ "configoptions" ][ $row[ 'configid' ] ][ 'prefix' ] = 'GB';
				}
				elseif( $service[ "configoption16" ] == $row[ "configid" ] ) {
					$service[ "additionalips" ]                                   = $row[ "order" ];
					$service[ "configoptions" ][ $row[ 'configid' ] ][ 'order' ]  = $service[ 'configoption18' ];
					$service[ "configoptions" ][ $row[ 'configid' ] ][ 'prefix' ] = '';
				}
				elseif( $service[ "configoption20" ] == $row[ "configid" ] ) {
					$service[ "additionalportspead" ]                             = $row[ "order" ];
					$service[ "configoptions" ][ $row[ 'configid' ] ][ 'order' ]  = $service[ 'configoption8' ];
					$service[ "configoptions" ][ $row[ 'configid' ] ][ 'prefix' ] = 'Mbps';
				}
				elseif( $service[ "configoption22" ] == $row[ "configid" ] ) {
					$service[ "additionalbandwidth" ]                             = $row[ "order" ];
					$service[ "configoptions" ][ $row[ 'configid' ] ][ 'order' ]  = $service[ 'bwlimit' ];
					$service[ "configoptions" ][ $row[ 'configid' ] ][ 'prefix' ] = 'MB';
				}
				elseif( $service[ "configoption19" ] == $row[ "configid" ] ) {
					$service[ "os" ] = $row[ "order" ];
				}
				elseif( $sec_net_configurable_option_id == $row[ "configid" ] ) {
					$service[ "sec_net_additionalips" ]                          = $row[ "order" ];
					$service[ "configoptions" ][ $row[ 'configid' ] ][ 'order' ] = $sec_net_ips;
				}

				$service[ "configoptions" ][ $row[ 'configid' ] ][ 'value' ] = $row[ 'qty' ];
			}

			$service[ "configoptions" ][ $row[ 'configid' ] ][ 'options' ][ $row[ 'sortorder' ] ] = array(
				'id'   => $row[ 'id' ],
				'name' => $row[ 'optionname' ] . ' - ' . $row[ 'prefix' ] . ' ' . $row[ 'price' ] . $row[ 'suffix' ],
				'max'  => $row[ 'max' ],
				'min'  => $row[ 'min' ]
			);
		}
	}

	return $service;
}

/**
 * Get onapp user data
 *
 */
function get_onapp_client( $service_id, $ONAPP_DEFAULT_USER_ROLE = 2, $ONAPP_DEFAULT_BILLING_PLAN = 1 ) {
	global $_LANG;

	$service = get_service( $service_id );

	if( $service[ 'serverid' ] != $service[ 'productserverid' ] ) {
		$service[ 'serverid' ] = $service[ 'productserverid' ];
	}

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
		$service[ 'userid' ],
		$service[ 'serverid' ] );

	$user = mysql_fetch_array( full_query( $sql_select ) );

	if( $user ) {
		$user[ "password" ] = decrypt( $user[ "password" ] );
	}
	else {
		$user = new OnApp_User();

		$onapp_config = get_onapp_config( $service[ 'serverid' ] );
		if( $service[ 'serverid' ] == "" ) {
			return array(
				"error" => $_LANG[ 'onappcantcreateuser' ]
			);
		}

		$user->auth(
			$onapp_config[ "adress" ],
			$onapp_config[ 'username' ],
			$onapp_config[ 'password' ]
		);

		$sql_select_client = sprintf(
			"SELECT * FROM tblclients WHERE id = '%s'",
			$service[ 'userid' ]
		);

		$clientsdetails = mysql_fetch_array( full_query( $sql_select_client ) );

		$password = $clientsdetails[ 'password' ];

		if( strlen( $password ) > 40 ) {
			$password = substr( $password, 0, 20 );
		}

		if( $option = (array)( json_decode( htmlspecialchars_decode( $service[ 'configoption21' ] ) ) ) ) {
			$user->_role_ids        = $option[ 'role_ids' ];
			$user->_user_group_id   = $option[ 'user_group' ];
			$user->_time_zone       = $option[ 'time_zone' ];
			$user->_billing_plan_id = $option[ 'billing_plan' ];
		}
		else {
			$user->_role_ids        = array( $ONAPP_DEFAULT_USER_ROLE );
			$user->_billing_plan_id = $ONAPP_DEFAULT_BILLING_PLAN;
		}

		$user->_email      = $clientsdetails[ 'email' ];
		$user->_password   = $password;
		$user->_login      = $clientsdetails[ 'email' ];
		$user->_first_name = $clientsdetails[ 'firstname' ];
		$user->_last_name  = $clientsdetails[ 'lastname' ];
		$user->save();

##TODO LOCALIZE
		if( ! is_null( $user->getErrorsAsArray() ) ) {
			return array( 'error' => $user->getErrorsAsString( ', ' ) );
		}
		if( ! is_null( $user->_obj->getErrorsAsArray() ) ) {
			return array( 'error' => $user->_obj->getErrorsAsString( ', ' ) );
		}
		elseif( is_null( $user->_obj->_id ) ) {
			return array( "error" => "Can't create OnApp User" );
		}

		$sql_replace = "REPLACE tblonappclients SET
          server_id = '" . $service[ 'serverid' ] . "' ,
          client_id = '" . $service[ "userid" ] . "' ,
          onapp_user_id = '" . $user->_obj->_id . "' ,
          password = '" . encrypt( $password ) . "' ,
          email = '" . $clientsdetails[ 'email' ] . "';";

		if( full_query( $sql_replace ) ) {
			update_user_limits( $service[ 'serverid' ], $service[ "userid" ] );
			$user = array(
				"onapp_user_id" => $user->_obj->_id,
				"email"         => $clientsdetails[ "email" ],
				"password"      => $password
			);
		}
		else {
			return array( "error" => "Can't update user data in Data Base" );
		}
		;
	}
	;
	return $user;
}

/**
 * Get vitual machine data
 *
 * return object ONAPP_VirtualMachine
 */
function get_vm( $service_id ) {
	$user = get_onapp_client( $service_id );
	$vm   = new OnApp_VirtualMachine();

	if( isset( $user[ 'error' ] ) ) {
		$vm->setErrors( $user[ 'error' ] );
		return $vm;
	}
	;

	$service = get_service( $service_id );

	$onapp_config = get_onapp_config( $service[ 'serverid' ] );

	if( isset( $service[ "vmid" ] ) && ! is_null( $service[ "vmid" ] ) ) {

		$vm->auth(
			$onapp_config[ "adress" ],
			$user[ "email" ],
			$user[ "password" ]
		);

		$vms    = $vm->getList();
		$vm_ids = array();

		if( $vms ) {
			foreach( $vms as $vm_fromlist ) {
				array_push( $vm_ids, $vm_fromlist->_id );
			}
		}

		if( in_array( $service[ "vmid" ], $vm_ids ) ) {
			$vm->_id = $service[ "vmid" ];

			$vm->load();
		}
	}
	else {
		$vm->error = "Cant load Virtual machine";
	}
	;

	return $vm;
}

/**
 * Get service OS templates list
 *
 * @param integer $serverid    service id
 * @param string  $templatesid list of templates CSV in format
 *
 * return array list of templates
 */
function get_templates( $serverid, $templatesid ) {
	$onapp_config = get_onapp_config( $serverid );

	$template = new OnApp_Template();

	$template->auth(
		$onapp_config[ "adress" ],
		$onapp_config[ 'username' ],
		$onapp_config[ 'password' ]
	);

	$templates_id = explode( ',', $templatesid );

	$templates = array();

	foreach( $template->getList() as $template ) {
		if( in_array( $template->_id, $templates_id ) ) {
			$templates[ $template->_id ] = $template;
		}
	}
	;

	return $templates;
}

/**
 * Add secondary network interface just after VM creation
 *
 * @todo add error verifications
 *
 * @param integer $vmid      VM ID
 * @param integer $hvzoneid  hypervisor zone ID
 * @param mixed   $service
 * @param mixed   $networkid phisical network ID
 * @param integer $portspeed secondary network port speed
 *
 * @return mixed network interface
 */

function _add_sec_network_interface( $vmid, $id, $service, $networkid, $portspeed, $jointype ) {
	$onapp_config = get_onapp_config( $service[ 'serverid' ] );

	$onapp = new OnApp_Factory(
		$onapp_config[ "adress" ],
		$onapp_config[ 'username' ],
		$onapp_config[ 'password' ]
	);

	if( $jointype == 'hv_hvzone' ) {
		$interface = _add_sec_network_interface( $vmid, $id[ 0 ], $service, $networkid, $portspeed, 'hvzone' );
		if( $interface ) {
			return;
		}
		else {
			_add_sec_network_interface( $vmid, $id[ 1 ], $service, $networkid, $portspeed, 'hv' );
			return;
		}
	}
	elseif( $jointype == 'hvzone' ) {
		$hvzone_network_join = $onapp->factory( 'HypervisorZone_NetworkJoin' );
		$network_join        = $hvzone_network_join->getList( $id );

		// find network join id of phisical network
		foreach( $network_join as $join ) {
			if( $join->_network_id == $networkid ) {
				$network_join_id = $join->_id;
			}
		}
	}
	elseif( $jointype == 'hv' ) {
		echo 'And here <br />';
		$hv_network_join = $onapp->factory( 'Hypervisor_NetworkJoin', true );
		$network_join    = $hv_network_join->getList( $id );

		// find network join id of phisical network
		foreach( $network_join as $join ) {
			if( $join->_network_id == $networkid ) {
				$network_join_id = $join->_id;
			}
		}
	}

	$network_interface = $onapp->factory( 'VirtualMachine_NetworkInterface', true );

	if( isset( $network_join_id ) ) {
		$network_interface->_label              = 'eth1';
		$network_interface->_rate_limit         = $portspeed;
		$network_interface->_network_join_id    = $network_join_id;
		$network_interface->_virtual_machine_id = $vmid;
		$network_interface->save();
	}
	else {
		return false;
	}

	return $network_interface;
}

/**
 * Verify if secondary network interface exists
 *
 * @param type $vmid
 *
 * @return type
 */
function sec_net_interface_exists( $vmid, $serverid ) {
	$onapp_config = get_onapp_config( $serverid );

	$onapp = new OnApp_Factory(
		$onapp_config[ "adress" ],
		$onapp_config[ 'username' ],
		$onapp_config[ 'password' ]
	);

	$networkinterface = $onapp->factory( 'VirtualMachine_NetworkInterface' );

	return count( $networkinterface->getList( $vmid ) ) > 1;
}

/**
 * Get VM IPs
 *
 * @param integer $service_id
 *
 * @return mixed ips
 */
function get_vm_ips( $service_id ) {
	$vm      = get_vm( $service_id );
	$service = get_service( $service_id );

	$ips                    = array();
	$base_ips               = array();
	$additional_ips         = array();
	$sec_net_base_ips       = array();
	$sec_net_additional_ips = array();

	if( is_null( $vm->_id ) ) {
		return array(
			'notresolved' => $ips,
			'base'        => $base_ips,
			'additional'  => $additional_ips,
		);
	}

	$options = (array)json_decode( htmlspecialchars_decode( $service[ 'configoption23' ] ) );

	if( ! $options ) {
		$sec_network_id = 0;
	}
	else {
		$sec_network_id = $options[ 'sec_network_id' ];

		if( ! sec_net_interface_exists( $vm->_id, $service[ 'serverid' ] ) ) {

			$hv_info = explode( ',', $service[ 'configoption4' ] );

			if( $options && count( $hv_info ) > 1 ) {
				$hvzoneid = $hv_info[ 1 ];
				$hvid     = $hvid = $hv_info[ 0 ] ? $hv_info[ 0 ] : $vm->_obj->_hypervisor_id;

				if( ( $hvzoneid && is_numeric( $hvzoneid ) ) && ( $hvid && is_numeric( $hvid ) ) ) {
					_add_sec_network_interface( $vm->_obj->_id, array(
						$hvzoneid,
						$hvid
					), $service, $sec_network_id, $options[ 'sec_net_port_speed' ], 'hv_hvzone' );
				}
				elseif( $hvzoneid && is_numeric( $hvzoneid ) ) {
					_add_sec_network_interface( $vm->_obj->_id, $hvzoneid, $service, $sec_network_id, $options[ 'sec_net_port_speed' ], 'hvzone' );
				}
				elseif( $hvid && is_numeric( $hvid ) ) {
					_add_sec_network_interface( $vm->_obj->_id, $hvid, $service, $sec_network_id, $options[ 'sec_net_port_speed' ], 'hv' );
				}
			}
		}
	}

	if( is_array( $vm->_obj->_ip_addresses ) ) {
		foreach( $vm->_obj->_ip_addresses as $ip ) {
			$ips[ $ip->_id ] = $ip;
		}
	}

	$select_ips = sprintf( "
        SELECT
            serviceid,
            ipid,
            isbase
        FROM tblonappips
        WHERE serviceid = '%s'",
		addslashes( $service_id )
	);

	$ips_rows = full_query( $select_ips );

	if( $ips_rows ) {
		while( $row = mysql_fetch_assoc( $ips_rows ) ) {
			if( ! isset( $ips[ $row[ 'ipid' ] ] ) ) {
				full_query( "DELETE FROM tblonappips WHERE
                  serviceid  = '$service_id'
                  AND ipid = '" . $row[ 'ipid' ] . "'"
				);
				// continue;
			}
			elseif( $row[ 'isbase' ] == 1 && $service[ 'configoption6' ] == $ips[ $row[ 'ipid' ] ]->_network_id ) {
				$base_ips[ $row[ 'ipid' ] ] = $ips[ $row[ 'ipid' ] ];
				unset( $ips[ $row[ 'ipid' ] ] );
			}
			elseif( $row[ 'isbase' ] != 1 && $service[ 'configoption6' ] == $ips[ $row[ 'ipid' ] ]->_network_id ) {
				$additional_ips[ $row[ 'ipid' ] ] = $ips[ $row[ 'ipid' ] ];
				unset( $ips[ $row[ 'ipid' ] ] );
			}
			elseif( $row[ 'isbase' ] == 1 && $sec_network_id == $ips[ $row[ 'ipid' ] ]->_network_id ) {
				$sec_net_base_ips[ $row[ 'ipid' ] ] = $ips[ $row[ 'ipid' ] ];
				unset( $ips[ $row[ 'ipid' ] ] );
			}
			elseif( $row[ 'isbase' ] != 1 && $sec_network_id == $ips[ $row[ 'ipid' ] ]->_network_id ) {
				$sec_net_additional_ips[ $row[ 'ipid' ] ] = $ips[ $row[ 'ipid' ] ];
				unset( $ips[ $row[ 'ipid' ] ] );
			}
		}
	}

	return array(
		'notresolved'        => $ips,
		'base'               => $base_ips,
		'additional'         => $additional_ips,
		'sec_net_additional' => $sec_net_additional_ips,
		'sec_net_base'       => $sec_net_base_ips,
	);
}

/**
 * Action resolve IP set base
 */
function _action_ip_setbase( $service_id, $ipid ) {
	$service = get_service( $service_id );

	$option = (array)json_decode( htmlspecialchars_decode( $service[ 'configoption23' ] ) );
	if( ! $option ) {
		$sec_net_ips_number = 0;
	}
	else {
		$sec_net_ips_number = $option[ 'sec_net_ips' ];
	}

	$ips = get_vm_ips( $service_id );

	if( $ipid == "" || ! isset( $ips[ 'notresolved' ][ $ipid ] ) ) {
		return array(
			'error' => "Can't found not resolved IP with id #" . $ipid,
		);
	}

	if( $service[ 'configoption18' ] - count( $ips[ 'base' ] ) < 1 && $sec_net_ips_number - count( $ips[ 'sec_net_base' ] ) < 1 ) {
		return array(
			'error' => "Can't found not not assigned base IPs ",
		);
	}

	$sql_insert_ip = "REPLACE tblonappips SET
        serviceid  = '$service_id' ,
        ipid       = '$ipid',
        isbase     = 1";

	if( ! full_query( $sql_insert_ip ) ) {
		return array( 'error' => "Can't resolve IP address" );
	}

	update_service_ips( $service_id );

	return array( 'success' => true );
}

/**
 * Action resolve IP set additional
 *
 * @param integer $service_id
 * @param integer $ipid
 *
 * @return mixed result
 */
function _action_ip_setadditional( $service_id, $ipid ) {
	$service = get_service( $service_id );

	$ips = get_vm_ips( $service_id );

	if( $ipid == "" || ! isset( $ips[ 'notresolved' ][ $ipid ] ) ) {
		return array(
			'error' => "Can't found not resolved IP with id #" . $ipid,
		);
	}

	if( $service[ 'additionalips' ] - count( $ips[ 'additional' ] ) < 1 && $service[ 'sec_net_additionalips' ] - count( $ips[ 'sec_net_additional' ] ) < 1 ) {
		return array(
			'error' => "Can't found not not assigned additional IPs ",
		);
	}

	$sql_insert_ip = "REPLACE tblonappips SET
        serviceid  = '$service_id' ,
        ipid       = '$ipid',
        isbase     = 0";

	if( ! full_query( $sql_insert_ip ) ) {
		return array( 'error' => "Can't resolve IP address" );
	}

	update_service_ips( $service_id );

	return array( 'success' => true );
}

/**
 * Add ip address to VM
 *
 * @param integet $service_id
 * @param boolean $isbase    whether base ip
 * @param boolean $secondary whether secondary network
 *
 * @return boolean result
 * will not work correctly if #5709 isn't fixed https://onapp.codebasehq.com/projects/onapp/tickets/5709
 */
function _action_ip_add( $service_id, $isbase, $secondary = false ) {
	$service = get_service( $service_id );
	$vm      = get_vm( $service_id );
	$ips     = get_vm_ips( $service_id );
	$user    = get_onapp_client( $service_id );

	if( $secondary ) {
		$option = (array)json_decode( htmlspecialchars_decode( $service[ 'configoption23' ] ) );

		$base_ips_number               = $option[ 'sec_net_ips' ];
		$additional_ips_number         = $service[ 'sec_net_additionalips' ];
		$network_id                    = $option[ 'sec_network_id' ];
		$current_base_ips_number       = $ips[ 'sec_net_base' ];
		$current_additional_ips_number = $ips[ 'sec_net_additional' ];
	}
	else {
		$base_ips_number               = $service[ 'configoption18' ];
		$additional_ips_number         = $service[ 'additionalips' ];
		$network_id                    = $service[ "configoption6" ];
		$current_base_ips_number       = $ips[ 'base' ];
		$current_additional_ips_number = $ips[ 'additional' ];
	}

	if( is_null( $vm->_id ) ) {
		return array( 'error' => "Can't save IP address" );
	}

	if( $isbase == 1 && $base_ips_number - count( $current_base_ips_number ) < 1 ) {
		return array(
			'error' => "Can't found not not assigned base IPs ",
		);
	}
	elseif( $isbase != 1 && $additional_ips_number - count( $current_additional_ips_number ) < 1 ) {
		return array(
			'error' => "Can't found not not assigned sec_net_additional IPs ",
		);
	}

	$onapp_config = get_onapp_config( $service[ 'serverid' ] );

	$ipaddress = new OnApp_IpAddress();

	$ipaddress->auth(
		$onapp_config[ "adress" ],
		$onapp_config[ 'username' ],
		$onapp_config[ 'password' ]
	);

	$ips = $ipaddress->getList( $network_id );

	if( $ips ) {
		foreach( $ips as $ip ) {
			if( $ip->_free == true && ( is_null( $ip->user_id ) || $ip->user_id == $vm->_obj->_user_id ) && ! isset( $free_ip ) ) {
				$free_ip = $ip;
			}
		}
	}

	if( ! isset( $free_ip ) || is_null( $free_ip ) ) {
		return array( 'error' => "Can't found free IP" );
	}
	else {

		$networkinterface = new OnApp_VirtualMachine_NetworkInterface();

		$networkinterface->_virtual_machine_id = $vm->_id;

		$networkinterface->auth(
			$onapp_config[ "adress" ],
			$user[ "email" ],
			$user[ "password" ]
		);

		if( $secondary ) {
			foreach( $networkinterface->getList() as $interface ) {
				if( $interface->_primary != true ) {
					$firstnetworkinterface = $interface;
				}
			}
		}
		else {
			foreach( $networkinterface->getList() as $interface ) {
				if( $interface->_primary == true ) {
					$firstnetworkinterface = $interface;
				}
			}
		}
	}

	if( ! isset( $firstnetworkinterface ) || is_null( $firstnetworkinterface ) ) {
		return array(
			'error' => "Can't found Virtual Machine network interface"
		);
	}

	$ipaddressjoin = new OnApp_VirtualMachine_IpAddressJoin();

	$ipaddressjoin->_virtual_machine_id   = $vm->_id;
	$ipaddressjoin->_network_interface_id = $firstnetworkinterface->_id;
	$ipaddressjoin->_ip_address_id        = $free_ip->_id;

	$ipaddressjoin->auth(
		$onapp_config[ "adress" ],
		$onapp_config[ 'username' ],
		$onapp_config[ 'password' ]
	);

	$ipaddressjoin->save();

	if( ! isset( $ipaddressjoin->_ip_address_id ) ) {
		return array( 'error' => "Can't save IP address" );
	}

	if( $ipaddressjoin->getErrorsAsArray() ) {
		return array( 'error' => $ipaddressjoin->getErrorsAsString() );
	}

	if( $isbase == 1 ) {
		$return = _action_ip_setbase( $service_id, $ipaddressjoin->_ip_address_id );
	}
	else {
		$return = _action_ip_setadditional( $service_id, $ipaddressjoin->_ip_address_id );
	}

	update_service_ips( $service_id );

	return $return;
}

/**
 * Resolve Ip addresses
 *
 * @param integer $service_id
 */
function _ips_resolve_all( $service_id ) {

	$service = get_service( $service_id );
	$ips     = get_vm_ips( $service_id );

// resolve base ips after upgrade
	$ips_count = $service[ 'configoption18' ] - count( $ips[ 'base' ] );
	for( $i = 0; $i < $ips_count; $i ++ ) {
		if( count( $ips[ 'notresolved' ] ) > 0 ) {
			$notresolvedip = array_shift( $ips[ 'notresolved' ] );
			_action_ip_setbase( $service_id, $notresolvedip->_id );
		}
		else {
			_action_ip_add( $service_id, 1 );
		}
		;

		$ips = get_vm_ips( $service_id );
	}

// resolve base ips after downgrade
	if( count( $ips[ 'base' ] ) > $service[ 'configoption18' ] ) {
		$ips_count = count( $ips[ 'base' ] ) - $service[ 'configoption18' ];
		$remove    = array();
		for( $i = 0; $i < $ips_count; $i ++ ) {
			$ip        = array_pop( $ips[ 'base' ] );
			$remove[ ] = $ip->_id;
		}
		;

		$sql_delete_ips_base = "DELETE FROM tblonappips WHERE
            serviceid  = '$service_id'
            and ipid in (" . implode( ',', $remove ) . ")";

		full_query( $sql_delete_ips_base );
		$ips = get_vm_ips( $service_id );
	}

// resolve additional ips after upgrade
	$ips_count = $service[ 'additionalips' ] - count( $ips[ 'additional' ] );
	for( $i = 0; $i < $ips_count; $i ++ ) {
		if( count( $ips[ 'notresolved' ] ) > 0 ) {
			$notresolvedip = array_shift( $ips[ 'notresolved' ] );
			_action_ip_setadditional( $service_id, $notresolvedip->_id );
		}
		else {
			_action_ip_add( $service_id, 0 );
		}
		;

		$ips = get_vm_ips( $service_id );
	}

// resolve additional ips after downgrade
	if( count( $ips[ 'additional' ] ) > $service[ 'additionalips' ] ) {
		$ips_count = count( $ips[ 'additional' ] ) - $service[ 'additionalips' ];
		$remove    = array();
		for( $i = 0; $i < $ips_count; $i ++ ) {
			$ip        = array_pop( $ips[ 'additional' ] );
			$remove[ ] = $ip->_id;
		}
		;

		$sql_delete_ips_base = "DELETE FROM tblonappips WHERE
            serviceid  = '$service_id'
            and ipid in (" . implode( ',', $remove ) . ")";

		full_query( $sql_delete_ips_base );
		$ips = get_vm_ips( $service_id );
	}

// Resolve secondary network ips if needed
	if( $option = (array)json_decode( htmlspecialchars_decode( $service[ 'configoption23' ] ) ) ) {

// resolve secondary network base ips after upgrade
		$ips_count = $option[ 'sec_net_ips' ] - count( $ips[ 'sec_net_base' ] );

		for( $i = 0; $i < $ips_count; $i ++ ) {
			if( count( $ips[ 'notresolved' ] ) > 0 ) {
				$notresolvedip = array_shift( $ips[ 'notresolved' ] );
				_action_ip_setbase( $service_id, $notresolvedip->_id );
			}
			else {
				_action_ip_add( $service_id, 1, 1 );
			}
			;

			$ips = get_vm_ips( $service_id );
		}

// resolve secondary network base ips after downgrade
		if( count( $ips[ 'sec_net_base' ] ) > $option[ 'sec_net_ips' ] ) {
			$ips_count = count( $ips[ 'sec_net_base' ] ) - $option[ 'sec_net_ips' ];
			$remove    = array();
			for( $i = 0; $i < $ips_count; $i ++ ) {
				$ip        = array_pop( $ips[ 'sec_net_base' ] );
				$remove[ ] = $ip->_id;
			}
			;

			$sql_delete_ips_base = "DELETE FROM tblonappips WHERE
                serviceid  = '$service_id'
                and ipid in (" . implode( ',', $remove ) . ")";

			full_query( $sql_delete_ips_base );
			$ips = get_vm_ips( $service_id );
		}

// resolve secondary network additional ips after upgrade
		$ips_count = $service[ 'sec_net_additionalips' ] - count( $ips[ 'sec_net_additional' ] );

		for( $i = 0; $i < $ips_count; $i ++ ) {
			if( count( $ips[ 'notresolved' ] ) > 0 ) {
				$notresolvedip = array_shift( $ips[ 'notresolved' ] );
				_action_ip_setadditional( $service_id, $notresolvedip->_id );
			}
			else {
				_action_ip_add( $service_id, 0, 1 );
			}

			$ips = get_vm_ips( $service_id );
		}

// resolve additional ips after downgrade
		if( count( $ips[ 'sec_net_additional' ] ) > $service[ 'sec_net_additionalips' ] ) {
			$ips_count = count( $ips[ 'sec_net_additional' ] ) - $service[ 'sec_net_additionalips' ];
			$remove    = array();
			for( $i = 0; $i < $ips_count; $i ++ ) {
				$ip        = array_pop( $ips[ 'sec_net_additional' ] );
				$remove[ ] = $ip->_id;
			}
			;

			$sql_delete_ips_base = "DELETE FROM tblonappips WHERE
                serviceid  = '$service_id'
                and ipid in (" . implode( ',', $remove ) . ")";

			full_query( $sql_delete_ips_base );
			$ips = get_vm_ips( $service_id );
		}
	}

// remove not resolved IPs
	foreach( $ips[ 'notresolved' ] as $ip ) {
		_action_ip_delete( $service_id, $ip->_id );
	}

	update_service_ips( $service_id );
}

/**
 * Unassign all ips
 *
 * @param integer $service_id
 *
 * @return mixed result
 */
function _ips_unassign_all( $service_id ) {
	$delete_ips = "DELETE FROM tblonappips WHERE
        serviceid  = '$service_id'";

	if( ! full_query( $delete_ips ) ) {
		return array( 'error' => "Can't delete IP addresses" );
	}

	update_service_ips( $service_id );

	return array( 'success' => true );
}

/**
 * Unassign Ip address.
 *
 * @param integer $service_id
 * @param integer $ipid
 *
 * @return boolean
 */
function _action_ip_delete( $service_id, $ipid ) {
	$service      = get_service( $service_id );
	$vm           = get_vm( $service_id );
	$ips          = get_vm_ips( $service_id );
	$onapp_config = get_onapp_config( $service[ 'serverid' ] );

	if( ! isset( $ips[ 'notresolved' ][ $ipid ] ) ) {
		return array( 'error' => "IP adress #$ipid is resolved or does not exist" );
	}

	$ipaddressjoin = new OnApp_VirtualMachine_IpAddressJoin();

	$ipaddressjoin->auth(
		$onapp_config[ "adress" ],
		$onapp_config[ 'username' ],
		$onapp_config[ 'password' ]
	);

	foreach( $ipaddressjoin->getList( $vm->_id ) as $ip ) {
		if( ! $ip_exist && $ip->_ip_address_id == $ipid ) {
			$ip_join = $ip;
			break;
		}
	}
	;

	$ip_join->_virtual_machine_id = $vm->_id;

	$ip_join->auth(
		$onapp_config[ "adress" ],
		$onapp_config[ 'username' ],
		$onapp_config[ 'password' ]
	);

	$ip_join->delete();

	if( ! is_null( $ip_join->_obj->getErrorsAsArray() ) ) {
		return array(
			'error' => "Can't delete IP Address " . $ip_join->_obj->getErrorsAsString( ', ' ),
		);
	}
	else {
		update_service_ips( $service_id );
		return true;
	}
}

/**
 * Updates service ips in WHMCS database
 *
 * @param integer $service_id
 *
 * @return boolean db query result
 */
function update_service_ips( $service_id ) {
	$vm = get_vm( $service_id );

	$ips = "";
	if( is_array( $vm->_obj->_ip_addresses ) ) {
		foreach( $vm->_obj->_ip_addresses as $ip ) {
			$ips .= $ip->_address . '\n';
		}
	}

	$sql_update = "UPDATE  tblhosting SET assignedips = '$ips' WHERE id = '$service_id'";

	return full_query( $sql_update );
}

/**
 * Create VM in OnApp and map to the WHMCS service
 *
 * @param integer $service_id
 * @param string  $hostname
 * @param integer $template_id
 *
 * @return \OnApp_VirtualMachine|\OnApp_Factory
 */
function create_vm( $service_id, $hostname, $template_id ) {
	$service = get_service( $service_id );

	if( $service[ 'serverid' ] != $service[ 'productserverid' ] ) {
		$service[ 'serverid' ] = $service[ 'productserverid' ];
	}

	$user = get_onapp_client( $service_id );

	$onapp_config = get_onapp_config( $service[ 'serverid' ] );

	if( isset( $user[ 'error' ] ) ) {
		$vm = new OnApp_VirtualMachine();
		$vm->setErrors( $user[ 'error' ] );
		return $vm;
	}

	$instance = new OnApp_Factory( $onapp_config[ "adress" ], $user[ "email" ], $user[ "password" ] );
	if( ! $instance->_is_auth ) {
		return $instance;
	}

	$vm  = $instance->factory( 'VirtualMachine', true );
	$tpl = $instance->factory( 'Template' );

	$option = explode( ",", $service[ 'configoption4' ] );
	if( count( $option ) > 1 ) {
		$vm->_hypervisor_group_id = $option[ 1 ];
		$vm->_hypervisor_id       = $option[ 0 ];
	}
	else {
		$vm->_hypervisor_id = $option[ 0 ];
	}

	$option = explode( ",", $service[ 'configoption11' ] );
	if( count( $option ) > 1 ) {
		$vm->_data_store_group_primary_id = $option[ 1 ];
	}

	$option = explode( ",", $service[ 'configoption9' ] );
	if( count( $option ) > 1 ) {
		$vm->_data_store_group_swap_id = $option[ 1 ];
	}

	$option10 = explode( ",", $service[ 'configoption10' ] );
	if( ! $option10[ 1 ] ) {
		$option10[ 1 ] = '0';
	}

	$memory            = $service[ 'configoption3' ] + $service[ 'additionalram' ];
	$cpus              = $service[ 'configoption5' ] + $service[ 'additionalcpus' ];
	$cpu_shares        = $service[ 'configoption7' ] + $service[ 'additionalcpushares' ];
	$primary_disk_size = $service[ 'configoption11' ] + $service[ 'additionaldisksize' ];
	$rate_limit        = $service[ 'configoption8' ] + $service[ 'additionalportspead' ];

	$vm->_template_id                    = isset( $service[ 'os' ] ) ? $service[ 'os' ] : $template_id;
	$vm->_primary_network_id             = $service[ 'configoption6' ];
	$vm->_required_virtual_machine_build = ( $option10[ 0 ] == 'on' ) ? '1' : '0';
	$vm->_required_automatic_backup      = ( $option10[ 1 ] == 'on' ) ? '1' : '0';
	$vm->_hostname                       = $hostname;
	$vm->_memory                         = $memory;
	$vm->_cpus                           = $cpus;
	$vm->_cpu_shares                     = $cpu_shares;
	$vm->_primary_disk_size              = round( $primary_disk_size );
	$vm->_swap_disk_size                 = round( $service[ 'configoption9' ] );
	$vm->_label                          = $hostname;
	$vm->_remote_access_password         = decrypt( $service[ 'password' ] );
	$vm->_initial_root_password          = decrypt( $service[ 'password' ] );
	$vm->_required_ip_address_assignment = '1';
	$vm->_rate_limit                     = $rate_limit;

	$tpl->load( $vm->_template_id );
	if( $tpl->_obj->_operating_system == 'windows' ) {
		$vm->_swap_disk_size = null;
	}

	$vm->save();

	if( ! is_null( $vm->_obj->error ) ) {
		$vm->error = $vm->_obj->error;
		return $vm;
	}
	elseif( is_null( $vm->_obj->_id ) ) {
		$vm->error = "Can't create virtual machine for service #" . $service_id;
		return $vm;
	}
	else {
		$sql_replace = "REPLACE tblonappservices SET
            service_id = '$service_id',
            vm_id      = '" . $vm->_obj->_id . "',
            memory     = '$memory',
            cpus       = '$cpus',
            cpu_shares = '$cpu_shares',
            disk_size  = '$primary_disk_size';";

		switch( $vm->_obj->_operating_system ) {
			case 'linux':
				$username = "root";
				break;
			case 'windows':
				$username = "administrator";
				break;
		}

		$sql_username_update = "UPDATE tblhosting SET
            username = '$username',
            server = '" . $service[ 'serverid' ] . "'
        WHERE
            id = '" . $service[ 'id' ] . "';";

		if( $username != "" ) {
			full_query( $sql_username_update );
		}

		if( full_query( $sql_replace ) ) {
			sendmessage( 'Virtual Machine Created', $service_id );
		}
		else {
			$vm->error = "Can't add virtual machine in DB";
			return $vm;
		}
	}

	update_service_ips( $service_id );

	return $vm;
}

/**
 * Delete VM
 *
 * @param integer $service_id
 *
 * @return \OnApp_VirtualMachine
 */
function delete_vm( $service_id ) {

	$vm = get_vm( $service_id );

	if( ! isset( $vm->_id ) ) {
		$vm->setErrors( "Can't Load Virtual Machine" );
		return $vm;
	}

	$vm->delete();

	if( count( $vm->getErrorsAsArray() ) > 0 ) {
		return $vm;
	}

	$delete_onapp_service = delete_onapp_service( $service_id );

	if( $delete_onapp_service != 'success' ) {
		$vm->setErrors( $delete_onapp_service );
	}

	_ips_unassign_all( $service_id );

	sendmessage( 'Virtual Machine Deleted', $service_id );

	return $vm;
}

/**
 * Delete mapping info from tblonappservices
 *
 */
function delete_onapp_service( $service_id ) {
	$result = full_query( "DELETE FROM tblonappservices WHERE service_id = $service_id" );
	if( $result ) {
		return 'success';
	}

	return 'Can\'t delete data from tblonappservices - ' . mysql_error();
}

/**
 * Get primary network interface
 *
 * @param integer $service_id
 *
 * @todo rename function
 * @return \OnApp_VirtualMachine_NetworkInterface
 */
function get_vm_interface( $service_id ) {

	$vm      = get_vm( $service_id );
	$service = get_service( $service_id );

	$network = new OnApp_VirtualMachine_NetworkInterface();

	$onapp_config = get_onapp_config( $service[ 'serverid' ] );

	$network->auth(
		$onapp_config[ "adress" ],
		$onapp_config[ 'username' ],
		$onapp_config[ 'password' ]
	);

	$network->_virtual_machine_id = $vm->_id;

	$networks = $network->getList();

	foreach( $networks as $net ) {
		if( $net->_primary == true ) {
			$result = $net;
		}
	}

	$result->auth(
		$onapp_config[ "adress" ],
		$onapp_config[ 'username' ],
		$onapp_config[ 'password' ]
	);

	return $result;
}

/**
 * Update User Limit
 *
 * @param integer $server_id
 * @param integer $client_id
 */
function update_user_limits( $server_id, $client_id ) {

	$sql_select = "SELECT
            configoption2 + sub.sortorder AS value,

            onapp_user_id AS serveruserid,
            tblservers.ipaddress AS serveripaddres,
            tblservers.hostname  AS serverhostname,
            tblservers.username  AS serverusername,
            tblservers.password  AS serverpassword
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

	$rows = full_query( $sql_select );

	$storage_disk_size_limit = 0;

	$sql_select_user = "SELECT
            onapp_user_id AS serveruserid,
            tblservers.ipaddress AS serveripaddres,
            tblservers.hostname  AS serverhostname,
            tblservers.username  AS serverusername,
            tblservers.password  AS serverpassword
        FROM
            tblonappclients
            LEFT JOIN tblservers ON tblservers.id = server_id
        WHERE
            tblonappclients.client_id = $client_id
            AND server_id = $server_id";

	$user_rows = full_query( $sql_select_user );
	if( $user_rows ) {
		while( $user_row = mysql_fetch_assoc( $user_rows ) ) {
			$user = array(
				'userid'   => $user_row[ 'serveruserid' ],
				'username' => $user_row[ 'serverusername' ],
				'password' => decrypt( $user_row[ 'serverpassword' ] ),
				'hostname' => $user_row[ "serveripaddres" ] != "" ?
					'http://' . $user_row[ "serveripaddres" ] :
					$user_row[ 'serverhostname' ],
			);
		}
	}

	if( $rows ) {
		while( $row = mysql_fetch_assoc( $rows ) ) {
			$storage_disk_size_limit += $row[ 'value' ];
		}
	}

	if( ! is_null( $user ) ) {
		$limits = new OnApp_ResourceLimit();

		$limits->auth(
			$user[ "hostname" ],
			$user[ 'username' ],
			$user[ 'password' ]
		);

		$limits->load( $user[ 'userid' ] );

		$limits->_storage_disk_size = $storage_disk_size_limit;

		$limits->save();
	}
	;
}

/**
 * Update Storage Disksize
 *
 * @param mixed  $params
 * @param string $action
 */
function update_user_storagedisksize( $params, $action = 'Active' ) {
	$serviceid = $params[ "serviceid" ];

	$status = serviceStatus( $serviceid );
	serviceStatus( $serviceid, $action );

	$sql_select = "
        SELECT
            configoption1 AS server_id,
            userid
        FROM
            tblhosting
            LEFT JOIN tblproducts
                ON tblproducts.id = packageid
        WHERE
            tblproducts.servertype = 'onappbackupspace' AND
            tblhosting.id = $serviceid";

	$rows = full_query( $sql_select );

	if( $rows ) {
		while( $row = mysql_fetch_assoc( $rows ) ) {
			update_user_limits( $row[ 'server_id' ], $row[ 'userid' ] );
		}
	}

	serviceStatus( $serviceid, $status );
}

/**
 * Converts to short nice look ipv6 ip address
 *
 * @param string $ip ip address
 *
 * @return string short ipv6 address,
 * if not ipv6 | ( PHP was built with IPv6 support disabled ) input ip address
 */
function ipv6_short( $ip ) {
	if( ! strpos( $ip, ':' ) ) {
		return $ip;
	}

	$ip_short = inet_ntop( inet_pton( $ip ) );

	if( ! $ip_short ) {
		return $ip;
	}

	return $ip_short;
}

/**
 * Displays error if wrapper not found
 *
 * @return void
 */
function wrapper_check() {
	global $_LANG;

	if( ! file_exists( ONAPP_WRAPPER_INIT ) ) {
		$file =  dirname( $_SERVER[ 'SCRIPT_FILENAME' ] ) . '/includes/wrapper/OnAppInit.php';

		if( file_exists( $file ) ) {
			require_once $file;
		} else {
			return sprintf(
				"%s " . realpath( dirname( __FILE__ ) . '/../../../' ) . "/includes ( %s )" ,
				$_LANG[ 'onappwrappernotfound' ], $_LANG[ 'onappmakesuredirectoryisaccessible' ]
			);
		}
	}

	return null;
}

/**
 * Gets next account period date
 *
 * @param string  $register_date hosting subscription register date in 'Y-m-d' forman
 *
 * @return string account date
 */
function getAccountDate( $register_date ) {

	$today     = date( 'Y-m-d' );
	$_date_reg = strtotime( $register_date );
	$_today    = strtotime( date( 'Y-m-d' ) );

	$days_left = ( $today == $register_date ) ?
		31 :
		31 - ( $_today - $_date_reg ) / ( 86400 ) % 31;

	return ( $days_left == 31 && $today != $register_date ) ?
		date( 'Y-m-d' ) :
		date( 'Y-m-d', $_today + ( 86400 * $days_left ) );
}

function is_private_ip( $ip ) {
	if( preg_match( '/^127/', $ip ) ) {
		return true;
	}

	return ! filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE );
}

/**
 * Set flash error
 *
 * @param mixed $error_message
 */
function setFlashError( $error_message ) {
	if( is_array( $error_message ) ) {
		$_SESSION[ 'onapp_flash' ][ 'error' ] = implode( PHP_EOL, $error_message );
	}
	else {
		$_SESSION[ 'onapp_flash' ][ 'error' ] = $error_message;
	}
}

/**
 * Get flash error
 *
 * @return string error message
 */
function getFlashError() {
    return array_key_exists('onapp_flash', $_SESSION) 
        ?  $_SESSION[ 'onapp_flash' ][ 'error' ]
        : NULL;
}
