<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 *
 * The CPU utilization for Virtual Machine
 *
 * @category  API WRAPPER
 * @package   ONAPP
 * @author    Vitaliy Kondratyuk
 * @copyright 2010 / OnApp
 * @link      http://www.onapp.com/
 * @see       ONAPP
 */

/**
 * require Base class
 */
require_once dirname( __FILE__ ) . '/../ONAPP.php';

/**
 * The CPU utilization for Virtual Machine
 *
 * The ONAPP_VirtualMachine_CpuUsage class uses the following basic methods:
 * {@link getList}.
 *
 * <b>Use the following XML API requests:</b>
 *
 * Get the list of CPU Usages
 *
 *     - <i>GET onapp.com/virtual_machines/{VM_ID}/cpu_usage.xml</i>
 *
 * <b>Use the following JSON API requests:</b>
 *
 * Get the list of CPU Usages
 *
 *     - <i>GET onapp.com/virtual_machines/{VM_ID}/cpu_usage.json</i>
 */
class ONAPP_VirtualMachine_CpuUsage extends ONAPP {

    /**
     * ID
     *
     * @var integer
     */
    var $_id;

    /**
     * the Cpu Usage creation date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_created_at;

    /**
     * the Cpu Usage update date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_updated_at;

    /**
     * @todo: Add description
     *
     * @var string
     */
    var $_period;

    /**
     * @todo Add description
     *
     * @var integer
     */
    var $_cpu_time;

    /**
     * @todo: Add description
     *
     * @var integer
     */
    var $_cpu_time_raw;

    /**
     * @todo: Add description
     *
     * @var integer
     */
    var $_elapsed_time;

    /**
     * VM ID
     *
     * @var integer
     */

    /**
     * Virtual machine id
     *
     * @var integer
     */
    var $_virtual_machine_id;

    /**
     * shows if monitis is enabled
     *
     * @var boolean
     */
    var $_enable_monitis;

    /**
     * the Cpu Usage start time date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_stat_time;

    /**
     * Used id
     *
     * @var integer
     */
    var $_user_id;

    /**
     * root tag used in the API request
     *
     * @var string
     */
    var $_tagRoot = 'cpu-hourly-stat';

    /**
     * alias processing the object data
     *
     * @var string
     */
    var $_resource = 'cpu_usage';

    /**
     *
     * called class name
     *
     * @var string
     */
    var $_called_class = 'ONAPP_VirtualMachine_CpuUsage';

    /**
     * API Fields description
     *
     * @access private
     * @var    array
     */
    function _init_fields( $version = NULL ) {
        if( !isset( $this->options[ ONAPP_OPTION_API_TYPE ] ) || ( $this->options[ ONAPP_OPTION_API_TYPE ] == 'json' ) ) {
            $this->_tagRoot = 'cpu_hourly_stat';
        }

        if( is_null( $version ) ) {
            $version = $this->_version;
        }

        switch( $version ) {
            case '2.0':
            case '2.1':
                $this->_fields = array(
                    'id' => array(
                        ONAPP_FIELD_MAP => '_id',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true
                    ),
                    'created_at' => array(
                        ONAPP_FIELD_MAP => '_created_at',
                        ONAPP_FIELD_TYPE => 'datetime',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'updated_at' => array(
                        ONAPP_FIELD_MAP => '_updated_at',
                        ONAPP_FIELD_TYPE => 'datetime',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'period' => array(
                        ONAPP_FIELD_MAP => '_period',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'cpu_time' => array(
                        ONAPP_FIELD_MAP => '_cpu_time',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'cpu_time_raw' => array(
                        ONAPP_FIELD_MAP => '_cpu_time_raw',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'elapsed_time' => array(
                        ONAPP_FIELD_MAP => '_elapsed_time',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'virtual_machine_id' => array(
                        ONAPP_FIELD_MAP => '_virtual_machine_id',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'stat_time' => array(
                        ONAPP_FIELD_MAP => '_stat_time',
                        ONAPP_FIELD_TYPE => 'datetime',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'user_id' => array(
                        ONAPP_FIELD_MAP => '_user_id',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                );

               if ( $this->_release == "0") {
                    unset($this->_fields[ 'elapsed_time' ]);
                    unset($this->_fields[ 'cpu_time_raw' ]);
                    unset($this->_fields[ 'period' ]);
                };

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
            case ONAPP_GETRESOURCE_LIST:
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
     * @param integer $virtual_machine_id Virtual Machine id
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
