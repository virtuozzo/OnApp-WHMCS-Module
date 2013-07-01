<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
//error_reporting( E_ALL );
//ini_set( 'display_errors', 1 );
//ini_set('html_errors', 1);
if( ! defined( 'ONAPP_FILE_NAME' ) ) {
	define( 'ONAPP_FILE_NAME', 'onapp.php' );
}

if( ! defined( 'ONAPP_WRAPPER_INIT' ) ) {
	define( 'ONAPP_WRAPPER_INIT', dirname( dirname( __FILE__ ) ) . '/includes/wrapper/OnAppInit.php' );
}

if( file_exists( ONAPP_WRAPPER_INIT ) ) {
	require_once ONAPP_WRAPPER_INIT;
}

require_once dirname( __FILE__ ) . '/lib.php';

load_language();

function onapp_createTables() {
	global $_LANG, $whmcsmysql;

	define ( "CREATE_TABLE_CLIENTS",
	"CREATE TABLE IF NOT EXISTS `tblonappclients` (
	  `server_id` INT(11) NOT NULL,
	  `client_id` INT(11) NOT NULL,
	  `onapp_user_id` INT(11) NOT NULL,
	  `password` TEXT NOT NULL,
	  `email` TEXT NOT NULL,
	  PRIMARY KEY (`server_id`, `client_id`),
	  KEY `client_id` (`client_id`)
	) ENGINE=InnoDB;" );

	define ( "CREATE_TABLE_SERVICES",
	"CREATE TABLE IF NOT EXISTS `tblonappservices` (
	  `service_id` INT(11) NOT NULL,
	  `vm_id` INT(11) NOT NULL,
	  `memory`INT(11) DEFAULT 0 NOT NULL,
	  `cpus` INT(11)  DEFAULT 0 NOT NULL,
	  `cpu_shares` INT(11) DEFAULT 0 NOT NULL,
	  `disk_size` INT(11) DEFAULT 0 NOT NULL,
	  PRIMARY KEY (`service_id`),
	  KEY `service_id` (`service_id`)
	) ENGINE=InnoDB;" );

	define( "CREATE_TABLE_IPS",
	"CREATE TABLE IF NOT EXISTS `tblonappips` (
	  `serviceid` INT(11) NOT NULL,
	  `ipid` INT(11) NOT NULL,
	  `isbase` TINYINT(1) DEFAULT 0 NOT NULL,
	  PRIMARY KEY (`serviceid`, `ipid`),
	  KEY `id` (`serviceid`, `ipid`)
	) ENGINE=InnoDB;" );

	define( "CREATE_TABLE_CRON_DATES",
	"CREATE TABLE IF NOT EXISTS `tblonappcronhostingdates` (
	  `hosting_id` INT(11) NOT NULL,
	  `account_date` DATETIME NOT NULL,
	  UNIQUE (
	`hosting_id`
	))
	 ENGINE=InnoDB;" );

	if( ! full_query( CREATE_TABLE_CLIENTS, $whmcsmysql ) ) {
		return array(
			"error" => sprintf( $_LANG[ "onapperrtablecreate" ], 'onappclients' )
		);
	}
	else {
		if( ! full_query( CREATE_TABLE_SERVICES, $whmcsmysql ) ) {
			return array(
				"error" => sprintf( $_LANG[ "onapperrtablecreate" ], 'onappservices' )
			);
		}
		else {
			if( ! full_query( CREATE_TABLE_IPS, $whmcsmysql ) ) {
				return array(
					"error" => sprintf(
						$_LANG[ "onapperrtablecreate" ],
						'tblonappips' )
				);
			}
			else {
				if( ! full_query( CREATE_TABLE_CRON_DATES, $whmcsmysql ) ) {
					return array(
						"error" => sprintf(
							$_LANG[ "onapperrtablecreate" ],
							'tblonappcronhostingdates' )
					);
				}
			}
		}
	}
	;

// Add VM creation template in to DB

	define( "SELECT_VM_CREATE_TEMPLATE",
	"SELECT * FROM tblemailtemplates WHERE type='product' AND name='Virtual Machine Created';"
	);

	define( "INSERT_VM_CREATE_TEMPLATE",
	"INSERT INTO tblemailtemplates ( type, name, subject, message, plaintext)
		  VALUES ('product', 'Virtual Machine Created', 'Virtual machine has been created', 'Dear {\$client_name},<br/><br/>This is a notice that an virtual machine has been created.', 0 );" );

	if( ! mysql_fetch_assoc( full_query( SELECT_VM_CREATE_TEMPLATE ) ) ) {
		if( ! full_query( INSERT_VM_CREATE_TEMPLATE ) ) {
			return array( "error" => sprintf( $_LANG[ "onapperrtemplatecreate" ], 'virtual machine create' ) );
		}
	}

	define( "SELECT_VM_DELETE_TEMPLATE",
	"SELECT * FROM tblemailtemplates WHERE type='product' AND name='Virtual Machine Deleted';"
	);

	define( "INSERT_VM_DELETE_TEMPLATE",
	"INSERT INTO tblemailtemplates ( type, name, subject, message, plaintext)
		  VALUES ('product', 'Virtual Machine Deleted', 'Virtual machine has been deleted', 'Dear {\$client_name},<br/><br/>This is a notice that an virtual machine has been deleted.', 0 );" );

	if( ! mysql_fetch_assoc( full_query( SELECT_VM_DELETE_TEMPLATE ) ) ) {
		if( ! full_query( INSERT_VM_DELETE_TEMPLATE ) ) {
			return array( "error" => sprintf( $_LANG[ "onapperrtemplatecreate" ], 'virtual machine delete' ) );
		}
	}

	return;
}

function onapp_ConfigOptions() {
	global $packageconfigoption, $_GET, $_POST, $_LANG;

	$serviceid = $_GET[ 'id' ] ? $_GET[ 'id' ] : $_POST[ 'id' ];
	$serviceid = addslashes( $serviceid );

	$configarray = array();

	if( ! file_exists( ONAPP_WRAPPER_INIT ) ) {
		return array(
			sprintf(
				'%s ' . realpath( dirname( __FILE__ ) . '/../../../' ) . "/includes ( %s )",
				$_LANG[ 'onappwrappernotfound' ], $_LANG[ 'onappmakesuredirectoryisaccessible' ]
			) => array()
		);
	}

	$table_result = onapp_createTables();

	if( $table_result[ 'error' ] ) {
		return array(
			sprintf(
				'<font color="red"><b>%s</b></font>',
				$table_result[ 'error' ]
			) => array()
		);
	}

	////////////////////////////
	// BEGIN Load Servers     //

	$sql_servers_result = full_query(
        'SELECT id, name FROM tblservers WHERE type = "onapp" AND disabled = 0 ORDER BY active desc'
	);

	$onapp_servers = array();

	while( $server = mysql_fetch_assoc( $sql_servers_result ) ) {
		$onapp_servers[ $server[ 'id' ] ] = $server[ 'name' ];
	}

	// Error if not found onapp server
	if( ! $onapp_servers ) {
		return array(
			'<font color="red"><b>' . $_LANG[ 'onapperrcantfoundactiveserver' ] . '</b></font>' => array()
		);
	}

	$onapp_server_id = $packageconfigoption[ 1 ] != '' ? $packageconfigoption[ 1 ] : array_shift( array_keys( $onapp_servers ) );

	$js_serverOptions = '';
	foreach( array_keys( $onapp_servers ) as $id_server ) {
		$js_serverOptions .= "    serverOptions[$id_server] = '" . addslashes( $onapp_servers[ $id_server ] ) . "';\n";
	}

	$onapp_config = onapp_Config( $onapp_server_id );

// GET OnApp Instance //
///////////////////////
	$onapp = new OnApp_Factory(
		$onapp_config[ 'adress' ],
		$onapp_config[ 'username' ],
		$onapp_config[ 'password' ]
	);

    if(! $onapp->_apiVersion())
        $onapp_config[ 'error' ] = 'Can\'t get OnApp version for server '. $onapp_config[ 'adress' ];

// END Get OnApp Instance //
///////////////////////////

	if( isset( $onapp_config[ 'error' ] ) ) {
// Error JS Begin
		$javascript = "
<script>
    var serverOptions = new Array();
$js_serverOptions

    serverSelect = $(\"select[name$='packageconfigoption[1]']\");
    serverSelected = serverSelect.val();

    selectHTML = '';
    for ( var option in serverOptions )
            selectHTML += '<option value=\"'+option+'\">'+serverOptions[option]+'</option>';

    serverSelect.html(selectHTML);
    serverSelect.val(serverSelected);

    serverSelect.change( function () {
        form = $(\"form[name$='packagefrm']\");
		form.submit();
    } );
</script>";

		return array(
			'Server' => array(
				'Type'        => 'dropdown',
				'Options'     => implode( ',', array_keys( $onapp_servers ) ),
				'Description' => '',
			),
			'<font color="red"><b>' . $onapp_config[ 'error' ] . '</b></font>' . $javascript => array()
		);
	}
	// END Load Servers       //
	////////////////////////////

	////////////////////////////
	// BEGIN Load Templates   //
	$template = $onapp->factory( 'Template' );

	$templates = $template->getList();

	$template_ids       = array();
	$js_templateOptions = '';
	$created_os         = array();

	if( ! empty( $templates ) ) {
		foreach( $templates as $_template ) {
			$template_ids[ $_template->_id ] = array(
				'label' => $_template->_label
			);

			$os = $_template->_operating_system;
			$os .= empty( $_template->_operating_system_distro ) ?
				'' :
				'_' . $_template->_operating_system_distro;
			if( ! in_array( $os, $created_os ) ) {
				array_push( $created_os, $os );
			}

			$oses[ $_template->_id ]               = $os;
			$js_templateOptions[ $_template->_id ] = htmlspecialchars( addslashes( preg_replace( '/\r\n|\n|\r/', " ", $_template->_label ) ) );
		}
// sorting templates and wrapper workout for Google Chrome and IE
		asort( $js_templateOptions );
		$templatesWrapper        = array();
		$templatesWrapper[ 'k' ] = array_keys( $js_templateOptions );
		$templatesWrapper[ 'v' ] = array_values( $js_templateOptions );
	}
	// END Load Templates     //
	////////////////////////////

	//////////////////////////////
	// BEGIN Load Data Store Zones //
	$option = explode( ",", $packageconfigoption[ 11 ] );

	if( count( $option ) > 1 ) {
		$ds_zone_primary_selected = $option[ 1 ];
	}
	else {
		$ds_zone_primary_selected = 0;
	}

	$option = explode( ",", $packageconfigoption[ 9 ] );

	if( count( $option ) > 1 ) {
		$ds_zone_swap_selected = $option[ 1 ];
	}
	else {
		$ds_zone_swap_selected = 0;
	}

	$dstore_zone = $onapp->factory( 'DataStoreZone' );

	$dstore_zones = $dstore_zone->getList();

	$js_dsOptions = "    dsOptions[0] = '" . $_LANG[ "onappautoselect" ] . "';\n";

	if( ! empty ( $dstore_zones ) ) {
		foreach( $dstore_zones as $_ds ) {
			$js_dsOptions .= "    dsOptions[$_ds->_id] = '" . addslashes( $_ds->_label ) . "';\n";
		}
		;
	}
	;

	// END Load Data Store Zones //
	////////////////////////////

	////////////////////////////
	// BEGIN Load Hypervisors //

	$option = explode( ",", $packageconfigoption[ 4 ] );

	if( count( $option ) > 1 ) {
		$hv_and_zone_selected = $option;
	}
	else {
		$hv_and_zone_selected = 0;
	}

	$hv       = $onapp->factory( 'Hypervisor' );
	$net_join = $onapp->factory( 'Hypervisor_NetworkJoin' );

	$hvs = $hv->getList();

	$hv_ids = array();

	$js_hvOptions    = "    hvOptions[0] = '" . $_LANG[ "onappautoselect" ] . "';\n";
	$js_hvZonesArray = '';
	$hvs_in_hvzones  = array();

	if( ! empty( $hvs ) ) {
		foreach( $hvs as $_hv ) {
			if( $_hv->_online == "true" && $_hv->_hypervisor_group_id ) {
				$hvs_in_hvzones[ $_hv->_hypervisor_group_id ][ ] = $_hv->_id;

// get networks by hypervisor
				$nets_join = $net_join->getList( $_hv->_id );

				if( is_array( $nets_join ) ) {
					foreach( $nets_join as $net ) {
						$nets_by_hv[ $_hv->_id ][ ] = $net->_network_id;
					}
				}

				$hv_ids[ $_hv->_id ] = array(
					'label' => $_hv->_label
				);

				$js_hvOptions .= "    hvOptions[$_hv->_id] = '" . addslashes( $_hv->_label ) . "';\n";
				$js_hvZonesArray .= " hvZonesArray[$_hv->_id] = $_hv->_hypervisor_group_id" . " \n";
			}
			;
		}
		;
	}
	;
	// END Load Hypervisors   //
	///////////////////////////

	////////////////////////////
	// BEGIN Load Hypervisor Zones //

	$hv_zone  = $onapp->factory( 'HypervisorZone' );
	$net_join = $onapp->factory( 'HypervisorZone_NetworkJoin' );

	$hv_zones = $hv_zone->getList();

	if( ! empty( $hv_zones ) ) {
		$js_hvZoneOptions = "    hvZoneOptions[0] = '" . $_LANG[ "onappautoselect" ] . "';\n";

		foreach( $hv_zones as $_hv_zone ) {
			$nets_join = $net_join->getList( $_hv_zone->_id );

// Include not only nets joined directly to hvzone but and to it's ( hvzone's ) hypervisors
			foreach( $hvs_in_hvzones[ $_hv_zone->_id ] as $hvs_ids ) {
				foreach( $nets_by_hv[ $hvs_ids ] as $_net_ ) {
					$nets_by_hvzone[ $_hv_zone->_id ][ ] = $_net_;
				}
			}

			if( is_array( $nets_join ) ) {
				foreach( $nets_join as $net ) {
					$nets_by_hvzone[ $_hv_zone->_id ][ ] = $net->_network_id;
				}
			}

			$js_hvZoneOptions .=
				"      hvZoneOptions[ $_hv_zone->_id ] = '" . addslashes( $_hv_zone->_label ) . "';\n";
		}
	}
	// END Load Hypervisor Zones //
	////////////////////////////

	////////////////////////////
	// BEGIN Primary networks //
	$network = $onapp->factory( 'Network' );

	$networks = $network->getList();

	$network_ids       = array();
	$js_networkOptions = "";

	if( ! empty( $networks ) ) {
		foreach( $networks as $_network ) {
			$network_ids[ $_network->_id ] = array(
				'label' => $_network->_label
			);

			$js_networkOptions .= "    networkOptions[$_network->_id] = '" . addslashes( $_network->_label ) . "';\n";
		}
		;
	}
	;
	// END Primary networks   //
	////////////////////////////

	////////////////////////////
	// BEGIN Load Roles //
	$role = $onapp->factory( 'Role' );

	$roles = $role->getList();

	$role_ids       = array();
	$js_roleOptions = '';

	if( $option = (array)json_decode( htmlspecialchars_decode( $packageconfigoption[ 21 ] ) ) ) {
		$js_rolesSelected       = $option[ 'role_ids' ];
		$js_userGroupSelected   = $option[ 'user_group' ];
		$js_timeZoneSelected    = $option[ 'time_zone' ];
		$js_billingPlanSelected = $option[ 'billing_plan' ];
	}
	else {
		$js_billingPlanSelected = 1;
		$js_rolesSelected       = array( 2 );
		$js_userGroupSelected   = 0;
		$js_timeZoneSelected    = 0;
	}

	if( ! empty ( $roles ) ) {
		foreach( $roles as $_role ) {
			$js_roleOptions .= "    roleOptions[$_role->_id] = '" . addslashes( $_role->_label ) . "';\n";
		}
		;
	}
	;
	// END Load Roles     //
	////////////////////////////

	// BEGIG get Secondary Network Params //
	///////////////////////////////////////
	if( $option = (array)json_decode( htmlspecialchars_decode( $packageconfigoption[ 23 ] ) ) ) {
		$js_SecNetIps               = $option[ 'sec_net_ips' ];
		$js_SecNetIdSelected        = $option[ 'sec_network_id' ];
		$js_SecNetPortSpeedSelected = $option[ 'sec_net_port_speed' ];
		$js_addSecNetIpsSelected    = $option[ 'sec_net_configurable_option_id' ];
	}
	else {
		$js_SecNetIps               = 0;
		$js_SecNetIdSelected        = 0;
		$js_SecNetPortSpeedSelected = 0;
		$js_addSecNetIpsSelected    = 0;
	}

	// END get Secondary Network Params //
//////////////////////////////////////

	////////////////////////////
	// BEGIN Load User Groups //
	$ugroup = $onapp->factory( 'UserGroup' );

	$ugroups = $ugroup->getList();

	$js_ugroupOptions = "    ugroupOptions[0] = '';\n";

	if( ! empty ( $ugroups ) ) {
		foreach( $ugroups as $_group ) {
			$js_ugroupOptions .= "    ugroupOptions[$_group->_id] = '" . addslashes( $_group->_label ) . "';\n";
		}
		;
	}
	;
	// END Load User Groups     //
	////////////////////////////

////////////////////////////////
//// BEGIN Load Billing Plans //
	$bplan = $onapp->factory( 'BillingPlan' );

	$bplans = $bplan->getList();

	$js_bplanOptions = '';

	if( ! empty ( $bplans ) ) {
		foreach( $bplans as $_plan ) {
			$js_bplanOptions .= "    bplanOptions[$_plan->_id] = '" . addslashes( $_plan->_label ) . "';\n";
		}
		;
	}
	;
	// END Load Billing Plans  //
	////////////////////////////

	////////////////////////////
	// BEGIN Config options   //
	$configoptions_query = full_query(
		"SELECT
            configoptions.id AS id,
            configoptions.optionname AS name,
            sub.id AS subid,
            sub.sortorder AS suborder
        FROM
            tblproductconfigoptions AS configoptions,
            tblproductconfiglinks AS configlinks,
            tblproductconfigoptionssub AS sub
        WHERE
            configlinks.gid = configoptions.gid
            AND sub.configid = configoptions.id
            AND configlinks.pid = $serviceid"
	);

	$js_ConfigOptions    = "    configOptions[0] = '" . $_LANG[ "onappselconfoption" ] . "';\n";
	$js_ConfigOptionsSub = "";
	$configoptions       = array();
	$options             = array();

	while( $option = mysql_fetch_assoc( $configoptions_query ) ) {
		$options[ $option[ 'id' ] ][ 'options' ][ $option[ 'suborder' ] ] = $option[ 'subid' ];
		if( ! isset( $options[ $option[ 'id' ] ][ 'name' ] ) ) {
			$options[ $option[ 'id' ] ][ 'name' ] = addslashes( $option[ 'name' ] );
		}
	}
	;

	foreach( $options as $key => $configoption ) {
		$js_ConfigOptions .= "    configOptions[$key] ='" . addslashes( $configoption[ 'name' ] ) . "';\n";
		$js_ConfigOptionsSub .= "    configOptionsSub[$key] = '" . implode( ",", array_keys( $configoption[ 'options' ] ) ) . "';\n";
		$configoptions[ ] = $key;
	}
	;

	$js_addBandwidthSelected = ( $packageconfigoption[ 22 ] ) ? $packageconfigoption[ 22 ] : "0";

	// END Config options     //
	////////////////////////////

	//GET build options
	$option                = explode( ',', $packageconfigoption[ 10 ] );
	$js_requireAutoBuild   = $option[ 0 ] ? $option[ 0 ] : 0;
	$js_requireAutoBackups = $option[ 1 ] ? $option[ 1 ] : 0;

	$js_error = "    var error_msg = ";

	if( count( $hv_ids ) == 0 ) {
		$js_error .= "'<b><font color=\'red\'>" . $_LANG[ 'onapphvnotfound' ] . "</font></b>'";
	}
	else {
		if( count( $template_ids ) == 0 ) {
			$js_error .= "'<b><font color=\'red\'>" . $_LANG[ 'onapposnotfound' ] . "</font></b>'";
		}
		else {
			if( count( $network_ids ) == 0 ) {
				$js_error .= "'<b><font color=\'red\'>" . $_LANG[ 'onappnetnotfound' ] . "</font></b>'";
			}
			else {
				$js_error .= "''";
			}
		}
	}

	$js_localization_array = array(
		'res',
		'netconfig',
		'addres',
		'youwantrefreshtemplates',
		'settemplate',
		'wrongram',
		'wrongcpucores',
		'wrongcpuprior',
		'wrongswap',
		'wrongdisksize',
		'wrongspead',
		'userroles',
		'hvzones',
		'primarydisk',
		'swapdisk',
		'dszone',
		'vmproperties',
		'usergroups',
		'userproperties',
		'timezones',
		'billingplans',
		'requireautobackups',
		'addbandwidth',
		'suspendifbwexceeded',
		'addsecnetips',
		'secnet',
		'secnetconfiguration',
		'secondarynetworkhvzonenotseterror',
		'primaryhavetodiffersecondary',
		'youhavetoselectactivenetwork',
	);

	$js_localization_string = '';

	foreach( $js_localization_array as $string ) {
		if( isset( $_LANG[ 'onapp' . $string ] ) ) {
			$js_localization_string .= "    LANG['onapp$string'] = '" . $_LANG[ 'onapp' . $string ] . "';\n";
		}
	}

	$javascript = "

<script type=\"text/javascript\">
    selectWidth = 280;

    var serverOptions = new Array();
$js_serverOptions
    var savedOptions = " . json_encode( $packageconfigoption ) . ";
    var templateOptions = " . json_encode( $templatesWrapper ) . "
    var OSByTemplateId  = " . json_encode( $oses ) . "
    var OSlist          = " . json_encode( array_unique( $oses ) ) . "
    var hvOptions = [];
$js_hvOptions
    var networkOptions = [];
$js_networkOptions
    var roleOptions = [];
$js_roleOptions
    var configOptions = [];
$js_ConfigOptions
    var configOptionsSub = [];
$js_ConfigOptionsSub
    var hvZonesArray = [];
$js_hvZonesArray
    var hvZoneOptions = [];
$js_hvZoneOptions
    var dsOptions = [];
$js_dsOptions
    var ugroupOptions = [];
$js_ugroupOptions
    var bplanOptions = [];
$js_bplanOptions
    var productAddons = [];

var hvAndZoneSelected   = " . json_encode( $hv_and_zone_selected ) . "
var dsPrimarySelected   = $ds_zone_primary_selected
var dsSwapSelected      = $ds_zone_swap_selected
var rolesSelected       = " . json_encode( $js_rolesSelected ) . "
var userGroupSelected   = $js_userGroupSelected
var timeZoneSelected    = '$js_timeZoneSelected'
var billingPlanSelected = $js_billingPlanSelected
var requireAutoBuild    = '$js_requireAutoBuild'
var requireAutoBackups  = '$js_requireAutoBackups'
var addBwSelected       = '$js_addBandwidthSelected'

var addSecNetworkIPSelected      = '$js_addSecNetIpsSelected'
var SecNetworkIps                = '$js_SecNetIps'
var SecNetworkIdSelected         = '$js_SecNetIdSelected'
var SecNetworkPortSpeedSelected  = '$js_SecNetPortSpeedSelected'

var networksByHypervisorZone     =  " . ( isset( $nets_by_hvzone ) ? json_encode( $nets_by_hvzone ) : '[]' ) . "
var networksByHypervisor         =  " . ( isset( $nets_by_hv ) ? json_encode( $nets_by_hv ) : '[]' ) . "

$js_error;

// Localization
    var LANG = [];
$js_localization_string

</script>
<script type=\"text/javascript\" src=\"../modules/servers/onapp/includes/jquery.multiselects.js\"></script>
<script type=\"text/javascript\" src=\"../modules/servers/onapp/includes/onapp.js\"></script>
<script type=\"text/javascript\" src=\"../modules/servers/onapp/includes/slider.js\"></script>
<script type=\"text/javascript\" src=\"../modules/servers/onapp/includes/tz.js\"></script>
";

	$configarray = array(
		$_LANG[ 'onappiservers' ]  => array(
			'Type'        => 'dropdown',
			'Options'     => implode( ',', array_keys( $onapp_servers ) ),
			'Description' => '',
		),
		$_LANG[ 'onapptemlates' ] => array(
			'Type'        => 'text',
			'Size'        => '5',
			'Description' => count( $template_ids ) != 0 ? '' : $_LANG[ 'onappnotfoundred' ],
		),
		$_LANG[ 'onappram' ] => array(
			'Type'        => 'text',
			'Size'        => '5',
			'Description' => 'MB',
		),
		$_LANG[ 'onapphv' ] => array(
			'Type'        => count( $hv_ids ) != 0 ? 'dropdown' : null,
			'Options'     => count( $hv_ids ) != 0 ? '0,' . implode( ',', array_keys( $hv_ids ) ) : null,
			'Description' => count( $hv_ids ) != 0 ? '' : $_LANG[ "onappnotfoundred" ],
		),
		$_LANG[ 'onappcpucores' ] => array(
			'Type'        => 'text',
			'Size'        => '5',
			'Description' => '',
		),
		$_LANG[ 'onappprimarynet' ] => array(
			'Type'        => count( $network_ids ) != 0 ? 'dropdown' : null,
			'Options'     => count( $network_ids ) != 0 ? implode( ',', array_keys( $network_ids ) ) : null,
			'Description' => count( $network_ids ) != 0 ?  '' : $_LANG[ 'onappnotfoundred' ],
		),
		$_LANG[ "onappcpuprior" ]               => array(
			"Type"        => "text",
			"Size"        => "5",
			"Description" => "%",
		),
		$_LANG[ "onappportspeed" ]              => array(
			"Type"        => "text",
			"Size"        => "5",
			"Description" => "Mbps ( Unlimited if not set )",
		),
		$_LANG[ "onappswapsize" ]               => array(
			"Type"        => "text",
			"Size"        => "5",
			"Description" => "GB",
		),
		$_LANG[ "onappbuildauto" ]              => array(
			"Type"        => "yesno",
			"Description" => $_LANG[ "onappticktobuildauto" ]
		),
		$_LANG[ "onappprivarydisksize" ]        => array(
			"Type"        => "text",
			"Size"        => "5",
			"Description" => "GB",
		),
		$_LANG[ "onappadditionalram" ]          => array(
			"Type"        => "dropdown",
			"Options"     => "0," . implode( ',', $configoptions ),
			"Description" => "",
		),
		$_LANG[ "onappadditionallcores" ]       => array(
			"Type"        => "dropdown",
			"Options"     => "0," . implode( ',', $configoptions ),
			"Description" => "",
		),
		$_LANG[ "onappadditionallcpupriority" ] => array(
			"Type"        => "dropdown",
			"Options"     => "0," . implode( ',', $configoptions ),
			"Description" => "",
		),
		$_LANG[ "onappadditionalldisksize" ]    => array(
			"Type"        => "dropdown",
			"Options"     => "0," . implode( ',', $configoptions ),
			"Description" => "",
		),
		$_LANG[ "onappipaddress" ]              => array(
			"Type"        => "dropdown",
			"Options"     => "0," . implode( ',', $configoptions ),
			"Description" => "",
		),
		$_LANG[ "onappbackup" ]                 => array(
			"Type"        => "dropdown",
			"Options"     => "0," . implode( ',', $configoptions ),
			"Description" => "",
		),
		$_LANG[ "onappincludedips" ]            => array(
			"Type"        => "text",
			"Size"        => "5",
			"Description" => "",
		),
		$_LANG[ "onappaddonresource" ]          => array(
			"Type"        => "dropdown",
			"Options"     => "0," . implode( ',', $configoptions ),
			"Description" => "",
		),
		$_LANG[ "onappadditionallportspeed" ]   => array(
			"Type"        => "dropdown",
			"Options"     => "0," . implode( ',', $configoptions ),
			"Description" => "",
		),
		"&nbsp;"                                => array(
			"Type"        => "text",
			"Description" => "\n$javascript",
		)
	);

	return $configarray;
}

function onapp_CreateAccount( $params ) {
	global $_LANG;

	if( wrapper_check() ) {
		return wrapper_check();
	}

	$status = serviceStatus( $params[ 'serviceid' ] );
	serviceStatus( $params[ 'serviceid' ], 'Active' );

	$service = get_service( $params[ 'serviceid' ] );

	$getvm = get_vm( $params[ 'serviceid' ] );

	serviceStatus( $params[ 'serviceid' ], $status );

	if( isset( $getvm->_id ) ) {
		return $_LANG[ "onappvmexist" ];
	}
	elseif( $params[ 'domain' ] == "" ) {
		return $_LANG[ "onapphostnamenotfound" ];
	}
	elseif( ( $params[ 'configoption2' ] == "" || count( explode( ',', $params[ 'configoption2' ] ) ) != 1 ) && ! isset( $service[ 'os' ] ) ) {
		return $_LANG[ "onapptemplatenotone" ];
	}

	serviceStatus( $params[ 'serviceid' ], 'Active' );

	$vm = create_vm(
		$params[ 'accountid' ],
		$params[ 'domain' ],
		isset( $service[ 'os' ] ) ? $service[ 'os' ] : $params[ 'configoption2' ]
	);

	if( ! is_null( $vm->getErrorsAsArray() ) ) {
		return $_LANG[ "onappcantcreatevm" ] . ":" . $vm->getErrorsAsString( ' ' );
	}
	elseif( ! is_null( $vm->_obj->getErrorsAsArray() ) ) {
		return $_LANG[ "onappcantcreatevm" ] . ":" . $vm->_obj->getErrorsAsString( ' ' );
	}

// create secondary network interface if needed
	$options = (array)json_decode( htmlspecialchars_decode( $service[ 'configoption23' ] ) );
	$hv_info = explode( ',', $service[ 'configoption4' ] );

	if( $options && count( $hv_info ) > 1 ) {
		$hvzoneid = $hv_info[ 1 ];
		$hvid     = $hvid = $hv_info[ 0 ] ? $hv_info[ 0 ] : $vm->_obj->_hypervisor_id;

		if( ( $hvzoneid && is_numeric( $hvzoneid ) ) && ( $hvid && is_numeric( $hvid ) ) ) {
			_add_sec_network_interface( $vm->_obj->_id, array(
				$hvzoneid,
				$hvid
			), $service, $options[ 'sec_network_id' ], $options[ 'sec_net_port_speed' ], 'hv_hvzone' );
		}
		elseif( $hvzoneid && is_numeric( $hvzoneid ) ) {
			_add_sec_network_interface( $vm->_obj->_id, $hvzoneid, $service, $options[ 'sec_network_id' ], $options[ 'sec_net_port_speed' ], 'hvzone' );
		}
		elseif( $hvid && is_numeric( $hvid ) ) {
			_add_sec_network_interface( $vm->_obj->_id, $hvid, $service, $options[ 'sec_network_id' ], $options[ 'sec_net_port_speed' ], 'hv' );
		}
	}

	_ips_resolve_all( $params[ 'accountid' ] );

	serviceStatus( $params[ 'serviceid' ], $status );

	if( ! is_null( $vm->getErrorsAsArray() ) ) {
		return $_LANG[ "onappcantcreatevm" ] . ":" . $vm->getErrorsAsString( ' ' );
	}
	elseif( ! is_null( $vm->_obj->getErrorsAsArray() ) ) {
		return $_LANG[ "onappcantcreatevm" ] . ":" . $vm->_obj->getErrorsAsString( ' ' );
	}

	return 'success';
}

function onapp_TerminateAccount( $params ) {
	global $_LANG;

	if( wrapper_check() ) {
		return wrapper_check();
	}

	$status = serviceStatus( $params[ 'serviceid' ] );
	serviceStatus( $params[ 'serviceid' ], 'Active' );

	$getvm = get_vm( $params[ 'serviceid' ] );

	if( ! is_null( $getvm->_id ) ) {
		$vm = delete_vm( $params[ 'serviceid' ] );

		serviceStatus( $params[ 'serviceid' ], $status );

		if( ! is_null( $vm->getErrorsAsArray() ) ) {
			return $_LANG[ "onappcantdeletevm" ] . ": " . $vm->getErrorsAsString( ', ' );
		}
		elseif( ! is_null( $vm->_obj->getErrorsAsArray() ) ) {
			return $_LANG[ "onappcantdeletevm" ] . ": " . $vm->_obj->getErrorsAsString( ', ' );
		}
	}
	else {
		$delete_onapp_user = delete_onapp_service( $params[ 'serviceid' ] );
		if( $delete_onapp_user != 'success' ) {
			return $delete_onapp_user;
		}
	}

	return 'success';
}

function onapp_SuspendAccount( $params ) {
	global $_LANG;

	if( wrapper_check() ) {
		return wrapper_check();
	}

	$vm = get_vm( $params[ 'serviceid' ] );

	if( $vm->_obj->_id && ! $vm->_obj->_suspended ) {
		$vm->_obj->auth(
			( $params[ 'serverip' ] ) ? $params[ 'serverip' ] : $params[ 'serverhostname' ],
			$params[ 'serverusername' ],
			$params[ 'serverpassword' ]
		);
		$vm->_obj->suspend();

		if( ! is_null( $vm->getErrorsAsArray() ) ) {
			return $_LANG[ "onappcantsuspendvm" ] . ": " . $vm->getErrorsAsString( ', ' );
		}
		elseif( ! is_null( $vm->_obj->getErrorsAsArray() ) ) {
			return $_LANG[ "onappcantsuspendvm" ] . ": " . $vm->_obj->getErrorsAsString( ', ' );
		}
	}
	else {
		return $_LANG[ 'onappvmalreadysuspended' ];
	}

	return 'success';
}

function onapp_UnsuspendAccount( $params ) {
	global $_LANG;

	if( wrapper_check() ) {
		return wrapper_check();
	}

	$status = serviceStatus( $params[ 'serviceid' ] );
	serviceStatus( $params[ 'serviceid' ], 'Active' );

	$vm = get_vm( $params[ 'serviceid' ] );

	if( $vm->_obj->_id && $vm->_obj->_suspended ) {
		$vm->_obj->auth(
			( $params[ 'serverip' ] ) ? $params[ 'serverip' ] : $params[ 'serverhostname' ],
			$params[ 'serverusername' ],
			$params[ 'serverpassword' ]
		);
		$vm->_obj->suspend();

		if( ! is_null( $vm->getErrorsAsArray() ) ) {
			return $_LANG[ "onappcantunsuspendvm" ] . ": " . $vm->getErrorsAsString( ', ' );
		}
		elseif( ! is_null( $vm->_obj->getErrorsAsArray() ) ) {
			return $_LANG[ "onappcantunsuspendvm" ] . ": " . $vm->_obj->getErrorsAsString( ', ' );
		}
	}
	else {
		return $_LANG[ 'onappvmalreadyactive' ];
	}

	serviceStatus( $params[ 'serviceid' ], 'Suspended' );

	return 'success';
}

function onapp_ClientArea( $params ) {
	global $_LANG;

	if( ! file_exists( dirname( $_SERVER[ 'SCRIPT_FILENAME' ] ) . '/includes/wrapper/OnAppInit.php' ) ) {
		return
			sprintf(
				"%s ",
				$_LANG[ 'onapponmaintenance' ]
			);
	}

	$service = get_service( $params[ 'serviceid' ] );

	if( ! is_null( $service[ "vmid" ] ) ) {
		return '<a href="' . ONAPP_FILE_NAME . '?page=productdetails&id=' . $params[ 'serviceid' ] . '">' . $_LANG[ "onappvmsettings" ] . '</a>';
	}
	else {
		return '<a href="' . ONAPP_FILE_NAME . '?page=productdetails&id=' . $params[ 'serviceid' ] . '">' . $_LANG[ "onappvmcreate" ] . '</a>';
	}
}

function onapp_UsageUpdate( $params ) {
	global $_LANG, $CONFIG;

	error_reporting( E_ERROR );
	ini_set( "display_errors", 1 );

//    date_default_timezone_set('UTC');
	$serverid = $params[ 'serverid' ];

	$query = "
        SELECT
            tblservers.id,
            tblservers.password,
            tblservers.hostname,
            tblservers.ipaddress,
            tblservers.username,
            tblhosting.regdate,
            tblhosting.id AS hosting_id,
            tblhosting.bwusage,
            tblhosting.domain,
            tblhosting.bwlimit,
            tblproducts.servertype,
            tblhosting.lastupdate,
            tblhosting.nextinvoicedate,
            tblhosting.paymentmethod,
            tblonappservices.vm_id,
            tblproducts.overagesbwlimit AS bwlimit,
            tblproducts.overagesdisklimit AS disklimit,
            tblproducts.overagesenabled AS enabled,
            tblproducts.configoption10 AS configoption10,
            tblproducts.configoption22 AS bandwidthconfigoption,
            tblproducts.name AS packagename,
            tblproducts.overagesbwprice,
            tblproducts.tax,
            tblhostingconfigoptions.optionid,
            tblproductconfigoptionssub.sortorder AS additional_bandwidth,
            tblclients.id AS clientid,
            tblclients.taxexempt,
            tblclients.state,
            tblclients.country,
            tblcurrencies.prefix,
            tblcurrencies.code,
            tblcurrencies.rate,
            tblonappcronhostingdates.account_date
        FROM
            tblservers

        LEFT JOIN
            tblhosting ON tblhosting.server = tblservers.id
        LEFT JOIN
            tblproducts ON tblhosting.packageid = tblproducts.id
        LEFT JOIN
            tblonappservices ON tblhosting.id = tblonappservices.service_id
        LEFT JOIN
            tblhostingconfigoptions
            ON tblhostingconfigoptions.relid = tblhosting.id
            AND tblhostingconfigoptions.configid = tblproducts.configoption22
        LEFT JOIN
            tblproductconfigoptionssub
            ON
            tblhostingconfigoptions.optionid = tblproductconfigoptionssub.id
        LEFT JOIN
            tblclients ON tblhosting.userid = tblclients.id
        LEFT JOIN
            tblcurrencies ON tblcurrencies.id = tblclients.currency
        LEFT JOIN
            tblonappcronhostingdates ON tblonappcronhostingdates.hosting_id = tblhosting.id

        WHERE
           tblservers.id = $serverid AND
           tblproducts.servertype = 'onapp' AND
           tblproducts.overagesenabled = 1 AND
           tblonappservices.vm_id != ''
    ";

	$result = full_query( $query );

	if( ! $result || mysql_num_rows( $result ) < 1 ) {
		return;
	}

	$duedate = date( 'Ymd', ( time() + $GLOBALS[ 'CONFIG' ][ 'CreateInvoiceDaysBefore' ] * 86400 ) );
	$today   = date( 'Y-m-d H:i:s' );
	$enddate = $today;

	$i = 0;

	while( $products = mysql_fetch_assoc( $result ) ) {

		if( $products[ 'account_date' ] ) {
			$invoicedate = date( 'Y-m-d', strtotime( $products[ 'account_date' ] ) + ( 31 * 24 * 60 * 60 ) );
			$startdate   = $products[ 'account_date' ];
		}
		else {
			$invoicedate = getAccountDate( $products[ 'regdate' ] );
			$time        = strtotime( $invoicedate ) - 2678400;
			$startdate   = date( 'Y-m-d H:00:00', $time );
		}

		$url = ( $products[ 'hostname' ] ) ? $products[ 'hostname' ] : $products[ 'ipaddress' ];

		if( strpos( $url, 'http' ) === false ) {
			$url = 'http://' . $url;
		}

		$onapp = new OnApp_Factory(
			$url,
			$products[ 'username' ],
			decrypt( $products[ 'password' ] )
		);

		if( $onapp->getErrorsAsArray() ) {
// Debug
			echo ( '<b>Get OnApp Version Permission Error: </b>' . $onapp->getErrorsAsString( ', ' ) ) . '. Skipping' . PHP_EOL;
			continue;
		}

		$network_interface = $onapp->factory( 'VirtualMachine_NetworkInterface' );

		if( ! $products[ 'vm_id' ] ) {
// Debug
			echo 'virtual_machine_id is empty. Skipping' . PHP_EOL;
			continue;
		}
		$network_interfaces = $network_interface->getList( $products[ 'vm_id' ] );

		if( $network_interface->getErrorsAsArray() ) {
// Debug
			echo ( '<b>Network Interface Get List Error : </b>' . $network_interface->getErrorsAsString( ', ' ) ) . '. Skipping' . PHP_EOL;
			continue;
		}

		$usage = $onapp->factory( 'VirtualMachine_NetworkInterface_Usage', true );

		$url_args = array(
			'period[startdate]' => $startdate,
			'period[enddate]'   => $enddate,
			//        'period[use_local_time]' => '1'
		);

		foreach( $network_interfaces as $interface ) {
			$usage_stats[ $i ][ $interface->_id ] = $usage->getList( $interface->_virtual_machine_id, $interface->_id, $url_args );
		}

		$traffic = 0;

		foreach( $usage_stats[ $i ] as $interface ) {
			foreach( $interface as $bandwidth ) {
				$traffic += $bandwidth->_data_sent;
				$traffic += $bandwidth->_data_received;
			}
		}

		$traffic = $traffic / 1024;

// Count bandwidth limit + upgrades if needed
		$bandwidth_limit = (
			$products[ 'optionid' ] && $products[ 'additional_bandwidth' ]
		)
			? $products[ 'bwlimit' ] + $products[ 'additional_bandwidth' ]
			: $products[ 'bwlimit' ];

		if( date( 'Y-m-d' ) == $invoicedate ) {
// debug
			echo 'Payment Day' . PHP_EOL;

			if( $traffic > $bandwidth_limit && ! $params[ 'extracall' ] ) {
// debug
				echo 'Called by the main cron' . PHP_EOL;
				echo 'Bandwidth Limit have been exceeded' . PHP_EOL;
				echo 'Update cron dates' . PHP_EOL;

				$query = "REPLACE INTO
                              tblonappcronhostingdates
                              ( hosting_id, account_date )
                          VALUES ( $products[hosting_id], '" . $enddate . "'  )
                ";

				$result = full_query( $query );
				if( ! $result ) {
// debug
					echo 'cron date REPLACE error ' . mysql_error() . PHP_EOL;
				}

/// Generating Invoice ///
/////////////////////////

// debug
				echo 'Generating Invoice' . PHP_EOL;

				$sql = 'SELECT username FROM tbladmins LIMIT 1';

				$res = full_query( $sql );

				$admin = mysql_fetch_assoc( $res );

				$taxed = empty( $products[ 'taxexempt' ] ) && $CONFIG[ 'TaxEnabled' ];

				if( $taxed ) {
// debug
					echo 'taxed invoice' . PHP_EOL;
					$taxrate = getTaxRate( 1, $products[ 'state' ], $products[ 'country' ] );
					$taxrate = $taxrate[ 'rate' ];
				}
				else {
					$taxrate = '';
				}

				$amount = round( ( ( $traffic - $bandwidth_limit ) * $products[ 'overagesbwprice' ] ) * $products[ 'rate' ], 2 );

				$description = $products[ 'packagename' ] . ' - ' . $products[ 'domain' ] . ' ( ' . $startdate . ' / ' . $enddate . ' )' . PHP_EOL .
					$_LANG[ 'onappbwusage' ] . ' - ' . $traffic . ' MB' . PHP_EOL .
					$_LANG[ 'onappbwlimit' ] . ' - ' . $bandwidth_limit . ' MB' . PHP_EOL .
					$_LANG[ 'onappbwoverages' ] . ' - ' . ( $traffic - $bandwidth_limit ) . ' MB' . PHP_EOL .
					$_LANG[ 'onapppriceformbbwoverages' ] . ' - ' . $products[ 'prefix' ] . round( $products[ 'rate' ] * $products[ 'overagesbwprice' ], 2 ) . ' ' . $products[ 'code' ] . PHP_EOL;

				$data = array(
					'userid'           => $products[ 'clientid' ],
					'date'             => $today,
					'duedate'          => $duedate,
					'paymentmethod'    => $products[ 'paymentmethod' ],
					'taxrate'          => $taxrate,
					'sendinvoice'      => true,
					'itemdescription1' => $description,
					'itemamount1'      => $amount,
					'itemtaxed1'       => $taxed
				);

// debug
				print( '<pre>' );
				print_r( $data );
				echo PHP_EOL;

				$result = localAPI( 'CreateInvoice', $data, $admin );

				if( $result[ 'result' ] != 'success' ) {
// debug
					echo 'Following error occurred: ' . $result[ 'result' ] . PHP_EOL;
				}

// Generating Invoice End //
///////////////////////////
// debug
				echo 'Reset bwusage to 0' . PHP_EOL;
				$traffic = 0;
			}
			elseif( ! $params[ 'extracall' ] && $traffic <= $bandwidth_limit ) {
// debug
				echo 'Called by the main cron' . PHP_EOL;
				echo 'Bandwidth Limit have not been exceeded' . PHP_EOL;
				echo 'Update cron dates' . PHP_EOL;
				echo 'Reset bwusage to 0 no invoice needed' . PHP_EOL;
				$traffic = 0;

				$query = "REPLACE INTO
                              tblonappcronhostingdates
                              ( hosting_id, account_date )
                          VALUES ( $products[hosting_id], '" . $enddate . "'  )
                ";

				$result = full_query( $query );

				if( ! $result ) {
// debug
					echo 'cron date REPLACE error ' . mysql_error() . PHP_EOL;
				}
			}
			else {
// debug
				echo 'Called not by the main cron, but by
                    bandwidth statistics cron: then neither updates account
                    dates nor creates invoices, only collects bandwidth statistics' . PHP_EOL;
			}
		}

		$results[ ] = array(
			'bwusage'   => $traffic,
			'disklimit' => $products[ 'disklimit' ],
			'bwlimit'   => $bandwidth_limit,
			'domain'    => $products[ 'domain' ],
		);

/// Debug block ///
//////////////////

		print( '<pre>' );
		print_r( $products );
		echo PHP_EOL;
		echo 'today  => ' . $today . PHP_EOL;
		echo 'regdate  => ' . $products[ 'regdate' ] . PHP_EOL;
		echo 'invoicedate  => ' . $invoicedate . PHP_EOL;
		echo 'startdate  => ' . $startdate . PHP_EOL;
		echo 'enddate  => ' . $enddate . PHP_EOL;
		echo 'bwlimit  (' . $products[ 'bwlimit' ] . ') + ';
		echo 'additional bwlimit (' . $products[ 'additional_bandwidth' ] . ') = ';
		echo $bandwidth_limit . PHP_EOL . PHP_EOL;
		echo 'Updating bwusage => ' . PHP_EOL;
		print( '<pre>' );
		print_r( $results );
		echo '************************************************' . PHP_EOL . PHP_EOL;

/// Debug block END ///
//////////////////////

		$i ++;
	}

// Updating Usage Overages
	foreach( $results as $domain => $values ) {
		update_query( "tblhosting", array(
			"disklimit"  => $values[ 'disklimit' ],
			"bwusage"    => $values[ 'bwusage' ],
			"bwlimit"    => $values[ 'bwlimit' ],
			"lastupdate" => $today,
		), array( "server" => $serverid, "domain" => $values[ 'domain' ] ) );
	}
}
