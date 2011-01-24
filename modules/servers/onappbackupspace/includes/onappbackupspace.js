$(document).ready(function(){
// replace add data into selectors using option values
    selectWidth = '240px';

    serverSelect = $("select[name$='packageconfigoption[1]']");
    serverSelected = serverSelect.val();
    serverSelect.width(selectWidth);

    selectHTML = '';
    for ( var option in serverOptions ) {
        selected = (option == serverSelected) ? ' selected="selected"' : '';
        selectHTML += '<option value="'+option+'"'+selected+'>'+serverOptions[option]+'</option>';
    }
    serverSelect.html(selectHTML);
    serverSelect.val(serverSelected);
    serverSelect.width(selectWidth);

    addSpaceSelect = $("select[name$='packageconfigoption[3]']");
    addSpaceSelected = addSpaceSelect.val();
    addSpaceSelect.width(selectWidth);
    selectHTML = '';
    for ( var option in configOptions ) {
        selected = (option == addSpaceSelected) ? ' selected="selected"' : '';
        selectHTML += '<option value="'+option+'"'+selected+'>'+configOptions[option]+'</option>';
    }
    addSpaceSelect.html(selectHTML);

// get base table
    var table = $('table').eq(5);

    var tr = table.find('tr').eq(0);

// get servers
    var servers_label = tr.find('td').eq(0).html();
    var servers_html  = tr.find('td').eq(1).html();

// get space
    var space_label = tr.find('td').eq(2).html();
    var space_html  = tr.find('td').eq(3).html();

// remove row
    tr.remove();

    var tr = table.find('tr').eq(0);

// get space config options
    var config_label = tr.find('td').eq(0).html();
    var config_html  = tr.find('td').eq(1).html();

// remove row
    tr.remove();

// tables
    var tbody = table.find('tbody');
    tbody.append( cell_html(servers_label, servers_html) );

//    var space_slider = create_slider_html(space_html, 20, 1, 2, 3);
    tbody.append( cell_html(space_label, space_html) );

    tbody.append( cell_html(config_label, config_html) );
});

function cell_html(label, html) {
    return '<tr><td class="fieldlabel" width="150">'+label+'</td><td class="fieldarea">'+html+'</td></tr>';
};

function create_slider_html(input_html, max, min, step, target_id) {
    return '<div class="input-with-slider">'+
                 input_html+
            '    <div class="slider" style="float:left; margin:5px 15px 0 5px; width:200px;" max="'+max+'" min="'+min+'" step="'+step+'" target="packageconfigoption['+target_id+']" width="200"></div>'+
            '</div>';
}
