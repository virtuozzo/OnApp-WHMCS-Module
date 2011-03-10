<?PHP
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Managing User Payments
 *
 * Payments list the invoices paid by the users.
 * Once the invoice is paid, you have to put it to the system to keep track of
 * them. 
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

/**
 * User Payments
 * 
 * This class represents the user payments entered to the system.
 *
 * The Payment class uses the following basic methods:
 * {@link load}, {@link save}, {@link delete}, and {@link getList}.
 *
 * <b>Use the following XML API requests:</b>
 *
 * Get the list of payments
 *
 *     - <i>GET onapp.com/payments.xml</i>
 *
 * Get a particular payment details 
 *
 *     - <i>GET onapp.com/payments/{ID}.xml</i>
 *
 * Add new payment
 *
 *     - <i>POST onapp.com/payments.xml</i>
 *
 * <code>
 * <?xml version="1.0" encoding="UTF-8"?>
 * <payment>
 *     <amount>{AMOUNT}<amount>
 *     <invoice-number>{NUMBER}<invoice-number>
 *     <user-id>{ID}<user-id>
 * </payment>
 * </code>
 *
 * Edit existing payment
 *
 *     - <i>PUT onapp.com/payments/{ID}.xml</i>
 *
 * <code>
 * <?xml version="1.0" encoding="UTF-8"?>
 * <payment>
 *     <amount>{AMOUNT}<amount>
 *     <invoice-number>{NUMBER}<invoice-number>
 *     <user-id>{ID}<user-id>
 * </payment>
 * </code>
 *
 * Delete payment
 *
 *     - <i>DELETE onapp.com/payments/{ID}.xml</i>
 *
 * <b>Use the following JSON API requests:</b>
 *
 * Get the list of payments
 *
 *     - <i>GET onapp.com/payments.json</i>
 *
 * Get a particular payment details 
 *
 *     - <i>GET onapp.com/payments/{ID}.json</i>
 *
 * Add new payment
 *
 *     - <i>POST onapp.com/payments.json</i>
 *
 * <code>
 * { 
 *      payment: {
 *          amount:{AMOUNT},
 *          invoice-number:{NUMBER},
 *          user-id:{ID}
 *      }
 * }
 * </code>
 *
 * Edit existing payment
 *
 *     - <i>PUT onapp.com/payments/{ID}.json</i>
 *
 * <code>
 * { 
 *      payment: {
 *          amount:{AMOUNT},
 *          invoice-number:{NUMBER},
 *          user-id:{ID}
 *      }
 * }
 * </code>
 *
 * Delete payment
 *
 *     - <i>DELETE onapp.com/payments/{ID}.json</i>
 */
class ONAPP_Payment extends ONAPP {

    /**
     * the payment ID
     *
     * @var integer
     */
    var $_id;
    
    /**
     * the amount paid
     *
     * @var integer
     */
    var $_amount;

    /**
     * the date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var datetime
     */
    var $_created_at;
    
    /**
     * the invoice number used for organizational purposes
     *
     * @var integer
     */
    var $_invoice_number;

    /**
     * the date when the payment was updated in the [YYYY][MM][DD]T[hh][mm]Z format  
     *
     * @var datetime
     */
    var $_updated_at;
    
    /**
     * the ID of the user who made a payment
     *
     * @var integer
     */
    var $_user_id;
    
    /**
     * root tag used in the API request
     *
     * @var string
     */
    var $_tagRoot  = 'payment';
    
    /**
     * alias processing the object data
     *
     * @var string
     */
    var $_resource = 'payments';
    
    /**
     * 
     * called class name
     * 
     * @var string
     */
    var $_called_class = 'ONAPP_Payment';
    
    /**
     * API Fields description
     *
     * @access private
     * @var    array
     */
    function _fields_2_0_0() {
        return $_fields   = array(
            'id' => array(
                ONAPP_FIELD_MAP           => '_id',
                ONAPP_FIELD_TYPE          => 'integer',
                ONAPP_FIELD_READ_ONLY     => true,
            //    ONAPP_FIELD_DEFAULT_VALUE => ''
            ),
            'amount' => array(
                ONAPP_FIELD_MAP           => '_amount',
                ONAPP_FIELD_TYPE          => 'decimal',
                ONAPP_FIELD_REQUIRED      => true,
                ONAPP_FIELD_DEFAULT_VALUE => '0.0',
            ),
            'created_at' => array(
                ONAPP_FIELD_MAP           => '_created_at',
                ONAPP_FIELD_TYPE          => 'datetime',
                ONAPP_FIELD_READ_ONLY     => true
            //    ONAPP_FIELD_DEFAULT_VALUE => ''
            ),
           'invoice_number' => array(
                ONAPP_FIELD_MAP           => '_invoice_number',
                ONAPP_FIELD_REQUIRED      => true,
            ),
            'updated_at' => array(
                ONAPP_FIELD_MAP           => '_updated_at',
                ONAPP_FIELD_TYPE          => 'datetime',
                ONAPP_FIELD_READ_ONLY     => true
            //    ONAPP_FIELD_DEFAULT_VALUE => ''
            ),
            'user_id' => array(
                ONAPP_FIELD_MAP           => '_user_id',
                ONAPP_FIELD_TYPE          => 'integer',
                ONAPP_FIELD_REQUIRED      => true,
                ONAPP_FIELD_READ_ONLY     => true
            ),
        );
    }

    function getResource($action = ONAPP_GETRESOURCE_DEFAULT) {
        switch ($action) {
            case ONAPP_GETRESOURCE_DEFAULT:
                $resource = 'users/' . $this->_user_id . '/' . $this->_resource;
                $this->_loger->debug("getResource($action): return ".$resource);
                break;

            default:
                $resource = parent::getResource($action);
                break;
        }

        return $resource;
    }

}

?>
