 <?PHP
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Data Stores
 *
 * An operational data store (or "ODS") is a database  designed to integrate data from multiple
 * sources to make analysis and reporting easier. Data stores are core segments of the cloud system. 
 * OnApp uses any block based storage, i.e. local disks in hypervisors, an Ethernet SAN like iSCSI / AoE, or hardware (fiber) SAN.
 * OnApp OnApp is configured to control SANs physical and virtual routing. 
 * This control enables seamless SAN failover management, including SAN testing, emergency migration and data backup.
 * The minimum requirements for the virtual machine Data Stores are:
 *  - 1TB Block Storage (iSCSI, AoE, Fiber - can even be on a shared SAN)
 * 
 * @category  API WRAPPER
 * @package   ONAPP
 * @author    Andrew Yatskovets
 * @copyright 2010 / OnApp
 * @link      http://www.onapp.com/
 * @see       ONAPP
 */

require_once 'ONAPP.php';

/**
 * Data Stores
 *
 * The DataStore class represents the Data Storages of the OnAPP installation. 
 * 
 * The Data Store class uses the following basic methods:
 * {@link load}, {@link save}, {@link delete}, and {@link getList}.
 * 
 * <b>Use the following XML API requests:</b>
 *
 * Get the list of data storages
 *
 *     - <i>GET onapp.com/settings/data_stores.xml</i>
 *
 * Get a particular data storage details 
 *
 *     - <i>GET onapp.com/settings/data_stores/{ID}.xml</i>
 *
 * Add new data storage
 *
 *     - <i>POST onapp.com/settings/data_stores.xml</i>
 *
 * <code>
 * <?xml version="1.0" encoding="UTF-8"?>
 * <data-store>
 *    <data_store_size>{SIZE}</data_store_size>
 *    <label>{LABEL}</label>
 * </data-store>
 * </code>
 *
 * Edit existing data storage
 *
 *     - <i>PUT onapp.com/settings/data_stores/{ID}.xml</i>
 *
 * <code>
 * <?xml version="1.0" encoding="UTF-8"?>
 * <data-store>
 *     <data_store_size>{SIZE}</data_store_size>
 *     <label>{LABEL}</label>
 * </data-store>
 * </code>
 *
 * Delete data storage
 *
 *     - <i>DELETE onapp.com/settings/data_stores/{ID}.xml</i>
 *
 * <b>Use the following JSON API requests:</b>
 *
 * Get the list of data storages
 *
 *     - <i>GET onapp.com/settings/data_stores.json</i>
 *
 * Get a particular data storage details 
 *
 *     - <i>GET onapp.com/settings/data_stores/{ID}.json</i>
 *
 * Add new data storage
 *
 *     - <i>POST onapp.com/settings/data_stores.json</i>
 *
 * <code>
 * { 
 *      data-store: {
 *          data_store_size:{SIZE},
 *          label:'{LABEL}'
 *      }
 * }
 * </code>
 *
 * Edit existing data storage
 *
 *     - <i>PUT onapp.com/settings/data_stores/{ID}.json</i>
 *
 * <code>
 * { 
 *      data-store: {
 *          data_store_size:{SIZE},
 *          label:'{LABEL}'
 *      }
 * }
 * </code>
 *
 * Delete data storage
 *
 *     - <i>DELETE onapp.com/settings/data_stores/{ID}.json</i>
 */
class ONAPP_DataStore extends ONAPP {

    /**
     * the data store ID
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
     * the size of your data store in human readable format (e.g., 1K 234M 2G)
     *
     * @var integer
     */
    var $_data_store_size;

    /**
     * the data store ID
     *
     * @var string
     */
    var $_identifier;

    /**
     * the data store label
     *
     * @var string
     *
     */
    var $_label;

    /**
     * the ID of the Hypervisors using this Data Store
     *
     * @var integer
     */
    var $_local_hypervisor_id;

    /**
     * the date when the Data Store was updated in the [YYYY][MM][DD]T[hh][mm]Z format  
     *
     * @var datetime
     */
    var $_updated_at;

    /**
     * the size of zombie disks in GB
     * 
     * @var integer
     */
    var $_zombie_disks_size;

    /**
     * is Data store enabled
     *
     * @var boolean
     */
    var $_enabled;

    /**
     * root tag used in the API request
     *
     * @var string
     */
    var $_tagRoot  = 'data-store';

    /**
     * alias processing the object data
     *
     * @var string
     */
    var $_resource = 'settings/data_stores';

    /**
     * 
     * called class name
     * 
     * @var string
     */
    var $_called_class = 'ONAPP_DataStore';
    
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
                ONAPP_FIELD_READ_ONLY     => true
            ),
            'created_at' => array(
                ONAPP_FIELD_MAP           => '_created_at',
                ONAPP_FIELD_TYPE          => 'datetime',
                ONAPP_FIELD_READ_ONLY     => true,
            //    ONAPP_FIELD_DEFAULT_VALUE => ''
            ),
            'data_store_size' => array(
                ONAPP_FIELD_MAP           => '_data_store_size',
                ONAPP_FIELD_TYPE          => 'integer',
                ONAPP_FIELD_REQUIRED      => true,
            //    ONAPP_FIELD_DEFAULT_VALUE => ''
            ),
            'identifier' => array(
                ONAPP_FIELD_MAP           => '_identifier',
                ONAPP_FIELD_READ_ONLY     => true,
            //    ONAPP_FIELD_DEFAULT_VALUE => ''
            ),
            'label' => array(
                ONAPP_FIELD_MAP           => '_label',
                ONAPP_FIELD_REQUIRED      => true,
                ONAPP_FIELD_DEFAULT_VALUE => ''
            ),
            'local_hypervisor_id' => array(
                ONAPP_FIELD_MAP           => '_local_hypervisor_id',
                ONAPP_FIELD_TYPE          => 'integer',
                ONAPP_FIELD_READ_ONLY     => true,
            //    ONAPP_FIELD_DEFAULT_VALUE => ''
            ),
            'updated_at' => array(
                ONAPP_FIELD_MAP           => '_updated_at',
                ONAPP_FIELD_TYPE          => 'datetime',
                ONAPP_FIELD_READ_ONLY     => true,
            //    ONAPP_FIELD_DEFAULT_VALUE => ''
            ),
            'zombie_disks_size' => array(
                ONAPP_FIELD_MAP           => '_zombie_disks_size',
                ONAPP_FIELD_TYPE          => 'integer',
                ONAPP_FIELD_READ_ONLY     => true,
            ),
            'enabled' => array(
                ONAPP_FIELD_MAP           => '_enabled',
                ONAPP_FIELD_READ_ONLY     => true,
                ONAPP_FIELD_REQUIRED      => true,
	        ), 
        );

        break;
        case '2.0.1':
          $this->_init_fields = $this->_init_fields("2.0.0");
        break;
      };

      return $this->_fields;
    }
}   

?>
