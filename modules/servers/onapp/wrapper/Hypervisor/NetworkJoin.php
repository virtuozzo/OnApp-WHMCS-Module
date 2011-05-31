<?PHP
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Network Joins
 *
 * @todo Add description
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
 * ONAPP_Hypervisor_NetworkJoin
 *
 * This class reprsents the Networks for Hypervisor.
 *
 * The ONAPP_Hypervisor_NetworkJoin class uses the following basic methods:
 * {@link load}, {@link save}, {@link delete}, and {@link getList}.
 *
 * <b>Use the following XML API requests:</b>
 *
 * Get the list of Network Joins
 *
 *     - <i>GET onapp.com/settings/hypervisors/{HYPERVISOR_ID}/network_joins.xml</i>
 *
 * Get a particular Network Join details
 *
 *     - <i>GET onapp.com/settings/hypervisors/{HYPERVISOR_ID}/network_joins/{ID}.xml</i>
 *
 * Add new Network Join
 *
 *     - <i>POST onapp.com/settings/hypervisors/{HYPERVISOR_ID}/network_joins.xml</i>
 *
 * <code>
 * <?xml version="1.0" encoding="UTF-8"?>
 * <network-join>
 *    <network_id>{NETWORK_ID}</network_id>
 *    <interface>{INTERFACE}</interface>
 * </network-join>
 * </code>
 *
 * Delete Network Join
 *
 *     - <i>DELETE onapp.com/settings/hypervisors/{HYPERVISOR_ID}/network_joins/{ID}.xml</i>
 *
 * <b>Use the following JSON API requests:</b>
 *
 * Get the list of Network Joins
 *
 *     - <i>GET onapp.com/settings/hypervisors/{HYPERVISOR_ID}/network_joins.json</i>
 *
 * Get a particular Network Join details
 *
 *     - <i>GET onapp.com/settings/hypervisors/{HYPERVISOR_ID}/network_joins/{ID}.json</i>
 *
 * Add new Network Join
 *
 *     - <i>POST onapp.com/settings/hypervisors/{HYPERVISOR_ID}/network_joins.json</i>
 *
 * <code>
 * {
 *      network-join: {
 *          network_id:{NETWORK_ID},
 *          interface:'{INTERFACE}'
 *      }
 * }
 * </code>
 *
 * Delete Network Join
 *
 *     - <i>DELETE onapp.com/settings/hypervisors/{HYPERVISOR_ID}/network_joins/{ID}.json</i>
 */
class ONAPP_Hypervisor_NetworkJoin extends ONAPP {

    /**
     * the Network Join ID
     *
     * @var integer
     */
    var $_id;

    /**
     * the Network Join creation date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_created_at;

    /**
     * the Network Join update date was updated in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_updated_at;

    /**
     * the Network ID
     *
     * @var integer
     */
    var $_network_id;

    /**
     * the Hypervisor ID
     *
     * @var integer
     */
    var $_hypervisor_id;

    /**
     * the Interface
     *
     * @var string
     */
    var $_interface;

    /**
     * Target join id
     *
     * @var integer
     */
    var $_target_join_id;

    /**
     * Target join type
     *
     * @var string
     */
    var $_target_join_type;

    /**
     * root tag used in the API request
     *
     * @var string
     */
    var $_tagRoot = 'network-join';

    /**
     * alias processing the object data
     *
     * @var string
     */
    var $_resource = 'network_joins';

    /**
     *
     * called class name
     *
     * @var string
     */
    var $_called_class = 'ONAPP_Hypervisor_NetworkJoin';

    /**
     * API Fields description
     *
     * @access private
     * @var    array
     */
    function _init_fields( $version = NULL ) {
        if( !isset( $this->options[ ONAPP_OPTION_API_TYPE ] ) || ( $this->options[ ONAPP_OPTION_API_TYPE ] == 'json' ) ) {
            $this->_tagRoot = 'network_join';
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
                    'network_id' => array(
                        ONAPP_FIELD_MAP => '_network_id',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_REQUIRED => true,
                    ),
                    'hypervisor_id' => array(
                        ONAPP_FIELD_MAP => '_hypervisor_id',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'interface' => array(
                        ONAPP_FIELD_MAP => '_interface',
                        ONAPP_FIELD_READ_ONLY => true,
                        ONAPP_FIELD_REQUIRED => true,
                    ),
                );

                break;

            case '2.1':

                $this->_fields = $this->_init_fields('2.0');

                $this->_fields[ 'target_join_id' ] = array(
                    ONAPP_FIELD_MAP => '_target_join_id',
                    ONAPP_FIELD_TYPE => 'integer',
                    ONAPP_FIELD_REQUIRED => true
                );

                $this->_fields[ 'target_join_type' ] = array(
                    ONAPP_FIELD_MAP => '_target_join_type',
                    ONAPP_FIELD_TYPE => 'string',
                    ONAPP_FIELD_REQUIRED => true
                );

                break;
        }
        ;

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
                if( is_null( $this->_hypervisor_id ) && is_null( $this->_obj->_hypervisor_id ) ) {
                    $this->_loger->error(
                        "getResource($action): argument _hypervisor_id not set.",
                        __FILE__,
                        __LINE__
                    );
                }
                else {
                    if( is_null( $this->_hypervisor_id ) ) {
                        $this->_hypervisor_id = $this->_obj->_hypervisor_id;
                    }
                }
                ;
                $resource = 'settings/hypervisors/' . $this->_hypervisor_id . '/' . $this->_resource;
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
     * @param integer $hypervisor_id Hypervisor ID
     *
     * @return mixed an array of Object instances on success. Otherwise false
     * @access public
     */
    function getList( $hypervisor_id = null ) {
        if( is_null( $hypervisor_id ) && !is_null( $this->_hypervisor_id ) ) {
            $hypervisor_id = $this->_hypervisor_id;
        }

        if( !is_null( $hypervisor_id ) ) {
            $this->_hypervisor_id = $hypervisor_id;

            return parent::getList( );
        }
        else {
            $this->_loger->error(
                'getList: argument _hypervisor_id not set.',
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
     * @param integer $id Network Join ID
     * @param integer $hypervisor_id Hypervisor ID
     *
     * @return mixed serialized Object instance from API
     * @access public
     */
    function load( $id = null, $hypervisor_id = null ) {
        if( is_null( $hypervisor_id ) && !is_null( $this->_hypervisor_id ) ) {
            $hypervisor_id = $this->_hypervisor_id;
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

        if( !is_null( $id ) && !is_null( $hypervisor_id ) ) {
            $this->_id = $id;
            $this->_hypervisor_id = $hypervisor_id;

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
                    'load: argument _hypervisor_id not set.',
                    __FILE__,
                    __LINE__
                );
            }
        }
    }
}

?>
