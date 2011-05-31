<?PHP
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Resolvers
 *
 * Resolvers in OnApp implement a name-service protocol. You can set the IP addresses corresponding to the hostnames added to the system.
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
 * Resolvers
 *
 * The Resolvers class represents the name-servers of the OnApp installation.
 *
 * The ONAPP_Nameserver class uses the following basic methods:
 * {@link load}, {@link save}, {@link delete}, and {@link getList}.
 *
 * <b>Use the following XML API requests:</b>
 *
 * Get the list of nameservers
 *
 *     - <i>GET onapp.com/settings/nameservers.xml</i>
 *
 * Get a particular nameserver details
 *
 *     - <i>GET onapp.com/settings/nameservers/{ID}.xml</i>
 *
 * Add new nameserver
 *
 *     - <i>POST onapp.com/settings/nameservers.xml</i>
 *
 * <code>
 * <?xml version="1.0" encoding="UTF-8"?>
 * <nameserver>
 *     <!-- TODO add description -->
 * </nameserver>
 * </code>
 *
 * Edit existing nameserver
 *
 *     - <i>PUT onapp.com/settings/nameservers/{ID}.xml</i>
 *
 * <code>
 * <?xml version="1.0" encoding="UTF-8"?>
 * <nameserver>
 *     <!-- TODO add description -->
 * </nameserver>
 * </code>
 *
 * Delete nameserver
 *
 *     - <i>DELETE onapp.com/settings/nameservers/{ID}.xml</i>
 *
 * <b>Use the following JSON API requests:</b>
 *
 * Get the list of nameservers
 *
 *     - <i>GET onapp.com/settings/nameservers.json</i>
 *
 * Get a particular nameserver details
 *
 *     - <i>GET onapp.com/settings/nameservers/{ID}.json</i>
 *
 * Add new nameserver
 *
 *     - <i>POST onapp.com/settings/nameservers.json</i>
 *
 * <code>
 * {
 *      nameserver: {
 *          # TODO add description
 *      }
 * }
 * </code>
 *
 * Edit existing nameserver
 *
 *     - <i>PUT onapp.com/settings/nameservers/{ID}.json</i>
 *
 * <code>
 * {
 *      nameserver: {
 *          # TODO add description
 *      }
 * }
 * </code>
 *
 * Delete nameserver
 *
 *     - <i>DELETE onapp.com/settings/nameservers/{ID}.json</i>
 */
class ONAPP_Nameserver extends ONAPP {

    /**
     * the resolvers ID
     *
     * @var integer
     */
    var $_id;

    /**
     * the IP address resolved
     *
     * @var integer
     */
    var $_address;

    /**
     * the Name Server creation date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_created_at;

    /**
     * the network ID which this resolver belongs to
     *
     * @var integer
     */

    var $_network_id;

    /**
     * the Name Server update date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_updated_at;

    /**
     * root tag used in the API request
     *
     * @var string
     */
    var $_tagRoot = 'nameserver';

    /**
     * alias processing the object data
     *
     * @var string
     */
    var $_resource = 'settings/nameservers';

    /**
     *
     * called class name
     *
     * @var string
     */
    var $_called_class = 'ONAPP_Nameserver';

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
                        ONAPP_FIELD_READ_ONLY => '',
                        #ONAPP_FIELD_REQUIRED      =>'',
                        ONAPP_FIELD_DEFAULT_VALUE => ''
                    ),
                    'address' => array(
                        ONAPP_FIELD_MAP => '_address',
                        ONAPP_FIELD_TYPE => '',
                        ONAPP_FIELD_READ_ONLY => '',
                        ONAPP_FIELD_REQUIRED => '',
                        ONAPP_FIELD_DEFAULT_VALUE => ''
                    ),
                    'created_at' => array(
                        ONAPP_FIELD_MAP => '_created_at',
                        ONAPP_FIELD_TYPE => 'datetime',
                        ONAPP_FIELD_READ_ONLY => '',
                        #ONAPP_FIELD_REQUIRED      =>'',
                        ONAPP_FIELD_DEFAULT_VALUE => ''
                    ),
                    'network_id' => array(
                        ONAPP_FIELD_MAP => '_network_id',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => '',
                        #ONAPP_FIELD_REQUIRED      =>'',
                        ONAPP_FIELD_DEFAULT_VALUE => ''
                    ),
                    'updated_at' => array(
                        ONAPP_FIELD_MAP => '_updated_at',
                        ONAPP_FIELD_TYPE => 'datetime',
                        ONAPP_FIELD_READ_ONLY => '',
                        #ONAPP_FIELD_REQUIRED      =>'',
                        ONAPP_FIELD_DEFAULT_VALUE => ''
                    ),
                );

                break;
        }
        ;

        return $this->_fields;
    }
}

?>
