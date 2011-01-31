<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */
define( 'MODULE_WRAPPER_DIR', dirname(__FILE__).'/wrapper' );

require_once MODULE_WRAPPER_DIR.'/Network.php';
require_once MODULE_WRAPPER_DIR.'/Template.php';
require_once MODULE_WRAPPER_DIR.'/Hypervisor.php';

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

function onapp_ConfigOptions() {
    global $packageconfigoption, $_GET, $_POST, $_LANG;

    $serviceid = $_GET["id"] ? $_GET["id"] : $_POST["id"];
    $serviceid = addslashes($serviceid);

    $configarray = array();

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
            $js_templateOptions .= "    templateOptions['$os'][$_template->_id] = '".addslashes($_template->_label)."';\n";
        };
    };
  // END Load Templates     //
  ////////////////////////////

  ////////////////////////////
  // BEGIN Load Hypervisors //
    $hv = new ONAPP_Hypervisor();

    $hv->auth(
        $onapp_config["adress"],
        $onapp_config['username'],
        $onapp_config['password']
    );

    $hvs = $hv->getList();

    $hv_ids = array();

    $js_hvOptions = "    hvOptions[0] = '" . $_LANG["onappautoselect"] . "';\n";
    
    if (!empty($hvs)) {
        foreach ($hvs as $_hv) {
            if ( $_hv->_online == "true" ) {
                $hv_ids[$_hv->_id] = array(
                    'label' => $_hv->_label
                );
    
                $js_hvOptions .= "    hvOptions[$_hv->_id] = '".addslashes($_hv->_label)."';\n";
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

  // END Config options     //
  ////////////////////////////

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
        'wrongspead'
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
    var configOptions = new Array();
$js_ConfigOptions
    var configOptionsSub = new Array();
$js_ConfigOptionsSub
    var productAddons = new Array();

$js_error;

// Localization

    var LANG = new Array();
$js_localization_string

</script>
<script type=\"text/javascript\" src=\"../modules/servers/onapp/includes/jquery.multiselects.js\"></script>
<script type=\"text/javascript\" src=\"../modules/servers/onapp/includes/onapp.js\"></script>
<script type=\"text/javascript\" src=\"../modules/servers/onapp/includes/slider.js\"></script>
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
            "Type"        => "",
            "Description" => "\n$javascript",
        )
    );

    return $configarray;
}

function onapp_CreateAccount($params) {
    global $_LANG;

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

function onapp_TerminateAccount( $params ) {
    global $_LANG;

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

    $getvm = get_vm($params['serviceid']);

    if ( ! is_null($getvm->_id) && $getvm->_obj->_booted == "true" ) {
        $getvm->shutdown();

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

function onapp_UnsuspendAccount($params) {
    global $_LANG;

    $getvm = get_vm($params['serviceid']);

    if ( ! is_null($getvm->_id) && $getvm->_obj->_booted == "false" ) {
        $getvm->startup();

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

function onapp_ClientArea($params) {
    global $_LANG;

    $service = get_service($params['serviceid']);

    if ( ! is_null($service["vmid"]) )
        return '<a href="onapp.php?page=productdetails&id=' . $params['serviceid'] . '">' . $_LANG["onappvmsettings"] . '</a>';
    else 
        return '<a href="onapp.php?page=productdetails&id=' . $params['serviceid'] . '">' . $_LANG["onappvmcreate"] . '</a>';
}

?>
