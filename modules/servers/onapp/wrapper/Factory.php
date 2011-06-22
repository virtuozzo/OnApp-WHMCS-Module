<?php
/**
 * API Factory Wrapper for OnApp
 *
 * This API provides an interface to onapp.com allowing common virtual machine
 * and account management tasks
 *
 * @category  Factory
 * @package   ONAPP
 * @author    Andrew Yatskovets
 * @copyright 2010 / OnApp
 * @link      http://www.onapp.com/
 */
require_once "ONAPP.php";

class ONAPP_Factory{

    public $instance;

    /**
     * Constructor
     *
     * @param string $hostname 
     * @param string $username 
     * @param string $password
     * @param string $proxy
     *
     * @return is autorized
     */
    public function __construct( $hostname, $username, $password, $proxy = null ){

        $this->instance = new ONAPP();

        $this->instance->auth(
            $hostname,
            $username,
            $password
        );

        return $this->instance->_is_auth;
    }

    /**
     * Craft new object
     *
     * @param string $name
     *
     * @return object
     */
    public function factory( $name, $debug = false ) {
        $class_name = "ONAPP_$name";

        $file_name  = dirname(__FILE__)."/".str_replace("_","/",$name).".php";

        if (! class_exists($class_name) && file_exists($file_name) )
            require_once $file_name;
        elseif (! file_exists($file_name) )
            die("File $file_name doesn't exist\n");

        if ( class_exists($class_name) ) {
	    $result = new $class_name();

            $result->_loger = new ONAPP_Logger;

            $result->_loger->setDebug( $debug );
            
            $result->setOption(ONAPP_OPTION_DEBUG_MODE, $debug);

            $result->_loger->setTimezone( );

            $result->_version = $this->instance->_version;
            $result->options = $this->instance->options;
            $result->_ch     = $this->instance->_ch;

            $result->_init_fields( $this->instance->_version );

            return $result;
        } else
            die("Class '$class_name' doesn't declared and can't be loaded so does it's factory\n");
    }
}

?>
