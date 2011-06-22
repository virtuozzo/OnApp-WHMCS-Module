<?PHP

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Billing Plan Base Resources
 *
 * @category  API WRAPPER
 * @package   ONAPP
 * @author    Yakubskiy Yuriy
 * @copyright 2011 / OnApp
 * @link      http://www.onapp.com/
 * @see       ONAPP
 */

/**
 * require Base class
 */
require_once dirname( __FILE__ ) . '/../ONAPP.php';

require_once 'BaseResource/Price.php';
require_once 'BaseResource/Limit.php';

/**
 * The ONAPP_BillingPlan_BaseResource uses the following basic methods:
 * {@link load}, {@link save}, {@link delete}, and {@link getList}.
 */
class ONAPP_BillingPlan_BaseResource extends ONAPP {

    /**
     * Billing Plan Resource ID
     *
     * @var integer
     */
    var $_id;

    /**
     * creation date in [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var datetime
     */
    var $_created_at;

    /**
     * updating date in [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var datetime
     */
    var $_updated_at;

    /**
     * Billing Plan Resource limits
     *
     * @var array
     */
    var $_limits;

    /**
     * the Billing Plan ID
     *
     * @var integer
     */
    var $_billing_plan_id;
    
    /**
     * Billing Plan Resource unit
     *
     * @var integer
     */
    var $_unit;
    
    /**
     * Billing Plan Resource name
     *
     * @var integer
     */
    var $_resource_name;
    
     /**
     * Billing Plan Resource prices
     *
     * @var array
     */
    var $_prices;

    /**
     * Billing Plan Resource Label
     *
     * @var string
     */
    var $_label;

    /**
     * Billing Plan Resource Limit
     * 
     * @var float
     */
    var $_limit;

    /**
     * Billing Plan Resource free limit
     *
     * @var float
     */
    var $_limit_free;

    /**
     * Billing Plan Resource price
     *
     * @var float
     */
    var $_price;

    /**
     * Billing Plan Resource switch on price
     *
     * @var float
     */
    var $_price_on;

    /**
     * Billing Plan Resource switch off price
     *
     * @var float
     */
    var $_price_off;

    /**
     * root tag used in the API request
     *
     * @var string
     */
    var $_tagRoot = 'base_resource';

    /**
     * alias processing the object data
     *
     * @var string
     */
    var $_resource = 'base_resources';
    
    /**
     *
     * called class name
     *
     * @var string
     */
    var $_called_class = 'ONAPP_BillingPlan_BaseResource';


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
            case '2.1':
                $this->_fields = array(
                    'id' => array(
                        ONAPP_FIELD_MAP => '_id',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                        ONAPP_FIELD_REQUIRED => true
                    ),
                    'label' => array(
                        ONAPP_FIELD_MAP => '_label',
                        ONAPP_FIELD_TYPE => 'string',
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
                    'limits' => array(
                        ONAPP_FIELD_MAP => '_limits',
                        ONAPP_FIELD_READ_ONLY => true,
                        ONAPP_FIELD_CLASS => 'BillingPlan_BaseResource_Limit',
//                        ONAPP_FIELD_REQUIRED => true,
                    ),         
                    'billing_plan_id' => array(
                        ONAPP_FIELD_MAP => '_billing_plan_id',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                        ONAPP_FIELD_REQUIRED => true,
                    ),
                    'unit' => array(
                        ONAPP_FIELD_MAP => '_unit',
                        ONAPP_FIELD_READ_ONLY => true,
                        ONAPP_FIELD_REQUIRED => true,
                    ),
                    'resource_name' => array(
                        ONAPP_FIELD_MAP => '_resource_name',
                        ONAPP_FIELD_TYPE => 'string',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'prices' => array(
                        ONAPP_FIELD_MAP => '_prices',
                        ONAPP_FIELD_READ_ONLY => true,
                        ONAPP_FIELD_CLASS => 'BillingPlan_BaseResource_Price',
//                        ONAPP_FIELD_REQUIRED => true,
                    ),

                    'limit'  => array(
                        ONAPP_FIELD_MAP => '_limit',
                        ONAPP_FIELD_TYPE => 'string',
                        ONAPP_FIELD_REQUIRED => true,
                    ),
                    'limit_free' => array(
                        ONAPP_FIELD_MAP => '_limit_free',
                        ONAPP_FIELD_TYPE => 'string',
                        ONAPP_FIELD_REQUIRED => true,
                    ),
                    'price' => array(
                        ONAPP_FIELD_MAP => '_price',
                        ONAPP_FIELD_TYPE => 'string',
                        ONAPP_FIELD_REQUIRED => true,
                    ),
                    'price_on' => array(
                        ONAPP_FIELD_MAP => '_price_on',
                        ONAPP_FIELD_TYPE => 'string',
                        ONAPP_FIELD_REQUIRED => true,
                    ),
                    'price_off' => array(
                        ONAPP_FIELD_MAP => '_price_off',
                        ONAPP_FIELD_TYPE => 'string',
                        ONAPP_FIELD_REQUIRED => true,
                    ),
                    'resource_class' => array(
                        ONAPP_FIELD_MAP => '_resource_class',
                        ONAPP_FIELD_TYPE => 'string',
                        ONAPP_FIELD_REQUIRED => true,
                        ONAPP_FIELD_DEFAULT_VALUE => 'Resource::CpuShare'
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
        $show_log_msg = true;
        switch( $action ) {
            case ONAPP_GETRESOURCE_DEFAULT:
                if( is_null( $this->_billing_plan_id ) && is_null( $this->_obj->_billing_plan_id ) ) {
                    $this->_loger->error(
                        "getResource($action): argument _billing_plan_id not set.",
                        __FILE__,
                        __LINE__
                    );
                }
                else {
                    if( is_null( $this->_billing_plan_id ) ) {
                        $this->_billing_plan_id = $this->_obj->_billing_plan_id;
                    }
                }

                $resource = 'billing_plans/' . $this->_billing_plan_id . '/' . $this->_resource;
                break;

            default:
                $resource = parent::getResource( $action );
                $show_log_msg = false;
                break;
        }

        if( $show_log_msg ) {
            $this->_loger->debug( "getResource($action): return " . $resource );
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
     function getList( $billing_plan_id = null ) {
        if( is_null( $billing_plan_id ) && !is_null( $this->_billing_plan_id ) )
            $billing_plan_id = $this->_billing_plan_id;

        if( !is_null( $billing_plan_id ) ) {
            $this->_billing_plan_id = $billing_plan_id;

            return parent::getList( );
        }
        else {
            $this->_loger->error(
                'getList: argument _billing_plan_id not set.',
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
     * This method can be closed for read only objects of the inherited class
     * <code>
     *    function save() {
     *        $this->_loger->error(
     *            "Call to undefined method ".__CLASS__."::save()",
     *            __FILE__,
     *            __LINE__
     *        );
     *    }
     * </code>
     *
     * @return void
     * @access public
     */
    function save( ) {

        if( is_null($this->_limit) )
            $this->_limit = isset($this->_limits->_limit)
                ? $this->_limits->_limit : ( 
                    isset($this->_obj->_limits->_limit) 
                    ? $this->_obj->_limits->_limit 
                    : ""
                );

        if( is_null($this->_limit_free) )
            $this->_limit_free = isset($this->_limits->_limit_free)
                ? $this->_limits->_limit_free : (
                    isset($this->_obj->_limits->_limit_free)
                    ? $this->_obj->_limits->_limit_free
                    : ""
                );

        if( is_null($this->_price_on) )
            $this->_price_on = isset($this->_prices->_price_on)
                ? $this->_prices->_price_on : (
                    isset($this->_obj->_prices->_price_on) ?
                    $this->_obj->_prices->_price_on 
                    : ""
                );

        if( is_null($this->_price_off) )
            $this->_price_off = isset($this->_limits->_price_off)
                ? $this->_prices->_price_off : (
                    isset($this->_obj->_prices->_price_off) 
                    ? $this->_obj->_prices->_price_off
                    : ""
                );

        if( is_null($this->_price) )
            $this->_price = isset($this->_limits->_price)
                ? $this->_prices->_price
                : (isset($this->_obj->_prices->_price)
                ? $this->_obj->_prices->_price
                : "");

        return parent::save( );
    }
}
