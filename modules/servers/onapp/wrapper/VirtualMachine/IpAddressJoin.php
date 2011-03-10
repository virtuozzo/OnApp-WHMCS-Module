<?PHP
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * VM IP Address Joins
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
require_once dirname(__FILE__).'/IpAddress.php';

/**
 * VM IP Address Joins
 *
 * The IP Address Join class uses the following basic methods:
 * {@link load}, {@link save}, {@link delete}, and {@link getList}.
 * 
 * <b>Use the following XML API requests:</b>
 *
 * Get the list of IP Address Joins
 *
 *     - <i>GET onapp.com/virtual_machines/{VM_ID}/ip_addresses.xml</i>
 *
 * Get a particular IP Address Join details 
 *
 *     - <i>GET onapp.com/virtual_machines/{VM_ID}/ip_addresses/{ID}.xml</i>
 *
 * Add new IP Address Join
 *
 *     - <i>POST onapp.com/svirtual_machines/{VM_ID}/ip_addresses.xml</i>
 * 
 * <code>
 * <?xml version="1.0" encoding="UTF-8"?>
 * <backup>
 *    <network_interface_id>{NETWORK_INTERFACE_ID}</network_interface_id>
 *    <ip_address_id>{IP_ADDRESS_ID}</ip_address_id>
 * </backup>
 * </code>
 * 
 * Delete IP Address Join
 *
 *     - <i>DELETE onapp.com/virtual_machines/{VM_ID}/ip_addresses/{ID}.xml</i>
 *
 * <b>Use the following JSON API requests:</b>
 *
 * Get the list of IP Address Joins
 *
 *     - <i>GET onapp.com/virtual_machines/{VM_ID}/ip_addresses.json</i>
 *
 * Get a particular IP Address Join details 
 *
 *     - <i>GET onapp.com/virtual_machines/{VM_ID}/ip_addresses/{ID}.json</i>
 *
 * Add new IP Address Join
 *
 *     - <i>POST onapp.com/virtual_machines/{VM_ID}/ip_addresses.json</i>
 *
 * <code>
 * { 
 *      backup: {
 *          network_interface_id:{NETWORK_INTERFACE_ID},
 *          ip_address_id:{IP_ADDRESS_ID}
 *      }
 * }
 * </code>
 *
 * Delete IP Address Join
 *
 *     - <i>DELETE onapp.com/virtual_machines/{VM_ID}/ip_addresses/{ID}.json</i>
 */
class ONAPP_VirtualMachine_IpAddressJoin extends ONAPP {

    /**
     * the IP Address Join ID
     *
     * @var integer
     */
    var $_id;

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
     * the Network Interface ID
     * 
     * @var integer
     */
    var $_network_interface_id;

    /**
     * the IP Address ID
     * 
     * @var integer
     */
    var $_ip_address_id;

    /**
     * the VM ID
     * 
     * @var integer
     */
    var $_virtual_machine_id;

    /**
     * the IP Address
     * 
     * @var string
     */
    var $_ip_address;

    /**
     * root tag used in the API request
     *
     * @var string
     */
    var $_tagRoot  = 'ip_address';
    
    /**
     * alias processing the object data
     *
     * @var string
     */
    var $_resource = 'ip_addresses';
    
    /**
     * 
     * called class name
     * 
     * @var string
     */
    var $_called_class = 'ONAPP_VirtualMachine_IpAddressJoin';
    
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
            'network_interface_id' => array(
                ONAPP_FIELD_MAP           => '_network_interface_id',
                ONAPP_FIELD_TYPE          => 'integer',
                ONAPP_FIELD_REQUIRED      => true,
            ),
            'ip_address_id' => array(
                ONAPP_FIELD_MAP           => '_ip_address_id',
                ONAPP_FIELD_TYPE          => 'integer',
                ONAPP_FIELD_REQUIRED      => true,
            ),
            'ip_address' => array(
                ONAPP_FIELD_MAP           => '_ip_address',
                ONAPP_FIELD_READ_ONLY     => true,
                ONAPP_FIELD_CLASS         => 'VirtualMachine_IpAddress',
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
     * @param integer $id IP Address Join id
     * @param integer $virtual_machine_id Virtual Machine id
     *
     * @return mixed serialized Object instance from API
     * @access public
     */
    function load( $id = null, $virtual_machine_id = null ) {
        if ( is_null($virtual_machine_id) && ! is_null($this->_virtual_machine_id) )
            $virtual_machine_id = $this->_virtual_machine_id;

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
}

?>
