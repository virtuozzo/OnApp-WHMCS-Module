<?PHP
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Provisioning Profile
 *
 * @category  API WRAPPER
 * @package   ONAPP
 * @author    Andrew Yatskovets
 * @copyright 2010 / OnApp
 * @link      http://www.onapp.com/
 * @see       ONAPP
 */

/**
 * requires Base class
 */
require_once 'ONAPP.php';
require_once 'Role.php';
require_once 'IpAddress.php';

/**
 *
 * Managing User Profile
 *
 * The ONAPP_Profile class uses the following basic methods:
 * {@link load} and {@link save}.
 *
 * The ONAPP_Profile class represents user profile.
 * The ONAPP class is a parent of ONAPP_Profile class.
 *
 */
 
class ONAPP_Profile extends ONAPP {

    /**
     * the User creation date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_created_at;

    /**
     * the User activation date when user was activated in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_activated_at;

    /**
     * available memory for user
     *
     * @var integer
     */
    var $_memory_available;

    /**
     * the User used memory
     *
     * @var integer
     */
    var $_used_memory;

    /**
     * the User outstanding amount
     *
     * @var float
     */
    var $_outstanding_amount;

    /**
     * the User suspend date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_suspend_at;

     /**
     * token expiration date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_remember_token_expires_at;

    /**
     * the user roles
     *
     * @var Array of roles
     */
    var $_roles;

    /**
     * the user total amount
     *
     * @var float
     */
    var $_total_amount;

    /**
     * the Profile update date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_updated_at;

    /**
     * the Profile delete date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_deleted_at;

     /**
     * the user used IP addresses
     *
     * @var integer
     */
    var $_used_ip_addresses;

     /**
     * the user billing plan id
     *
     * @var integer
     */
    var $_billing_plan_id;


     /**
     * the disk size used by user
     *
     * @var integer
     */
    var $_used_disk_size;

    /**
     * user's group id
     *
     * @var integer
     */
    var $_group_id;

    /**
     * user's group id
     *
     * @var integer
     */
    var $_user_group_id;

    /**
     * available disck space for user
     *
     * @var integer
     */
    var $_disk_space_available;

    /**
     * the user used CPU shares
     *
     * @var integer
     */
    var $_used_cpu_shares;

    /**
     * the user payment amount
     *
     * @var float
     */
    var $_payment_amount;

    /**
     * the session ID
     *
     * @var integer
     */
    var $_remember_token;

    /**
     * the user last name
     *
     * @var integer
     */
    var $_last_name;

    /**
     * the user time zone
     *
     * @var string
     */
    var $_time_zone;

    /**
     * user's locate
     *
     * @var string
     */
    var $_locale;

    /**
     * Template group id
     *
     * @var integer
     */
    var $_image_template_group_id;

    /**
     * the user used CPUs
     *
     * @var integer
     */
    var $_used_cpus;

    /**
     * user status
     *
     * @var string
     */
    var $_status;

    /**
     * user's login
     *
     * @var string
     */
    var $_login;

    /**
     * user's first name
     *
     * @var string
     */
    var $_first_name;

    /**
     * user's e-mail address
     *
     * @var string
     */
    var $_email;

    /**
     * the template ID
     *
     * @var integer
     */
    var $_id;

    /**
     * root tag used in the API request
     *
     * @var string
     */
    var $_tagRoot = 'user';

    /**
     * alias processing the object data
     *
     * @var string
     */
    var $_resource = 'profile';

    /**
     *
     * called class name
     *
     * @var string
     */
    var $_called_class = 'ONAPP_Profile';

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
            default:
                $this->_fields = array(
                    'created_at' => array(
                        ONAPP_FIELD_MAP       => '_created_at',
                        ONAPP_FIELD_TYPE      => 'datetime',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'activated_at' => array(
                        ONAPP_FIELD_MAP       => '_activated_at',
                        ONAPP_FIELD_TYPE      => 'datetime',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'memory_available' => array(
                        ONAPP_FIELD_MAP       => '_memory_available',
                        ONAPP_FIELD_TYPE      => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'used_memory' => array(
                        ONAPP_FIELD_MAP       => '_used_memory',
                        ONAPP_FIELD_TYPE      => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'outstanding_amount' => array(
                        ONAPP_FIELD_MAP       => '_outstanding_amount',
                        ONAPP_FIELD_TYPE      => 'float',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'suspend_at' => array(
                        ONAPP_FIELD_MAP       => '_suspend_at',
                        ONAPP_FIELD_TYPE      => 'datetime',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'remember_token_expires_at' => array(
                        ONAPP_FIELD_MAP       => '_remember_token_expires_at',
                        ONAPP_FIELD_TYPE      => 'datetime',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'roles' => array(
                        ONAPP_FIELD_MAP       => '_roles',
                        ONAPP_FIELD_TYPE      => 'array',
                        ONAPP_FIELD_CLASS     => 'Role',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'total_amount' => array(
                        ONAPP_FIELD_MAP       => '_total_amount',
                        ONAPP_FIELD_TYPE      => 'float',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'updated_at' => array(
                        ONAPP_FIELD_MAP       => '_updated_at',
                        ONAPP_FIELD_TYPE      => 'datetime',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'deleted_at' => array(
                        ONAPP_FIELD_MAP       => '_deleted_at',
                        ONAPP_FIELD_TYPE      => 'datetime',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'used_ip_addresses' => array(
                        ONAPP_FIELD_MAP       => '_used_ip_addresses',
                        ONAPP_FIELD_TYPE      => 'array',
                        ONAPP_FIELD_CLASS     => 'IpAddress',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'billing_plan_id' => array(
                        ONAPP_FIELD_MAP       => '_billing_plan_id',
                        ONAPP_FIELD_TYPE      => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'used_disk_size' => array(
                        ONAPP_FIELD_MAP       => '_used_disk_size',
                        ONAPP_FIELD_TYPE      => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'id' => array(
                        ONAPP_FIELD_MAP       => '_id',
                        ONAPP_FIELD_TYPE      => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'group_id' => array(
                        ONAPP_FIELD_MAP       => '_group_id',
                        ONAPP_FIELD_TYPE      => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'user_group_id' => array(
                        ONAPP_FIELD_MAP       => '_user_group_id',
                        ONAPP_FIELD_TYPE      => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'disk_space_available' => array(
                        ONAPP_FIELD_MAP       => '_disk_space_available',
                        ONAPP_FIELD_TYPE      => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'used_cpu_shares' => array(
                        ONAPP_FIELD_MAP       => '_used_cpu_shares',
                        ONAPP_FIELD_TYPE      => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'payment_amount' => array(
                        ONAPP_FIELD_MAP       => '_payment_amount',
                        ONAPP_FIELD_TYPE      => 'decimal',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'remember_token' => array(
                        ONAPP_FIELD_MAP       => '_remember_token',
                        ONAPP_FIELD_TYPE      => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'last_name' => array(
                        ONAPP_FIELD_MAP       => '_last_name',
                        ONAPP_FIELD_TYPE      => 'string',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'time_zone' => array(
                        ONAPP_FIELD_MAP       => '_time_zone',
                        ONAPP_FIELD_TYPE      => 'string',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'locale' => array(
                        ONAPP_FIELD_MAP       => '_locale',
                        ONAPP_FIELD_TYPE      => 'string',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'image_template_group_id' => array(
                        ONAPP_FIELD_MAP       => '_image_template_group_id',
                        ONAPP_FIELD_TYPE      => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'used_cpus' => array(
                        ONAPP_FIELD_MAP       => '_used_cpus',
                        ONAPP_FIELD_TYPE      => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'status' => array(
                        ONAPP_FIELD_MAP       => '_status',
                        ONAPP_FIELD_TYPE      => 'string',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'login' => array(
                        ONAPP_FIELD_MAP       => '_login',
                        ONAPP_FIELD_TYPE      => 'string',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'first_name' => array(
                        ONAPP_FIELD_MAP       => '_first_name',
                        ONAPP_FIELD_TYPE      => 'string',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'email' => array(
                        ONAPP_FIELD_MAP       => '_email',
                        ONAPP_FIELD_TYPE      => 'string',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                );

                break;
        }

        return $this->_fields;
    }

    function activate( $action_name ) {
        switch( $action_name ) {
            case ONAPP_ACTIVATE_GETLIST:
            case ONAPP_ACTIVATE_DELETE:
                die( "Call to undefined method " . __CLASS__ . "::$action_name()" );
                break;
        }
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
        $resource = $this->_resource;
        $this->_loger->debug( "getResource($action): return " . $resource );

        return $resource;
    }

    /**
     * Sends an API request to get the Object after sending,
     * unserializes the response into an object
     *
     * The key field Parameter ID is used to load the Object. You can re-set
     * this parameter in the class inheriting Class ONAPP.
     *
     * @param integer $id Object id
     *
     * @return mixed serialized Object instance from API
     * @access public
     */
    function load( $id = null ) {
        $this->activate( ONAPP_ACTIVATE_LOAD );

        $this->_loger->add( "load: Load class" );

        $this->setAPIResource( $this->getResource( ONAPP_GETRESOURCE_LOAD ) );

        $response = $this->sendRequest( ONAPP_REQUEST_METHOD_GET );

        $result = $this->_castResponseToClass( $response );

        $this->_obj = $result;

        return $result;
    }

}

?>