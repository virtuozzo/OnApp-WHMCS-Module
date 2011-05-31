<?PHP
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * VM IP Adresses
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
require_once dirname( __FILE__ ) . '/../IpAddress.php';

/**
 * VM IP Adresses
 *
 * The ONAPP_VirtualMachine_IpAddress class doesn't support any basic method.
 *
 */
class ONAPP_VirtualMachine_IpAddress extends ONAPP_IpAddress {

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
    var $_resource = '';

    /**
     *
     * called class name
     *
     * @var string
     */
    var $_called_class = 'ONAPP_VirtualMachine_IpAddress';

    /**
     * API Fields description
     *
     * @access private
     * @var    array
     */
    function _init_fields( $version = NULL ) {

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
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'netmask' => array(
                        ONAPP_FIELD_MAP => '_netmask',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'broadcast' => array(
                        ONAPP_FIELD_MAP => '_broadcast',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'network_address' => array(
                        ONAPP_FIELD_MAP => '_network_address',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'gateway' => array(
                        ONAPP_FIELD_MAP => '_gateway',
                        ONAPP_FIELD_READ_ONLY => true,
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
                );

                break;
        }
        ;

        return $this->_fields;
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
            case ONAPP_ACTIVATE_GETLIST:
            case ONAPP_ACTIVATE_LOAD:
            case ONAPP_ACTIVATE_SAVE:
            case ONAPP_ACTIVATE_DELETE:
                die( "Call to undefined method " . __CLASS__ . "::$action_name()" );
                break;
        }
    }
}

?>
