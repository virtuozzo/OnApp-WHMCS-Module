<?php

function afterConfigOptionsUpgrade($vars) {
    require_once dirname(__FILE__).'/../../modules/servers/onapp/wrapper/VirtualMachine.php';
    require_once dirname(__FILE__).'/../../modules/servers/onapp/wrapper/Disk.php';
    require_once dirname(__FILE__).'/../../modules/servers/onapp/wrapper/VirtualMachine/NetworkInterface.php';

    $service_id = $_POST["id"];
    $option_ids = implode(',', array_keys( $_POST["configoption"] ) );
    $new_options = $_POST["configoption"];

    if( count($_SESSION["upgradeids"]) == 1 ) {
        $sql_options = "
            SELECT 
                productconfigoptions.*
            FROM 
                tblhostingconfigoptions AS configoptions
                LEFT JOIN tblproductconfigoptions AS productconfigoptions
                    ON configoptions.configid = productconfigoptions.id
                LEFT JOIN tblproductconfigoptionssub AS configoptionssub
                    ON configoptionssub.configid = productconfigoptions.id
                    AND configoptions.optionid = configoptionssub.id
            WHERE
                configoptions.configid in ($option_ids) 
                AND configoptions.relid = $service_id;";

        $rows_options = full_query($sql_options);

        $options = array();
        $configoptionssub = array();
        $configoptionssub_coufigs = array();
        if ($rows_options) // do not go here if wrong sql query
            while ( $row_option = mysql_fetch_assoc($rows_options) ) {
                $options[ $row_option['id'] ] = $row_option;
                if ( $row_option["optiontype"] != 4)
                    $configoptionssub[] = $new_options[ $row_option['id'] ];
                else
                    $configoptions[] = $row_option['id'];
            };

            $configoptionssub_ids = count($configoptionssub) > 0 ? implode(',', $configoptionssub ) : '0';
            $configoptions_ids    = count($configoptions) > 0 ? implode(',', $configoptions) : '0';

            $sql_suboptions = "
                SELECT
                    * 
                FROM
                    tblproductconfigoptionssub
                WHERE
                    id in ($configoptionssub_ids) OR configid in ($configoptions_ids)";

                $rows_suboptions = full_query($sql_suboptions);

                if($rows_suboptions)
                    while($row_suboption = mysql_fetch_assoc($rows_suboptions) )
                        $options[ $row_suboption['configid'] ]['value'] = $options[ $row_suboption['configid'] ]["optiontype"] != "4" ?
                            $row_suboption['sortorder'] :
                            $new_options[ $row_suboption['configid']] * $row_suboption['sortorder'];

           $sql_get_hosting = "
            SELECT
                tblproducts.configoption3,
                tblproducts.configoption5,
                tblproducts.configoption7,
                tblproducts.configoption8,
                tblproducts.configoption11,

                tblproducts.configoption12,
                tblproducts.configoption13,
                tblproducts.configoption14,
                tblproducts.configoption15,
                tblproducts.configoption16,
                tblproducts.configoption19,
                tblproducts.configoption20,
                tblservers.*,
                tblonappservices.vm_id
            FROM 
                tblhosting 
                LEFT JOIN tblproducts 
                    ON tblproducts.id = packageid
                LEFT JOIN tblservers 
                    ON tblservers.id = tblproducts.configoption1 
                LEFT JOIN tblonappservices 
                    ON tblhosting.id = service_id
            WHERE 
                tblhosting.id = $service_id";

            $rows_service = full_query($sql_get_hosting);

            if ($rows_service)
                while($row_service = mysql_fetch_assoc($rows_service) ) {
                    $resources = array();

                    if ( isset( $options[ $row_service['configoption12'] ] ) )
                        $resources['memory']     = $row_service['configoption3']  + $options[ $row_service['configoption12'] ]['value'];

                    if ( isset( $options[ $row_service['configoption13'] ] ) )
                        $resources['cpus']       = $row_service['configoption5']  + $options[ $row_service['configoption13'] ]['value'];

                    if ( isset( $options[ $row_service['configoption14'] ] ) )
                        $resources['cpu_shares'] = $row_service['configoption7']  + $options[ $row_service['configoption14'] ]['value'];

                    if ( isset( $options[ $row_service['configoption15'] ] ) )
                        $resources['disk_size']  = $row_service['configoption11'] + $options[ $row_service['configoption15'] ]['value'];

                    if ( isset( $options[ $row_service['configoption20'] ] ) )
                        $resources['rate_limit'] = $row_service['configoption8']  + $options[ $row_service['configoption20'] ]['value'];

                    if ( isset( $options[ $row_service['configoption16'] ] ) )
                        $resources['ips'] = $row_service['configoption18']  + $options[ $row_service['configoption16'] ]['value'];
// Close OS changing
//                    if ( isset( $options[ $row_service['configoption19'] ] ) )
//                        $resources['template_id'] = $options[ $row_service['configoption19'] ]['value'];

                    $vm_info = array(
                        "vm_id"    => $row_service["vm_id"],
                        "username" => $row_service["username"],
                        "password" => decrypt($row_service["password"]),
                        "hostname" => $row_service["ipaddress"] != "" ?
                            $row_service["ipaddress"] :
                            $row_service["hostname"]
                    );


                    $vm = new ONAPP_VirtualMachine();

                    $vm->auth(
                        $vm_info["hostname"],
                        $vm_info["username"],
                        $vm_info["password"]
                    );

                    // if virtual machine not found
                    if ($vm_info["vm_id"] > 0)
                        $vm->load($vm_info["vm_id"]);
                    else 
                        return null;

                    // Change resources
                    $vm->_memory     = $resources['memory'];
                    $vm->_cpus       = $resources['cpus'];
                    $vm->_cpu_shares = $resources['cpu_shares'];
                    $vm->_rate_limit = $resources['rate_limit'];

                    $vm->save();

                    if( ! is_null($vm->error) || ! is_null($vm->_obj->error) )
                        return null;

                    // Change Disk size
                    $disks = new ONAPP_Disk();

                    $disks->auth(
                        $vm_info["hostname"],
                        $vm_info["username"],
                        $vm_info["password"]
                    );

                    $primary_disk = null;

                    foreach($disks->getList( $vm_info["vm_id"] ) as $disk )
                        if( $disk->_primary == "true" )
                            $primary_disk = $disk;

                    if ( ! is_null($primary_disk) && $primary_disk->_disk_size != $resources['disk_size'] ) {

                        $primary_disk->auth(
                            $vm_info["hostname"],
                            $vm_info["username"],
                            $vm_info["password"]
                        );

                        $primary_disk->_disk_size = $resources['disk_size'];

                        $primary_disk->save();

                    };

                    // Chanege Port Speed
                    $network = new ONAPP_VirtualMachine_NetworkInterface();

                    $network->auth(
                        $vm_info["hostname"],
                        $vm_info["username"],
                        $vm_info["password"]
                    );

                    $networks = $network->getList($vm_info["vm_id"]);

                    $primary_network = null;

                    foreach( $networks as $network )
                        if($net->_primary == "true")
                            $primary_network = $network;

                    if( ! is_null($primary_network) 
						&& isset($resources['rate_limit']) 
						&& $primary_network->_rate_limit != $resources['rate_limit'] 
					) {
                        $primary_network->auth(
                            $onapp_config["adress"],
                            $onapp_config['username'],
                            $onapp_config['password']
                        );

                        $primary_network->_rate_limit = $resources['rate_limit'];
                        $primary_network->save();
                    };

                    // Rebuild || Restart
                    if( isset($resources['template_id']) && $resources['template_id'] != $vm->_obj->_template_id ) {
                        $vm->_template_id = $resources['template_id'];
                        $vm->save();

                        $vm->build();
                    } elseif ( ($vm->_obj->_booted == "true") && (
                         $vm->_obj->_memory     != $resources['memory'] ||
                         $vm->_obj->_cpus       != $resources['cpus'] ||
                         $vm->_obj->_cpu_shares != $resources['cpu_shares']
                    )) {
                        $vm->reboot();
                    };

                };
    };

}

function afterBackupSpaceUpgrade($vars) {
    require_once dirname(__FILE__).'/../../modules/servers/onappbackupspace/onappbackupspace.php';

    update_user_storagedisksize(
        array("serviceid" => $_POST["id"])
    );
}

//add_hook("AfterConfigOptionsUpgrade", 0, 'afterConfigOptionsUpgrade','');
//add_hook("AfterConfigOptionsUpgrade", 1, 'afterBackupSpaceUpgrade' )
?>
