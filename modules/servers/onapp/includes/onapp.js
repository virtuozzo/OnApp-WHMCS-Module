$(document).ready(function(){
// form submit action
    form = $("form[name$='packagefrm']");

    form.submit(function() {
        return checkvars(check_vars);
    });

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
    hvSelected = hvSelect.val();

    selectHTML = '';
    for ( option in hvOptions ) {
        selected = (option == hvSelected) ? ' selected="selected"' : '';
        selectHTML += '<option value="'+option+'"'+selected+'>'+hvOptions[option]+'</option>';
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
    var build_auto_html  = tr.find('td').eq(3).html();

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

// remove row
    tr.remove();

// tables
    var tbody = table.find('tbody');
    tbody.append( cell_html(servers_label, servers_html) );
    tbody.append( cell_html(hypervisors_label, hypervisors_html) );

    if ( error_msg != "" ) {
        table.after( '<br/>'+error_msg+'<br/>' );
        check_vars = false;
    } else {

    // first table
        table.after('<br><table class="form" width="100%" border="0" cellspacing="2" cellpadding="3"><tbody></tbody></table>');
        var second_table = $('table').eq(6);
        tbody = second_table.find('tbody');

        tbody.append('<tr><td class="fieldlabel" colspan="2"><b>'+LANG['onappres']+'</b></td></tr>');

    // sliders
        var ram_slider = create_slider_html(ram_html, 8192, 256, 4, 3);
        var cores_slider = create_slider_html(cores_html, 4, 1, 1, 5);
        var priority_slider = create_slider_html(priority_html, 100, 1, 1, 7);
        var disk_slider = create_slider_html(disk_html, 240, 0, 1, 11);
        var swap_slider = create_slider_html(swap_html, 240, 0, 1, 9);
        var port_speed_slider = create_slider_html(port_speed_html, 1000, 0, 1, 8);
        var ip_address_slider = create_slider_html(ipbase_html, 20, 1, 1, 18);

        tbody.append( cell_html(ram_label, ram_slider) );
        tbody.append( cell_html(cores_label, cores_slider) );
        tbody.append( cell_html(priority_label, priority_slider) );
        tbody.append( cell_html(disk_label, disk_slider) );
        tbody.append( cell_html(swap_label, swap_slider) );

    // second table
        tbody.append('<tr><td class="fieldlabel" colspan="2"><b>'+LANG['onappnetconfig']+'</b></td></tr>');
        tbody.append( cell_html(networks_label, networks_html) );
        tbody.append( cell_html(port_speed_label, port_speed_slider) );
        tbody.append( cell_html(ipbase_label, ip_address_slider) );

    // third table
        second_table.after('<br><table class="form" width="100%" border="0" cellspacing="2" cellpadding="3"><tbody></tbody></table>');
        var third_table = $('table').eq(7);
        tbody = third_table.find('tbody');

        tbody.append( cell_html('<b>'+templates_label+'</b>', create_template_filter_html()) );
        tbody.append( cell_html('', templates_html+create_templates_html()) );
        tbody.append( cell_html(ostemplates_label, ostemplates_html ) );
        tbody.append( cell_html(build_auto_label, build_auto_html) );

    // forth table
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
//        tbody.append( cell_html(backup_label, backup_html) );

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
    
});

function checkvars(check_vars) { //console.log('checkvars function')

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
            '    <div class="slider" style="float:left; margin:5px 15px 0 5px; width:200px;" max="'+max+'" min="'+min+'" step="'+step+'" target="packageconfigoption['+target_id+']" width="200"></div>'+
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
    for (var os in templateOptions){
       var os_arr = templateOptions[os];
       for ( var option in os_arr)
            selectHTML += '<option class="'+os+'" value="'+option+'">'+os_arr[option]+'</option>';
    }

    return selectHTML;
}

function create_filter_tpl_otions() {
    selectHTML = '<option value="all">All</option>';
    for (var os in templateOptions){
       selectHTML += '<option value="'+os+'">'+os.replace('_', ' - ')+'</option>';
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
    var autobuild = $("input[name$='packageconfigoption[10]']");
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

           var idx = ALL_AVAILABLE_TEMPLATES.indexOf( $(this).attr('value') )
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

           var idx = ALL_SELECTED_TEMPLATES.indexOf( $(this).attr('value') )
           if ( idx != -1 ) {
               ALL_SELECTED_TEMPLATES.splice( idx, 1 )
           }
       }
    })

    osFilter();
    check_autobuild();
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
