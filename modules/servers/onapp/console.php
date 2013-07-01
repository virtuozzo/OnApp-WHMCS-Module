<?PHP

function getSQLResult($sql) {
    return mysql_fetch_array(
        full_query($sql)
    );
}

    require_once dirname(__FILE__).'/../../../dbconnect.php';
    require_once dirname(__FILE__).'/../../../includes/functions.php';
    require_once dirname(__FILE__).'/lib.php';
    require_once dirname(__FILE__).'/../../../includes/wrapper/OnAppInit.php';

    session_start();
    $user_id = $_SESSION["uid"];
    $vm_id   = $_GET["id"];

// Check VM access
    $sql = sprintf("SELECT
        service_id
    FROM
        tblonappservices
        LEFT JOIN tblhosting ON tblhosting.id = service_id
    WHERE
        userid = '%s'
        AND vm_id = '%s';",
        stripcslashes($user_id),
        stripcslashes($vm_id));

    $sql_result = getSQLResult($sql);

    if (! isset($sql_result["service_id"]) )
        die("Access denied to this Console");
    else
        $service_id = $sql_result["service_id"];

    unset($sql);
    unset($sql_result);

// Load VM server id

    $service      = get_service( $service_id );
    $onapp_config = get_onapp_config( $service['serverid'] );
    $user         = get_onapp_client( $service_id );

    if ( ! $onapp_config )
        die("Can't found active OnApp server #".addslashes($server_id)." in Data Base");

// Load VM

    $vm = new OnApp_VirtualMachine();

    $vm->auth(
        $onapp_config["adress"],
        $user["email"],
        $user["password"]
    );

    $vm->_id = $vm_id;

    $vm->load();

// Load console

    $console = new OnApp_Console();

    $console->auth(
        $onapp_config["adress"],
        $user["email"],
        $user["password"]
    );

    $console->load($vm_id);

    $url = ( ($onapp_config["hostname"]) ? $onapp_config["hostname"] : $onapp_config["adress"]  ). "/console_remote/".$console->_obj->_remote_key;

    if ( strpos( $url, 'http' ) === false && strpos( $url, 'http' ) === false ) {
        $url = 'http://'.$url;
    }

    header( "Location: $url" ) ;
