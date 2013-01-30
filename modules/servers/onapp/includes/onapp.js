function setSavedOptions() {
	$( '#tab' ).before( '<div id="prevOptions"></div>' );
	var html = '';
	for( i in savedOptions ) {
		console.log( i, savedOptions[ i ] );
		html += '<input type="text" name="packageconfigoption[' + i + ']" value="' + savedOptions[ i ] + '">';
	}
	$( '#prevOptions' ).html( $( html ) );
}

$(document).ready(function(){
// take away server group selection
    $("select[name='servergroup']").parent().parent().hide();

// form submit action
    var form = $("form[name$='packagefrm']");

    form.submit(function() {
		if( ! $( 'input[name^="packageconfigoption"]' ).length ) {
			setSavedOptions();
			return;
		}

		if ( ! add_secondary_network_info() ) {
            return false
        }

        add_user_info()
        add_hv_zone()
        if ( ! check_networks() ){
            return false
        }

        add_build_options()
        var checkresult = checkvars(check_vars);
        add_ds_zone(checkresult)

        return true
    });

    function check_networks() {
        var secondary_network_id = $('select[name="sec_network_id"]').val()
        var primary_network_id   = $('select[name="packageconfigoption[6]"]').val()
        var hvzone               = $('select[name="hvzones"]').val()
        var hv                   = $('select[name="packageconfigoption[4]"]').val()
        var sec_net_enabled      = $('select[name="sec_net_configurable_option_id"]').val()

        if ( !in_array( primary_network_id, get_networks() ) && ! ( ( hv == '0' || typeof hv == 'undefined' ) && ( hvzone == '0' || hvzone == 'no_zone' ))  ){
            alert( LANG['onappyouhavetoselectactivenetwork'])
            $('select[name="packageconfigoption[6]"]').focus()
            return false;
        }

        if ( sec_net_enabled != 0 && !in_array( secondary_network_id, get_networks() ) &&
            ! ( ( hv == '0' || typeof hv == 'undefined' ) && ( hvzone == '0' || hvzone == 'no_zone' ))  ){
                alert( LANG['onappyouhavetoselectactivenetwork'])
                $('select[name="sec_network_id"]').focus()
                return false;
        }

        return true;
    }

// replace values
    serverSelect = $("select[name$='packageconfigoption[1]']");

    serverSelected = serverSelect.val();

    selectHTML = '';
    for ( var option in serverOptions )
            selectHTML += '<option value="'+option+'">'+serverOptions[option]+'</option>';

    serverSelect.html(selectHTML);
    serverSelect.val(serverSelected);
    serverSelect.width(180);

    templateSelect = $("input[name$='packageconfigoption[2]']");
    templateSelected = templateSelect.val();
    templateSelect.val(templateSelected);
    templateSelect.width(selectWidth);
    templateSelect.css('display', 'none');

    hvSelect = $("select[name$='packageconfigoption[4]']");

// if ( hv_id , hv_zone ) in database
    var hvZoneId = ( hvAndZoneSelected ) ? hvAndZoneSelected[1] : hvZonesArray[hvSelect.val()]

    hvSelected =  ( hvAndZoneSelected ) ? hvAndZoneSelected[0] : hvSelect.val();

    selectHTML = '';
    for ( option in hvOptions ) {
        selected = (option == hvSelected) ? ' selected="selected"' : '';
        if ( option == 0 ) hvZonesArray[option] = 'autoselect'
        selectHTML += '<option zone="'+ hvZonesArray[option] +'" value="'+option+'"'+selected+'>'+hvOptions[option]+'</option>';
    }

    hvSelect.html(selectHTML);
    hvSelect.width(selectWidth);

    networkSelect = $("select[name$='packageconfigoption[6]']");
    networkSelected = networkSelect.val();

    selectHTML = '';
    for ( option in networkOptions ) {
        selected = (option == networkSelected) ? ' selected="selected"' : '';
        selectHTML += '<option value="'+option+'"'+selected+'>'+networkOptions[option]+'</option>';
    }

    networkSelect.html(selectHTML);
    networkSelect.width(selectWidth);

    addRAMSelect = $("select[name$='packageconfigoption[12]']");
    addRAMSelected = addRAMSelect.val();
    addRAMSelect.width(selectWidth);
    selectHTML = '';
    for ( option in configOptions ) {
        selected = (option == addRAMSelected) ? ' selected="selected"' : '';
        selectHTML += '<option value="'+option+'"'+selected+'>'+configOptions[option]+'</option>';
    }
    addRAMSelect.html(selectHTML);

    addCoresSelect = $("select[name$='packageconfigoption[13]']");
    addCoresSelected = addCoresSelect.val();
    addCoresSelect.width(selectWidth);
    selectHTML = '';
    for ( option in configOptions ) {
        selected = (option == addCoresSelected) ? ' selected="selected"' : '';
        selectHTML += '<option value="'+option+'"'+selected+'>'+configOptions[option]+'</option>';
    }
    addCoresSelect.html(selectHTML);

    addPrioritySelect = $("select[name$='packageconfigoption[14]']");
    addPrioritySelected = addPrioritySelect.val();
    addPrioritySelect.width(selectWidth);
    selectHTML = '';
    for ( option in configOptions ) {
        selected = (option == addPrioritySelected) ? ' selected="selected"' : '';
        selectHTML += '<option value="'+option+'"'+selected+'>'+configOptions[option]+'</option>';
    }
    addPrioritySelect.html(selectHTML);

    addBandwidthSelect = $("select[name='packageconfigoption[22]']");
    addBandwidthSelected = addBandwidthSelect.val();
    selectHTML = '';
    for ( option in configOptions ) {
        selected = (option == addPrioritySelected) ? ' selected="selected"' : '';
        selectHTML += '<option value="'+option+'"'+selected+'>'+configOptions[option]+'</option>';
    }
    addPrioritySelect.html(selectHTML);

    addDiskSelect = $("select[name$='packageconfigoption[15]']");
    addDiskSelected = addDiskSelect.val();
    addDiskSelect.width(selectWidth);
    selectHTML = '';
    for ( option in configOptions ) {
        selected = (option == addDiskSelected) ? ' selected="selected"' : '';
        selectHTML += '<option value="'+option+'"'+selected+'>'+configOptions[option]+'</option>';
    }
    addDiskSelect.html(selectHTML);

    addIPSelect = $("select[name$='packageconfigoption[16]']");
    addIPSelected = addIPSelect.val();
    addIPSelect.width(selectWidth);
    selectHTML = '';
    for ( option in configOptions ) {
        selected = (option == addIPSelected) ? ' selected="selected"' : '';
        selectHTML += '<option value="'+option+'"'+selected+'>'+configOptions[option]+'</option>';
    }
    addIPSelect.html(selectHTML);

    addBackupSelect = $("select[name$='packageconfigoption[17]']");
    addBackupSelected = addBackupSelect.val();
    addBackupSelect.width(selectWidth);
    selectHTML = '';
    for ( option in configOptions ) {
        selected = (option == addBackupSelected) ? ' selected="selected"' : '';
        selectHTML += '<option value="'+option+'"'+selected+'>'+configOptions[option]+'</option>';
    }
    addBackupSelect.html(selectHTML);

    addIPBaseSelect = $("select[name$='packageconfigoption[18]']");
    addIPBaseSelected = addIPBaseSelect.val();
    addIPBaseSelect.width(selectWidth);
    selectHTML = '';
    for ( option in productAddons ) {
        selected = (option == addIPBaseSelected) ? ' selected="selected"' : '';
        selectHTML += '<option value="'+option+'"'+selected+'>'+productAddons[option]+'</option>';
    }
    addIPBaseSelect.html(selectHTML);

    addTemplatesSelect = $("select[name$='packageconfigoption[19]']");
    addTemplatesSelected = addTemplatesSelect.val();
    addTemplatesSelect.width(selectWidth);
    selectHTML = '';
    for ( option in configOptions ) {
        selected = (option == addTemplatesSelected) ? ' selected="selected"' : '';
        selectHTML += '<option value="'+option+'"'+selected+'>'+configOptions[option]+'</option>';
    }
    addTemplatesSelect.html(selectHTML);

    addPortSpead = $("select[name$='packageconfigoption[20]']");
    addPortSpeadSelected = addPortSpead.val();
    addPortSpead.width(selectWidth);
    selectHTML = '';
    for ( option in configOptions ) {
        selected = (option == addPortSpeadSelected) ? ' selected="selected"' : '';
        selectHTML += '<option value="'+option+'"'+selected+'>'+configOptions[option]+'</option>';
    }
    addPortSpead.html(selectHTML);

    var rolesHTML = '';
    for ( option in roleOptions ) {
        checked = ( in_array ( option, rolesSelected ) ) ? ' checked="checked"' : '';
        rolesHTML += '<input name="role_ids" type="checkbox" value="'+option+'"'+checked+'>'+roleOptions[option] + '<br />';
    }

   var dsZoneHtml = '';
   for ( option in dsOptions ) {
       dsZoneHtml += '<option value="'+option+'">'+dsOptions[option]+'</option>';
   }

   var userGroupHtml =
       '<select name="user_group">'
   for ( option in ugroupOptions ) {
       selected = ( option == userGroupSelected ) ? 'selected' : ''
       userGroupHtml +=
           '    <option value="'+option+'"'+selected+'>'+ugroupOptions[option]+'</option>';
   }
   userGroupHtml += '</select>'

   var billingPlanHtml =
       '<select name="billing_plan">'   +
       '    <option value="0"></option>'
   for ( option in bplanOptions ) {
       if ( billingPlanSelected )
           selected = ( option == billingPlanSelected ) ? 'selected' : ''
       billingPlanHtml +=
           '    <option value="'+option+'"'+selected+'>'+bplanOptions[option]+'</option>';
   }
   billingPlanHtml += '</select>'

   var timeZoneHtml =
       '<select name="time_zone">'      +
            OnAppUsersTZs               +
       '</select>'

// get base table
    var table = $('table').eq(5);
    var tr = table.find('tr').eq(0);

// get servers
    var servers_label = tr.find('td').eq(0).html();
    var servers_html  = tr.find('td').eq(1).html();

// get templates
    var templates_label = tr.find('td').eq(2).html();
    var templates_html  = tr.find('td').eq(3).html();

// remove row
    tr.remove();
    tr = table.find('tr').eq(0);

// get templates
    var ram_label = tr.find('td').eq(0).html();
    var ram_html  = tr.find('td').eq(1).html();

// get ram
    var hypervisors_label = tr.find('td').eq(2).html();
    var hypervisors_html  = tr.find('td').eq(3).html();

// remove row
    tr.remove();
    tr = table.find('tr').eq(0);

// get cores
    var cores_label = tr.find('td').eq(0).html();
    var cores_html  = tr.find('td').eq(1).html();

// get networks
    var networks_label = tr.find('td').eq(2).html();
    var networks_html  = tr.find('td').eq(3).html();

// remove row
    tr.remove();
    tr = table.find('tr').eq(0);

// get cpu priority
    var priority_label = tr.find('td').eq(0).html();
    var priority_html  = tr.find('td').eq(1).html();

// get port speed
    var port_speed_label = tr.find('td').eq(2).html();
   var port_speed_html   = tr.find('td').eq(3).html();

// remove row
    tr.remove();
    tr = table.find('tr').eq(0);

// get swap
    var swap_label = tr.find('td').eq(0).html();
    var swap_html  = tr.find('td').eq(1).html();

// get build_auto
    var build_auto_label = tr.find('td').eq(2).html();
    checked = requireAutoBuild == 'on' ? 'checked' : ''
    var build_auto_html  = '<input type="checkbox" name="autobuild"' + checked +'>'
// get build_autobackups
    var backups_auto_label = LANG['onapprequireautobackups']
    checked = requireAutoBackups == 'on' ? 'checked' : ''
    var backups_auto_html  = '<input type="checkbox" name="autobackups"' + checked +'>'

// remove row
    tr.remove();
    tr = table.find('tr').eq(0);

// get disk
    var disk_label = tr.find('td').eq(0).html();
    var disk_html  = tr.find('td').eq(1).html();

// get additional RAM
    var addram_label = tr.find('td').eq(2).html();
    var addram_html  = tr.find('td').eq(3).html();

// remove row
    tr.remove();
    tr = table.find('tr').eq(0);

// get additional CPU Cores
    var addcores_label = tr.find('td').eq(0).html();
    var addcores_html  = tr.find('td').eq(1).html();

// get additional CPU Priority
    var addpriority_label = tr.find('td').eq(2).html();
    var addpriority_html  = tr.find('td').eq(3).html();

// remove row
    tr.remove();
    tr = table.find('tr').eq(0);

// get additional Primary Disk
    var adddisk_label = tr.find('td').eq(0).html();
    var adddisk_html  = tr.find('td').eq(1).html();

// get IP Address
    var ip_label = tr.find('td').eq(2).html();
    var ip_html  = tr.find('td').eq(3).html();

// remove row
    tr.remove();
    tr = table.find('tr').eq(0);

// get backup
    var backup_label = tr.find('td').eq(0).html();
    var backup_html  = tr.find('td').eq(1).html();

// get IP Address
    var ipbase_label = tr.find('td').eq(2).html();
    var ipbase_html  = tr.find('td').eq(3).html();

// remove row
    tr.remove();
    tr = table.find('tr').eq(0);

// get OS Template
    var ostemplates_label = tr.find('td').eq(0).html();
    var ostemplates_html  = tr.find('td').eq(1).html();

    var addport_speed_label  = tr.find('td').eq(2).html();
    var addport_speed_html   = tr.find('td').eq(3).html();

// remove row
    tr.remove();
    tr = table.find('tr').eq(0);

// get additional bandwidth
   var addbandwidth_label = LANG['onappaddbandwidth']
   var addbandwidth_html  = '<select name="packageconfigoption[22]" >'

    for ( option in configOptions ) {
        selected = (option == addBwSelected) ? ' selected="selected"' : '';
        addbandwidth_html += '<option value="'+option+'"'+selected+'>'+configOptions[option]+'</option>';
    }

    addbandwidth_html += '</select>'

// set secondary network port speed
   var sec_net_port_speed_html = '<input type="text" name="sec_net_port_speed" size="5" value="'+SecNetworkPortSpeedSelected+'"> Mbps ( Unlimited if not set )'
   var sec_net_port_speed_label = port_speed_label
// set secondary network ip addresses
   var sec_net_ips_html = '<input type="text" name="sec_net_ips" size="5" value="'+SecNetworkIps+'">'

// get additional secondary network configurable options ip addresses
   var add_sec_net_ips_label = LANG['onappaddsecnetips']
   var add_sec_net_ips_html  = '<select name="sec_net_configurable_option_id" >'

    for ( option in configOptions ) {
        selected = (option == addSecNetworkIPSelected) ? ' selected="selected"' : '';
        add_sec_net_ips_html += '<option value="'+option+'"'+selected+'>'+configOptions[option]+'</option>';
    }

    add_sec_net_ips_html += '</select>'

// get User Roles
    var roles_label = LANG['onappuserroles'];
    var roles_html  = rolesHTML

// get Hypervisor Zones
    var hv_zones_label = LANG['onapphvzones']
    var hv_zones_html  =
        '<select name="hvzones">' +
        '    <option value="no_zone"></option>';
    for ( option in hvZoneOptions )
        hv_zones_html +=
            '    <option value="'+option+'">'+hvZoneOptions[option]+'</option>';
    hv_zones_html += '</select>';

// get Secondary Networks Select
    var sec_net_networks_label = LANG['onappsecnet']
    var sec_networks_html = '<select name="sec_network_id">'
    for ( option in networkOptions ) {
        selected = (option == SecNetworkIdSelected) ? ' selected="selected"' : '';
        sec_networks_html += '<option value="'+option+'"'+selected+'>'+networkOptions[option]+'</option>';
    }

    sec_networks_html += '</select>';

// get Data Store Zones
    var ds_zones_label = LANG['onappdszone'];
    var ds_zones_primary_html =
        '<select name="ds_zones_primary"> '+
            dsZoneHtml                     +
        '</select>'
    var ds_zones_swap_html =
        '<select name="ds_zones_swap"> '   +
            dsZoneHtml                     +
        '</select>'
// get user groups
    var user_groups_label = LANG['onappusergroups']
    var user_groups_html  = userGroupHtml

// get billing plans
    var billing_plans_label = LANG['onappbillingplans']
    var billing_plans_html  = billingPlanHtml

// get time zones
    var time_zones_label = LANG['onapptimezones']
    var time_zones_html  = timeZoneHtml

// remove row
    tr.remove();

// tables
    var tbody = table.find('tbody');
    tbody.find("tr").eq(0).remove()
    tbody.append( cell_html(servers_label, servers_html) );

    if ( error_msg != "" ) {
        table.after( '<br/>'+error_msg+'<br/>' );
        check_vars = false;
    } else {

    // first table
        table.after('<br><table class="form" width="100%" border="0" cellspacing="2" cellpadding="3"><tbody></tbody></table>');
        var second_table = $('table').eq(6);
        tbody = second_table.find('tbody');
        tbody.append('<tr><td class="fieldlabel" colspan="2"><b>'+LANG['onappvmproperties']+'</b></td></tr>');
        tbody.append( cell_html(hv_zones_label, hv_zones_html ) );
        tbody.append( cell_html(hypervisors_label, hypervisors_html) );
        tbody.append('<tr><td class="fieldlabel" colspan="2"><b>'+LANG['onappuserproperties']+'</b></td></tr>');
        tbody.append( cell_html(billing_plans_label, billing_plans_html ) );
        tbody.append( cell_html(roles_label, roles_html ) );
        tbody.append( cell_html(time_zones_label, time_zones_html ) );
        tbody.append( cell_html(user_groups_label, user_groups_html ) );
        tbody.append('<tr><td class="fieldlabel" colspan="2"><b>'+LANG['onappres']+'</b></td></tr>');

    // sliders
        var ram_slider = create_slider_html(ram_html, 12288, 256, 4, 'packageconfigoption[3]');
        var cores_slider = create_slider_html(cores_html, 16, 1, 1, 'packageconfigoption[5]');
        var priority_slider = create_slider_html(priority_html, 100, 1, 1, 'packageconfigoption[7]');
        var disk_slider = create_slider_html(disk_html, 240, 5, 1, 'packageconfigoption[11]');
        var swap_slider = create_slider_html(swap_html, 240, 1, 1, 'packageconfigoption[9]');
        var port_speed_slider = create_slider_html(port_speed_html, 1000, 0, 1, 'packageconfigoption[8]');
        var ip_address_slider = create_slider_html(ipbase_html, 20, 1, 1, 'packageconfigoption[18]');
        var sec_net_port_speed_slider = create_slider_html(sec_net_port_speed_html, 1000, 0, 1, 'sec_net_port_speed');
        var sec_net_ips_slider = create_slider_html(sec_net_ips_html, 200, 0, 1, 'sec_net_ips');

        tbody.append( cell_html(ram_label, ram_slider) );
        tbody.append( cell_html(cores_label, cores_slider) );
        tbody.append( cell_html(priority_label, priority_slider) );
        tbody.append('<tr><td class="fieldlabel" colspan="2"><b>'+LANG['onappprimarydisk']+'</b></td></tr>');
        tbody.append( cell_html(ds_zones_label, ds_zones_primary_html) );
        tbody.append( cell_html(disk_label, disk_slider) );
        tbody.append('<tr><td class="fieldlabel" colspan="2"><b>'+LANG['onappswapdisk']+'</b></td></tr>');
        tbody.append( cell_html(ds_zones_label, ds_zones_swap_html) );
        tbody.append( cell_html(swap_label, swap_slider) );

    // second table
        tbody.append('<tr><td class="fieldlabel" colspan="2"><b>'+LANG['onappnetconfig']+'</b></td></tr>');
        tbody.append( cell_html(networks_label, networks_html) );
        tbody.append( cell_html(port_speed_label, port_speed_slider) );
        tbody.append( cell_html(ipbase_label, ip_address_slider) );

    // third third
        tbody.append('<tr><td class="fieldlabel" colspan="2"><b>'+LANG['onappsecnetconfiguration']+'</b></td></tr>');
        tbody.append( cell_html(sec_net_networks_label, sec_networks_html) );
        tbody.append( cell_html(sec_net_port_speed_label, sec_net_port_speed_slider) );
        tbody.append( cell_html(ipbase_label, sec_net_ips_slider) );

    // forth table
        second_table.after('<br><table class="form" width="100%" border="0" cellspacing="2" cellpadding="3"><tbody></tbody></table>');
        var third_table = $('table').eq(7);
        tbody = third_table.find('tbody');

        tbody.append( cell_html('<b>'+templates_label+'</b>', create_template_filter_html()) );
        tbody.append( cell_html('', templates_html+create_templates_html()) );
        tbody.append( cell_html(ostemplates_label, ostemplates_html ) );
        tbody.append( cell_html(build_auto_label, build_auto_html) );
        tbody.append( cell_html(backups_auto_label, backups_auto_html) );

    // fifth table
        third_table.after('<br><table class="form" width="100%" border="0" cellspacing="2" cellpadding="3"><tbody></tbody></table>');
        var forth_table = $('table').eq(8);
        tbody = forth_table.find('tbody');

        tbody.append('<tr><td class="fieldlabel" colspan="2"><b>'+LANG["onappaddres"]+'</b></td></tr>');
        tbody.append( cell_html(addram_label, addram_html) );
        tbody.append( cell_html(addcores_label, addcores_html) );
        tbody.append( cell_html(addpriority_label, addpriority_html) );
        tbody.append( cell_html(adddisk_label, adddisk_html) );
        tbody.append( cell_html(ip_label, ip_html) );
        tbody.append( cell_html(addport_speed_label, addport_speed_html) );
        tbody.append( cell_html(addbandwidth_label, addbandwidth_html) );
        tbody.append( cell_html(add_sec_net_ips_label, add_sec_net_ips_html) );

// Set selects width
        hvZonesSelect = $("select[name='hvzones']")
        hvZonesSelect.width(selectWidth)
        dsPrimarySelect = $("select[name='ds_zones_primary']")
        dsPrimarySelect.width(selectWidth)
        dsSwapSelect = $("select[name='ds_zones_swap']")
        dsSwapSelect.width(selectWidth)
        timeZoneSelect = $("select[name='time_zone']")
        timeZoneSelect.width(selectWidth)
        $("select[name='user_group']").width(selectWidth)
        $("select[name='billing_plan']").width(selectWidth)
        $("select[name='packageconfigoption[22]']").width(selectWidth)
        $("select[name='sec_net_configurable_option_id']").width(selectWidth)
        $("select[name='sec_network_id']").width(selectWidth)

// Get hypervisor Select HTML
        hvSelectHtml = hvSelect.html()

// set hvzones value if needed
        if ( hvZoneId ) {
            hvZonesSelect.val( hvZoneId )
        }
// set data store swap and primary zone values as needed
        if ( dsPrimarySelected ) {
            dsPrimarySelect.val( dsPrimarySelected )
        }

        if ( dsSwapSelected ) {
            dsSwapSelect.val( dsSwapSelected )
        }
// set time zone value if needed
        if ( timeZoneSelected )
            timeZoneSelect.val( timeZoneSelected )

// disable hypervisor select if needed
       hvSelect = $("select[name$='packageconfigoption[4]']");
        if ( hvZonesSelect.val() == '0' ) {
            hvSelect.val(0).attr('disabled', 'disabled');
        }

// disable hypervisor zones options with no hypervisors
        hvZonesSelect.children().each( function () {
            if ( in_array( $(this).val(), hvZonesArray ) === false &&
                $(this).val() != 0 && $(this).val() != 'no_zone' ) {
                $(this).attr('disabled', 'disabled')
            }
        })

// assign hypervisor zones onChange action
        hvZonesSelect.change( function () {
            deal_hvs()
            deal_networks()
        })

        deal_hvs()

// hide secondary network configuration if configurable additional resources option is not checked

       if ( addSecNetworkIPSelected == 0){
           $('table').eq(6).find('tr').eq(22).hide()
           $('table').eq(6).find('tr').eq(23).hide()
           $('table').eq(6).find('tr').eq(24).hide()
           $('table').eq(6).find('tr').eq(25).hide()
       }

// assign secondary network ip configurable option onChange function
        addSecNetworkIpAddressSelect = $("select[name='sec_net_configurable_option_id']");

        addSecNetworkIpAddressSelect.change( function(){
            if ( addSecNetworkIpAddressSelect.val() != '0' ){
               $('table').eq(6).find('tr').eq(22).show()
               $('table').eq(6).find('tr').eq(23).show()
               $('table').eq(6).find('tr').eq(24).show()
               $('table').eq(6).find('tr').eq(25).show()
               $("select[name='sec_network_id']").focus()
            } else {
               $('table').eq(6).find('tr').eq(22).hide()
               $('table').eq(6).find('tr').eq(23).hide()
               $('table').eq(6).find('tr').eq(24).hide()
               $('table').eq(6).find('tr').eq(25).hide()
            }
        })

// assign os templates addons onChange action
        ostemplatesSelect = $("select[name$='packageconfigoption[19]']");

        ostemplatesSelect.change( function () {
          if ( confirm(LANG['onappyouwantrefreshtemplates']) ) {
              $('#removeAll').click();
              reload_template_from_addon_res();
              selected_tpls();
              osFilter();
              ostemplates = ostemplatesSelect.val();
          } else {
              ostemplatesSelect.val( ostemplates );
          }
        } );

        ostemplates = ostemplatesSelect.val();
    }

// assign server select onChange action
    serverSelect = $("select[name$='packageconfigoption[1]']");

    serverSelect.change( function () {
        check_vars = false;
        form = $("form[name$='packagefrm']");
        form.submit();
    } );

// assign

    $('select[name="packageconfigoption[4]"]').change( function(){
        deal_networks()
    })



    serverSelect.val(serverSelected);

    check_vars = error_msg == "";

    // assign constants for the templates filter
    ALL_SELECTED_TEMPLATES = new Array()
    $("#selected_tpl option").each( function () {
        ALL_SELECTED_TEMPLATES.push( $(this).attr('value') );
    })

    ALL_AVAILABLE_TEMPLATES = new Array()
    $("#available_tpl option").each( function () {
        ALL_AVAILABLE_TEMPLATES.push( $(this).attr('value') );
    })

    ALL_AVAILABLE_HTML = $("#available_tpl").html()

    deal_networks()
});

function checkvars(check_vars) {

    if (! check_vars ) return true;

    input2  = $("input[name$='packageconfigoption[2]']");
    input3  = $("input[name$='packageconfigoption[3]']");
    input5  = $("input[name$='packageconfigoption[5]']");
    input7  = $("input[name$='packageconfigoption[7]']");
    input8  = $("input[name$='packageconfigoption[8]']");
    input9  = $("input[name$='packageconfigoption[9]']");
    input11 = $("input[name$='packageconfigoption[11]']");

    add_selected_tpls();

    if ( input2.val() == "" ) {
        alert(LANG['onappsettemplate']);
    } else if ( parseInt(input3.val()).toString() != input3.val() ) {
        alert(LANG['onappwrongram']);
        input3.focus();
    } else if ( parseInt(input5.val()).toString() != input5.val() ) {
        alert(LANG['onappwrongcpucores']);
        input5.focus();
    } else if ( parseInt(input7.val()).toString() != input7.val() ) {
        alert(LANG['onappwrongcpuprior']);
        input7.focus();
    } else if ( parseInt(input9.val()).toString() != input9.val() ) {
        alert(LANG['onappwrongswap']);
        input9.focus();
    } else if ( parseInt(input11.val()).toString() != input11.val() ) {
        alert(LANG['onappwrongdisksize']);
        input11.focus();
    } else if ( input8.val() != '' && parseInt(input8.val()).toString() != input8.val() ) {
        alert(LANG['onappwrongspead']);
        input8.focus();
    } else {
        return true;
    }

    return false;
}

function cell_html(label, html) {
    return '<tr><td class="fieldlabel" width="150">'+label+'</td><td class="fieldarea">'+html+'</td></tr>';
}

function create_slider_html(input_html, max, min, step, target_id){
    return '<div class="input-with-slider">'+
                 input_html+
            '    <div class="slider" style="float:left; margin:5px 15px 0 5px; width:200px;" max="'+max+'" min="'+min+'" step="'+step+'" target="'+target_id+'" width="200"></div>'+
            '</div>';
}

function create_templates_html(){
    tplHTML =
        '<div>'+
        '   <div class="available_tpl" style="float:left;width:35%;max-width:280px;">'+
        '       <select name="available_tpl[]" id="available_tpl" multiple="multiple" style="height:280px;width:100%;">'+create_available_tpl_otions()+'</select>'+
        '   </div>'+
        '   <div class="pick-buttons" style="float:left; width: 40px; text-align:center;padding:80px 20px">'+
        '       <input type="button" title="Choose available and add" value="&gt;" class="button addButton" name="add" id="add" style="width:30px;" />'+
        '           <br/><br/>'+
        '       <input type="button" title="Choose selected and remove" value="&lt;" class="button removeButton" name="remove" id="remove" style="width:30px;" />'+
        '           <br/><br/>'+
        '       <input type="button" title="Add all" value="&gt;&gt;" class="button addAllButton" name="addAll" id="addAll" style="width:30px;" />'+
        '           <br/><br/>'+
        '       <input type="button" title="Remove all" value="&lt;&lt;" class="button removeAllButton" name="removeAll" id="removeAll" style="width:30px;" />'+
        '   </div>'+
        '   <div class="selected_tpl" style="float:left;width:35%;max-width:280px;">'+
        '       <select name="selected_tpl[]" id="selected_tpl" multiple="multiple" style="height:280px;width:100%;"></select>'+
        '   </div>'+
        '   <div class="clear"></div>'+
        '</div>';

    return tplHTML
}

function create_template_filter_html(){
    tplHTML =
        '<div>'+
        '   <div class="filter_tpl">'+
        '       <select name="filter_tpl" id="filter_tpl" style="width:280px;">'+create_filter_tpl_otions()+'</select>'+
        '   </div>'+
        '</div>';

    return tplHTML
}

function create_available_tpl_otions(){
    selectHTML = '';
    for (var option in templateOptions['k']){
            selectHTML += '<option class="'+OSByTemplateId[templateOptions['k'][option]]+'" value="'+templateOptions['k'][option]+'">'+templateOptions['v'][option]+'</option>';
    }

    return selectHTML;
}

function create_filter_tpl_otions() {
    selectHTML = '<option value="all">All</option>';
    for (var os in OSlist){
       selectHTML += '<option value="'+OSlist[os]+'">'+OSlist[os].replace('_', ' - ')+'</option>';
    }

    return selectHTML;
}

function osFilter() {
    var os = $("#filter_tpl").val();

    $("#available_tpl").html(ALL_AVAILABLE_HTML)
    $("#selected_tpl").html(null)

    $("#available_tpl option").each(function(){
         if(in_array($(this).attr('value'), ALL_SELECTED_TEMPLATES ))
            $(this).attr('selected', 'selected');
    })

    $("#add").click()

    if( os == 'all') {return true}

    $("#available_tpl option[class!="+os+"]").each(function(){
       $(this).remove()
    });
    $("#selected_tpl option[class!="+os+"]").each(function(){
        $(this).remove()
    });

    return true;
}

function add_selected_tpls(){
    $("input[name$='packageconfigoption[2]']").val(ALL_SELECTED_TEMPLATES.join());
}

function in_array(needle, haystack){
    for(var i=0; i<haystack.length; i++)
        if(needle == haystack[i])
            return true;
    return false;
}

function reload_template_from_addon_res() {
    var confsub = $("select[name$='packageconfigoption[19]']").val();

    $("input[name$='packageconfigoption[2]']").val(
        confsub == 0 ? '' : configOptionsSub[confsub]
    );
}

function get_saved_tpls(){
    return $("input[name$='packageconfigoption[2]']").val().split(',');
}

function selected_tpls_after_page_load () {

}

function selected_tpls(){
    var saved_tpls = get_saved_tpls();

    $("#removeAll").click();

    $("#available_tpl option").each(function(){
        if(in_array($(this).val(), saved_tpls))
            $(this).attr('selected', 'selected');
    });

    $("#add").trigger('click');

    $("#filter_tpl").val('all');
    $("#filter_tpl").attr('disabled', $("select[name$='packageconfigoption[19]']").val() != '0');

    $("input[name$='add']").attr('disabled', $("select[name$='packageconfigoption[19]']").val() != '0');
    $("input[name$='remove']").attr('disabled', $("select[name$='packageconfigoption[19]']").val() != '0');
    $("input[name$='addAll']").attr('disabled', $("select[name$='packageconfigoption[19]']").val() != '0');
    $("input[name$='removeAll']").attr('disabled', $("select[name$='packageconfigoption[19]']").val() != '0');
}

function check_autobuild(){
    var selected_count = 0;
    var autobuild = $("input[name='autobuild']");
    var confsub = $("select[name$='packageconfigoption[19]']").val();
    $("#selected_tpl option").each(function(){
        if ($(this).css('display') != 'none')
           selected_count++;
    });

    if ( selected_count == 1 || confsub != 0 ) {
        autobuild.attr('disabled', false);
    } else {
        autobuild.attr('disabled', true);
    }
}

function after_add() {
    $("#selected_tpl option").each( function () {
       if ( ! in_array( $(this).attr('value'), ALL_SELECTED_TEMPLATES ) ) {
           ALL_SELECTED_TEMPLATES.push($(this).attr('value') )

           var idx = jQuery.inArray( $(this).attr('value'), ALL_AVAILABLE_TEMPLATES );
           if ( idx != -1 ) {
               ALL_AVAILABLE_TEMPLATES.splice( idx, 1 )
           }
       }
    })

    check_autobuild();
}

function after_remove(){
    $("#available_tpl option").each( function () {
       if ( ! in_array( $(this).attr('value'), ALL_AVAILABLE_TEMPLATES ) ) {
           ALL_AVAILABLE_TEMPLATES.push($(this).attr('value') )

           var idx = jQuery.inArray( $(this).attr('value'), ALL_SELECTED_TEMPLATES );
           if ( idx != -1 ) {
               ALL_SELECTED_TEMPLATES.splice( idx, 1 )
           }
       }
    })

    osFilter();
    check_autobuild();
}

function add_build_options() {
    var autobuild   = $("input[name$='autobuild']").is(':checked') ? 'on' : '0'
    var autobackups = $("input[name$='autobackups']").is(':checked') ? 'on' : '0'

    var html =
         '<input type="hidden" value="'+autobuild+','+autobackups+'" name="packageconfigoption[10]"/>'
    var parent = hvSelect.parent()
    parent.append(html)
}

function add_hv_zone() {
      //if ( hvSelect.val() == 0 && hvZonesSelect.val() != 'no_zone' ) {
          var html =
              '<input type="hidden" value="'+hvSelect.val()+','+hvZonesSelect.val()+'" name="packageconfigoption[4]"/>'
          var parent = hvSelect.parent()
          hvSelect.attr('name', 'renamed');
          parent.append(html)
      //}
}

function add_ds_zone( checkresult ) {
      input9 = $("input[name$='packageconfigoption[9]']")
      input11 = $("input[name$='packageconfigoption[11]']")
      var parent = input9.parent()
      var html

      if ( dsPrimarySelect.val() != 0 ) {
          html =
              '<input type="hidden" value="'+input11.val()+','+dsPrimarySelect.val()+'" name="packageconfigoption[11]"/>'
          input11.attr('name', 'renamed')
          parent.append( html )
      }
      if ( dsSwapSelect.val() != 0 ) {
          html =
              '<input type="hidden" value="'+input9.val()+','+dsSwapSelect.val()+'" name="packageconfigoption[9]"/>'
          input9.attr('name', 'renamed')
          parent.append( html )
      }
      return ( ! checkresult ) ? false : true
}

function deal_hvs () {
    var zone =  hvZonesSelect.val()
        hvSelect.html(hvSelectHtml)
        if ( zone != '0' ) {
            hvSelect.removeAttr('disabled')

            if ( hvZonesSelect.val() != 'no_zone' ) {
                hvSelect.children().each( function () {
                    if ( zone != $(this).attr('zone') && $(this).attr('zone') != 'autoselect' ) {
                        $(this).remove()
                    }
                })
            }
        }
        else {
            hvSelect.val('0')
            hvSelect.attr('disabled', 'disabled');
        }
}

function add_user_info () {
    var form = $("form[name$='packagefrm']");
    var user_group = $("select[name='user_group']").val()
    var role_ids   = $("input[name='role_ids']:checked").map( function(){ return this.value} ).get()
    var time_zone  = $("select[name='time_zone']").val()
    var billing_plan = $("select[name='billing_plan']").val()

    var user_info = '{"role_ids":['+ role_ids +'], "user_group":'+ user_group +
        ', "time_zone":"'+ time_zone +'", "billing_plan":"'+ billing_plan +'"}'
    var html =
        "<input type='hidden' value='"+ user_info +"' name='packageconfigoption[21]'/>"

    form.append(html)
}

function primary_is_equal_secondary(){
    if( $('select[name="sec_network_id"]').val() == $('select[name="packageconfigoption[6]"]').val() ){
        return true
    }

    return false
}

function add_secondary_network_info () {
    var form = $("form[name$='packagefrm']");
    var f = []
    var html

    f.sec_net_ips                    = $("input[name='sec_net_ips']").val()
    f.sec_network_id                 = $("select[name='sec_network_id']").val()
    f.sec_net_port_speed             = $("input[name='sec_net_port_speed']").val()
    f.sec_net_configurable_option_id = $("select[name='sec_net_configurable_option_id']").val()

    if ( f.sec_net_configurable_option_id == 0 ) {
        html = "<input type='hidden' value='' name='packageconfigoption[23]'/>"
    } else {
        if( ! if_hv_or_hv_zone_set() ) {
            alert( LANG['onappsecondarynetworkhvzonenotseterror'] )
            $('select[name="hvzones"]').focus()
            return false
        }

//        if( primary_is_equal_secondary() ) {
//            alert( LANG['onappprimaryhavetodiffersecondary'] )
//            $('select[name="sec_network_id"]').focus()
//            return false
//        }

        var configurations = '{'

        for ( var i in f ) {
            configurations += '"'+i+'":"'+f[i]+'", '
        }

        configurations = configurations.replace(/,\s$/, '}');
        html = "<input type='hidden' value='"+ configurations +"' name='packageconfigoption[23]'/>"
    }

    form.append(html)

    return true
}

function if_hv_or_hv_zone_set() {
    var hvzoneid = $('select[name="hvzones"]').val()
    var hvid     = $('select[name="packageconfigoption[4]"]').val()

    if ( ( hvzoneid == 'no_zone' || hvzoneid == '0' ) && ( hvid == '0' || typeof hvid == 'undefined' ) ) {
        return false
    }

    return true
}

function get_networks() {
    var hv_zone_id = $('select[name="hvzones"]').val()
    var hv_id = $('select[name="packageconfigoption[4]"]').val()

    if ( typeof hv_id == 'undefined' ) {
        hv_id = $('select[name="renamed"]').val()
    }

    if ( typeof networksByHypervisorZone[hv_zone_id] == 'undefined' ) {
        networksByHypervisorZone[hv_zone_id] = []
    }
    if ( typeof networksByHypervisor[hv_id] == 'undefined' ) {
        networksByHypervisor[hv_id] = []
    }

    return networksByHypervisorZone[hv_zone_id].concat(networksByHypervisor[hv_id])
}

function deal_networks ( ) {
    var $nets   = get_networks()
    var hv_zone_id = $('select[name="hvzones"]').val()
    var hv_id = $('select[name="packageconfigoption[4]"]').val()

    var options = $('select[name="sec_network_id"] option, select[name="packageconfigoption[6]"] option')

    if ( ( hv_zone_id == 'no_zone' || hv_zone_id == '0' ) && ( typeof hv_id == 'undefined' || hv_id == '0' )){
        options.each( function(){
            $(this).removeAttr("disabled")
        })
    } else {
        options.each( function(){
            if ( ! in_array( $(this).val(), $nets ) ){
                $(this).attr('disabled', 'disabled')
            } else {
                $(this).removeAttr('disabled')
            }
        })
    }
}

$(function() {
    if ($("#available_tpl").length && $("#selected_tpl").length) {
        $("#available_tpl").multiSelect("#selected_tpl", {trigger: "#add", triggerAll: "#addAll", sortOptions: false, autoSubmit: false, afterMove: after_add});
        $("#selected_tpl").multiSelect("#available_tpl", {trigger: "#remove", triggerAll: "#removeAll", sortOptions: false, autoSubmit: false, afterMove: after_remove});

        $("#filter_tpl").change( function(){
            osFilter();
        })
        selected_tpls()
    }
});
