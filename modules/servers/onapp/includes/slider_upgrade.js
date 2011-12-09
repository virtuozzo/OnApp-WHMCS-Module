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

            var update_target = function() {
                target.attr("value", parseInt(slider.slider("value").toString()) || 0);
            }

            var update_slider = function() {
                slider.slider("value", parseInt(target.attr("value").toString()) || 0);
            }

            slider.bind("slidestart", function() {
                slider.interval = setInterval(update_target, 200);
                update_target();
            });

            slider.bind("slidestop", function() {
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

           update_slider();
        }
  });
}

$(document).ready(function() {
    init_sliders();
    $('input[name^="configoption"]').each(function(){$(this).removeAttr('readonly')})
});
