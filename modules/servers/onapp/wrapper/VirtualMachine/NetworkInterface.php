<?PHP
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * VM Network Interface
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
require_once dirname(__FILE__).'/../ONAPP.php';

/**
 * VM Network Interface
 *
 * The Network Interface class uses the following basic methods:
 * {@link load}, {@link save}, {@link delete}, and {@link getList}.
 *
 * <b>Use the following XML API requests:</b>
 *
 * Get the list of Network Interfaces
 *
 *     - <i>GET onapp.com/virtual_machines/{VM_ID}/network_interfaces.xml</i>
 *
 * Get a particular Network Interface details 
 *
 *     - <i>GET onapp.com/virtual_machines/{VM_ID}/network_interfaces/{ID}.xml</i>
 *
 * Add new Network Interface
 *
 *     - <i>POST onapp.com/virtual_machines/{VM_ID}/network_interfaces.xml</i>
 *
 * <code>
 * <?xml version="1.0" encoding="UTF-8"?>
 * <network-interface>
 *     <label>{LABEL}</label>
 *     <network_join_id>{NETWORK_JOIN_ID}</network_join_id>
 *     <rate_limit>{RATE_LIMIT}</rate_limit>
 * </network-interface>
 * </code>
 *
 * Edit existing Network Interface
 *
 *     - <i>PUT onapp.com/virtual_machines/{VM_ID}/network_interfaces/{ID}.xml</i>
 *
 * <code>
 * <?xml version="1.0" encoding="UTF-8"?>
 * <network-interface>
 *     <label>{LABEL}</label>
 *     <network_join_id>{NETWORK_JOIN_ID}</network_join_id>
 *     <rate_limit>{RATE_LIMIT}</rate_limit>
 * </network-interface>
 * </code>
 *
 * Delete Network Interface
 *
 *     - <i>DELETE onapp.com/virtual_machines/{VM_ID}/network_interfaces/{ID}.xml</i>
 *
 * <b>Use the following JSON API requests:</b>
 *
 * Get the list of Network Interfaces
 *
 *     - <i>GET onapp.com/virtual_machines/{VM_ID}/network_interfaces.json</i>
 *
 * Get a particular Network Interface details 
 *
 *     - <i>GET onapp.com/virtual_machines/{VM_ID}/network_interfaces/{ID}.json</i>
 *
 * Add new Network Interface
 *
 *     - <i>POST onapp.com/virtual_machines/{VM_ID}/network_interfaces.json</i>
 *
 * <code>
 * { 
 *      network-interface: {
 *          label:'{LABEL}',
 *          network_join_id:{NETWORK_JOIN_ID},
 *          rate_limit:{RATE_LIMIT}
 *      }
 * }
 * </code>
 *
 * Edit existing Network Interface
 *
 *     - <i>PUT onapp.com/virtual_machines/{VM_ID}/network_interfaces/{ID}.json</i>
 *
 * <code>
 * { 
 *      network-interface: {
 *          label:'{LABEL}',
 *          network_join_id:{NETWORK_JOIN_ID},
 *          rate_limit:{RATE_LIMIT}
 *      }
 * }
 * </code>
 *
 * Delete Network Interface
 *
 *     - <i>DELETE onapp.com/virtual_machines/{VM_ID}/network_interfaces/{ID}.json</i>
 */
class ONAPP_VirtualMachine_NetworkInterface extends ONAPP {

    /**
     * the Network Interface ID
     *
     * @var integer
     */
    var $_id;

    /**
     * the Network Interface label 
     * 
     * @var string
     */
    var $_label;

    /**
     * the date when the Network Interface was created in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var datetime
     */
    var $_created_at;

    /**
     * the date when the Network Interface was updated in the [YYYY][MM][DD]T[hh][mm]Z format  
     *
     * @var datetime
     */
    var $_updated_at;

    /**
     * the Network Interface usage
     * 
     * @var string
     */
    var $_usage;

    /**
     * true if the Network Interface is primary. Otherwise, false.
     * 
     * @var boolean
     */
    var $_primary;

    /**
     * @todo: Add description
     * 
     * @var date
     */
    var $_usage_month_rolled_at;

    /**
     * the MAC address
     * 
     * @var string
     */
    var $_mac_address;

    /**
     * @todo: Add description
     * 
     * @var datetime
     */
    var $_usage_last_reset_at;

    /**
     * the port speed limit
     * 
     * @var integer
     */
    var $_rate_limit;

    /**
     * the identifier
     * 
     * @var string
     */
    var $_identifier;

    /**
     * the Network Join ID
     * 
     * @var integer
     */
    var $_network_join_id;

    /**
     * the VM ID
     * 
     * @var integer
     */
    var $_virtual_machine_id;

    /**
     * root tag used in the API request
     *
     * @var string
     */
    var $_tagRoot  = 'network-interface';
    
    /**
     * alias processing the object data
     *
     * @var string
     */
    var $_resource = 'network_interfaces';
    
    /**
     * 
     * called class name
     * 
     * @var string
     */
    var $_called_class = 'ONAPP_VirtualMachine_NetworkInterface';
    
    /**
     * API Fields description
     *
     * @access private
     * @var    array
     */
    function _init_fields( $version = NULL ) {

      if ( is_null($version) )
        $version = $this->_version;

      switch ($version) {
        case '2.0.0':
          $this->_fields = array(
            'id' => array(
                ONAPP_FIELD_MAP           => '_id',
                ONAPP_FIELD_TYPE          => 'integer',
                ONAPP_FIELD_READ_ONLY     => true
            ),
            'label' => array(
                ONAPP_FIELD_MAP           => '_label',
                ONAPP_FIELD_REQUIRED      => true,
            ),
            'created_at' => array(
                ONAPP_FIELD_MAP           => '_created_at',
                ONAPP_FIELD_TYPE          => 'datetime',
                ONAPP_FIELD_READ_ONLY     => true,
            ),
            'updated_at' => array(
                ONAPP_FIELD_MAP           => '_updated_at',
                ONAPP_FIELD_TYPE          => 'datetime',
                ONAPP_FIELD_READ_ONLY     => true,
            ),
            'usage' => array(
                ONAPP_FIELD_MAP           => '_usage',
                ONAPP_FIELD_READ_ONLY     => true,
            ),
            'primary' => array(
                ONAPP_FIELD_MAP           => '_primary',
                ONAPP_FIELD_TYPE          => 'boolean',
                ONAPP_FIELD_READ_ONLY     => true,
            ),
            'usage_month_rolled_at' => array(
                ONAPP_FIELD_MAP           => '_usage_month_rolled_at',
                ONAPP_FIELD_TYPE          => 'date',
                ONAPP_FIELD_READ_ONLY     => true,
            ),
            'mac_address' => array(
                ONAPP_FIELD_MAP           => '_mac_address',
                ONAPP_FIELD_READ_ONLY     => true,
            ),
             'usage_last_reset_at' => array(
                ONAPP_FIELD_MAP           => '_usage_last_reset_at',
                ONAPP_FIELD_TYPE          => 'datetime',
                ONAPP_FIELD_READ_ONLY     => true,
            ),
            'rate_limit' => array(
                ONAPP_FIELD_MAP           => '_rate_limit',
                ONAPP_FIELD_TYPE          => 'integer',
                ONAPP_FIELD_REQUIRED      => true,
                ONAPP_FIELD_DEFAULT_VALUE => ''
            ),
            'identifier' => array(
                ONAPP_FIELD_MAP           => '_identifier',
                ONAPP_FIELD_READ_ONLY     => true,
            ),
            'network_join_id' => array(
                ONAPP_FIELD_MAP           => '_network_join_id',
                ONAPP_FIELD_TYPE          => 'integer',
                ONAPP_FIELD_REQUIRED      => true,
            ),
            'virtual_machine_id' => array(
                ONAPP_FIELD_MAP           => '_virtual_machine_id',
                ONAPP_FIELD_TYPE          => 'integer',
                ONAPP_FIELD_READ_ONLY     => true,
            ),
        );

        break;
        case '2.0.1':
          $this->_fields = $this->_init_fields("2.0.0");
        break;
      };

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
    function getResource($action = ONAPP_GETRESOURCE_DEFAULT) {
        switch ($action) {
            case ONAPP_GETRESOURCE_DEFAULT:
                if ( is_null($this->_virtual_machine_id) && is_null($this->_obj->_virtual_machine_id) ) {
                    $this->_loger->error(
                       "getResource($action): argument _virtual_machine_id not set.", 
                        __FILE__, 
                        __LINE__
                    );
                } else if ( is_null($this->_virtual_machine_id) ) {
                    $this->_virtual_machine_id = $this->_obj->_virtual_machine_id;
                };
                $resource = 'virtual_machines/' . $this->_virtual_machine_id . '/' . $this->_resource;
                $this->_loger->debug("getResource($action): return ".$resource);
                break;

            default:
                $resource = parent::getResource($action);
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
    function getList($virtual_machine_id = null) {
        if ( is_null($virtual_machine_id) && ! is_null($this->_virtual_machine_id) )
            $virtual_machine_id = $this->_virtual_machine_id;

        if ( is_null($virtual_machine_id) &&
            isset($this->_obj) &&
            ! is_null($this->_obj->_virtual_machine_id)
        )
            $virtual_machine_id = $this->_obj->_virtual_machine_id;

        if ( ! is_null($virtual_machine_id) ) {
            $this->_virtual_machine_id = $virtual_machine_id;
            return parent::getList();
        } else {
            $this->_loger->error(
               'getList: argument _virtual_machine_id not set.', 
                __FILE__, 
                __LINE__
            );
        }
    }

    /**
     * Sends an API request to get the Object after sending, 
     * unserializes the response into an object
     *
     * The key field Parameter ID is used to load the Object. You can re-set
     * this parameter in the class inheriting Class ONAPP.
     *
     * @param integer $id Network Interface id
     * @param integer $virtual_machine_id Virtual Machine id
     *
     * @return mixed serialized Object instance from API
     * @access public
     */
    function load( $id = null, $virtual_machine_id = null ) {
        if ( is_null($virtual_machine_id) && ! is_null($this->_virtual_machine_id) )
            $virtual_machine_id = $this->_virtual_machine_id;

        if ( is_null($virtual_machine_id) &&
            isset($this->_obj) &&
            ! is_null($this->_obj->_virtual_machine_id)
        )
            $virtual_machine_id = $this->_obj->_virtual_machine_id;

        if ( is_null($id) && ! is_null($this->_id) )
            $id = $this->_id;

        if ( is_null($id) &&
            isset($this->_obj) &&
            ! is_null($this->_obj->_id)
        )
            $id = $this->_obj->_id;

        $this->_loger->add("load: Load class ( id => '$id').");

        if ( ! is_null($id) && ! is_null($virtual_machine_id) ) {
            $this->_id = $id;
            $this->_virtual_machine_id = $virtual_machine_id;

            $this->setAPIResource( $this->getResource(ONAPP_GETRESOURCE_LOAD) );

            $response = $this->sendRequest(ONAPP_REQUEST_METHOD_GET);

            $result = $this->_castResponseToClass( $response );

            $this->_obj = $result;

            return $result;
        } else {
            if (is_null($id))
                $this->_loger->error(
                   'load: argument _id not set.', 
                    __FILE__, 
                    __LINE__
                );
            else
                $this->_loger->error(
                   'load: argument _virtual_machine_id not set.', 
                    __FILE__, 
                    __LINE__
                );
        }
    }

    /**
     * The method saves an Object to your account
     *
     * After sending an API request to create an object or change the data in
     * the existing object, the method checks the response and loads the
     * exisitng object with the new data. 
     *
     * @return void
     * @access public
     */
    function save() {
        if ( isset( $this->_id ) ) {
            $obj = $this->_edit();
            
            $this->load();
        }
    }
}

?>
