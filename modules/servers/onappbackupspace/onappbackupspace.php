<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

function onappbackupspace_ConfigOptions() {

    $configarray = array(
        "Additional Resource" => array(
            "Type" => "dropdown",
            "Options" => '',
            "Description" => "",
        ),
        "&nbsp;" => array(
            "Type" => "",
            "Description" => "&nbsp;\n",
        ),
    );

    return $configarray;
}

?>
