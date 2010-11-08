<?PHP

function getSQLResult($sql) {
    return mysql_fetch_array(
        full_query($sql)
    );
}

    require_once dirname(__FILE__).'/../../../dbconnect.php';
    require_once dirname(__FILE__).'/wrapper/VirtualMachine.php';
    require_once dirname(__FILE__).'/wrapper/Console.php';

    session_start(); 

    $user_id = $_SESSION["uid"];
    $vm_id   = $_GET["id"];

// Check VM access

    $sql = "SELECT 
        service_id 
    FROM 
        tblonappservices 
        LEFT JOIN tblhosting ON tblhosting.id = service_id 
    WHERE 
        userid = '$user_id' 
        AND vm_id = '$vm_id';";

    $sql_result = getSQLResult($sql);

    if (! isset($sql_result["service_id"]) )
        die("Access denied to this Console");
    else 
        $service_id = $sql_result["service_id"];

    unset($sql);
    unset($sql_result);

// Load VM server id

    $sql =  "SELECT 
        tblhosting.id as id,
        tblonappservices.vm_id as vm_id,
        tblproducts.configoption1 as serverid
    FROM 
        tblhosting 
        LEFT JOIN tblproducts ON tblproducts.id = packageid 
        LEFT JOIN tblonappservices ON service_id = tblhosting.id
    WHERE
        tblhosting.id = '$service_id'";

    $sql_result = getSQLResult($sql);

    if (! $sql_result || ! $sql_result['serverid'] || $sql_result['serverid'] == 0 )
        die("Can't find Virtual Machine in WHMCS data base");
    else 
        $server_id = $sql_result['serverid'];

    unset($sql);
    unset($sql_result);

// Load server config

    $sql = "SELECT id, name, ipaddress, hostname, username, password FROM tblservers WHERE id = " . addslashes( $server_id );

    $onapp_config = getSQLResult($sql);

    if ( $onapp_config ) {
        $onapp_config["adress"] = $onapp_config["ipaddress"] != "" ? $onapp_config["ipaddress"] : $onapp_config["hostname"];
        $onapp_config[] = $onapp_config["adress"];
   } else
        die("Can't found active OnApp server #".addslashes($server_id)." in Data Base");

    if ( ! $onapp_config["adress"] ) 
        die("OnApp server adress (IP and hostname) not set for #".$onapp_config["id"]." '".$onapp_config["name"]."'" );

    unset($sql);

// Load user access in to OnApp server

    $sql = "SELECT onapp_user_id, email, password FROM tblonappclients WHERE client_id = $user_id AND server_id = $server_id";

    $user = getSQLResult($sql);

    if ( ! $user["onapp_user_id"] )
        die("User do not have access on OnApp server" );

// Load VM

    $vm = new ONAPP_VirtualMachine();

    $vm->auth(
        $onapp_config["adress"],
        $user["email"],
        $user["password"]
    );

    $vm->_id = $vm_id;

    $vm->load();

// Load console

    $console = new ONAPP_Console();

    $console->auth(
        $onapp_config["adress"],
        $user["email"],
        $user["password"]
    );

    $console->load($vm->_id);

    $url="http://".$onapp_config["adress"]."/console_remote/".$console->_obj->_remote_key;

    header( "Location: $url" ) ;
?>
