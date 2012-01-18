<?php

/**
 * Set subscription server id in accordance with product server id if it differs
 * from default server id
 *
 * @param array $vars with orderid
 * @return
 */
function AfterAcceptOrder( $vars ) {
    $query = "
        SELECT
            tblhosting.server as default_server_id,
            tblproducts.configoption1 as product_server_id,
            tblhosting.id as hosting_id
        FROM
            tblorders
        LEFT JOIN
            tblhosting ON tblorders.id = tblhosting.orderid
        LEFT JOIN
            tblproducts ON tblhosting.packageid = tblproducts.id
        WHERE
            tblorders.id = $vars[orderid]
    ";

    $result = full_query( $query );

    if ( ! $result || mysql_num_rows( $result )  < 1 ) {
        return;
    }

    $row = mysql_fetch_assoc( $result );

    if ( $row['default_server_id'] != $row['product_server_id'] ) {
        $query = "
            UPDATE tblhosting SET
                server = $row[product_server_id]
            WHERE
                id = $row[hosting_id]
        ";

        $result = full_query( $query );
    }
    
    return;
}


function afterConfigOptionsUpgrade($vars) {
    $cycle_count = count( $_SESSION['upgradeids'] );
    
    if ( $vars['upgradeid'] == $_SESSION['upgradeids'][$cycle_count-1]) {
        _action();
    }
}

/**
 *
 * Upgrade resources
 * 
 * @return void
 */
function _action() {
    if ( ! defined('ONAPP_WRAPPER_INIT') )
        define('ONAPP_WRAPPER_INIT', dirname(__FILE__).
               '/../wrapper/OnAppInit.php');

    if ( file_exists( ONAPP_WRAPPER_INIT ) )
        require_once ONAPP_WRAPPER_INIT;

    $query = "
        SELECT
            tblupgrades.id as upgrade_id,
            tblproductconfigoptionssub.configid,
            tblproductconfigoptionssub.sortorder as additional_value,
            tblproducts.configoption12 as ram_configid,
            tblproducts.configoption13 as cpu_cores_configid,
            tblproducts.configoption14 as cpu_priority_configid,
            tblproducts.configoption15 as primary_disk_size_configid,
            tblproducts.configoption16 as ipaddress_configid,
            tblproducts.configoption6  as primary_network_id,
            tblproducts.configoption19 as template_configid,
            tblproducts.configoption20 as port_speed_configid,
            tblproducts.configoption22 as bandwidth_configid,
            tblproducts.configoption2 as product_template_ids,
            tblproducts.configoption3 as product_ram,
            tblproducts.configoption5 as product_cpu_cores,
            tblproducts.configoption7 as product_cpu_priority,
            tblproducts.configoption11 as product_primary_disk_size,
            tblproducts.configoption8 as product_port_speed,
            tblproducts.configoption18 as product_ip_addresses,
            tblproducts.overagesbwlimit as product_bandwidth,
            tblproducts.name as product_name,
            tblhosting.id as hosting_id,
            tblupgrades.newvalue as additional_value_id,
            tblonappservices.*,
            tblservers.ipaddress,
            tblservers.hostname,
            tblservers.username,
            tblservers.password
        FROM
            tblupgrades
        LEFT JOIN
            tblproductconfigoptionssub ON
            tblproductconfigoptionssub.id = tblupgrades.newvalue
        LEFT JOIN
            tblhosting ON tblhosting.id = tblupgrades.relid
        LEFT JOIN
            tblproducts ON tblproducts.id = tblhosting.packageid
        LEFT JOIN
            tblonappservices ON tblonappservices.service_id = tblhosting.id
        LEFT JOIN
            tblservers ON tblhosting.server = tblservers.id
        WHERE
            tblupgrades.id IN ( " . implode(',', $_SESSION['upgradeids']) . " )
            AND tblservers.type = 'onapp'
    ";

    $result = full_query( $query );
    
    if ( mysql_num_rows( $result )  < 1 ) {
        return;
    }

    while( $row = mysql_fetch_assoc( $result ) ) {
        $configurableoptions_labels = array(
            '_memory'             => $row['ram_configid'],
            '_cpus'               => $row['cpu_cores_configid'],
            '_cpu_shares'         => $row['cpu_priority_configid'],
            '_primary_disk_size'  => $row['primary_disk_size_configid'],
            'ipaddresses'         => $row['ipaddress_configid'],
            '_template_id'        => $row['template_configid'],
            '_rate_limit'         => $row['port_speed_configid'],
            'bandwidth'           => $row['bandwidth_configid'],
        );

        $configurableoptions_product_values = array(
            '_memory'             => $row['product_ram'],
            '_cpus'               => $row['product_cpu_cores'],
            '_cpu_shares'         => $row['product_cpu_priority'],
            '_primary_disk_size'  => $row['product_primary_disk_size'],
            'ipaddresses'         => $row['product_ip_addresses'],
            '_template_id'        => $row['product_template_ids'],
            '_rate_limit'         => $row['product_port_speed'],
            'bandwidth'           => $row['product_bandwidth'],
        );

        if( ! count($configurableoptions_labels) ==
            count( array_unique ( $configurableoptions_labels ) ) )
        {
            return 'Wrong configurable options settings in '
                . product_name . 'product';
        }

        $resource_label = array_search($row['configid'],
                                       $configurableoptions_labels);

        if ( $resource_label != '_template_id' ){
            $vm_resource[ $resource_label ] = $row['additional_value'] +
                 $configurableoptions_product_values[ $resource_label ];
        }
        elseif ( $resource_label == '_template_id' ) {
            $vm_resource[ $resource_label ] = $row['additional_value'];
        }

        $ipaddress          = $row['ipaddress'];
        $hostname           = $row['hostname'];
        $username           = $row['username'];
        $password           = $row['password'];
        $vm_id              = $row['vm_id'];
        $hosting_id         = $row['hosting_id'];
        $primary_network_id = $row['primary_network_id'];

    }

    $onapp = new OnApp_Factory(
        ( $hostname ) ? $hostname : $ipaddress,
        $username,
        decrypt( $password )
    );

        $vm = $onapp->factory( 'VirtualMachine', true );

    foreach ( $vm_resource as $label => $value ) {
        if ( $label == 'bandwidth' ) {
            $update_bandwidth = $value;
        }
        
        if ( $label == '_template_id' ) {
            $rebuild_vm = true;
            $template_id = $value;
        }

        if ( $label == '_rate_limit' ) {
            $rate_limit = $value ;
        }

        if ( $label == '_primary_disk_size' ) {
            $primary_disk_size = $value ;
        }

        if ( $label == 'ipaddresses') {
            $ipaddresses_number = $value;
        }

        if ( $label != '_template_id' && $label != 'bandwidth' &&
             $label != '_rate_limit' && $label != '_primary_disk_size' &&
             $label != 'ipaddresses' )
        {
            $vm->$label = $value;
        }
    }

// Edit VM resourses RAM, cpus, cpu_shares //
////////////////////////////////////////////

    $vm->_id = $vm_id;
    $vm->save();

// Edit VM resourses RAM, cpus, cpu_shares //
////////////////////////////////////////////

// Upgrade / Downgrade Primary Disk size //
//////////////////////////////////////////

    if ( isset( $primary_disk_size ) ) {
        $vm_disk = $onapp->factory('Disk', true);
        $vm_disks = $vm_disk->getList( $vm_id );

        foreach ( $vm_disks as $disk) {
            if ( $disk->_primary ) {
                $disk_id = $disk->_id;
            }
        }

        $vm_disk->_id = $disk_id;
        $vm_disk->_disk_size = $primary_disk_size;
        $vm_disk->save();

    }

// End Upgrade / Downgrade Primary disk size //
//////////////////////////////////////////////

// Upgrade / Downgrade Rate Limit //
///////////////////////////////////

    if ( isset( $rate_limit ) ) {
        $vm_interface = $onapp->factory('VirtualMachine_NetworkInterface');
        $vm_interfaces = $vm_interface->getList( $vm_id );

        foreach ( $vm_interfaces as $interface ) {
            if ( $interface->_primary ) {
                $interface_id = $interface->_id;
            }
        }

        $vm_interface->_id = $interface_id;
        $vm_interface->_rate_limit = $rate_limit;
        $vm_interface->save();

        if ( ! isset( $template_id ) ){
            $vm->rebuild_network( );
        }
    }

// End Upgrade / Downgrade Rate Limit //
///////////////////////////////////////

// Upgrade / Downgrade Bandwidth Limit //
////////////////////////////////////////

    if ( $update_bandwidth ) {
        $query = "
            UPDATE
                tblhosting
            SET
                bwlimit    = '$update_bandwidth'
            WHERE
                id = '$hosting_id'";

        $result = full_query( $query );

    }

// End Upgrade / Downgrade Bandwidth  Limit //
/////////////////////////////////////////////

// Upgrade / Downgrade Ipaddresses //
////////////////////////////////////

    if (  isset( $ipaddresses_number ) ) {
        $vm_obj = $onapp->factory('VirtualMachine');
        $vm     = $vm_obj->load( $vm_id );

        $vm_ipaddresses_number = count( $vm->_ip_addresses);
        $ipaddresses_to_add = $ipaddresses_number - $vm_ipaddresses_number;

        if ( $ipaddresses_to_add > 0 ) {
            $ip_address_obj = $onapp->factory('IpAddress');

            $vm_network_interface = $onapp
                                 ->factory( 'VirtualMachine_NetworkInterface' );

            $vm_network_interfaces = $vm_network_interface->getList( $vm_id );

            foreach ( $vm_network_interfaces as $interface ) {
                if ( $interface->_primary ) {
                    $primary_interface_id = $interface->_id;
                }
            }

            for ( $i = 0; $i < $ipaddresses_to_add; $i++) {
                $ip_addresses_list = $ip_address_obj
                                     ->getList( $primary_network_id );

                foreach( $ip_addresses_list as $ip ) {
                    if ( $ip->_free ) {
                        $free_ip = $ip->_id;
                        break;
                    }
                }

                if ( $free_ip && isset( $primary_interface_id ) ) {
                    $ip_address_join = $onapp
                                     ->factory( 'VirtualMachine_IpAddressJoin', true);
                    $ip_address_join->_virtual_machine_id = $vm_id;
                    $ip_address_join->_network_interface_id =
                                                          $primary_interface_id;
                    $ip_address_join->_ip_address_id = $free_ip;

                    $ip_address_join->save();

                    if ( $ip_address_join->_id ) {
                        $query = "
                            INSERT INTO
                                tblonappips ( serviceid, ipid, isbase )
                            VALUES
                                ( $hosting_id, $free_ip, '0')
                        ";

                        $result = full_query( $query );
                    }
                }

                $free_ip = null;
            }

        }

        $vm_obj = $onapp->factory('VirtualMachine');
        $vm = $vm_obj->load( $vm_id );

        foreach( $vm->_ip_addresses as $ip ) {
            $ips .= $ip->_address.'\n';
        }

        $query = "UPDATE  tblhosting SET assignedips = '$ips' WHERE id = '$hosting_id'";

        $result = full_query( $query );

    }
    
// End Upgrade / Downgrade Ipaddresses //
////////////////////////////////////////

// Upgrade / Downgrade template //
/////////////////////////////////

    if ( isset( $rebuild_vm ) && $index == 0 ) {
        $_vm = $onapp->factory('VirtualMachine',true);
        $_vm->_id = $vm_id;
        $_vm->_required_startup = '1';
        $_vm->_template_id = $template_id;
        $_vm->build( );
    }

// End Upgrade / Downgrade Template //
/////////////////////////////////////
    
}

add_hook( "AfterConfigOptionsUpgrade", 1, 'afterConfigOptionsUpgrade' );
add_hook( "AcceptOrder", 1, 'AfterAcceptOrder' );
