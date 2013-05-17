var init_sliders = function() {
    $(".slider").each(function(index, object) {
        var slider = $(object).slider();
        var attr = slider.attr("target");

        var target = $('input[name="'+attr+'"]');

        if (max = slider.attr("max"))
            slider.slider("option", "max", parseInt(max.toString()));

        if (min = slider.attr("min"))
            slider.slider("option", "min", parseInt(min.toString()));

        if (step = slider.attr("step"))
            slider.slider("option", "step", parseInt(step.toString()));

        slider.slider("option", "orientation", 'horizontal');

        if (target.length) {
            target.interval = null;
            slider.interval = null;

            slider.slider( "option", "value", parseInt(target.attr("value").toString()) );

            var update_target = function() {
                update_swap_disk_size();
                target.attr("value", parseInt(slider.slider("value").toString()) || 0);
            }

            var update_slider = function() {
                update_swap_disk_size();
                slider.slider("value", parseInt(target.attr("value").toString()) || 0);
            }

            slider.bind("slidestart", function() {
                slider.interval = setInterval(update_target, 200);
                update_target();
            });

            slider.bind("slidestop", function() {
                update_swap_disk_size();
                clearInterval(slider.interval);
                slider.interval = null;
            });

            slider.bind("slidechange", function() {
                if (target.interval == null) update_target();
            });

            target.bind("focusin", function() {
                target.interval = setInterval(update_slider, 300);
            });

            target.bind("focusout", function() {
                target.attr("value", parseInt(slider.slider("value").toString()) || 0);
                clearInterval(target.interval);
                target.interval = null;
           });
        }
  });
}

var update_swap_disk_size = function(){
    var configoption9  = "packageconfigoption\[9\]"
    var configoption11 = "packageconfigoption\[11\]"

    var max_disk_size = $('div[target="'+configoption11+'"]').slider( "option", "max" );

    var current_disc_size = parseInt($('input[name="'+configoption11+'"]').attr("value"));

    var max_swap_size = max_disk_size - current_disc_size;

    $('div[target="'+configoption9+'"]' ).slider( "option", "max", max_swap_size );

    if(max_swap_size < parseInt($('input[name="'+configoption9+'"]').attr("value"))){
        $('input[name="'+configoption9+'"]').attr("value", max_swap_size);
        $('div[target="'+configoption9+'"]').slider( "option", "value", max_swap_size);
    }
}

var disable_inputs = function(){
    $('.input-with-slider input[name^="packageconfigoption"]').each(function(){
        $(this).attr('readonly', 'readonly');
    });
}

$(document).ready(function() {
//    disable_inputs(); 
    init_sliders();

    try {
        $('a[rel*=facebox]').facebox({
            loading_image : '../modules/servers/onapp/includes/loading.gif',
           close_image   : '../modules/servers/onapp/includes/closelabel.gif'
        });
    } catch (ex) { }
});
