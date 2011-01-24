<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

require_once dirname(__FILE__).'/../onapp/onapp.php';

function onappbackupspace_ConfigOptions() {
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

?>
