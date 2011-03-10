<?PHP
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Configuring Network
 *
 * With OnApp you can create complex networks between virtual machines residing on a
 * single host or across multiple installations of OnApp for production deployments or
 * development and testing purposes. Configure each virtual machine with one or more
 * virtual NICs, each with its own IP and MAC address, to make virtual machines act like
 * physical machines. We take care that each customer has their own VLAN. This provides
 * customers with their own Virtual network which provides network isolation and thus
 * security. Nobody but you will see your traffic, even if they are located on the same physical
 * server. There is a possibility to modify network configurations without changing actual
 * cabling and switch setups.
 *
 * Each virtual server has at least one network interface card, so network traffic can flow into
 * and out of your server. All servers are given static IP addresses. You don't need to worry
 * about that address changing. You can tie your domain names to these IP addresses.
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
 * Configuring Network
 * 
 * This class represents the Networks added to your system.
 *
 * The Network class uses the following basic methods:
 * {@link load}, {@link save}, {@link delete}, and {@link getList}.
 *
 * <b>Use the following XML API requests:</b>
 *
 * Get the list of networks
 *
 *     - <i>GET onapp.com/settings/networks.xml</i>
 *
 * Get a particular network details 
 *
 *     - <i>GET onapp.com/settings/networks/{ID}.xml</i>
 *
 * Add new network
 *
 *     - <i>POST onapp.com/settings/networks.xml</i>
 *
 * <code>
 * <?xml version="1.0" encoding="UTF-8"?>
 * <network>
 *     <!-- TODO add description -->
 * </network>
 * </code>
 *
 * Edit existing network
 *
 *     - <i>PUT onapp.com/settings/networks/{ID}.xml</i>
 *
 * <code>
 * <?xml version="1.0" encoding="UTF-8"?>
 * <network>
 *     <!-- TODO add description -->
 * </network>
 * </code>
 *
 * Delete network
 *
 *     - <i>DELETE onapp.com/settings/networks/{ID}.xml</i>
 *
 * <b>Use the following JSON API requests:</b>
 *
 * Get the list of networks
 *
 *     - <i>GET onapp.com/settings/networks.json</i>
 *
 * Get a particular network details 
 *
 *     - <i>GET onapp.com/settings/networks/{ID}.json</i>
 *
 * Add new network
 *
 *     - <i>POST onapp.com/settings/networks.json</i>
 *
 * <code>
 * { 
 *      network: {
 *          # TODO add description
 *      }
 * }
 * </code>
 *
 * Edit existing network
 *
 *     - <i>PUT onapp.com/settings/networks/{ID}.json</i>
 *
 * <code>
 * { 
 *      network: {
 *          # TODO add description
 *      }
 * }
 * </code>
 *
 * Delete network
 *
 *     - <i>DELETE onapp.com/settings/networks/{ID}.json</i>
 */

class ONAPP_Network extends ONAPP {

    /**
     * the network ID
     *
     * @var integer
     */
    var $_id;

    /**
     * the date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var datetime
     */
    var $_created_at;
    
    /**
     * the network Identifier
     *
     * @var integer
     */
     
    var $_identifier;
    
    /**
     * the optional Network label
     *
     * @var integer
     */
    var $_label;

    /**
     * the date when the Network was updated in the [YYYY][MM][DD]T[hh][mm]Z format  
     *
     * @var datetime
     */
    var $_updated_at;
   
   /**
     * the VLAN this network belongs to
     *
     * @var integer
     */
    var $_vlan;
    
    /**
     * root tag used in the API request
     *
     * @var string
     */
    var $_tagRoot  = 'network';
    
    /**
     * alias processing the object data
     *
     * @var string
     */
    var $_resource = 'settings/networks';
    
    /**
     * 
     * called class name
     * 
     * @var string
     */
    var $_called_class = 'ONAPP_Network';
    
    /**
     * API Fields description
     *
     * @access private
     * @var    array
     */
    function _init_fields( $version = NULL ) {

      if ( is_null($version) )
        $version = $this->_version;

      switch ($version) {
        case '2.0.0':
          $this->_fields = array(
           'id' => array(
                ONAPP_FIELD_MAP           => '_id',
                ONAPP_FIELD_TYPE          => 'integer',
                ONAPP_FIELD_READ_ONLY     => true,
                ONAPP_FIELD_REQUIRED      => true,
            //    ONAPP_FIELD_DEFAULT_VALUE => ''
            ),
            'created_at' => array(
                ONAPP_FIELD_MAP           => '_created_at',
                ONAPP_FIELD_TYPE          => 'datetime',
                ONAPP_FIELD_READ_ONLY     => true,
                ONAPP_FIELD_REQUIRED      => true,
            //    ONAPP_FIELD_DEFAULT_VALUE => ''
            ),
            'identifier' => array(
                ONAPP_FIELD_MAP           => '_identifier',
                ONAPP_FIELD_READ_ONLY     => true,
                ONAPP_FIELD_REQUIRED      => true,
            //    ONAPP_FIELD_DEFAULT_VALUE => ''
            ),
            'label' => array(
                ONAPP_FIELD_MAP           => '_label',
                ONAPP_FIELD_READ_ONLY     => true,
                ONAPP_FIELD_REQUIRED      => true,
            //    ONAPP_FIELD_DEFAULT_VALUE => ''
            ),
            'updated_at' => array(
                ONAPP_FIELD_MAP           => '_updated_at',
                ONAPP_FIELD_TYPE          => 'datetime',
                ONAPP_FIELD_READ_ONLY     => true,
                ONAPP_FIELD_REQUIRED      => true,
            //    ONAPP_FIELD_DEFAULT_VALUE => ''
            ),
            'vlan' => array(
                ONAPP_FIELD_MAP           => '_vlan',
                ONAPP_FIELD_TYPE          => 'integer',
                ONAPP_FIELD_READ_ONLY     => true,
                ONAPP_FIELD_REQUIRED      => true,
            //    ONAPP_FIELD_DEFAULT_VALUE => ''
            ),
          );

        break;
        case '2.0.1':
          $this->_init_fields = $this->_init_fields("2.0.0");
        break;
      };

      return $this->_fields;
    }

    function activate($action_name) {
        switch ($action_name) {
            case ONAPP_ACTIVATE_SAVE:
            case ONAPP_ACTIVATE_DELETE:
                die("Call to undefined method ".__CLASS__."::$action_name()");
                break;
        }
    }
}

?>
