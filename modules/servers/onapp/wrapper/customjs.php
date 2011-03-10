<?
require("../../../../dbconnect.php");
require("../../../../includes/functions.php");

$planid   = $_GET['id'];

//db hole
$sqlselect = "select * from tblproducts where id = '$planid'";
$rows = full_query($sqlselect);
$plan = mysql_fetch_assoc($rows);
if($plan) {
    $configids = array(
        $plan["configoption12"], // Additional RAM
        $plan["configoption13"], // Additional CPU Cores
        $plan["configoption14"], // Additional CPU Priority
        $plan["configoption15"], // Additional Primary Disk Size
        $plan["configoption16"], // IP Address
        $plan["configoption20"] // Additional Port Speed
    );
    $JS = "";

    if( $configids )
        foreach($configids as $config) {
            $JS .= "
    var config = $('input[name$=\"configoption\[$config\]\"]');

    // get select if input not found
    if(config.length == 0)
        var config = $('select[name$=\"configoption\[$config\]\"]');

    // hide if config option found
    if(config.length == 1)
        config.parent().parent().hide();
";
        };

    echo "
$(document).ready(function(){
$JS
});";
}

?>
