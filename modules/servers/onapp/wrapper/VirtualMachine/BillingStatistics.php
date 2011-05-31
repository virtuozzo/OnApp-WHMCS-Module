<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * User IP Adresses
 *
 * @todo write description
 *
 * @category  API WRAPPER
 * @package   ONAPP
 * @author    Vitaliy Kondratyuk
 * @copyright 2010 / OnApp
 * @link      http://www.onapp.com/
 * @see       ONAPP
 */

/**
 * requires Base class
 */
require_once dirname( __FILE__ ) . '/../ONAPP.php';

/**
 * User IP Billing Statistics
 *
 * The ONAPP_VirtualMachine_BillingStatistics class uses the following basic methods:
 * {@link getList}.
 *
 */
class ONAPP_VirtualMachine_BillingStatistics extends ONAPP {

    /**
     * the Billing Statistic creation date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_created_at;

    /**
     * Price
     *
     * @var float
     */
    var $_cost;

    /**
     * the Billing Statistics update date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_updated_at;

    /**
     * the Billing Statistics start date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_stat_time;

    /**
     * the vm stats ID
     *
     * @var integer
     */
    var $_id;

    /**
     * User ID 
     *
     * @var integer
     */
    var $_user_id;

    /**
     * Virtual Machine Billing stat ID 
     *
     * @var integer
     */
    var $_vm_billing_stat_id;

    /**
     * Virtual Machine ID
     *
     * @var integer
     */
    var $_virtual_machine_id;

     /**
     * Billing stat info
     *
     * @var array of objects
     */
    var $_billing_stats;

    /**
     * root tag used in the API request
     *
     * @var string
     */
    var $_tagRoot = 'vm_stat';

    /**
     * alias processing the object data
     *
     * @var string
     */
    var $_resource = 'vm_stats';

    /**
     *
     * called class name
     *
     * @var string
     */
    var $_called_class = 'ONAPP_VirtualMachine_BillingStatistics';

    /**
     * API Fields description
     *
     * @access private
     * @var    array
     */
    function _init_fields( $version = NULL ) {
        if( !isset( $this->options[ ONAPP_OPTION_API_TYPE ] ) || ( $this->options[ ONAPP_OPTION_API_TYPE ] == 'json' ) ) {
            $this->_tagRoot = 'vm_stats';
        }

        if( is_null( $version ) ) {
            $version = $this->_version;
        }

        switch( $version ) {
            case '2.0':
            case '2.1':
                $this->_fields = array(
                    'created_at' => array(
                        ONAPP_FIELD_MAP => '_created_at',
                        ONAPP_FIELD_TYPE => 'datetime',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'cost' => array(
                        ONAPP_FIELD_MAP => '_cost',
                        ONAPP_FIELD_TYPE => 'float',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'updated_at' => array(
                        ONAPP_FIELD_MAP => '_updated_at',
                        ONAPP_FIELD_TYPE => 'datetime',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'stat_time' => array(
                        ONAPP_FIELD_MAP => '_stat_time',
                        ONAPP_FIELD_TYPE => 'datetime',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'id' => array(
                        ONAPP_FIELD_MAP => '_id',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'user_id' => array(
                        ONAPP_FIELD_MAP => '_user_id',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'vm_billing_stat_id' => array(
                        ONAPP_FIELD_MAP => '_vm_billing_stat_id',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'virtual_machine_id' => array(
                        ONAPP_FIELD_MAP => '_virtual_machine_id',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'billing_stats' => array(
                        ONAPP_FIELD_MAP => '_billing_stats',
                        ONAPP_FIELD_TYPE => 'string',
                        ONAPP_FIELD_READ_ONLY => true,
                    )
                );

                break;
        }

        return $this->_fields;
    }

    /**
     * Returns the URL Alias of the API Class that inherits the Class ONAPP
     *
     * @param string $action action name
     *
     * @return string API resource
     * @access public
     */
    function getResource( $action = ONAPP_GETRESOURCE_DEFAULT ) {
        switch( $action ) {
            case ONAPP_GETRESOURCE_DEFAULT:
                if( is_null( $this->_virtual_machine_id ) && is_null( $this->_obj->_virtual_machine_id ) ) {
                    $this->_loger->error(
                        "getResource($action): argument _virtual_machine_id not set.",
                        __FILE__,
                        __LINE__
                    );
                }
                else {
                    if( is_null( $this->_virtual_machine_id ) ) {
                        $this->_virtual_machine_id = $this->_obj->_virtual_machine_id;
                    }
                }
                ;
                $resource = 'virtual_machines/' . $this->_virtual_machine_id . '/' . $this->_resource;
                $this->_loger->debug( "getResource($action): return " . $resource );
                break;

            default:
                $resource = parent::getResource( $action );
                break;
        }

        return $resource;
    }

    /**
     * Sends an API request to get the Objects. After requesting,
     * unserializes the received response into the array of Objects
     *
     * @param integer $virtual_machine_id User ID
     *
     * @return mixed an array of Object instances on success. Otherwise false
     * @access public
     */
    function getList( $virtual_machine_id = null ) {

        if( is_null( $virtual_machine_id ) && !is_null( $this->_virtual_machine_id ) ) {
            $virtual_machine_id = $this->_virtual_machine_id;
        }

        if( !is_null( $virtual_machine_id ) ) {
            $this->_virtual_machine_id = $virtual_machine_id;

            return parent::getList( );
        }
        else {
            $this->_loger->error(
                'getList: argument _virtual_machine_id not set.',
                __FILE__,
                __LINE__
            );
        }
    }


    /**
     * Activates action performed with object
     *
     * @param string $action_name the name of action
     *
     * @access public
     */
    function activate( $action_name ) {
        switch( $action_name ) {
            case ONAPP_ACTIVATE_LOAD:
            case ONAPP_ACTIVATE_SAVE:
            case ONAPP_ACTIVATE_DELETE:
                die( "Call to undefined method " . __CLASS__ . "::$action_name()" );
                break;
        }
    }
}
