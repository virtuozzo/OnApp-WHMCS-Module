<?PHP

/**
 * Network Zone
 *
 * @todo Add description
 *
 * @category  API WRAPPER
 * @package   ONAPP
 * @author    Andrew Yatskovets
 * @copyright 2011 / OnApp
 * @link      http://www.onapp.com/
 * @see       ONAPP
 */

/**
 * require Base class
 */
require_once dirname( __FILE__ ) . '/ONAPP.php';

/**
 *
 * Managing Network Zones
 *
 * The ONAPP_NetworkZone class uses the following basic methods:
 * {@link load}, {@link save}, {@link delete}, and {@link getList}.
 *
 * The ONAPP_NetworkZone class represents virtual machine network groups.  The ONAPP class is a parent of ONAPP_NetworkZone class.
 *
 * <b>Use the following XML API requests:</b>
 *
 * Get the list of groups
 *
 *     - <i>GET onapp.com/network_zones.xml</i>
 *
 * Get a particular group details
 *
 *     - <i>GET onapp.com/network_zones/{ID}.xml</i>
 *
 * Add new group
 *
 *     - <i>POST onapp.com/network_zones.xml</i>
 *
 * <network_groups type="array">
 *
 * <code>
 * <?xml version="1.0" encoding="UTF-8"?>
 * <network_groups type="array">
 *  <network_group>
 *    <label>{LABEL}</label>
 *  </network_group>
 * </network_groups>
 * </code>
 *
 * Edit existing group
 *
 *     - <i>PUT onapp.com/network_zones/{ID}.xml</i>
 *
 * <?xml version="1.0" encoding="UTF-8"?>
 * <network_groups type="array">
 *  <network_group>
 *    <label>{LABEL}</label>
 *  </network_group>
 * </network_groups>
 * </code>
 *
 * Delete group
 *
 *     - <i>DELETE onapp.com/network_zones/{ID}.xml</i>
 *
 * <b>Use the following JSON API requests:</b>
 *
 * Get the list of groups
 *
 *     - <i>GET onapp.com/network_zones.json</i>
 *
 * Get a particular group details
 *
 *     - <i>GET onapp.com/network_zones/{ID}.json</i>
 *
 * Add new group
 *
 *     - <i>POST onapp.com/network_zones.json</i>
 *
 * <code>
 * {
 *      network_group: {
 *          label:'{LABEL}',
 *      }
 * }
 * </code>
 *
 * Edit existing group
 *
 *     - <i>PUT onapp.com/network_zones/{ID}.json</i>
 *
 * <code>
 * {
 *      network_group: {
 *          label:'{LABEL}',
 *      }
 * }
 * </code>
 *
 * Delete group
 *
 *     - <i>DELETE onapp.com/network_zones/{ID}.json</i>
 *
 * 
 *
 */

class ONAPP_NetworkZone extends ONAPP {

    /**
     * the Hypervisor's Zone ID
     *
     * @var integer
     */
    var $_id;

    /**
     * the Network Zone Label
     *
     * @var integer
     */
    var $_label;

    /**
     * the Network Zone creation date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_created_at;

    /**
     * the Network Zone update date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_updated_at;

    /**
     * root tag used in the API request
     *
     * @var string
     */
    var $_tagRoot = 'network_group';

    /**
     * alias processing the object data
     *
     * @var string
     */
    var $_resource = 'network_zones';

    /**
     *
     * called class name
     *
     * @var string
     */
    var $_called_class = 'ONAPP_NetworkZone';

    /**
     * API Fields description
     *
     * @access private
     * @var    array
     */
    function _init_fields( $version = NULL ) {
        if( !isset( $this->options[ ONAPP_OPTION_API_TYPE ] ) || ( $this->options[ ONAPP_OPTION_API_TYPE ] == 'json' ) ) {
            $this->_tagRoot = 'network_group';
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
                    'label' => array(
                        ONAPP_FIELD_MAP => '_label',
                        ONAPP_FIELD_TYPE => 'string',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                 
                );

                break;

            case '2.1':

                $this->_fields = $this->_init_fields('2.0');

                break;
        }
        ;

        return $this->_fields;
    }

}

?>