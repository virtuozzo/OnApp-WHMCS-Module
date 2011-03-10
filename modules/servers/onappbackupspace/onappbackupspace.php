<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

require_once dirname(__FILE__).'/../onapp/lib.php';

load_language();

function onappbackupspace_ConfigOptions() {
    global $packageconfigoption, $_GET, $_POST, $_LANG;

    $serviceid = $_GET["id"] ? $_GET["id"] : $_POST["id"];
    $serviceid = addslashes($serviceid);

    $configarray = array();

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

    $javascript = "

<script type=\"text/javascript\">
    selectWidth = 280;

    var serverOptions = new Array();
$js_serverOptions
    var configOptions = new Array();
$js_ConfigOptions
    var configOptionsSub = new Array();
$js_ConfigOptionsSub
    var productAddons = new Array();
</script>

<script type=\"text/javascript\" src=\"../modules/servers/onappbackupspace/includes/onappbackupspace.js\"></script>
<script type=\"text/javascript\" src=\"../modules/servers/onapp/includes/slider.js\"></script>
";

    $configarray = array(
        $_LANG["onappiservers"] => array(
            "Type" => "dropdown",
            "Options" => implode( ',', array_keys($onapp_servers) ),
            "Description" => "",
        ),
        $_LANG["onappprivarydisksize"] => array(
            "Type"        => "text",
            "Size"        => "5",
            "Description" => "MB",
        ),
        $_LANG["onappaddonresource"] => array(
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

function onappbackupspace_CreateAccount($params) {
    update_user_storagedisksize(
        $params, 'Active'
    );

    return 'success';
}

function onappbackupspace_TerminateAccount( $params ) {
    $serviceid = $params["serviceid"];

    if ( canbedeleted($serviceid) ) {
        update_user_storagedisksize(
            $params, 'Terminate'
        );

        return 'success';
    } else
        return "You don't have enough disk space for backups";
};

function onappbackupspace_SuspendAccount($params) {
    $serviceid = $params["serviceid"];

    if ( canbedeleted($serviceid) ) {
        update_user_storagedisksize(
            $params, 'Pending'
        );

        return 'success';
    } else
        return "You don't have enough disk space for backups";
}

function onappbackupspace_UnsuspendAccount($params) {
    update_user_storagedisksize(
        $params, 'Active'
    );

    return 'success';
}

function canbedeleted($serviceid) {

    $sql_select = "
SELECT
    tblhosting.id as id,
    tblhosting.userid as userid,
    tblonappclients.onapp_user_id,
    tblproducts.configoption2 basespace,
    CASE optiontype
        WHEN 1 THEN optionssub.sortorder
        WHEN 2 THEN optionssub.sortorder
        WHEN 4 THEN options.qty * optionssub.sortorder
        ELSE 0
    END AS additionalspace,
    tblservers.name as servername,

    tblservers.id as serverid,
    tblservers.ipaddress as serveripaddres,
    tblservers.hostname  as serverhostname,
    tblservers.username  as serverusername,
    tblservers.password  as serverpassword
FROM
    tblhosting
    LEFT JOIN tblproducts
        ON tblproducts.id = packageid
    LEFT JOIN tblservers
        ON configoption1 = tblservers.id
    LEFT JOIN tblonappclients ON
        tblproducts.configoption1 = tblonappclients.server_id
        AND tblhosting.userid = tblonappclients.client_id
    LEFT JOIN tblhostingconfigoptions AS options ON
        relid = tblhosting.id
        AND options.configid = tblproducts.configoption3
    LEFT JOIN tblproductconfigoptions
        ON tblproductconfigoptions.id = options.configid
    LEFT JOIN tblproductconfigoptionssub AS optionssub 
        ON optionssub.configid = tblproductconfigoptions.id AND 
        options.configid = tblproducts.configoption3 AND
        options.optionid = optionssub.id
WHERE
    tblhosting.id = $serviceid";

    $rows = full_query($sql_select);

    $service = mysql_fetch_assoc($rows);

    $server_id = $service["serverid"];
    $user_id = $service["userid"];

    $vms = new ONAPP_VirtualMachine();
    $vms->auth(
        $service["serveripaddres"] != "" ?
            'http://' . $service["serveripaddres"] :
            $service['serverhostname'],
        $service['serverusername'],
        decrypt($service['serverpassword'])
    );

    $backups_size = 0;

    foreach ( $vms->getList($service['onapp_user_id']) as $vm ) {
        $backups = new ONAPP_VirtualMachine_Backup();
        $backups->_virtual_machine_id = $vm->_id;

        $backups->auth(

            $service["serveripaddres"] != "" ?
                'http://' . $service["serveripaddres"] :
                $service['serverhostname'],
            $service['serverusername'],
            decrypt($service['serverpassword'])
        );

        $backupslist = $backups->getList();

        if ($backupslist)
            foreach( $backups->getList() as $backup )
                $backups_size += $backup->_backup_size;
    };

    $backups_size_gb = $backups_size / 1024 / 1024;


    $sql_select_services = "
SELECT
    tblhosting.id as id,
    tblproducts.configoption2 basespace,
    CASE optiontype
        WHEN 1 THEN optionssub.sortorder
        WHEN 2 THEN optionssub.sortorder
        WHEN 4 THEN options.qty * optionssub.sortorder
        ELSE 0
    END AS additionalspace
FROM
    tblhosting
    LEFT JOIN tblproducts
        ON tblproducts.id = packageid
    LEFT JOIN tblservers
        ON configoption1 = tblservers.id
    LEFT JOIN tblonappclients ON
        tblproducts.configoption1 = tblonappclients.server_id
        AND tblhosting.userid = tblonappclients.client_id
    LEFT JOIN tblhostingconfigoptions AS options ON
        relid = tblhosting.id
        AND options.configid = tblproducts.configoption3
    LEFT JOIN tblproductconfigoptions
        ON tblproductconfigoptions.id = options.configid
    LEFT JOIN tblproductconfigoptionssub AS optionssub 
        ON optionssub.configid = tblproductconfigoptions.id AND 
        options.configid = tblproducts.configoption3 AND
        options.optionid = optionssub.id
WHERE
    tblproducts.servertype = 'onappbackupspace'
    AND tblhosting.domainstatus = 'Active'
    AND tblhosting.userid = $user_id
    AND tblservers.id = $server_id";

    $services_rows = full_query($sql_select_services);
    $availablespace = 0;

    while ($backupservice =  mysql_fetch_assoc( $services_rows ) )
        $availablespace += $backupservice['basespace'] + $backupservice['additionalspace'];


   return ($availablespace - $backups_size_gb) >= ($service['basespace'] + $service['additionalspace']);
}

function onappbackupspace_ClientArea($params) {
    global $_LANG;

    return '<a href="onapp.php?page=storagedisksize">' . $_LANG["onappstoragedisksize"] . '</a>';
}

?>
