<?PHP
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Managing Hypervisors
 *
 * In computing, a hypervisor, also called virtual machine monitor (VMM), allows
 * multiple operating systems to run concurrently on a host computer - a feature
 * called hardware virtualization. The hypervisor presents the guest operating
 * systems with a virtual platform  and monitors the execution of the guest
 * operating systems. In that way, multiple operating systems, including
 * multiple instances of the same operating system, can share hardware
 * resources.
 * In OnApp the Hypervisor servers:
 *    - Provide the system resources, such as CPU, memory, and network
 *    - Control the virtual differentiation of entities, such as machines and corresponding application data being delivered to cloud-hosted applications
 *    - Take care of secure virtualization and channeling of storage, data communications and machine processing
 *    - Can be located at different geographical zones
 *    - Can have different CPU and RAM

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
 * Hypervisors
 *
 * This class represents the Hypervisors of your OnApp installation. The ONAPP class is the parent of the Hypervisors class.
 *
 * The ONAPP_Hypervisor class uses the following basic methods:
 * {@link load}, {@link save}, {@link delete}, and {@link getList}.
 *
 * <b>Use the following XML API requests:</b>
 *
 * Get the list of hypervisors
 *
 *     - <i>GET onapp.com/settings/settings/hypervisors.xml</i>
 *
 * Get a particular hypervisor details
 *
 *     - <i>GET onapp.com/settings/hypervisors/{ID}.xml</i>
 *
 * Add new hypervisor
 *
 *     - <i>POST onapp.com/settings/hypervisors.xml</i>
 *
 * <code>
 * <?xml version="1.0" encoding="UTF-8"?>
 * <hypervisor>
 *    <ip_address>{IP}</ip_address>
 *    <label>{LABEL}</label>
 * </hypervisor>
 * </code>
 *
 * Edit existing hypervisor
 *
 *     - <i>PUT onapp.com/settings/hypervisors/{ID}.xml</i>
 *
 * <code>
 * <?xml version="1.0" encoding="UTF-8"?>
 * <hypervisor>
 *     <ip_address>{IP}</ip_address>
 *     <label>{LABEL}</label>
 * </hypervisor>
 * </code>
 *
 * Delete hypervisor
 *
 *     - <i>DELETE onapp.com/settings/hypervisors/{ID}.xml</i>
 *
 * <b>Use the following JSON API requests:</b>
 *
 * Get the list of hypervisors
 *
 *     - <i>GET onapp.com/settings/hypervisors.json</i>
 *
 * Get a particular hypervisor details
 *
 *     - <i>GET onapp.com/settings/hypervisors/{ID}.json</i>
 *
 * Add new hypervisor
 *
 *     - <i>POST onapp.com/settings/hypervisors.json</i>
 *
 * <code>
 * {
 *      hypervisor: {
 *          ip_address:{IP},
 *          label:'{LABEL}'
 *      }
 * }
 * </code>
 *
 * Edit existing hypervisor
 *
 *     - <i>PUT onapp.com/settings/hypervisors/{ID}.json</i>
 *
 * <code>
 * {
 *      hypervisor: {
 *          ip_address:{IP},
 *          label:'{LABEL}'
 *      }
 * }
 * </code>
 *
 * Delete hypervisor
 *
 *     - <i>DELETE onapp.com/settings/hypervisors/{ID}.json</i>
 */
class ONAPP_Hypervisor extends ONAPP {

    /**
     * the Hypervisor ID
     *
     * @var integer
     */
    var $_id;

    /**
     * the Hypervisor call date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_called_in_at;

    /**
     * the Hypervisor creation date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_created_at;

    /**
     * the number of failures
     *
     * @var integer
     */
    var $_failure_count;

    /**
     * the array of the xm_info, disk, memory, and xm_list variables
     *
     * @var yaml
     */
    var $_health;

    /**
     * the Hypervisor IP address
     *
     * @var string
     */
    var $_ip_address;

    /**
     * the Hypervisor Label
     *
     * @var string
     */
    var $_label;

    /**
     * true if the Hypervisor is locked, otherwise false
     *
     * @var boolean
     */
    var $_locked;

    /**
     * shows the memory overhead
     *
     * @var integer
     */
    var $_memory_overhead;

    /**
     * true if online, otherwise false
     *
     * @var boolean
     */
    var $_online;

    /**
     * true if spare, otherwise false
     *
     * @var boolean
     */
    var $_spare;

    /**
     * the Hypervisor update date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_updated_at;

    /**
     * the info on the Xen
     *
     * @var yaml
     */
    var $_xen_info;

    /**
     * shows whether hypervisor is enabled
     *
     * @var boolean
     */
    var $_enabled;

    /**
     * Hypervisor group id
     *
     * @var integer
     */
    var $_hypervisor_group_id;

    /**
     * Hypervisor group id
     *
     * @var string
     */
    var $_hypervisor_type;

    /**
     * root tag used in the API request
     *
     * @var string
     */
    var $_tagRoot = 'hypervisor';

    /**
     * alias processing the object data
     *
     * @var string
     */
    var $_resource = 'settings/hypervisors';

    /**
     *
     * called class name
     *
     * @var string
     */
    var $_called_class = 'ONAPP_Hypervisor';

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
                $this->_fields = array(
                    'id' => array(
                        ONAPP_FIELD_MAP => '_id',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true
                        //    ONAPP_FIELD_DEFAULT_VALUE => ''
                    ),
                    'called_in_at' => array(
                        ONAPP_FIELD_MAP => '_called_in_at',
                        ONAPP_FIELD_TYPE => 'datetime',
                        ONAPP_FIELD_READ_ONLY => true,
                        //    ONAPP_FIELD_DEFAULT_VALUE => ''
                    ),
                    'created_at' => array(
                        ONAPP_FIELD_MAP => '_created_at',
                        ONAPP_FIELD_TYPE => 'datetime',
                        ONAPP_FIELD_READ_ONLY => true,
                        //    ONAPP_FIELD_DEFAULT_VALUE => ''
                    ),
                    'failure_count' => array(
                        ONAPP_FIELD_MAP => '_failure_count',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                        //    ONAPP_FIELD_DEFAULT_VALUE => ''
                    ),
                    'health' => array(
                        ONAPP_FIELD_MAP => '_health',
                        ONAPP_FIELD_TYPE => 'yaml',
                        ONAPP_FIELD_READ_ONLY => true,
                        //    ONAPP_FIELD_DEFAULT_VALUE => ''
                    ),
                    'ip_address' => array(
                        ONAPP_FIELD_MAP => '_ip_address',
                        ONAPP_FIELD_READ_ONLY => true,
                        ONAPP_FIELD_REQUIRED => true,
                        //    ONAPP_FIELD_DEFAULT_VALUE => ''
                    ),
                    'label' => array(
                        ONAPP_FIELD_MAP => '_label',
                        ONAPP_FIELD_READ_ONLY => true,
                        ONAPP_FIELD_REQUIRED => true,
                        ONAPP_FIELD_DEFAULT_VALUE => ''
                    ),
                    'locked' => array(
                        ONAPP_FIELD_MAP => '_locked',
                        ONAPP_FIELD_TYPE => 'boolean',
                        ONAPP_FIELD_READ_ONLY => true,
                        //    ONAPP_FIELD_DEFAULT_VALUE => ''
                    ),
                    'memory_overhead' => array(
                        ONAPP_FIELD_MAP => '_memory_overhead',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                        //    ONAPP_FIELD_DEFAULT_VALUE => ''
                    ),
                    'online' => array(
                        ONAPP_FIELD_MAP => '_online',
                        ONAPP_FIELD_TYPE => 'boolean',
                        ONAPP_FIELD_READ_ONLY => true,
                        //    ONAPP_FIELD_DEFAULT_VALUE => ''
                    ),
                    'spare' => array(
                        ONAPP_FIELD_MAP => '_spare',
                        ONAPP_FIELD_TYPE => 'boolean',
                        ONAPP_FIELD_READ_ONLY => true,
                        //    ONAPP_FIELD_DEFAULT_VALUE => ''
                    ),
                    'updated_at' => array(
                        ONAPP_FIELD_MAP => '_updated_at',
                        ONAPP_FIELD_TYPE => 'datetime',
                        ONAPP_FIELD_READ_ONLY => true,
                        //    ONAPP_FIELD_DEFAULT_VALUE => ''
                    ),
                    'xen_info' => array(
                        ONAPP_FIELD_MAP => '_xen_info',
                        ONAPP_FIELD_TYPE => 'yaml',
                        ONAPP_FIELD_READ_ONLY => true,
                        //    ONAPP_FIELD_DEFAULT_VALUE => ''
                    ),
                );
                break;

            case '2.1':
                $this->_fields = $this->_init_fields('2.0');

                $this->_fields['enabled'] = array(
                    ONAPP_FIELD_MAP => '_enabled',
                    ONAPP_FIELD_TYPE => 'boolean',
                    ONAPP_FIELD_READ_ONLY => true,
                );

                $this->_fields['hypervisor_group_id'] = array(
                    ONAPP_FIELD_MAP => '_hypervisor_group_id',
                    ONAPP_FIELD_TYPE => 'integer',
                    ONAPP_FIELD_REQUIRED => true,
                );

                $this->_fields['hypervisor_type'] = array(
                    ONAPP_FIELD_MAP => '_hypervisor_type',
                    ONAPP_FIELD_TYPE => 'string',
                    ONAPP_FIELD_REQUIRED => true,
                );

                break;
        }
        ;

        return $this->_fields;
    }
}

?>
