<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
//error_reporting( E_ALL );
//ini_set( 'display_errors', 1 );
if ( ! defined('ONAPP_FILE_NAME') )
    define("ONAPP_FILE_NAME", "onapp.php");

if ( ! defined('ONAPP_WRAPPER_INIT') )
    define('ONAPP_WRAPPER_INIT', dirname(__FILE__).'/../../../includes/wrapper/OnAppInit.php');

if ( file_exists( ONAPP_WRAPPER_INIT ) )
    require_once ONAPP_WRAPPER_INIT;

require_once dirname(__FILE__).'/lib.php';

load_language();

function onapp_createTables() {
    global $_LANG, $whmcsmysql;

    define ("CREATE_TABLE_CLIENTS",
"CREATE TABLE IF NOT EXISTS `tblonappclients` (
  `server_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `onapp_user_id` int(11) NOT NULL,
  `password` text NOT NULL,
  `email` text NOT NULL,
  PRIMARY KEY (`server_id`, `client_id`),
  KEY `client_id` (`client_id`)
) ENGINE=InnoDB;");

    define ("CREATE_TABLE_SERVICES",
"CREATE TABLE IF NOT EXISTS `tblonappservices` (
  `service_id` int(11) NOT NULL,
  `vm_id` int(11) NOT NULL,
  `memory`int(11) DEFAULT 0 NOT NULL,
  `cpus` int(11)  DEFAULT 0 NOT NULL,
  `cpu_shares` int(11) DEFAULT 0 NOT NULL,
  `disk_size` int(11) DEFAULT 0 NOT NULL,
  PRIMARY KEY (`service_id`),
  KEY `service_id` (`service_id`)
) ENGINE=InnoDB;");

    define("CREATE_TABLE_IPS",
"CREATE TABLE IF NOT EXISTS `tblonappips` (
  `serviceid` int(11) NOT NULL,
  `ipid` int(11) NOT NULL,
  `isbase` TINYINT(1) DEFAULT 0 NOT NULL,
  PRIMARY KEY (`serviceid`, `ipid`),
  KEY `id` (`serviceid`, `ipid`)
) ENGINE=InnoDB;");

    if ( ! full_query( CREATE_TABLE_CLIENTS, $whmcsmysql ) ) {
        return array(
            "error" => sprintf($_LANG["onapperrtablecreate"], 'onappclients')
        );
    } else if ( ! full_query( CREATE_TABLE_SERVICES, $whmcsmysql ) ) {
        return array(
            "error" => sprintf($_LANG["onapperrtablecreate"], 'onappservices')
        );
    } else if ( ! full_query( CREATE_TABLE_IPS, $whmcsmysql ) ) {
        return array(
            "error" => sprintf(
                $_LANG["onapperrtablecreate"],
                'tblonappips'));
    };

// Add VM creation template in to DB

    define("SELECT_VM_CREATE_TEMPLATE",
      "SELECT * FROM tblemailtemplates WHERE type='product' AND name='Virtual Machine Created';"
    );

    define("INSERT_VM_CREATE_TEMPLATE",
      "INSERT INTO tblemailtemplates ( type, name, subject, message, plaintext)
          VALUES ('product', 'Virtual Machine Created', 'Virtual machine has been created', 'Dear {\$client_name},<br/><br/>This is a notice that an virtual machine has been created.', 0 );");

    if ( ! mysql_fetch_assoc( full_query(SELECT_VM_CREATE_TEMPLATE) ) )
        if( ! full_query( INSERT_VM_CREATE_TEMPLATE ) )
            return array( "error" => sprintf($_LANG["onapperrtemplatecreate"], 'virtual machine create') );

    define("SELECT_VM_DELETE_TEMPLATE",
      "SELECT * FROM tblemailtemplates WHERE type='product' AND name='Virtual Machine Deleted';"
    );

    define("INSERT_VM_DELETE_TEMPLATE",
      "INSERT INTO tblemailtemplates ( type, name, subject, message, plaintext)
          VALUES ('product', 'Virtual Machine Deleted', 'Virtual machine has been deleted', 'Dear {\$client_name},<br/><br/>This is a notice that an virtual machine has been deleted.', 0 );");

    if ( ! mysql_fetch_assoc( full_query(SELECT_VM_DELETE_TEMPLATE) ) )
        if( ! full_query( INSERT_VM_DELETE_TEMPLATE ) )
            return array( "error" => sprintf($_LANG["onapperrtemplatecreate"], 'virtual machine delete') );

    return;
}

function onapp_ConfigOptions() {
    global $packageconfigoption, $_GET, $_POST, $_LANG;

    $serviceid = $_GET["id"] ? $_GET["id"] : $_POST["id"];
    $serviceid = addslashes($serviceid);

    $configarray = array();

    if ( ! file_exists( ONAPP_WRAPPER_INIT ) ) {
        return array(
            sprintf(
                "%s " . realpath( dirname(__FILE__).'/../../../' ) . "/includes",
                $_LANG['onappwrappernotfound']
            ) => array()
        );
    }

    $table_result = onapp_createTables();

    if ( $table_result["error"] )
        return array(
            sprintf(
                "<font color='red'><b>%s</b></font>",
                $table_result["error"]
            ) => array()
        );

  ////////////////////////////
  // BEGIN Load Servers     //

    $sql_servers_result = full_query(
        "SELECT id, name FROM tblservers WHERE type = 'onapp'"
    );

    $onapp_servers = array();

    while ( $server = mysql_fetch_assoc($sql_servers_result)) {
        $onapp_servers[$server['id']] = $server['name'];
    };

    // Error if not found onapp server
    if ( ! $onapp_servers )
        return array(
            "<font color='red'><b>" . $_LANG["onapperrcantfoundactiveserver"] . "</b></font>" => array()
        );

    $onapp_server_id = $packageconfigoption[1] != "" ? $packageconfigoption[1] : array_shift(array_keys($onapp_servers));

    $js_serverOptions = "";
    foreach ( array_keys($onapp_servers) as $id_server )
        $js_serverOptions .= "    serverOptions[$id_server] = '".addslashes($onapp_servers[$id_server])."';\n";

    $onapp_config = onapp_Config( $onapp_server_id );

    if ( isset($onapp_config["error"]) ) {

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
            "Server" => array(
                "Type" => "dropdown",
                "Options" => implode( ',', array_keys($onapp_servers) ),
                "Description" => "",
            ),
            "<font color='red'><b>".$onapp_config['error']."</b></font>" . $javascript => array()
        );
    };

  // END Load Servers       //
  ////////////////////////////

  ////////////////////////////
  // BEGIN Load Templates   //
    $template = new ONAPP_Template();

    $template->auth(
        $onapp_config["adress"],
        $onapp_config['username'],
        $onapp_config['password']
    );

    $templates = $template->getList();

    $template_ids = array();
    $js_templateOptions = "";
    $created_os = array();

    if (!empty($templates)) {
        foreach ($templates as $_template) {
            $template_ids[$_template->_id] = array(
                'label' => $_template->_label
            );

            $os = $_template->_operating_system;
            $os .= empty($_template->_operating_system_distro) ?
                '' :
                '_' . $_template->_operating_system_distro;
            if (!in_array($os, $created_os)){
                array_push($created_os, $os);
                $js_templateOptions .= "    templateOptions['$os'] = new Array();\n";
            }
            $js_templateOptions .= "    templateOptions['$os'][$_template->_id] = '".addslashes( preg_replace('/\r\n|\n|\r/', " ", $_template->_label ) )."';\n";
        };
    };
  // END Load Templates     //
  ////////////////////////////

    //////////////////////////////
  // BEGIN Load Data Store Zones //
    $option = explode( ",", $packageconfigoption[11] );

    if ( count($option) > 1 ) {
        $ds_zone_primary_selected = $option[1];
    }
    else $ds_zone_primary_selected = 0;

    $option = explode( ",", $packageconfigoption[9] );

    if ( count($option) > 1 ) {
        $ds_zone_swap_selected = $option[1];
    }
    else $ds_zone_swap_selected = 0;

    $dstore_zone = new ONAPP_DataStoreZone();

    $dstore_zone->auth(
        $onapp_config["adress"],
        $onapp_config['username'],
        $onapp_config['password']
    );

    $dstore_zones = $dstore_zone->getList(); 

    $js_dsOptions = "    dsOptions[0] = '" . $_LANG["onappautoselect"] . "';\n";

    if ( ! empty ( $dstore_zones ) ) {
        foreach ( $dstore_zones as $_ds ) {
            $js_dsOptions .= "    dsOptions[$_ds->_id] = '".addslashes($_ds->_label)."';\n";
        };
    };
  // END Load Data Store Zones //
  ////////////////////////////

 ////////////////////////////
  // BEGIN Load Hypervisor Zones // '
    $hv_zone = new ONAPP_HypervisorZone();

    $hv_zone->auth(
        $onapp_config["adress"],
        $onapp_config['username'],
        $onapp_config['password']
    );

    $hv_zones = $hv_zone->getList();

    $hv_zone_ids = array();

    if ( ! empty( $hv_zones ) ) {
        $js_hvZoneOptions = "    hvZoneOptions[0] = '" . $_LANG["onappautoselect"] . "';\n";

        foreach ( $hv_zones as $_hv_zone ) {
            $js_hvZoneOptions .=
                "      hvZoneOptions[ $_hv_zone->_id ] = '".addslashes( $_hv_zone->_label )."';\n";
        }
    }
  // END Load Hypervisor Zones //
  ////////////////////////////
  
  ////////////////////////////
  // BEGIN Load Hypervisors //

    $option = explode( ",", $packageconfigoption[4] );
    
    if ( count($option) > 1 ) {
        $hv_and_zone_selected = $option;
    }
    else {
        $hv_and_zone_selected = 0;
    }

    $hv = new ONAPP_Hypervisor();

    $hv->auth(
        $onapp_config["adress"],
        $onapp_config['username'],
        $onapp_config['password']
    );

    $hvs = $hv->getList();

    $hv_ids = array();

    $js_hvOptions = "    hvOptions[0] = '" . $_LANG["onappautoselect"] . "';\n";
    $js_hvZonesArray = '';

    if (!empty($hvs)) {
        foreach ($hvs as $_hv) {
            if ( $_hv->_online == "true" && $_hv->_hypervisor_group_id ) {
                $hv_ids[$_hv->_id] = array(
                    'label' => $_hv->_label
                );

                $js_hvOptions .= "    hvOptions[$_hv->_id] = '".addslashes($_hv->_label)."';\n";
                $js_hvZonesArray .= " hvZonesArray[$_hv->_id] = $_hv->_hypervisor_group_id" . " \n";
            };
        };
    };
  // END Load Hypervisors   //
  ////////////////////////////

  ////////////////////////////
  // BEGIN Primary networks //
    $network = new ONAPP_Network();

    $network->auth(
        $onapp_config["adress"],
        $onapp_config['username'],
        $onapp_config['password']
    );

    $networks = $network->getList();

    $network_ids = array();
    $js_networkOptions = "";

    if (!empty($networks)) {
        foreach ($networks as $_network) {
            $network_ids[$_network->_id] = array(
                'label' => $_network->_label
            );

            $js_networkOptions .= "    networkOptions[$_network->_id] = '".addslashes($_network->_label)."';\n";
        };
    };
  // END Primary networks   //
  ////////////////////////////
  //
   ////////////////////////////
  // BEGIN Load Roles //
    $role = new ONAPP_Role();

    $role->auth(
        $onapp_config["adress"],
        $onapp_config['username'],
        $onapp_config['password']
    );

    $roles = $role->getList();

    $role_ids = array();
    $js_roleOptions = '';

    if ( $option = (array)json_decode( htmlspecialchars_decode ( $packageconfigoption[21] ) ) ) {
        $js_rolesSelected = $option['role_ids'];
        $js_userGroupSelected = $option['user_group'];
        $js_timeZoneSelected = $option['time_zone'];
        $js_billingPlanSelected = $option['billing_plan'];
    }
    else {
        $js_billingPlanSelected = 1;
        $js_rolesSelected     = array(2);
        $js_userGroupSelected = 0;
        $js_timeZoneSelected  = 0;
    }

    if ( ! empty ( $roles ) ) {
        foreach ( $roles as $_role) {
            $js_roleOptions .= "    roleOptions[$_role->_id] = '".addslashes($_role->_label)."';\n";
        };
    };
  // END Load Roles     //
  ////////////////////////////

   ////////////////////////////
  // BEGIN Load User Groups //
    $ugroup = new ONAPP_UserGroup();

    $ugroup->auth(
        $onapp_config["adress"],
        $onapp_config['username'],
        $onapp_config['password']
    );

    $ugroups = $ugroup->getList();

    $js_ugroupOptions = "    ugroupOptions[0] = '';\n";

    if ( ! empty ( $ugroups ) ) {
        foreach ( $ugroups as $_group ) {
            $js_ugroupOptions .= "    ugroupOptions[$_group->_id] = '".addslashes($_group->_label)."';\n";
        };
    };
  // END Load User Groups     //
  ////////////////////////////

////////////////////////////////
//// BEGIN Load Billing Plans //
    $bplan = new ONAPP_BillingPlan();

    $bplan->auth(
        $onapp_config["adress"],
        $onapp_config['username'],
        $onapp_config['password']
    );

    $bplans = $bplan->getList();

    $js_bplanOptions = '';

    if ( ! empty ( $bplans ) ) {
        foreach ( $bplans as $_plan ) {
            $js_bplanOptions .= "    bplanOptions[$_plan->_id] = '".addslashes($_plan->_label)."';\n";
        };
    };
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

    $js_ConfigOptions = "    configOptions[0] = '" . $_LANG["onappselconfoption"] . "';\n";
    $js_ConfigOptionsSub = "";
    $configoptions = array();
    $options = array();

    while($option = mysql_fetch_assoc($configoptions_query)){
       $options[$option['id']]['options'][$option['suborder']] = $option['subid'];
       if (! isset($options[$option['id']]['name']))
           $options[$option['id']]['name'] = addslashes($option['name']);
    };

    foreach ( $options as $key => $configoption) {
        $js_ConfigOptions .= "    configOptions[$key] ='".addslashes($configoption['name'])."';\n";
        $js_ConfigOptionsSub .= "    configOptionsSub[$key] = '".implode(",", array_keys($configoption['options']) )."';\n";
        $configoptions[] = $key;
    };

    $js_addBandwidthSelected = ( $packageconfigoption[22] ) ? $packageconfigoption[22] : "0";

  // END Config options     //
  ////////////////////////////

 //GET build options
    $option = explode( ',', $packageconfigoption[10] );
    $js_requireAutoBuild   = $option[0] ? $option[0] : 0;
    $js_requireAutoBackups = $option[1] ? $option[1] : 0;
    $js_bandwidthSuspend   = $option[2] ? $option[2] : 0;

    $js_error = "    var error_msg = ";

    if ( count($hv_ids) == 0 )
        $js_error .= "'<b><font color=\'red\'>" . $_LANG["onapphvnotfound"] . "</font></b>'";
    else if ( count($template_ids) == 0 )
        $js_error .= "'<b><font color=\'red\'>" . $_LANG["onapposnotfound"] . "</font></b>'";
    else if ( count($network_ids) == 0 )
        $js_error .= "'<b><font color=\'red\'>" . $_LANG["onappnetnotfound"] . "</font></b>'";
    else
        $js_error .= "''";

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
    );

    $js_localization_string = '';

    foreach ($js_localization_array as $string)
        if (isset($_LANG['onapp'.$string]))
            $js_localization_string .= "    LANG['onapp$string'] = '".$_LANG['onapp'.$string]."';\n";

    $javascript = "

<script type=\"text/javascript\">
    selectWidth = 280;

    var serverOptions = new Array();
$js_serverOptions
    var templateOptions = {};
$js_templateOptions
    var hvOptions = new Array();
$js_hvOptions
    var networkOptions = new Array();
$js_networkOptions
    var roleOptions = new Array();
$js_roleOptions
    var configOptions = new Array();
$js_ConfigOptions
    var configOptionsSub = new Array();
$js_ConfigOptionsSub
    var hvZonesArray = new Array();
$js_hvZonesArray
    var hvZoneOptions = new Array();
$js_hvZoneOptions
    var dsOptions = new Array();
$js_dsOptions
    var ugroupOptions = new Array();
$js_ugroupOptions
    var bplanOptions = new Array();
$js_bplanOptions
    var productAddons = new Array();
        
var hvAndZoneSelected   = ". json_encode( $hv_and_zone_selected ) ."
var dsPrimarySelected   = $ds_zone_primary_selected
var dsSwapSelected      = $ds_zone_swap_selected
var rolesSelected       = ". json_encode( $js_rolesSelected ) ."
var userGroupSelected   = $js_userGroupSelected
var timeZoneSelected    = '$js_timeZoneSelected'
var billingPlanSelected = $js_billingPlanSelected
var requireAutoBuild    = '$js_requireAutoBuild'
var requireAutoBackups  = '$js_requireAutoBackups'
var addBwSelected       = '$js_addBandwidthSelected'
var bandwidthSuspend    = '$js_bandwidthSuspend'

$js_error;

// Localization
    var LANG = new Array();
$js_localization_string

</script>
<script type=\"text/javascript\" src=\"../modules/servers/onapp/includes/jquery.multiselects.js\"></script>
<script type=\"text/javascript\" src=\"../modules/servers/onapp/includes/onapp.js\"></script>
<script type=\"text/javascript\" src=\"../modules/servers/onapp/includes/slider.js\"></script>
<script type=\"text/javascript\" src=\"../modules/servers/onapp/includes/tz.js\"></script>
";

    $configarray = array(
        $_LANG["onappiservers"] => array(
            "Type" => "dropdown",
            "Options" => implode( ',', array_keys($onapp_servers) ),
            "Description" => "",
        ),
        $_LANG["onapptemlates"] => array(
            "Type" => "text",
            "Size"        => "5",
            "Description" => count($template_ids) != 0 ?
                "" :
                $_LANG["onappnotfoundred"],
        ),
        $_LANG["onappram"] => array(
            "Type"        => "text",
            "Size"        => "5",
            "Description" => "MB",
        ),
        $_LANG["onapphv"] => array(
            "Type" => count($hv_ids) != 0 ? "dropdown" : null,
            "Options" => count($hv_ids) != 0 ?
                "0,".implode( ',', array_keys($hv_ids) ) :
                null,
            "Description" => count($hv_ids) != 0 ?
                "" :
                $_LANG["onappnotfoundred"],
        ),
        $_LANG["onappcpucores"] => array(
            "Type"        => "text",
            "Size"        => "5",
            "Description" => "",
        ),
        $_LANG["onappprimarynet"] => array(
            "Type" => count($network_ids) != 0 ? "dropdown" : null,
            "Options" => count($network_ids) != 0 ?
                implode( ',', array_keys($network_ids) ) :
                null,
            "Description" => count($network_ids) != 0 ?
                "" :
                $_LANG["onappnotfoundred"],
        ),
        $_LANG["onappcpuprior"] => array(
            "Type"        => "text",
            "Size"        => "5",
            "Description" => "%",
        ),
        $_LANG["onappportspeed"] => array(
            "Type"        => "text",
            "Size"        => "5",
            "Description" => "Mbps ( Unlimited if not set )",
        ),
        $_LANG["onappswapsize"] => array(
            "Type"        => "text",
            "Size"        => "5",
            "Description" => "GB",
        ),
        $_LANG["onappbuildauto"] => array(
            "Type" => "yesno",
            "Description" => $_LANG["onappticktobuildauto"]
        ),
        $_LANG["onappprivarydisksize"] => array(
            "Type"        => "text",
            "Size"        => "5",
            "Description" => "GB",
        ),
        $_LANG["onappadditionalram"] => array(
            "Type"        => "dropdown",
            "Options"     => "0,".implode(',', $configoptions),
            "Description" => "",
        ),
        $_LANG["onappadditionallcores"] => array(
            "Type"        => "dropdown",
            "Options"     => "0,".implode(',', $configoptions),
            "Description" => "",
        ),
        $_LANG["onappadditionallcpupriority"] => array(
            "Type"        => "dropdown",
            "Options"     => "0,".implode(',', $configoptions),
            "Description" => "",
        ),
        $_LANG["onappadditionalldisksize"] => array(
            "Type"        => "dropdown",
            "Options"     => "0,".implode(',', $configoptions),
            "Description" => "",
        ),
        $_LANG["onappipaddress"] => array(
            "Type"        => "dropdown",
            "Options"     => "0,".implode(',', $configoptions),
            "Description" => "",
        ),
        $_LANG["onappbackup"] => array(
            "Type"        => "dropdown",
            "Options"     => "0,".implode(',', $configoptions),
            "Description" => "",
        ),
        $_LANG["onappincludedips"] => array(
            "Type"        => "text",
            "Size"        => "5",
            "Description" => "",
        ),
        $_LANG["onappaddonresource"] => array(
            "Type"        => "dropdown",
            "Options"     => "0,".implode(',', $configoptions),
            "Description" => "",
        ),
        $_LANG["onappadditionallportspeed"] => array(
            "Type"        => "dropdown",
            "Options"     => "0,".implode(',', $configoptions),
            "Description" => "",
        ),
        "&nbsp;" => array(
            "Type"        => "text",
            "Description" => "\n$javascript",
        )
    );

    return $configarray;
}

function onapp_CreateAccount($params) {
    global $_LANG;

    if ( wrapper_check() )
        return wrapper_check();
    
    $status = serviceStatus($params['serviceid']);
    serviceStatus($params['serviceid'], 'Active');

    $service = get_service($params['serviceid']);

    $getvm = get_vm($params['serviceid']);

    serviceStatus($params['serviceid'], $status);

    if( isset($getvm->_id) )
        return $_LANG["onappvmexist"];
    elseif ( $params['domain'] == "" )
        return $_LANG["onapphostnamenotfound"];
    elseif( ($params['configoption2'] == "" || count(explode(',', $params['configoption2'])) != 1 ) && ! isset($service['os']) )
        return $_LANG["onapptemplatenotone"];

    serviceStatus($params['serviceid'], 'Active');

    $vm = create_vm(
        $params['accountid'],
        $params['domain'],
        isset($service['os']) ? $service['os'] : $params['configoption2']
    );

    _ips_resolve_all( $params['accountid'] );

    serviceStatus($params['serviceid'], $status);

    if ( ! is_null($vm->error) )
        return is_array($vm->error) ?
            $_LANG["onappcantcreatevm"] ."<br/>\n " . implode(', ', $vm->error) :
            $_LANG["onappcantcreatevm"] . $vm->error;
    elseif ( ! is_null($vm->_obj->error) )
        return is_array($vm->_obj->error) ?
            $_LANG["onappcantcreatevm"] . "<br/>\n " . implode(', ', $vm->_obj->error) :
            $_LANG["onappcantcreatevm"] . $vm->_obj->error;

    return 'success';
}

function onapp_TerminateAccount( $params ) {
    global $_LANG;

    if ( wrapper_check() )
        return wrapper_check();

    $status = serviceStatus($params['serviceid']);
    serviceStatus($params['serviceid'], 'Active');

    $getvm = get_vm($params['serviceid']);

    if ( ! is_null($getvm->_id) ) {
        $vm = delete_vm($params['serviceid']);

        serviceStatus($params['serviceid'], $status);

        if ( ! is_null($vm->error) )
            return is_array($vm->error) ?
                $_LANG["onappcantdeletevm"] . "<br/>\n " . implode(', ', $vm->error) :
                $_LANG["onappcantdeletevm"] . $vm->error;
        elseif ( ! is_null($vm->_obj->error) )
            return is_array($vm->_obj->error) ?
                $_LANG["onappcantdeletevm"] . "<br/>\n " . implode(', ', $vm->_obj->error) :
                $_LANG["onappcantdeletevm"] . $vm->_obj->error;
    };

    return 'success';
}

function onapp_SuspendAccount($params) {
    global $_LANG;

    if ( wrapper_check() )
        return wrapper_check();

    $vm = get_vm($params['serviceid']);

    if ( $vm->_obj->_id && ! $vm->_obj->_suspended ) {
        $vm->_obj->auth(
            ($params['serverip']) ? $params['serverip'] : $params['serverhostname'],
            $params['serverusername'],
            $params['serverpassword']
        );
        $vm->_obj->suspend();

        if ( ! is_null($vm->error) )
            return is_array($vm->error) ?
                $_LANG["onappcantdeletevm"] . "<br/>\n " . implode(', ', $vm->error) :
                $_LANG["onappcantdeletevm"] . $vm->error;
        elseif ( ! is_null($vm->_obj->error) )
            return is_array($vm->_obj->error) ?
                $_LANG["onappcantdeletevm"] . "<br/>\n " . implode(', ', $vm->_obj->error) :
                $_LANG["onappcantdeletevm"] . $vm->_obj->error;
    }
    else {
        return $_LANG['onappvmalreadysuspended'];
    }

    return 'success';
}

function onapp_UnsuspendAccount($params) {
    global $_LANG;

    if ( wrapper_check() )
        return wrapper_check();

    $status = serviceStatus($params['serviceid']);
    serviceStatus($params['serviceid'], 'Active');
    
    $vm = get_vm($params['serviceid']);

    if ( $vm->_obj->_id && $vm->_obj->_suspended ) {
        $vm->_obj->auth(
            ($params['serverip']) ? $params['serverip'] : $params['serverhostname'],
            $params['serverusername'],
            $params['serverpassword']
        );
        $vm->_obj->suspend();
        
        if ( ! is_null($vm->error) )
            return is_array($vm->error) ?
                $_LANG["onappcantdeletevm"] . "<br/>\n " . implode(', ', $vm->error) :
                $_LANG["onappcantdeletevm"] . $vm->error;
        elseif ( ! is_null($vm->_obj->error) )
            return is_array($vm->_obj->error) ?
                $_LANG["onappcantdeletevm"] . "<br/>\n " . implode(', ', $vm->_obj->error) :
                $_LANG["onappcantdeletevm"] . $vm->_obj->error;
    }
    else {
        return $_LANG['onappvmalreadyactive'];
    }

    serviceStatus($params['serviceid'], 'Suspended');

    return 'success';
}

function onapp_ClientArea($params) {
    global $_LANG;
    
    if ( ! file_exists( ONAPP_WRAPPER_INIT ) )
        return
            sprintf(
                "%s ",
                $_LANG['onapponmaintenance']
        );

    $service = get_service($params['serviceid']);

    if ( ! is_null($service["vmid"]) )
        return '<a href="' . ONAPP_FILE_NAME . '?page=productdetails&id=' . $params['serviceid'] . '">' . $_LANG["onappvmsettings"] . '</a>';
    else
        return '<a href="' . ONAPP_FILE_NAME . '?page=productdetails&id=' . $params['serviceid'] . '">' . $_LANG["onappvmcreate"] . '</a>';
}
