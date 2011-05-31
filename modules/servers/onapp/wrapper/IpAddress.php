<?PHP
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Managing IP Addresses
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
require_once 'ONAPP.php';

/**
 * IP Addresses
 *
 * The ONAPP_IpAddress class uses the following basic methods:
 * {@link load}, {@link save}, {@link delete}, and {@link getList}.
 *
 * <b>Use the following XML API requests:</b>
 *
 * Get the list of Network IP Addresses
 *
 *     - <i>GET onapp.com/settings/networks/{NETWORK_ID}/ip_addresses.xml</i>
 *
 * Get a particular IP Address details
 *
 *     - <i>GET onapp.com/settings/networks/{NETWORK_ID}/ip_addresses/{ID}.xml</i>
 *
 * Add new IP Address
 *
 *     - <i>POST onapp.com/settings/networks/{NETWORK_ID}/ip_addresses.xml</i>
 *
 * <code>
 * <?xml version="1.0" encoding="UTF-8"?>
 * <ip-address>
 *     <address>{ADDRESS}</address>
 *     <netmask>{NETMASK}</netmask>
 *     <gateway>{GATEWAY}</gateway>
 * </ip-address>
 * </code>
 *
 * Edit existing IP Address
 *
 *     - <i>PUT onapp.com/settings/networks/{NETWORK_ID}/ip_addresses/{ID}.xml</i>
 *
 * <code>
 * <?xml version="1.0" encoding="UTF-8"?>
 * <ip-address>
 *     <address>{ADDRESS}</address>
 *     <netmask>{NETMASK}</netmask>
 *     <gateway>{GATEWAY}</gateway>
 *     <broadcast>{BROADCAST}</address>
 *     <network_address>{NETWORK_ADDRESS}</network_address>
 * </ip-address>
 * </code>
 *
 * Delete IP Address
 *
 *     - <i>DELETE onapp.com/settings/networks/{NETWORK_ID}/ip_addresses/{ID}.xml</i>
 *
 * <b>Use the following JSON API requests:</b>
 *
 * Get the list of IP Addresses
 *
 *     - <i>GET onapp.com/settings/networks/{NETWORK_ID}/ip_addresses.json</i>
 *
 * Get a particular IP Address details
 *
 *     - <i>GET onapp.com/settings/networks/{NETWORK_ID}/ip_addresses/{ID}.json</i>
 *
 * Add new IP Address
 *
 *     - <i>POST onapp.com/settings/networks/{NETWORK_ID}/ip_addresses.json</i>
 *
 * <code>
 * {
 *      ip-address: {
 *          address:'{ADDRESS}',
 *          netmask:'{NETMASK}',
 *          gateway:'{GATEWAY}'
 *      }
 * }
 * </code>
 *
 * Edit existing IP Address
 *
 *     - <i>PUT onapp.com/settings/networks/{NETWORK_ID}/ip_addresses/{ID}.json</i>
 *
 * <code>
 * {
 *      ip-address: {
 *          address:'{ADDRESS}',
 *          netmask:'{NETMASK}',
 *          gateway:'{GATEWAY}',
 *          broadcast:'{BROADCAST}',
 *          network_address:'{NETWORK_ADDRESS}'
 *      }
 * }
 * </code>
 *
 * Delete IP Address
 *
 *     - <i>DELETE onapp.com/settings/networks/{NETWORK_ID}/ip_addresses/{ID}.json</i>
 *
 */
class ONAPP_IpAddress extends ONAPP {

    /**
     * the IP Address ID
     *
     * @var integer
     */
    var $_id;

    /**
     * the Ip Address creation date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_created_at;

    /**
     * the IP Address
     *
     * @var string
     */
    var $_address;

    /**
     * the netmask
     *
     * @var string
     */

    var $_netmask;

    /**
     * the broadcast
     *
     * @var string
     */
    var $_broadcast;

    /**
     * the network address
     *
     * @var string
     */
    var $_network_address;

    /**
     * the network ID
     *
     * @var string
     */
    var $_network_id;

    /**
     * the Ip Address update date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_updated_at;

    /**
     * the gateway
     *
     * @var string
     */
    var $_gateway;

    /**
     * is the IP Address free
     *
     * @var boolean
     */
    var $_free;

    /**
     * don't use on guest during build
     *
     * @var boolean
     */
    var $_disallowed_primary;

    /**
     * root tag used in the API request
     *
     * @var string
     */
    var $_tagRoot = 'ip_address';

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
    var $_called_class = 'ONAPP_IpAddress';

    /**
     * API Fields description
     *
     * @access private
     * @var    array
     */
    function _init_fields( $version = NULL ) {
        if( !isset( $this->options[ ONAPP_OPTION_API_TYPE ] ) || ( $this->options[ ONAPP_OPTION_API_TYPE ] == 'json' ) ) {
            $this->_tagRoot = 'ip_address';
        }

        if( is_null( $version ) ) {
            $version = $this->_version;
        }

        switch( $version ) {
            case '2.0':
                $this->_fields = array(
                    'id' => array(
                        ONAPP_FIELD_MAP => '_id',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
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
                    'address' => array(
                        ONAPP_FIELD_MAP => '_address',
                        ONAPP_FIELD_REQUIRED => true,
                        //    ONAPP_FIELD_DEFAULT_VALUE => ''
                    ),
                    'netmask' => array(
                        ONAPP_FIELD_MAP => '_netmask',
                        ONAPP_FIELD_REQUIRED => true,
                        //    ONAPP_FIELD_DEFAULT_VALUE => ''
                    ),
                    'broadcast' => array(
                        ONAPP_FIELD_MAP => '_broadcast',
                        ONAPP_FIELD_REQUIRED => true,
                        //    ONAPP_FIELD_DEFAULT_VALUE => ''
                    ),
                    'network_address' => array(
                        ONAPP_FIELD_MAP => '_network_address',
                        ONAPP_FIELD_REQUIRED => true,
                        //    ONAPP_FIELD_DEFAULT_VALUE => ''
                    ),
                    'gateway' => array(
                        ONAPP_FIELD_MAP => '_gateway',
                        ONAPP_FIELD_REQUIRED => true,
                        //    ONAPP_FIELD_DEFAULT_VALUE => ''
                    ),
                    'network_id' => array(
                        ONAPP_FIELD_MAP => '_network_id',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'free' => array(
                        ONAPP_FIELD_MAP => '_free',
                        ONAPP_FIELD_TYPE => 'boolean',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'disallowed_primary' => array(
                        ONAPP_FIELD_MAP => '_disallowed_primary',
                        ONAPP_FIELD_TYPE => 'boolean',
                        ONAPP_FIELD_READ_ONLY => true,
                    )
                );
                break;

                case '2.1':
                    $this->_fields = $this->_init_fields('2.0');

//                    if ( $this->_release == "0") {
//                        unset($this->_fields[ 'free' ]);
//                    };
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
                if( is_null( $this->_network_id ) && is_null( $this->_obj->_network_id ) ) {
                    $this->_loger->error(
                        "getResource($action): argument _network_id not set.",
                        __FILE__,
                        __LINE__
                    );
                }
                else {
                    if( is_null( $this->_network_id ) ) {
                        $this->_network_id = $this->_obj->_network_id;
                    }
                }
                ;
                $resource = 'settings/networks/' . $this->_network_id . '/' . $this->_resource;
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
     * @param integer $network_id Network ID
     *
     * @return mixed an array of Object instances on success. Otherwise false
     * @access public
     */
    function getList( $network_id = null ) {
        if( is_null( $network_id ) && !is_null( $this->_network_id ) ) {
            $network_id = $this->_network_id;
        }

        if( !is_null( $network_id ) ) {
            $this->_network_id = $network_id;

            return parent::getList( );
        }
        else {
            $this->_loger->error(
                'getList: argument _network_id not set.',
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
    function load( $id = null, $network_id = null ) {
        if( is_null( $network_id ) && !is_null( $this->_network_id ) ) {
            $network_id = $this->_network_id;
        }

        if( is_null( $id ) && !is_null( $this->_id ) ) {
            $id = $this->_id;
        }

        if( is_null( $id ) &&
            isset( $this->_obj ) &&
            !is_null( $this->_obj->_id )
        ) {
            $id = $this->_obj->_id;
        }

        $this->_loger->add( "load: Load class ( id => '$id')." );

        if( !is_null( $id ) && !is_null( $network_id ) ) {
            $this->_id = $id;
            $this->_network_id = $network_id;

            $this->setAPIResource( $this->getResource( ONAPP_GETRESOURCE_LOAD ) );

            $response = $this->sendRequest( ONAPP_REQUEST_METHOD_GET );

            $result = $this->_castResponseToClass( $response );

            $this->_obj = $result;

            return $result;
        }
        else {
            if( is_null( $id ) ) {
                $this->_loger->error(
                    'load: argument _id not set.',
                    __FILE__,
                    __LINE__
                );
            }
            else
            {
                $this->_loger->error(
                    'load: argument _network_id not set.',
                    __FILE__,
                    __LINE__
                );
            }
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
    function save( ) {
        if( isset( $this->_id ) ) {
            $obj = $this->_edit( );

            if( isset( $obj ) && !isset( $obj->error ) ) {
                $this->load( );
            }
        }
    }
}
