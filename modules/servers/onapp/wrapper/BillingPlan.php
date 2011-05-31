<?php
/**
 * Managing Billing Plans
 *
 * Billing Plans are created to set prices for the resources so that users know how
 * much they will be charged per unit.
 *
 * @category  API WRAPPER
 * @package   ONAPP
 * @author    Lev Bartashevsky
 * @copyright 2010 / OnApp
 * @link     http://www.onapp.com/
 * @see       ONAPP
 */

/**
 * requires Base class
 */
require_once 'ONAPP.php';
require_once 'Logger.php';
require_once 'User.php';

/**
 *
 *
 */
define( 'ONAPP_GETRESOURCE_GETLIST_USERS', 'users' );


/**
 * Managing Billing Plans
 *
 * The ONAPP_BillingPlan class represents the billing plans.  The ONAPP class is the parent of the BillingPlan class.
 *
 * The ONAPP_BillingPlan class uses the following basic methods:
 * {@link load}, {@link save}, {@link delete}, and {@link getList}.
 *
 * <b>Use the following XML API requests:</b>
 *
 * Get the list of groups
 *
 *     - <i>GET onapp.com/settings/billing_plans.xml</i>
 *
 * Get a particular group details
 *
 *     - <i>GET onapp.com/settings/billing_plans/{ID}.xml</i>
 *
 * Add new group
 *
 *     - <i>POST onapp.com/settings/billing_plans.xml</i>
 *
 * <code>
 * <?xml version="1.0" encoding="UTF-8"?>
 * <billing-plan>
 *    <label>{LABEL}</label>
 * </billing-plan>
 * </code>
 *
 * Edit existing group
 *
 *     - <i>PUT onapp.com/settings/billing_plans/{ID}.xml</i>
 *
 * <code>
 * <?xml version="1.0" encoding="UTF-8"?>
 * <billing-plan>
 *    <label>{LABEL}</label>
 * </billing-plan>
 * </code>
 *
 * Delete group
 *
 *     - <i>DELETE onapp.com/settings/billing_plans/{ID}.xml</i>
 *
 * <b>Use the following JSON API requests:</b>
 *
 * Get the list of groups
 *
 *     - <i>GET onapp.com/settings/billing_plans.json</i>
 *
 * Get a particular group details
 *
 *     - <i>GET onapp.com/settings/billing_plans/{ID}.json</i>
 *
 * Add new group
 *
 *     - <i>POST onapp.com/settings/billing_plans.json</i>
 *
 * <code>
 * {
 *      billing-plan: {
 *          label:'{LABEL}',
 *      }
 * }
 * </code>
 *
 * Edit existing group
 *
 *     - <i>PUT onapp.com/settings/billing_plans/{ID}.json</i>
 *
 * <code>
 * {
 *      billing-plan: {
 *          label:'{LABEL}',
 *      }
 * }
 * </code>
 *
 * Delete group
 *
 *     - <i>DELETE onapp.com/settings/billing_plans/{ID}.json</i>
 */
class ONAPP_BillingPlan extends ONAPP {
    /**
     *
     * the group ID
     */
    var $_id;

    /**
     * Billing Plan creation date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_created_at;

    /**
     * Billing Plan update date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_updated_at;

    /**
     * Billing Plan label
     *
     * @var string
     */
    var $_label;

    /**
     * the group identifier
     *
     * @var integer
     */
    var $_currency_code;

    /**
     * the group identifier
     *
     * @var integer
     */
    var $_show_price;

    /**
     * root tag used in the API request
     *
     * @var string
     */
    var $_tagRoot = 'billing-plans';

    /**
     * alias processing the object data
     *
     * @var string
     */
    var $_resource = 'billing_plans';

    /**
     *
     * called class name
     *
     * @var string
     */
    var $_called_class = 'ONAPP_BillingPlan';

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
        ;

        switch( $version ) {
            case '2.0':
            case '2.1':
                $this->_fields = array(
                    'label' => array(
                        ONAPP_FIELD_MAP => '_label',
                        ONAPP_FIELD_REQUIRED => true,
                        ONAPP_FIELD_DEFAULT_VALUE => ''
                    ),
                    'created_at' => array(
                        ONAPP_FIELD_MAP => '_created_at',
                        ONAPP_FIELD_TYPE => 'datetime',
                        ONAPP_FIELD_READ_ONLY => true
                        //    ONAPP_FIELD_DEFAULT_VALUE => ''
                    ),
                    'updated_at' => array(
                        ONAPP_FIELD_MAP => '_updated_at',
                        ONAPP_FIELD_TYPE => 'datetime',
                        ONAPP_FIELD_READ_ONLY => true
                        //    ONAPP_FIELD_DEFAULT_VALUE => ''
                    ),
                    'id' => array(
                        ONAPP_FIELD_MAP => '_id',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                        //    ONAPP_FIELD_DEFAULT_VALUE => ''
                    ),
                    'currency_code' => array(
                        ONAPP_FIELD_MAP => '_currency_code',
                        ONAPP_FIELD_TYPE => 'string',
                        ONAPP_FIELD_REQUIRED => true,
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'show_price' => array(
                        ONAPP_FIELD_MAP => '_show_price',
                        ONAPP_FIELD_TYPE => 'boolean',
                        ONAPP_FIELD_REQUIRED => true,
                        ONAPP_FIELD_DEFAULT_VALUE => true,
                        ONAPP_FIELD_READ_ONLY => true
                    )
                );

                break;
        }
        ;

        return $this->_fields;
    }

    function getResource( $action = ONAPP_GETRESOURCE_DEFAULT ) {
        switch( $action ) {
            case ONAPP_GETRESOURCE_GETLIST_USERS:
                $resource = $this->getResource( ONAPP_GETRESOURCE_LOAD ) . '/users';
                break;
            default:
                $resource = parent::getResource( $action );
                break;
        }

        return $resource;
    }

    function users( ) {

        $this->_loger->add( "getList: Get Users list." );

        $this->setAPIResource( $this->getResource( ONAPP_GETRESOURCE_GETLIST_USERS ) );

        $response = $this->sendRequest( ONAPP_REQUEST_METHOD_GET );

        if( !empty( $response[ 'errors' ] ) ) {
            $this->error = $response[ 'errors' ];
            return false;
        }

        $class = new ONAPP_User(); 

        $class->_loger = $this->_loger;

        $class->options = $this->options;

        $class->_loger->setTimezone( );

        $class->_ch = $this->_ch;

        $class->_load_fields( );

        return $class->castStringToClass(
            $response[ "response_body" ],
            true
        );
    }
}
