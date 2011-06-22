<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Managing Users
 *
 * Users are created by administrators and have access only to those actions
 * which are specified by the administrator. You can add as many users as you
 * need. When creating, you can edit User Details, track Payments, and set the
 * Limits.
 * With OnApp you can assign resource limits to users. This will prevent users
 * from exceeding the resources you specify.
 *
 * @category  API WRAPPER
 * @package   ONAPP
 * @author  Andrew Yatskovets
 * @copyright 2010 / OnApp
 * @link      http://www.onapp.com/
 * @see    ONAPP
 */

/**
 * requires Base class
 */
require_once 'ONAPP.php';
require_once 'Role.php';
require_once 'User/UsedIpAddress.php';

/**
 *
 */
define( 'ONAPP_GETRESOURCE_SUSPEND_USER', 'suspend' );

/**
 *
 */
define( 'ONAPP_GETRESOURCE_ACTIVATE', 'activate' );

/**
 * Users
 *
 * The User class represents the Users of the OnApp installation.
 *
 * The ONAPP_User class uses the following basic methods:
 * {@link load}, {@link save}, {@link delete}, and {@link getList}.
 *
 * <b>Use the following XML API requests:</b>
 *
 * Get the list of users
 *
 *   - <i>GET onapp.com/users.xml</i>
 *
 * Get a particular user details
 *
 *   - <i>GET onapp.com/users/{ID}.xml</i>
 *
 * Add new user
 *
 *   - <i>POST onapp.com/users.xml</i>
 *
 * <code>
 * <?xml version="1.0" encoding="UTF-8"?>
 * <users>
 *  <email>{EMAIL}</email>
 *  <first-name>{FIRST NAME}</first-name>
 *  <last-name>{LAST NAME}</last-name>
 *  <login>{LOGIN}</login>
 *  <password>{PASSWORD}</password>
 * </users>
 * </code>
 *
 * Edit existing user
 *
 *   - <i>PUT onapp.com/users/{ID}.xml</i>
 *
 * <code>
 * <?xml version="1.0" encoding="UTF-8"?>
 * <users>
 *  <email>{EMAIL}</email>
 *  <first-name>{FIRST NAME}</first-name>
 *  <last-name>{LAST NAME}</last-name>
 *  <login>{LOGIN}</login>
 * </users>
 * </code>
 *
 * Delete user
 *
 *   - <i>DELETE onapp.com/users/{ID}.xml</i>
 *
 * <b>Use the following JSON API requests:</b>
 *
 * Get the list of users
 *
 *   - <i>GET onapp.com/users.json</i>
 *
 * Get a particular user details
 *
 *   - <i>GET onapp.com/users/{ID}.json</i>
 *
 * Add new user
 *
 *   - <i>POST onapp.com/users.json</i>
 *
 * <code>
 * {
 *    users: {
 *        email:'{EMAIL}',
 *        first-name:'{FIRST NAME}',
 *        last-name:'{LAST NAME}',
 *        login:'{LOGIN}',
 *        password:'{PASSWORD}'
 *    }
 * }
 * </code>
 *
 * Edit existing user
 *
 *   - <i>PUT onapp.com/users/{ID}.json</i>
 *
 * <code>
 * {
 *    users: {
 *        email:'{EMAIL}',
 *        first-name:'{FIRST NAME}',
 *        last-name:'{LAST NAME}',
 *        login:'{LOGIN}'
 *    }
 * }
 * </code>
 *
 * Delete user
 *
 *   - <i>DELETE onapp.com/users/{ID}.json</i>
 */
class ONAPP_User extends ONAPP {

    /**
     * the user ID
     *
     * @var integer
     */
    var $_id;

    /**
     * the date when the User was activated in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_activated_at;

    /**
     * the code to activate the user
     *
     * @var integer
     */
    var $_activation_code;

    /**
     * the date when user was created in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_created_at;

    /**
     * the user email
     *
     * @var integer
     */
    var $_email;

    /**
     * the user first name
     *
     * @var integer
     */
    var $_first_name;

    /**
     * the user outstanding amount
     *
     * @var float
     */
    var $_outstanding_amount;

    /**
     * the user payment amount
     *
     * @var float
     */
    var $_payment_amount;

    /**
     * the user group ID
     *
     * @var integer
     */
    var $_group_id;

    /**
     * the user last name
     *
     * @var integer
     */
    var $_last_name;

    /**
     * the user login
     *
     * @var integer
     */
    var $_login;

    /**
     * the session ID
     *
     * @var integer
     */
    var $_remember_token;

    /**
     * the date when User was deleted in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_deleted_at;

    /**
     * the date when the session ID expires in the [YYYY][MM][DD]T[hh][mm]Z format
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
     * the user time zone
     *
     * @var string
     */
    var $_time_zone;

    /**
     * the user total amount
     *
     * @var float
     */
    var $_total_amount;

    /**
     * the date when the User was updated in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_updated_at;

    /**
     * the user used CPU shares
     *
     * @var integer
     */
    var $_used_cpu_shares;

    /**
     * the user used CPUs
     *
     * @var integer
     */
    var $_used_cpus;

    /**
     * the user used disc size
     *
     * @var integer
     */
    var $_used_disk_size;

    /**
     * the user used IP addresses
     *
     * @var integer
     */
    var $_used_ip_addresses;

    /**
     * the user used memory
     *
     * @var integer
     */
    var $_used_memory;

    /**
     * user password
     *
     * @var string
     */
    var $_password;

    /**
     * user password confirmation
     *
     * @var string
     */
    var $_password_confirmation;

    /**
     * available memory for user
     *
     * @var integer
     */
    var $_memory_available;

    /**
     * available disck space for user
     *
     * @var integer
     */
    var $_disk_space_available;

    /**
     * user status
     *
     * @var string
     */
    var $_status;

    /**
     * user role ids array
     *
     * @var array
     */
    var $_role_ids;

    /**
     * use billing plan id
     *
     * @var integer
     */
    var $_billing_plan_id;

    /**
     * user template group id
     *
     * @var integer
     */
    var $_image_template_group_id;

    /**
     * the date when the User was suspended in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_suspend_at;

    /**
     * user group id
     *
     * @var integer
     */
    var $_user_group_id;

    /**
     * user locale
     *
     * @var string
     */
    var $_locale;

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
    var $_resource = 'users';

    /**
     *
     * called class name
     *
     * @var string
     */
    var $_called_class = 'ONAPP_User';

    /**
     * API Fields description
     *
     * @access private
     * @var array
     */
    function _init_fields( $version = NULL ) {
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
                    'activated_at' => array(
                        ONAPP_FIELD_MAP => '_activated_at',
                        ONAPP_FIELD_TYPE => 'datetime',
                        ONAPP_FIELD_READ_ONLY => true
                    ),
                    'activation_code' => array(
                        ONAPP_FIELD_MAP => '_activation_code',
                        ONAPP_FIELD_READ_ONLY => true
                    ),
                    'created_at' => array(
                        ONAPP_FIELD_MAP => '_created_at',
                        ONAPP_FIELD_TYPE => 'datetime',
                        ONAPP_FIELD_READ_ONLY => true
                    ),
                    'email' => array(
                        ONAPP_FIELD_MAP => '_email',
                        ONAPP_FIELD_DEFAULT_VALUE => '',
                        ONAPP_FIELD_REQUIRED => true,
                    ),
                    'first_name' => array(
                        ONAPP_FIELD_MAP => '_first_name',
                        ONAPP_FIELD_READ_ONLY => true,
                        ONAPP_FIELD_REQUIRED => true,
                    ),
                    'outstanding_amount' => array(
                        ONAPP_FIELD_MAP => '_outstanding_amount',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'payment_amount' => array(
                        ONAPP_FIELD_MAP => '_payment_amount',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'group_id' => array(
                        ONAPP_FIELD_MAP => '_group_id',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_REQUIRED => true
                    ),
                    'last_name' => array(
                        ONAPP_FIELD_MAP => '_last_name',
                        ONAPP_FIELD_READ_ONLY => true,
                        ONAPP_FIELD_REQUIRED => true,
                    ),
                    'login' => array(
                        ONAPP_FIELD_MAP => '_login',
                        ONAPP_FIELD_REQUIRED => true,
                    ),
                    'remember_token' => array(
                        ONAPP_FIELD_MAP => '_remember_token',
                        ONAPP_FIELD_READ_ONLY => true
                    ),
                    'deleted_at' => array(
                        ONAPP_FIELD_MAP => '_deleted_at',
                        ONAPP_FIELD_READ_ONLY => true
                    ),
                    'remember_token_expires_at' => array(
                        ONAPP_FIELD_MAP => '_remember_token_expires_at',
                        ONAPP_FIELD_TYPE => 'datetime',
                        ONAPP_FIELD_READ_ONLY => true
                    ),
                    'roles' => array(
                        ONAPP_FIELD_MAP => '_roles',
                        ONAPP_FIELD_TYPE => 'array',
                        ONAPP_FIELD_CLASS => 'Role',
                    ),
                    'time_zone' => array(
                        ONAPP_FIELD_MAP => '_time_zone',
                        ONAPP_FIELD_TYPE => 'string',
                    ),
                    'total_amount' => array(
                        ONAPP_FIELD_MAP => '_total_amount',
                        ONAPP_FIELD_READ_ONLY => true
                    ),
                    'updated_at' => array(
                        ONAPP_FIELD_MAP => '_updated_at',
                        ONAPP_FIELD_TYPE => 'datetime',
                        ONAPP_FIELD_READ_ONLY => true
                    ),
                    'used_cpu_shares' => array(
                        ONAPP_FIELD_MAP => '_used_cpu_shares',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true
                    ),
                    'used_cpus' => array(
                        ONAPP_FIELD_MAP => '_used_cpus',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true
                    ),
                    'used_disk_size' => array(
                        ONAPP_FIELD_MAP => '_used_disk_size',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true
                    ),
                    'used_ip_addresses' => array(
                        ONAPP_FIELD_MAP => '_used_ip_addresses',
                        ONAPP_FIELD_TYPE => 'array',
                        ONAPP_FIELD_READ_ONLY => true,
                        ONAPP_FIELD_CLASS => 'User_UsedIpAddress',
                    ),
                    'used_memory' => array(
                        ONAPP_FIELD_MAP => '_used_memory',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true
                    ),
                    'memory_available' => array(
                        ONAPP_FIELD_MAP => '_memory_available',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true
                    ),
                    'disk_space_available' => array(
                        ONAPP_FIELD_MAP => '_disk_space_available',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true
                    ),
                    'status' => array(
                        ONAPP_FIELD_MAP => '_status'
                    ),
                    'password' => array(
                        ONAPP_FIELD_MAP => '_password',
                    ),
                    'password_confirmation' => array(
                        ONAPP_FIELD_MAP => '_password_confirmation',
                    ),
                );

                break;

            case '2.1':
                $this->_fields = $this->_init_fields( '2.0' );

                unset($this->_fields['activation_code']);

                $this->_fields[ 'group_id' ][ ONAPP_FIELD_REQUIRED ] = false;

                $this->_fields[ 'billing_plan_id' ] = array(
                    ONAPP_FIELD_MAP => '_billing_plan_id',
                    ONAPP_FIELD_TYPE => 'integer',
                    ONAPP_FIELD_REQUIRED => true
                );

                $this->_fields[ 'role_ids' ] = array(
                    ONAPP_FIELD_MAP => '_role_ids',
                    ONAPP_FIELD_REQUIRED => true,
                );

                $this->_fields[ 'image_template_group_id' ] = array(
                    ONAPP_FIELD_MAP => '_image_template_group_id',
                    ONAPP_FIELD_TYPE => 'integer',
                );

                $this->_fields[ 'suspend_at' ] = array(
                    ONAPP_FIELD_MAP => '_suspend_at',
                    ONAPP_FIELD_TYPE => 'string',
                    ONAPP_FIELD_READ_ONLY => true
                );

                $this->_fields[ 'user_group_id' ] = array(
                    ONAPP_FIELD_MAP => '_user_group_id',
                    ONAPP_FIELD_TYPE => 'integer',
                );

                $this->_fields[ 'locale' ] = array(
                    ONAPP_FIELD_MAP => '_locale',
                    ONAPP_FIELD_TYPE => 'integer',
                    ONAPP_FIELD_REQUIRED => true,
                    ONAPP_FIELD_DEFAULT_VALUE => 'en',
                );

                break;
        }

        if( is_null( $this->_id ) ) {
            $this->_fields[ "password" ] = array(
                ONAPP_FIELD_MAP => '_password',
                ONAPP_FIELD_REQUIRED => true,
            );
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
            case ONAPP_GETRESOURCE_SUSPEND_USER:
                $resource = $this->getResource( ONAPP_GETRESOURCE_LOAD ) . "/suspend";
                break;

            case ONAPP_GETRESOURCE_ACTIVATE:
                $resource = $this->getResource( ONAPP_GETRESOURCE_LOAD ) . "/activate_user";
                break;

            default:
                $resource = parent::getResource( $action );
                break;
        }

        $actions = array(
            ONAPP_GETRESOURCE_SUSPEND_USER,
            ONAPP_GETRESOURCE_ACTIVATE,
        );
        if( in_array( $action, $actions ) ) {
            $this->_loger->debug( "getResource($action): return " . $resource );
        }

        return $resource;
    }

    /**
     * Suspend User
     *
     * @access public
     */
    function suspend( ) {
        $this->setAPIResource( $this->getResource( ONAPP_GETRESOURCE_SUSPEND_USER ) );

        $response = $this->sendRequest( ONAPP_REQUEST_METHOD_GET );

        $result = $this->_castResponseToClass( $response );

        $this->_obj = $result;
    }

    /**
     * Activate User
     *
     * @access public
     */
    function activate_user( ) {
        $this->setAPIResource( $this->getResource( ONAPP_GETRESOURCE_ACTIVATE ) );

        $response = $this->sendRequest( ONAPP_REQUEST_METHOD_GET );

        $result = $this->_castResponseToClass( $response );

        $this->_obj = $result;
    }

    /**
     * Save Object in to your account.
     */
    function save( ) {
        $this->_role_ids = $this->fillRolesIDs( );

        if( is_null( $this->_id ) ) {
            $obj = $this->_create( );

            unset( $this->_fields[ 'password' ] );
        }
        else {
            if( isset( $this->_password ) ) {
                $this->_fields[ 'password' ][ ONAPP_FIELD_REQUIRED ] = true;
                $this->_fields[ 'password_confirmation' ][ ONAPP_FIELD_REQUIRED ] = true;
            }

            $obj = $this->_edit( );
            unset( $this->_fields[ 'password' ], $this->_fields[ 'password_confirmation' ] );
        }

        if( isset( $obj ) && !isset( $obj->error ) ) {
            $this->load( );
        }
    }

    function load( $id = null ) {
        $result = parent::load( $id );

        $this->_init_fields( );

        return $result;
    }

    function fillRolesIDs( ) {
        if( is_null( $this->_role_ids ) ) {
            $ids = array( );
            if( !is_null( $this->_roles ) ) {
                $data = $this->_roles;
            }
            elseif( !is_null( $this->_obj->_roles ) ) {
                $data = $this->_obj->_roles;
            }
            else {
                return null;
            }

            foreach( $data as $role ) {
                $ids[ ] = $role->_id;
            }

            return $ids;
        }
        else {
            return $this->_role_ids;
        }
    }
}
