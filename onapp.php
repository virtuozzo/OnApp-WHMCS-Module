<?php
// TODO add onapp $_LANG in to file
// error_reporting(E_ALL);

if( ! defined( 'ONAPP_FILE_NAME' ) ) {
	define( 'ONAPP_FILE_NAME', basename( __FILE__ ) );
}
define( 'CLIENTAREA', true );

require_once 'dbconnect.php';
require_once 'includes/functions.php';
require_once 'includes/clientareafunctions.php';

if( ! defined( 'ONAPP_WRAPPER_INIT' ) ) {
	define( 'ONAPP_WRAPPER_INIT', dirname( $_SERVER[ 'SCRIPT_FILENAME' ] ) . '/includes/wrapper/OnAppInit.php' );
}

require_once dirname( __FILE__ ) . '/modules/servers/onapp/lib.php';

if( isset( $_POST[ 'language' ] ) ) {
	$_SESSION[ 'Language' ] = $_POST[ 'language' ];
}
else {
	if( isset( $GLOBALS[ 'CONFIG' ][ 'Language' ] ) ) {
		$_SESSION[ 'Language' ] = ucfirst( $GLOBALS[ 'CONFIG' ][ 'Language' ] );
	}
}

load_language();

if( file_exists( ONAPP_WRAPPER_INIT ) ) {
	require_once ONAPP_WRAPPER_INIT;
}

/**
 * If they are not logged in divert them
 */
$user_id = $_SESSION[ 'uid' ];

if( ! $user_id ) {
	redirect( 'clientarea.php' );
	exit();
}
;

/**
 * Set global variables
 */
$_ONAPPVARS = array();

foreach( array( 'id', 'page', 'action' ) as $val ) {
	$_ONAPPVARS[ $val ] = get_value( $val );
}

/**
 * Set base noavigation bar
 */

$breadcrumbnav = ' <a href="index.php">' . $_LANG[ "globalsystemname" ] . '</a>';
$breadcrumbnav .= ' &gt; <a href="clientarea.php">' . $_LANG[ "clientareatitle" ] . '</a>';
$breadcrumbnav .= ' &gt; <a href="' . ONAPP_FILE_NAME . '">' . $_LANG[ "onappmyvms" ] . '</a>';

if( in_array( $_ONAPPVARS[ 'page' ], array(
	'productdetails',
	'disks',
	'cpuusage',
	'ipaddresses',
	'backups',
	'upgrade',
	'firewallrules'
) )
) {
	$breadcrumbnav .= ' &gt; <a title="' . $_LANG[ "clientareaproductdetails" ] . '" href="' . ONAPP_FILE_NAME . '?page=productdetails&id=' . $id . '">' . $_LANG[ "clientareaproductdetails" ] . '</a>';
}

/**
 * Check if service exist
 **/
if( $_ONAPPVARS[ 'id' ] !== null ) {

	$_ONAPPVARS[ 'service' ] = get_service( $_ONAPPVARS[ 'id' ] );

	if( ! $_ONAPPVARS[ 'service' ] ) {
		$_ONAPPVARS[ 'error' ] = sprintf( $_LANG[ "onappservicenotfound" ], $id );
	}
	elseif( ! is_null( $_ONAPPVARS[ 'service' ][ 'vmid' ] ) && $_ONAPPVARS[ 'service' ][ 'userid' ] == $user_id ) {
		$_ONAPPVARS[ 'vm' ] = get_vm( $_ONAPPVARS[ 'id' ] );
	}
}
;

/**
 * Chose page to show
 */
if( isset( $_ONAPPVARS[ 'page' ] ) && $_ONAPPVARS[ 'service' ] && $_ONAPPVARS[ 'service' ][ 'userid' ] == $user_id ) {
	switch( $_ONAPPVARS[ 'page' ] ) {
		case 'productdetails':
			productdetails();
			break;
		case 'cpuusage':
			$breadcrumbnav .= ' &gt; <a title="' . $_LANG[ "onappcpuusage" ] . '" href="' . ONAPP_FILE_NAME . '?page=cpuusage&id=' . $id . '">' . $_LANG[ "onappcpuusage" ] . '</a>';
			productcpuusage();
			break;
		case 'ipaddresses':
			$breadcrumbnav .= ' &gt; <a title="' . $_LANG[ "onappipaddresses" ] . '" href="' . ONAPP_FILE_NAME . '?page=ipaddresses&id=' . $id . '">' . $_LANG[ "onappipaddresses" ] . '</a>';
			productipaddresses();
			break;
		case 'disks':
			$breadcrumbnav .= ' &gt; <a title="' . $_LANG[ "onappdisks" ] . '" href="' . ONAPP_FILE_NAME . '?page=disks&id=' . $id . '">' . $_LANG[ "onappdisks" ] . '</a>';
			productdisks();
			break;
		case 'backups':
			$breadcrumbnav .= ' &gt; <a title="' . $_LANG[ "onappbackups" ] . '" href="' . ONAPP_FILE_NAME . '?page=backups&id=' . $id . '">' . $_LANG[ "onappbackups" ] . '</a>';
			productbackups();
			break;
		case 'firewallrules':
			firewallrules();
			break;
		case 'upgrade':
			if( $_ONAPPVARS[ 'service' ][ 'configoptionsupgrade' ] == "on" ) {
				$breadcrumbnav .= ' &gt; <a title="' . $_LANG[ "onappupgradedowngrade" ] . '" href="' . ONAPP_FILE_NAME . '?page=upgrade&id=' . $id . '">' . $_LANG[ "onappupgradedowngrade" ] . '</a>';
				productupgrade();
			}
			else {
				$_ONAPPVARS[ 'error' ] = sprintf( $_LANG[ "onapppagenotfound" ], $_ONAPPVARS[ 'page' ] );
				productdetails();
			}
			;
			break;
		default:
			$_ONAPPVARS[ 'error' ] = sprintf( $_LANG[ "onapppagenotfound" ], $_ONAPPVARS[ 'page' ] );
			productdetails();
			break;
	}
}
elseif( isset( $_ONAPPVARS[ 'page' ] ) && $_ONAPPVARS[ 'page' ] == "storagedisksize" ) {
	clientareastoragedisksizes();
}
else {
	clientareaproducts();
}
;

/**
 * Manage firewall rules tab
 *
 * @global mixed $_ONAPPVARS
 * @global mixed $_LANG
 */
function firewallrules() {
	global $_ONAPPVARS, $_LANG;

	$user         = get_onapp_client( $_ONAPPVARS[ 'id' ] );
	$onapp_config = get_onapp_config( $_ONAPPVARS[ 'service' ][ 'serverid' ] );

	$onapp        = new OnApp_Factory( $onapp_config[ "adress" ], $user[ "email" ], $user[ "password" ] );
	$firewallrule = $onapp->factory( 'VirtualMachine_FirewallRule', true );

	$fr = isset( $_POST[ 'fr' ] ) ? $_POST[ 'fr' ] : null;

	$action = $_ONAPPVARS[ 'action' ];

	$network_interfaces = isset( $_POST[ 'network_interfaces' ] ) ? $_POST[ 'network_interfaces' ] : null;
	$ruleid             = get_value( 'ruleid' );
	$position           = get_value( 'position' );

	if( ! is_null( $action ) && $action != "" ) {
		switch( $action ) {
			case 'save':
				$return = _firewallrule_save( $_ONAPPVARS[ 'service' ][ 'vmid' ], $fr, $firewallrule );
				break;
			case 'delete':
				if( ! $ruleid ) {
					$_ONAPPVARS[ 'error' ] = sprintf( $_LANG[ "onappnotenoughparams" ], $action );
					break;
				}
				$return = _firewallrule_delete( $_ONAPPVARS[ 'service' ][ 'vmid' ], $ruleid, $firewallrule );
				break;
			case 'move':
				if( ! $ruleid || ! $position ) {
					$_ONAPPVARS[ 'error' ] = sprintf( $_LANG[ "onappnotenoughparams" ], $action );
					break;
				}
				$return = _firewallrule_move( $_ONAPPVARS[ 'service' ][ 'vmid' ], $ruleid, $position, $firewallrule );
				break;
			case 'set_defaults':
				if( ! $network_interfaces ) {
					$_ONAPPVARS[ 'error' ] = sprintf( $_LANG[ "onappnotenoughparams" ], $action );
					break;
				}
				$return = _firewallrule_set_default( $_ONAPPVARS[ 'service' ][ 'vmid' ], $network_interfaces, $firewallrule );
				break;
			case 'apply':

				$return = _firewallrule_apply( $_ONAPPVARS[ 'service' ][ 'vmid' ], $firewallrule );

			default:
				$_ONAPPVARS[ 'error' ] = sprintf( $_LANG[ "onappactionnotfound" ], $action );
				break;
		}
	}

	$networkinterface = $onapp->factory( 'VirtualMachine_NetworkInterface', true );

	$firewallrules     = $firewallrule->getList( $_ONAPPVARS[ 'service' ][ 'vmid' ] );
	$networkinterfaces = $networkinterface->getList( $_ONAPPVARS[ 'service' ][ 'vmid' ] );

	$error = isset( $_ONAPPVARS[ 'error' ] ) ? $_ONAPPVARS[ 'error' ] : getFlashError();

	$_networkinterfaces = array();

	foreach( $networkinterfaces as $interface ) {
		$_networkinterfaces[ $interface->_id ] = $interface;
	}

	if( ! is_null( $firewallrules ) ) {
		foreach( $firewallrules as $firewall ) {
			$firewall_by_network[ $firewall->_network_interface_id ][ ] = $firewall;
		}
	}
	else {
		$firewall_by_network = null;
	}

	show_template(
		"onapp/clientareafirewallrules",
		array(
			'commands'             => array( 'ACCEPT', 'DROP' ),
			'networkinterfaces'    => $_networkinterfaces,
			'firewall_by_network'  => $firewall_by_network,
			'id'                   => $_ONAPPVARS[ 'id' ],
			'configoptionsupgrade' => $_ONAPPVARS[ 'service' ][ 'configoptionsupgrade' ],
			'error'                => $error,
		)
	);
}

function _firewallrule_apply( $vmid, $firewallrule ) {
	global $_ONAPPVARS;

	$firewallrule->update( $vmid );

	if( $firewallrule->getErrorsAsArray() ) {
		setFlashError( $firewallrule->getErrorsAsString( ', ' ) );
	}

	redirect( ONAPP_FILE_NAME . "?page=firewallrules&id=" . $_ONAPPVARS[ 'id' ] );
}

/**
 * Set default commands for network interfaces
 *
 * @global mixed  $_ONAPPVARS
 *
 * @param integer $vmid
 * @param mixed   $network_interfaces
 * @param mixed   $firewallrule
 */
function _firewallrule_set_default( $vmid, $network_interfaces, $firewallrule ) {
	global $_ONAPPVARS;

	$firewallrule->updateDefaults( $vmid, $network_interfaces );

	//todo add error verification Ticket #4885 codebase

	redirect( ONAPP_FILE_NAME . "?page=firewallrules&id=" . $_ONAPPVARS[ 'id' ] );
}

/**
 * Delete firewall rule
 *
 * @global mixed  $_ONAPPVARS
 *
 * @param integer $vmid
 * @param integer $ruleid
 * @param mixed   $firewallrule
 */
function _firewallrule_delete( $vmid, $ruleid, $firewallrule ) {
	global $_ONAPPVARS;

	$firewallrule->_id                 = $ruleid;
	$firewallrule->_virtual_machine_id = $vmid;
	$firewallrule->delete();

	if( $firewallrule->getErrorsAsArray() ) {
		setFlashError( $firewallrule->getErrorsAsString( ', ' ) );
	}

	redirect( ONAPP_FILE_NAME . "?page=firewallrules&id=" . $_ONAPPVARS[ 'id' ] );
}

/**
 * Create a new firewall rule
 *
 * @global mixed  $_ONAPPVARS
 *
 * @param integer $vmid
 * @param mixed   $fr
 * @param mixed   $firewallrule
 */
function _firewallrule_save( $vmid, $fr, $firewallrule ) {
	global $_ONAPPVARS;

	foreach( $fr as $field => $value ) {
		$_field                = '_' . $field;
		$firewallrule->$_field = $value;
	}

	$firewallrule->_virtual_machine_id = $vmid;

	$firewallrule->save();

	if( $firewallrule->getErrorsAsArray() ) {
		setFlashError( $firewallrule->getErrorsAsString( ', ' ) );
	}

	redirect( ONAPP_FILE_NAME . "?page=firewallrules&id=" . $_ONAPPVARS[ 'id' ] );
}

/**
 * Move firewall rule
 *
 * @global mixed  $_ONAPPVARS
 *
 * @param integer $vmid
 * @param integer $ruleid
 * @param integer $position
 * @param mixed   $firewallrule
 */
function _firewallrule_move( $vmid, $ruleid, $position, $firewallrule ) {
	global $_ONAPPVARS;

	$firewallrule->_virtual_machine_id = $vmid;
	$firewallrule->_id                 = $ruleid;
	$firewallrule->move( $position );

	//todo add error verification Ticket #2358 codebase

	redirect( ONAPP_FILE_NAME . "?page=firewallrules&id=" . $_ONAPPVARS[ 'id' ] );
}

/**
 * Redirect to another page
 *
 * @param string $url redirection url
 */
function redirect( $url ) {
	if( ! headers_sent() ) {
		header( 'Location: ' . $url );
		exit;
	}
	else {
		echo '<script type="text/javascript">';
		echo 'window.location.href="' . $url . '";';
		echo '</script>';
		echo '<noscript>';
		echo '<meta http-equiv="refresh" content="0;url=' . $url . '" />';
		echo '</noscript>';
		exit;
	}
	;
}

/**
 * Get POST or GET value
 *
 * @param string $name value name
 */
function get_value( $name ) {
	global $_GET, $_POST;

	return isset( $_POST[ $name ] )
		? $_POST[ $name ]
		: isset( $_GET[ $name ] )
			? $_GET[ $name ]
			: null;
}

/**
 * Show Client area
 *
 * @param string $templatefile template name
 * @param array  $values       smarty values
 */
function show_template( $templatefile, $values ) {

	global $_LANG, $breadcrumbnav, $smartyvalues, $CONFIG;

	$pagetitle = $_LANG[ "clientareatitle" ];
	$pageicon  = "images/support/clientarea.gif";

	initialiseClientArea( $pagetitle, $pageicon, $breadcrumbnav );

	$smartyvalues = $values;

	if( $CONFIG[ 'SystemSSLURL' ] ) {
		$smartyvalues[ 'systemurl' ] = $CONFIG[ 'SystemSSLURL' ] . '/';
	}
	else {
		if( $CONFIG[ 'SystemURL' ] != 'http://www.yourdomain.com/whmcs' ) /* Do not change this URL!!! - Otherwise WHMCS Failed ! */ {
			$smartyvalues[ 'systemurl' ] = $CONFIG[ 'SystemURL' ] . '/';
		}
	}

	if( isset( $_SESSION[ 'onapp_flash' ][ 'error' ] ) ) {
		unset( $_SESSION[ 'onapp_flash' ][ 'error' ] );
	}
	outputClientArea( $templatefile );
}

/**
 * Show user Virtual machines list
 */
function clientareaproducts() {
	global $user_id, $_ONAPPVARS, $_LANG;

	if( wrapper_check() ) {
		show_template(
			"onapp/clientareaproducts",
			array(
				'services'         => array(),
				'not_resolved_vms' => array(),
				'error'            => $_LANG[ 'onapponmaintenance' ],
			)
		);

		return;
	}

	$services         = array();
	$not_resolved_vms = array();

// Get OnApp VMs
	$select_onapp_users = sprintf(
		"SELECT
			*,
			tblonappclients.password AS userpassword
		FROM
			tblonappclients
			LEFT JOIN tblservers ON tblservers.id = server_id
		WHERE client_id = '%s' AND tblservers.type = 'onapp' AND tblservers.disabled = '0';",
		$user_id
	);

	$onapp_users_query = full_query( $select_onapp_users );

	while( $onapp_user = mysql_fetch_assoc( $onapp_users_query ) ) {
		if( ! $onapp_user[ 'ipaddress' ] && ! $onapp_user[ 'hostname' ] ) {
			continue;
		}
		$vm = new OnApp_VirtualMachine();

		$url = ( $onapp_user[ 'hostname' ] ) ? $onapp_user[ 'hostname' ] : $onapp_user[ 'ipaddress' ];

		if( strpos( $url, 'http' ) === false ) {
			$url = 'http://' . $url;
		}

		$vm->auth(
			$url,
			$onapp_user[ "email" ],
			decrypt( $onapp_user[ "userpassword" ] )
		);

		$tmp_vms = $vm->getList();

		if( is_array( $tmp_vms ) ) {
			foreach( $tmp_vms as $tmp_vm ) {
				$not_resolved_vms[ $onapp_user[ "server_id" ] ][ $tmp_vm->_id ] = array(
					'vm'     => $tmp_vm,
					'server' => $onapp_user
				);
			}
		}
	}
	;

// Get services
	$select_services = "SELECT
        tblhosting.id AS id,
        tblhosting.domain AS domain,
        tblhosting.server AS serverid,
        tblonappservices.vm_id AS vmid,
        tblproducts.name AS product
    FROM
        tblhosting
        LEFT JOIN tblproducts ON tblproducts.id = packageid
        LEFT JOIN tblonappservices ON service_id = tblhosting.id
    WHERE
        servertype = 'onapp'
        AND tblhosting.domainstatus = 'Active'
        AND userid = '$user_id'
    ORDER BY tblhosting.id ASC";

	$services_rows = full_query( $select_services );

	if( $services_rows ) {
		while( $service = mysql_fetch_assoc( $services_rows ) ) {
			$services[ $service[ 'id' ] ] = $service;

			if( is_null( $service[ 'vmid' ] ) ) {
				$services[ $service[ 'id' ] ][ 'error' ] = $_LANG[ "onappvmnotcreated" ];
			}
			elseif( ! isset( $not_resolved_vms[ $service[ 'serverid' ] ][ $service[ 'vmid' ] ] ) ) {
				$services[ $service[ 'id' ] ][ 'error' ] = sprintf(
					$_LANG[ "onappvmnotfound" ],
					$service[ 'vmid' ]
				);
			}
			else {
				$services[ $service[ 'id' ] ][ 'obj' ] = $not_resolved_vms[ $service[ 'serverid' ] ][ $service[ 'vmid' ] ][ 'vm' ];
				unset( $not_resolved_vms[ $service[ 'serverid' ] ][ $service[ 'vmid' ] ] );
				if( count( $not_resolved_vms[ $service[ 'serverid' ] ] ) == 0 ) {
					unset( $not_resolved_vms[ $service[ 'serverid' ] ] );
				}
			}
			;
		}
	}
	;

	show_template(
		"onapp/clientareaproducts",
		array(
			'services'         => $services,
			'not_resolved_vms' => $not_resolved_vms,
			'error'            => isset( $_ONAPPVARS[ 'error' ] ) ? $_ONAPPVARS[ 'error' ] : null,
		)
	);
}

/**
 * Show Virtual machine page
 */
function productdetails() {
	global $_ONAPPVARS;

	if( ! isset( $_ONAPPVARS[ 'service' ] ) ) {
		clientareaproducts();
	}
	if( isset( $_ONAPPVARS[ 'action' ] ) && ! isset( $_ONAPPVARS[ 'error' ] ) && ! isset( $_ONAPPVARS[ 'vm' ]->_obj->error ) ) {
		_actions_vm( $_ONAPPVARS[ 'action' ] );
	}
	elseif( ! is_null( $_ONAPPVARS[ 'service' ][ 'vmid' ] ) ) {
		showproduct();
	}
	else {
		showcreateproduct();
	}
}

/**
 * Run action for virtual machine
 */
function _actions_vm( $action ) {
	global $_ONAPPVARS, $_LANG;

	$action = $_ONAPPVARS[ 'action' ];

	if( ! is_null( $action ) ) {
		switch( $action ) {
			case 'create':
				_action_vm_create();
				break;
			case 'unlock':
				$_ONAPPVARS[ 'vm' ]->unlock();
				break;
			case 'build':
				_action_update_res();
				$_ONAPPVARS[ 'vm' ]->build();
				break;
			case 'rebuild':
				rebuild();
				break;
			case 'start':
				_action_update_res();
				$_ONAPPVARS[ 'vm' ]->startup();
				break;
			case 'stop':
				$_ONAPPVARS[ 'vm' ]->shutdown();
				break;
			case 'reboot':
				_action_update_res();
				$_ONAPPVARS[ 'vm' ]->reboot();
				break;
			case 'delete':
				_action_vm_delete();
				break;
			case 'reset_pass':
				$_ONAPPVARS[ 'vm' ]->reset_password();
				break;
			case 'rebuild_network':
				$_ONAPPVARS[ 'vm' ]->rebuild_network(
					get_value( 'shutdown_type' ),
					get_value( 'required_startup' )
				);
				break;
			default:
				$_ONAPPVARS[ 'error' ] = sprintf( $_LANG[ "onappactionnotfound" ], $action );
				break;
		}
	}
	;

	unset( $_ONAPPVARS[ 'action' ] );

	if( isset( $_ONAPPVARS[ 'vm' ] ) && ! is_null( $_ONAPPVARS[ 'vm' ]->getErrorsAsArray() ) ) {
		$_ONAPPVARS[ 'error' ] = $_ONAPPVARS[ 'vm' ]->getErrorsAsString( ', ' );
	}
	elseif( isset( $_ONAPPVARS[ 'vm' ] ) && ! is_null( $_ONAPPVARS[ 'vm' ]->_obj->getErrorsAsArray() ) ) {
		$_ONAPPVARS[ 'error' ] = $_ONAPPVARS[ 'vm' ]->_obj->getErrorsAsString( ', ' );
	}

	if( ! isset( $_ONAPPVARS[ 'error' ] ) ) {
		redirect( ONAPP_FILE_NAME . "?page=productdetails&id=" . $_ONAPPVARS[ 'id' ] );
	}
	else {
		productdetails();
	}
}

function rebuild() {
	global $_ONAPPVARS, $_LANG;

	_action_update_res();

	$_ONAPPVARS[ 'vm' ]->_template_id = isset( $_ONAPPVARS[ 'service' ][ 'os' ] ) ? $_ONAPPVARS[ 'service' ][ 'os' ] : $_ONAPPVARS[ 'service' ][ 'configoption2' ];

	$_ONAPPVARS[ 'vm' ]->_required_startup = '1';
	$_ONAPPVARS[ 'vm' ]->build();
}

/**
 * Action create virtual machine
 */
function _action_vm_create() {
	global $_ONAPPVARS, $_LANG;

	foreach( array( 'templateid', 'hostname' ) as $val ) {
		$_ONAPPVARS[ $val ] = get_value( $val );
	}

	if( isset( $_ONAPPVARS[ 'vm' ]->_id ) ) {
		$_ONAPPVARS[ 'error' ] = $_LANG[ "onappvmexist" ];
	}
	elseif( ! isset( $_ONAPPVARS[ 'hostname' ] ) || $_ONAPPVARS[ 'hostname' ] == "" ) {
		$_ONAPPVARS[ 'error' ] = $_LANG[ "onapphostnamenotfound" ];
	}
	elseif( ! isset( $_ONAPPVARS[ 'templateid' ] ) ) {
		$_ONAPPVARS[ 'error' ] = $_LANG[ "onapptemplatenotset" ];
	}

	if( isset( $_ONAPPVARS[ 'error' ] ) ) {
		return false;
	}

	$_ONAPPVARS[ 'vm' ] = create_vm( $_ONAPPVARS[ 'id' ], $_ONAPPVARS[ 'hostname' ], $_ONAPPVARS[ 'templateid' ] );
	_ips_resolve_all( $_ONAPPVARS[ 'id' ] );

	return true;
}

function _action_vm_delete() {
	global $_ONAPPVARS;

	$_ONAPPVARS[ 'vm' ] = delete_vm( $_ONAPPVARS[ 'id' ] );

	return true;
}

function _action_update_res() {
	global $_ONAPPVARS;

	$vm           = $_ONAPPVARS[ 'vm' ]->_obj;
	$service      = $_ONAPPVARS[ 'service' ];
	$user         = get_onapp_client( $_ONAPPVARS[ 'id' ] );
	$onapp_config = get_onapp_config( $service[ 'serverid' ] );

	$memory            = $service[ 'configoption3' ] + $service[ 'additionalram' ];
	$cpus              = $service[ 'configoption5' ] + $service[ 'additionalcpus' ];
	$cpu_shares        = $service[ 'configoption7' ] + $service[ 'additionalcpushares' ];
	$primary_disk_size = $service[ 'configoption11' ] + $service[ 'additionaldisksize' ];
	$rate_limit        = $service[ 'configoption8' ] + $service[ 'additionalportspead' ];

	if( $option = (array)( json_decode( htmlspecialchars_decode( $service[ 'configoption23' ] ) ) ) ) {
		$sec_net_port_speed = $option[ 'sec_net_port_speed' ];
		$sec_network_id     = $option[ 'sec_network_id' ];
	}

	// Adjust Resource Allocations
	if( $vm->_memory != $memory ||
		$vm->_cpus != $cpus ||
		$vm->_cpu_shares != $cpu_shares
	) {
		$_ONAPPVARS[ 'vm' ]->_memory            = $memory;
		$_ONAPPVARS[ 'vm' ]->_cpus              = $cpus;
		$_ONAPPVARS[ 'vm' ]->_cpu_shares        = $cpu_shares;
		$_ONAPPVARS[ 'vm' ]->_primary_disk_size = $primary_disk_size;

		$_ONAPPVARS[ 'vm' ]->save();
	}
	;

	// Change Disk size
	$disks = new OnApp_Disk();

	$disks->auth(
		$onapp_config[ "adress" ],
		$user[ "email" ],
		$user[ "password" ]
	);

	$primary_disk = null;

	foreach( $disks->getList( $_ONAPPVARS[ 'vm' ]->_id ) as $disk ) {
		if( $disk->_primary == "true" ) {
			$primary_disk = $disk;
		}
	}

	if( $primary_disk->_disk_size != $primary_disk_size ) {
		$primary_disk->_disk_size = $primary_disk_size;

		$primary_disk->auth(
			$onapp_config[ "adress" ],
			$user[ "email" ],
			$user[ "password" ]
		);

		$primary_disk->save();
	}
	;

// Update Primary Network Port Speed
	$network = get_vm_interface( $_ONAPPVARS[ 'id' ] );

	if( $network && $rate_limit != $network->_rate_limit ) {
		$network->_rate_limit = $rate_limit;
		$network->save();
	}

// Update Secondary Network Port Speed if exists and needed
	if( $sec_network_id ) {
		$sec_network = get_sec_networkinterface( $service[ 'vmid' ], $service[ 'serverid' ] );

		if($sec_network && $sec_net_port_speed && $sec_net_port_speed != $sec_network->_rate_limit ) {
			$sec_network->_rate_limit = $sec_net_port_speed;
			$sec_network->save();
		}
	}

	// resolve all IPs
	_ips_resolve_all( $_ONAPPVARS[ 'id' ] );

	return true;
}

/**
 * Get secondary network interface
 *
 * @param type $vmid
 * @param type $serverid
 *
 * @return type
 */
function get_sec_networkinterface( $vmid, $serverid ) {
	$result  = false;
	$network = new OnApp_VirtualMachine_NetworkInterface();

	$onapp_config = get_onapp_config( $serverid );

	$network->auth(
		$onapp_config[ "adress" ],
		$onapp_config[ 'username' ],
		$onapp_config[ 'password' ]
	);

	$network->_virtual_machine_id = $vmid;

	$networks = $network->getList();

	foreach( $networks as $net ) {
		if( $net->_primary != true ) {
			$result = $net;
		}
	}

	if( $result ) {
		$result->auth(
			$onapp_config[ "adress" ],
			$onapp_config[ 'username' ],
			$onapp_config[ 'password' ]
		);
	}

	return $result;
}

/**
 * Show virtual machine details
 */
function showproduct() {
	global $_ONAPPVARS, $_LANG;

// Geting transaction by Ajax request //
///////////////////////////////////////
	if( isset( $_GET[ 'transactionid' ] ) ) {
		if( $_GET[ 'type' ] != 'Transaction' ) {
			exit();
		}

		$user         = get_onapp_client( $_ONAPPVARS[ 'id' ] );
		$onapp_config = get_onapp_config( $_ONAPPVARS[ 'service' ][ 'serverid' ] );
		$onapp        = new OnApp_Factory( $onapp_config[ "adress" ], $user[ "email" ], $user[ "password" ] );

		$transaction  = $onapp->factory( 'Transaction', true );
		$_transaction = $transaction->load_with_output( $_GET[ 'transactionid' ] );

		$transaction_js [ 'output' ] = $_transaction->_log_output;

		$transaction_js = json_encode( $transaction_js );

		ob_end_clean();
		exit( $transaction_js );
	}
// End Geting transaction by Ajax request //
///////////////////////////////////////////

	$onapp_config = get_onapp_config( $_ONAPPVARS[ 'service' ][ 'serverid' ] );

	if( ! is_null( $_ONAPPVARS[ 'vm' ]->getErrorsAsArray() ) ) {
		$_ONAPPVARS[ 'error' ] = $_ONAPPVARS[ 'vm' ]->getErrorsAsString( ', ' );

		clientareaproducts();
	}
	elseif( is_null( $_ONAPPVARS[ 'vm' ]->_id ) ) {
		$_ONAPPVARS[ 'error' ] = sprintf(
			$_LANG[ "onappvmnotfoundonserver" ],
			$_ONAPPVARS[ 'service' ][ 'vmid' ],
			$onapp_config[ "adress" ]
		);

		showcreateproduct();
	}
	else {

		$network = get_vm_interface( $_ONAPPVARS[ 'id' ] );

// Getting log info //
/////////////////////

		$user         = get_onapp_client( $_ONAPPVARS[ 'id' ] );
		$onapp_config = get_onapp_config( $_ONAPPVARS[ 'service' ][ 'serverid' ] );

		$onapp = new OnApp_Factory( $onapp_config[ "adress" ], $user[ "email" ], $user[ "password" ] );

		$log = $onapp->factory( 'Log', true );

		$url_args = array(
			'q' => $_ONAPPVARS[ 'vm' ]->_obj->_identifier,
		);

		$logs = $log->getList( $url_args );

		foreach( $logs as $item ) {
			$log_items[ $item->_id ][ 'target_type' ] = $item->_target_type;
			$log_items[ $item->_id ][ 'target_id' ]   = $item->_target_id;
			$log_items[ $item->_id ][ 'created_at' ]  = str_replace( 'T', ' ', substr( $item->_created_at, 0, 16 ) );
			$log_items[ $item->_id ][ 'updated_at' ]  = $item->_updated_at;
			$log_items[ $item->_id ][ 'status' ]      = $item->_status;
			$log_items[ $item->_id ][ 'action' ]      = $item->_action;
		}

		$log_items = array_slice( $log_items, 0, 15, true );

// End Getting Log Info //
/////////////////////////

// Update VM root password in WHMCS database
		if( $_ONAPPVARS[ 'vm' ]->_obj->_initial_root_password && $_ONAPPVARS[ 'service' ][ 'id' ] ) {
			full_query( "UPDATE
                tblhosting
            SET password = '"
				. encrypt( $_ONAPPVARS[ 'vm' ]->_obj->_initial_root_password ) . "'
            WHERE
                id = " . $_ONAPPVARS[ 'service' ][ 'id' ] . "
            " );
		}

		show_template(
			"onapp/clientareaoverview",
			array(
				'virtualmachine'       => $_ONAPPVARS[ 'vm' ]->_obj,
				'id'                   => $_ONAPPVARS[ 'id' ],
				'error'                => isset( $_ONAPPVARS[ 'error' ] ) ? $_ONAPPVARS[ 'error' ] : null,
				'configoptionsupgrade' => $_ONAPPVARS[ 'service' ][ 'configoptionsupgrade' ],
				'rate_limit'           => $network->_rate_limit,
				'vm_logs'              => $log_items,
				'overagesenabled'      => $_ONAPPVARS[ 'service' ][ 'overagesenabled' ],
			)
		);
	}
}

/**
 * Show user Virtual machine creation
 */
function showcreateproduct() {
	global $_ONAPPVARS;

	$service_server_id = $_ONAPPVARS[ 'service' ][ 'serverid' ];
	$product_server_id = $_ONAPPVARS[ 'service' ][ 'productserverid' ];

	if( $service_server_id != $product_server_id ) {
		$service_server_id = $product_server_id;
	}

	$templates = get_templates( $service_server_id, $_ONAPPVARS[ 'service' ][ "configoption2" ] );
	$os        = $_ONAPPVARS[ 'service' ][ 'os' ];

	if( ! is_null( $os ) && isset( $templates[ $os ] ) ) {
		$templates = array(
			$os => $templates[ $os ]
		);
	}
	;

	$_ONAPPVARS[ 'service' ][ 'configoption9' ]  = round( $_ONAPPVARS[ 'service' ][ 'configoption9' ] );
	$_ONAPPVARS[ 'service' ][ 'configoption11' ] = round( $_ONAPPVARS[ 'service' ][ 'configoption11' ] );

	show_template(
		"onapp/clientareacreateproduct",
		array(
			'service'   => $_ONAPPVARS[ 'service' ],
			'templates' => $templates,
			'error'     => isset( $_ONAPPVARS[ 'error' ] ) ? $_ONAPPVARS[ 'error' ] : null,
		)
	);
}

/**
 * Show Virtual machine CPU usage
 */
function productcpuusage() {
	global $_ONAPPVARS, $_LANG;

	$onapp_config = get_onapp_config( $_ONAPPVARS[ 'service' ][ 'serverid' ] );

	$cpuusage = new OnApp_VirtualMachine_CpuUsage();

	$cpuusage->_virtual_machine_id = $_ONAPPVARS[ 'vm' ]->_id;

	$user = get_onapp_client( $_ONAPPVARS[ 'id' ] );

	$cpuusage->auth(
		$onapp_config[ "adress" ],
		$user[ "email" ],
		$user[ "password" ]
	);

	$list = $cpuusage->getList();

	$hourly_stat = array();

	foreach( $list as $key => $stat ) {
		$hourly_stat[ $key ][ 'date' ]  = strtotime( $stat->_created_at ) * 1000;
                $hourly_stat[ $key ][ 'usage' ] = number_format( $stat->_cpu_time, 2, '.', '' );
	}

	$content = '';

	foreach( $hourly_stat as $stat ) {
		$content .= '[' . $stat[ 'date' ] . ', ' . $stat[ 'usage' ] . '],';
	}

	$data = "[{data: [ " . $content . "], name: '" . $_LANG[ 'onappcpuusage' ] . "'";

	$data = str_replace( '],]', ']]', $data );

	show_template(
		"onapp/clientareacpuusage",
		array(
			'id'      => $_ONAPPVARS[ 'id' ],
			'address' => $onapp_config[ "adress" ],
			'error'   => isset( $_ONAPPVARS[ 'error' ] ) ? $_ONAPPVARS[ 'error' ] : null,
			'data'    => $data,
            'configoptionsupgrade' => $_ONAPPVARS[ 'service' ][ 'configoptionsupgrade' ],
		)
	);
}

/**
 * Show virtual machine addresses
 */
function productipaddresses() {
	global $_ONAPPVARS, $_LANG;

	foreach( array( 'ipid' ) as $val ) {
		$_ONAPPVARS[ $val ] = get_value( $val );
	}

	$action = $_ONAPPVARS[ 'action' ];

	if( ! is_null( $action ) && $action != "" ) {
		switch( $action ) {
			case 'setbase':
				$return = _action_ip_setbase( $_ONAPPVARS[ 'id' ], $_ONAPPVARS[ 'ipid' ] );
				break;
			case 'setadditional':
				$return = _action_ip_setadditional( $_ONAPPVARS[ 'id' ], $_ONAPPVARS[ 'ipid' ] );
				break;
			case 'assignbase':
				$return = _action_ip_add( $_ONAPPVARS[ 'id' ], 1 );
				break;
			case 'sec_net_assignbase':
				$return = _action_ip_add( $_ONAPPVARS[ 'id' ], 1, 1 );
				break;
			case 'assignadditional':
				$return = _action_ip_add( $_ONAPPVARS[ 'id' ], 0 );
				break;
			case 'sec_net_assignadditional':
				$return = _action_ip_add( $_ONAPPVARS[ 'id' ], 0, 1 );
				break;
			case 'resolveall':
				$return = _ips_resolve_all( $_ONAPPVARS[ 'id' ] );
				break;
			case 'delete':
				$return = _action_ip_delete( $_ONAPPVARS[ 'id' ], $_ONAPPVARS[ 'ipid' ] );
				break;
			default:
				$_ONAPPVARS[ 'error' ] = sprintf( $_LANG[ "onappactionnotfound" ], $action );
				break;
		}
	}
	;

	if( isset( $return ) ) {
		if( isset( $return[ 'error' ] ) ) {
			$_ONAPPVARS[ 'error' ] = $return[ 'error' ];
		}
		else {
			redirect( ONAPP_FILE_NAME . "?page=ipaddresses&id=" . $_ONAPPVARS[ 'id' ] );
		}
	}

	clientareaipaddresses();
}

/**
 * Show Virtual machine network adresses
 */
function clientareaipaddresses() {
	global $_ONAPPVARS;

	$service = $_ONAPPVARS[ 'service' ];

	if( $option = (array)( json_decode( htmlspecialchars_decode( $service[ 'configoption23' ] ) ) ) ) {
		$sec_net_ips = $option[ 'sec_net_ips' ];
	}

	$ips = get_vm_ips( $_ONAPPVARS[ 'id' ] );

	show_template(
		"onapp/clientareaipaddresses",
		array(
			'base_ips'                        => $ips[ 'base' ],
			'additional_ips'                  => $ips[ 'additional' ],
			'not_resolved_ips'                => $ips[ 'notresolved' ],
			'not_resloved_base'               => $service[ 'configoption18' ] - count( $ips[ 'base' ] ),
			'not_resloved_additional'         => $service[ 'additionalips' ] - count( $ips[ 'additional' ] ),
			'sec_net_not_resloved_base'       => $sec_net_ips - count( $ips[ 'sec_net_base' ] ),
			'sec_net_not_resloved_additional' => $service[ 'sec_net_additionalips' ] - count( $ips[ 'sec_net_additional' ] ),
			'sec_net_additional'              => $ips[ 'sec_net_additional' ],
			'sec_net_base'                    => $ips[ 'sec_net_base' ],
			'id'                              => $_ONAPPVARS[ 'id' ],
			'service'                         => $_ONAPPVARS[ 'service' ],
			'error'                           => isset( $_ONAPPVARS[ 'error' ] ) ? $_ONAPPVARS[ 'error' ] : null,
			'configoptionsupgrade'            => $_ONAPPVARS[ 'service' ][ 'configoptionsupgrade' ],
		)
	);
}

/**
 * Show Virtual machine Disks
 */

function productdisks() {
	global $_ONAPPVARS, $_LANG;

	foreach( array( 'mode', 'diskid' ) as $val ) {
		$_ONAPPVARS[ $val ] = get_value( $val );
	}

	$action = $_ONAPPVARS[ 'action' ];

	if( ! is_null( $action ) && $action != "" ) {
		switch( $action ) {
			case 'autobackup':
				$return = _action_change_disk_mode( $_ONAPPVARS[ 'service' ][ 'serverid' ], $_ONAPPVARS[ 'diskid' ], $_ONAPPVARS[ 'mode' ] );
				break;
			default:
				$_ONAPPVARS[ 'error' ] = sprintf( $_LANG[ "onappactionnotfound" ], $action );
				break;
		}
	}
	;

	if( isset( $return ) ) {
		if( is_array( $return ) && isset( $return[ 'error' ] ) ) {
			$_ONAPPVARS[ 'error' ] = $return[ 'error' ];
		}
		else {
			redirect( ONAPP_FILE_NAME . "?page=disks&id=" . $_ONAPPVARS[ 'id' ] );
		}
	}

	clientareadisks();
}

function _action_change_disk_mode( $server_id, $disk_id, $mode ) {
	global $_ONAPPVARS;

	$onapp_config = get_onapp_config( $_ONAPPVARS[ 'service' ][ 'serverid' ] );

	$user = get_onapp_client( $_ONAPPVARS[ 'id' ] );

	$disk = new OnApp_Disk();

	$disk->auth(
		$onapp_config[ "adress" ],
		$user[ "email" ],
		$user[ "password" ]
	);

	$disk->load( $disk_id );

	switch( $mode ) {
		case 'true':
			$disk->enableAutobackup();
			break;
		case 'false':
			$disk->disableAutobackup();
			break;
		default:
			return array( "error" => "Wrong disk autobackup mode" );
			break;
	}
	;

	if( $disk->error ) {
		return array( "error" => $disk->getErrorsAsString( ', ' ) );
	}
	elseif( $disk->_obj->error ) {
		return array( "error" => $disk->_obj->getErrorsAsString( ', ' ) );
	}
	else {
		return $disk;
	}
}

function clientareadisks() {
	global $_ONAPPVARS;

	$onapp_config = get_onapp_config( $_ONAPPVARS[ 'service' ][ 'serverid' ] );

	$disks = new OnApp_Disk();

	$user = get_onapp_client( $_ONAPPVARS[ 'id' ] );

	$disks->auth(
		$onapp_config[ "adress" ],
		$user[ "email" ],
		$user[ "password" ]
	);

	$vms = new OnApp_VirtualMachine();

	$vms->auth(
		$onapp_config[ "adress" ],
		$user[ "email" ],
		$user[ "password" ]
	);

	show_template(
		"onapp/clientareadisks",
		array(
			'vm'                   => $vms->load( $_ONAPPVARS[ 'vm' ]->_id ),
			'disks'                => $disks->getList( $_ONAPPVARS[ 'vm' ]->_id ),
			'id'                   => $_ONAPPVARS[ 'id' ],
			'error'                => isset( $_ONAPPVARS[ 'error' ] ) ? $_ONAPPVARS[ 'error' ] : null,
			'configoptionsupgrade' => $_ONAPPVARS[ 'service' ][ 'configoptionsupgrade' ],
		)
	);
}

/**
 * Show Product Backups
 */
function productbackups() {
	global $_ONAPPVARS;

	foreach( array( 'diskid', 'backupid' ) as $val ) {
		$_ONAPPVARS[ $val ] = get_value( $val );
	}

	$action = $_ONAPPVARS[ 'action' ];

	if( ! is_null( $action ) && $action != "" ) {
		switch( $action ) {
			case 'add':
				$return = _action_backup_add( $_ONAPPVARS[ 'id' ], $_ONAPPVARS[ 'diskid' ] );
				break;
			case 'restore':
				$return = _action_backup_restore( $_ONAPPVARS[ 'id' ], $_ONAPPVARS[ 'backupid' ] );
				break;
			case 'delete':
				$return = _action_backup_delete( $_ONAPPVARS[ 'id' ], $_ONAPPVARS[ 'backupid' ] );
			default:
				$_ONAPPVARS[ 'error' ] = sprintf( $_LANG[ "onappactionnotfound" ], $action );
				break;
		}
	}

	if( isset( $return ) ) {
		if( isset( $return[ 'error' ] ) ) {
			$_ONAPPVARS[ 'error' ] = $return[ 'error' ];
		}
		else {
			redirect( ONAPP_FILE_NAME . "?page=backups&id=" . $_ONAPPVARS[ 'id' ] );
		}
	}

	clientareabackups();
}

/**
 * Show Virtual machine Backups
 */
function clientareabackups() {
	global $_ONAPPVARS;

	$onapp_config = get_onapp_config( $_ONAPPVARS[ 'service' ][ 'serverid' ] );

	$backups = new OnApp_VirtualMachine_Backup();

	$backups->_virtual_machine_id = $_ONAPPVARS[ 'vm' ]->_id;

	$user = get_onapp_client( $_ONAPPVARS[ 'id' ] );

	$backups->auth(
		$onapp_config[ "adress" ],
		$user[ "email" ],
		$user[ "password" ]
	);

	show_template(
		"onapp/clientareabackups",
		array(
			'backups'              => $backups->getList(),
			'id'                   => $_ONAPPVARS[ 'id' ],
			'error'                => isset( $_ONAPPVARS[ 'error' ] ) ? $_ONAPPVARS[ 'error' ] : null,
			'configoptionsupgrade' => $_ONAPPVARS[ 'service' ][ 'configoptionsupgrade' ],
		)
	);
}

/**
 * Action create backup
 */
function _action_backup_add( $id, $diskid ) {
	if( is_null( $diskid ) ) {
		return array( 'error' => 'Disk ID not set' );
	}

	$vm           = get_vm( $id );
	$service      = get_service( $id );
	$onapp_config = get_onapp_config( $service[ 'serverid' ] );

	$backup = new OnApp_VirtualMachine_Backup();

	$backup->_virtual_machine_id = $vm->_id;
	$backup->_disk_id            = $diskid;

	$user = get_onapp_client( $id );

	$backup->auth(
		$onapp_config[ "adress" ],
		$user[ "email" ],
		$user[ "password" ]
	);

	$backup->save();

	if( ! is_null( $backup->_obj->getErrorsAsArray() ) ) {
		return array(
			'error' => $backup->_obj->getErrorsAsString( ', ' ),
		);
	}
	elseif( is_null( $backup->_obj->_id ) ) {
		return array( 'error' => "Can't create Backup" );
	}

	return true;
}

/**
 * Action restore backup
 */
function _action_backup_restore( $id, $backupid ) {
	if( is_null( $backupid ) ) {
		return array( 'error' => 'Backup ID not set' );
	}

	$service      = get_service( $id );
	$onapp_config = get_onapp_config( $service[ 'serverid' ] );

	$backup = new OnApp_VirtualMachine_Backup();

	$backup->_id = $backupid;

	$user = get_onapp_client( $id );

	$backup->auth(
		$onapp_config[ "adress" ],
		$user[ "email" ],
		$user[ "password" ]
	);

	$backup->restore();

	if( ! is_null( $backup->_obj->getErrorsAsArray() ) ) {
		return array(
			'error' => "Can't restore Backup: " . $backup->_obj->getErrorsAsString( ', ' ),
		);
	}
	else {
		return true;
	}
}

/**
 * Action delete backup
 */
function _action_backup_delete( $id, $backupid ) {
	if( is_null( $backupid ) ) {
		return array( 'error' => 'Backup ID not set' );
	}

	$service      = get_service( $id );
	$onapp_config = get_onapp_config( $service[ 'serverid' ] );

	$backup = new OnApp_VirtualMachine_Backup();

	$backup->_id = $backupid;

	$user = get_onapp_client( $id );

	$backup->auth(
		$onapp_config[ "adress" ],
		$user[ "email" ],
		$user[ "password" ]
	);

	$backup->delete();

	if( ! is_null( $backup->_obj->getErrorsAsArray() ) ) {
		return array(
			'error' => "Can't delete Backup: " . $backup->_obj->getErrorsAsString( ', ' ),
		);
	}
	else {
		return true;
	}
}

function productupgrade() {
	global $_ONAPPVARS, $_LANG;

	$onapp_config = get_onapp_config( $_ONAPPVARS[ 'service' ][ 'serverid' ] );

	$service = $_ONAPPVARS[ 'service' ];

	$templates = get_templates(
		$service[ 'serverid' ],
		$service[ "configoption2" ]
	);

	if( ! is_null( $_ONAPPVARS[ 'vm' ]->getErrorsAsArray() ) ) {
		$_ONAPPVARS[ 'error' ] = $_ONAPPVARS[ 'vm' ]->getErrorsAsString( ', ' );

		clientareaproducts();
	}
	elseif( is_null( $_ONAPPVARS[ 'vm' ]->_id ) ) {
		$_ONAPPVARS[ 'error' ] = sprintf(
			$_LANG[ "onappvmnotfoundonserver" ],
			$_ONAPPVARS[ 'service' ][ 'vmid' ],
			$onapp_config[ "adress" ]
		);

		clientareaproducts();
	}
	else {
		show_template(
			"onapp/clientareaupgrade",
			array(
				'templates'      => $templates,
				'virtualmachine' => $_ONAPPVARS[ 'vm' ]->_obj,
				'service'        => $service,
				'configoptions'  => $service[ 'configoptions' ],
				'id'             => $_ONAPPVARS[ 'id' ],
				'error'          => isset( $_ONAPPVARS[ 'error' ] ) ? $_ONAPPVARS[ 'error' ] : null,
			)
		);
	}
}

function get_storage_service( $service_id ) {

	$select_service = "SELECT
        tblhosting.id AS id,
        userid,
        tblproducts.configoption1 AS serverid,
        tblonappservices.vm_id AS vmid,
        tblhosting.password,
        tblhosting.domain AS domain,
        tblhosting.orderid AS orderid,
        tblproducts.name AS product,
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
        0 AS additionalram,
        0 AS additionalcpus,
        0 AS additionalcpushares,
        0 AS additionaldisksize,
        0 AS additionalips,
        0 AS additionalportspead
    FROM
        tblhosting
        LEFT JOIN tblproducts ON tblproducts.id = packageid
        LEFT JOIN tblonappservices ON service_id = tblhosting.id
    WHERE
        servertype = 'onappbackupspace'
        AND tblhosting.id = '$service_id'";

	$service_rows = full_query( $select_service );

	if( ! $service_rows ) {
		return false;
	}
	$service = mysql_fetch_assoc( $service_rows );

	return $service;
}

function clientareastoragedisksizes() {
	global $_ONAPPVARS, $breadcrumbnav, $_LANG;

	$breadcrumbnav .= ' &gt; <a title="' . $_LANG[ "onappstoragedisksize" ] . '" href="' . ONAPP_FILE_NAME . '?page=storagedisksize">' . $_LANG[ "onappstoragedisksize" ] . '</a>';

	$_ONAPPVARS[ 'service' ] = get_storage_service( $_ONAPPVARS[ 'id' ] );

	storagedisksizes();
}

function storagedisksizes() {
	global $user_id;

	$select_services = "SELECT
        tblhosting.id AS id,

        tblonappclients.onapp_user_id,
        tblonappclients.email,
        tblonappclients.password,

        tblproducts.name AS product,
        LOWER(domainstatus) AS domainstatus,
        tblproducts.configoption1 AS serverid,
        tblproducts.configoption2 basespace,
        tblproducts.configoption3,

        CASE optiontype
            WHEN 1 THEN optionssub.sortorder
            WHEN 2 THEN optionssub.sortorder
            WHEN 4 THEN options.qty * optionssub.sortorder
            ELSE 0
        END AS additionalspace,

        tblservers.name      AS servername,
        tblservers.ipaddress AS serveripaddres,
        tblservers.hostname  AS serverhostname,
        tblservers.username  AS serverusername,
        tblservers.password  AS serverpassword,

        optionssub.id AS subid,
        optionssub.optionname,
        options.configid,
        tblproductconfigoptions.optionname AS configoptionname,
        tblproductconfigoptions.optiontype,
        tblproductconfigoptions.qtymaximum AS max,
        tblproductconfigoptions.qtyminimum AS min,
        options.qty,
        optionssub.sortorder,
        options.optionid AS active
    FROM
        tblhosting
        LEFT JOIN tblproducts ON
            tblproducts.id = packageid
        LEFT JOIN tblonappservices ON
            service_id = tblhosting.id
        LEFT JOIN tblonappclients ON
            tblproducts.configoption1 = tblonappclients.server_id AND
            tblhosting.userid = tblonappclients.client_id
        LEFT JOIN tblhostingconfigoptions AS options ON
            relid = tblhosting.id
        LEFT JOIN tblproductconfigoptionssub AS sub
            ON options.configid = sub.configid
            AND options.configid = tblproducts.configoption3
            AND optionid = sub.id
        LEFT JOIN tblproductconfigoptions
            ON tblproductconfigoptions.id = options.configid
        LEFT JOIN tblproductconfigoptionssub AS optionssub
            ON optionssub.configid = tblproductconfigoptions.id AND
            options.configid = tblproducts.configoption3
        LEFT JOIN tblservers ON tblproducts.configoption1 = tblservers.id
    WHERE
        servertype = 'onappbackupspace'
        AND options.optionid = optionssub.id
        AND userid = '$user_id'
    ORDER BY servername, tblhosting.id ASC";

	$services_rows = full_query( $select_services );

	while( $service = mysql_fetch_assoc( $services_rows ) ) {
		$rows[ ] = $service;
	}

	$servers = array();

	if( count( $rows ) ) {
		foreach( $rows as $key => $value ) {
			if( ! isset( $servers[ $value[ 'serverid' ] ] ) ) {
				$servers[ $value[ 'serverid' ] ] = array(
					'services'      => array(),
					'name'          => $rows[ $key ][ 'servername' ],
					'adress'        => $rows[ $key ][ "serveripaddres" ] != "" ?
						'http://' . $rows[ $key ][ "serveripaddres" ] :
						$rows[ $key ][ 'serverhostname' ],
					'username'      => $rows[ $key ][ 'serverusername' ],
					'password'      => decrypt( $rows[ $key ][ 'serverpassword' ] ),
					'onapp_user_id' => $rows[ $key ][ 'onapp_user_id' ],
				);
			}
			;

			$servers[ $value[ 'serverid' ] ][ 'services' ][ ] = $value;
		}
	}
	;

	foreach( $servers as $key => $server ) {
		$limit = new OnApp_ResourceLimit();
		$limit->auth(
			$server[ 'adress' ],
			$server[ 'username' ],
			$server[ 'password' ]
		);
		$limit->load( $server[ 'onapp_user_id' ] );

		$servers[ $key ][ 'storage_disk_size' ] = $limit->_obj->_storage_disk_size ? $limit->_obj->_storage_disk_size : 0;

		$vms = new OnApp_VirtualMachine();
		$vms->auth(
			$server[ 'adress' ],
			$server[ 'username' ],
			$server[ 'password' ]
		);

		$backups_size = 0;

		foreach( $vms->getList( $server[ 'onapp_user_id' ] ) as $vm ) {
			$backups                      = new OnApp_VirtualMachine_Backup();
			$backups->_virtual_machine_id = $vm->_id;

			$backups->auth(
				$server[ 'adress' ],
				$server[ 'username' ],
				$server[ 'password' ]
			);

			foreach( $backups->getList() as $backup ) {
				$backups_size += $backup->_backup_size;
			}
		}
		;

		if( $backups_size > 0 ) {
			$backups_size = sprintf( "%01.2f", $backups_size / 1024 / 1024 );
		}

		$servers[ $value[ 'serverid' ] ][ 'backups_size' ] = $backups_size;
	}
	;

	show_template(
		"onapp/clientareastoragedisksizes",
		array(
			'rows' => $servers,
		)
	);
}
