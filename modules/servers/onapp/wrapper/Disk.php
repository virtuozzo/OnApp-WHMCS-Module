<?PHP
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Managing Disks
 *
 * @category  API WRAPPER
 * @package   ONAPP
 * @author    Vitaliy Kondratyuk
 * @copyright 2010 / OnApp
 * @link      http://www.onapp.com/
 * @see       ONAPP
 */

/**
 * requires Base class
 */
require_once 'ONAPP.php';

/**
 *
 */
define( 'ONAPP_GETRESOURCE_AUTOBACKUP_ENABLE', 'autobackup_enable' );

/**
 *
 */
define( 'ONAPP_GETRESOURCE_AUTOBACKUP_DISABLE', 'autobackup_disable' );

/**
 * Disks
 *
 * The ONAPP_Disk class uses the following basic methods:
 * {@link load}, {@link save}, {@link delete}, {@link getList}.
 *
 * <b>Use the following XML API requests:</b>
 *
 * Get the list of Disks
 *
 *     - <i>GET onapp.com/settings/disks.xml</i>
 *
 * Get the list of Disks for particular VM
 *
 *     - <i>GET onapp.com/virtual_machines/{VM_ID}/disks.xml</i>
 *
 * Get a particular Disk details
 *
 *     - <i>GET onapp.com/settings/disks/{ID}.xml</i>
 *
 * Add new Disk
 *
 *     - <i>POST onapp.com/virtual_machines/{VM_ID}/disks.xml</i>
 *
 * <code>
 * <?xml version="1.0" encoding="UTF-8"?>
 * <disk>
 *     <data_store_id>{DATA_STORE_ID}</data_store_id>
 *     <disk_size>{DISK_SIZE}</disk_size>
 * </disk>
 * </code>
 *
 * Edit existing Disk
 *
 *     - <i>PUT onapp.com/settings/disks/{ID}.xml</i>
 *
 * <code>
 * <?xml version="1.0" encoding="UTF-8"?>
 * <disk>
 *     <disk_size>{DISK_SIZE}</disk_size>
 * </disk>
 * </code>
 *
 * Enable disk autobackup
 *
 *     - <i>POST onapp.com/settings/disks/{ID}/autobackup_enable.xml</i>
 *
 * Disable disk autobackup
 *
 *     - <i>POST onapp.com/settings/disks/{ID}/autobackup_disable.xml</i>
 *
 * Delete Disk
 *
 *     - <i>DELETE onapp.com/settings/disks/{ID}.xml</i>
 *
 * <b>Use the following JSON API requests:</b>
 *
 * Get the list of Disks
 *
 *     - <i>GET onapp.com/settings/disks.json</i>
 *
 * Get the list of Disks for particular VM
 *
 *     - <i>GET onapp.com/virtual_machines/{VM_ID}/disks.json</i>
 *
 * Get a particular Disk details
 *
 *     - <i>GET onapp.com/settings/disks/{ID}.json</i>
 *
 * Add new Disk
 *
 *     - <i>POST onapp.com/virtual_machines/{VM_ID}/disks.json</i>
 *
 * <code>
 * {
 *      disk: {
 *          data_store_id:'{DATA_STORE_ID}',
 *          disk_size:{DISK_SIZE}
 *      }
 * }
 * </code>
 *
 * Edit existing Disk
 *
 *     - <i>PUT onapp.com/settings/disks/{ID}.json</i>
 *
 * <code>
 * {
 *      disk: {
 *          disk_size:{DISK_SIZE}
 *      }
 * }
 * </code>
 *
 * Enable disk autobackup
 *
 *     - <i>POST onapp.com/settings/disks/{ID}/autobackup_enable.json</i>
 *
 * Disable disk autobackup
 *
 *     - <i>POST onapp.com/settings/disks/{ID}/autobackup_disable.json</i>
 *
 * Delete Disk
 *
 *     - <i>DELETE onapp.com/settings/disks/{ID}.json</i>
 */
class ONAPP_Disk extends ONAPP {

    /**
     * the Disk ID
     *
     * @var integer
     */
    var $_id;

    /**
     * the Disk creation date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_created_at;

    /**
     * the Disk update date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_updated_at;

    /**
     * true if add to Linux FSTAB. Otherwise, false.
     *
     * @var boolean
     */
    var $_add_to_linux_fstab;

    /**
     * Disk size in GB
     *
     * @var integer
     */
    var $_disk_size;

    /**
     * is the Disk primary
     *
     * @var boolean
     */
    var $_primary;

    /**
     * the ID of the data store this disk is located
     *
     * @var integer
     */
    var $_data_store_id;

    /**
     * the number of virtual machines using this disk
     *
     * @var integer
     */
    var $_disk_vm_number;

    /**
     * true if this is a swap disk. Otherwise false
     *
     * @var boolean
     */
    var $_is_swap;

    /**
     * the mount point
     */
    var $_mount_point;

    /**
     * the Disc identifier
     *
     * @var string
     */
    var $_identifier;

    /**
     * the ID of the virtual machine using this disk
     *
     * @var integer
     */
    var $_virtual_machine_id;

    /**
     * true if the disk is built. Otherwise false.
     *
     * @var boolean
     */
    var $_built;

    /**
     * true if the disk is locked. Otherwise false.
     *
     * @var boolean
     */
    var $_locked;

    /**
     * if automatic backup is scheduled. Otherwise false.
     *
     * @var boolean
     */
    var $_has_autobackups;

    /**
     * root tag used in the API request
     *
     * @var string
     */
    var $_tagRoot = 'disk';

    /**
     * alias processing the object data
     *
     * @var string
     */
    var $_resource = 'settings/disks';

    /**
     *
     * called class name
     *
     * @var string
     */
    var $_called_class = 'ONAPP_Disk';

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
                    'add_to_linux_fstab' => array(
                        ONAPP_FIELD_MAP => '_add_to_linux_fstab',
                        ONAPP_FIELD_TYPE => 'boolean',
                        ONAPP_FIELD_REQUIRED => true,
                        ONAPP_FIELD_DEFAULT_VALUE => false,
                    ),
                    'disk_size' => array(
                        ONAPP_FIELD_MAP => '_disk_size',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_REQUIRED => true,
                    ),
                    'primary' => array(
                        ONAPP_FIELD_MAP => '_primary',
                        ONAPP_FIELD_TYPE => 'boolean',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'data_store_id' => array(
                        ONAPP_FIELD_MAP => '_data_store_id',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_REQUIRED => true,
                    ),
                    'disk_vm_number' => array(
                        ONAPP_FIELD_MAP => '_disk_vm_number',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'is_swap' => array(
                        ONAPP_FIELD_MAP => '_is_swap',
                        ONAPP_FIELD_TYPE => 'boolean',
                        ONAPP_FIELD_REQUIRED => true,
                        ONAPP_FIELD_DEFAULT_VALUE => false,
                    ),
                    'mount_point' => array(
                        ONAPP_FIELD_MAP => '_mount_point',
                        ONAPP_FIELD_REQUIRED => true,
                        ONAPP_FIELD_DEFAULT_VALUE => '',
                    ),
                    'identifier' => array(
                        ONAPP_FIELD_MAP => '_identifier',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'virtual_machine_id' => array(
                        ONAPP_FIELD_MAP => '_virtual_machine_id',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'built' => array(
                        ONAPP_FIELD_MAP => '_built',
                        ONAPP_FIELD_TYPE => 'boolean',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'locked' => array(
                        ONAPP_FIELD_MAP => '_locked',
                        ONAPP_FIELD_TYPE => 'boolean',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                    'has_autobackups' => array(
                        ONAPP_FIELD_MAP => '_has_autobackups',
                        ONAPP_FIELD_TYPE => 'boolean',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                );

                break;
        }
        ;

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
            case ONAPP_GETRESOURCE_LIST:
                $resource = $this->_virtual_machine_id ?
                        'virtual_machines/' . $this->_virtual_machine_id . '/disks' :
                        $this->getResource( );
                break;

            case ONAPP_GETRESOURCE_ADD:
                if( is_null( $this->_virtual_machine_id ) ) {
                    $this->_loger->error(
                        "getResource($action): argument _virtual_machine_id not set.",
                        __FILE__,
                        __LINE__
                    );
                }
                else {
                    $resource = 'virtual_machines/' .
                                $this->_virtual_machine_id .
                                '/disks';
                }
                break;

            case ONAPP_GETRESOURCE_AUTOBACKUP_ENABLE:
                $resource = $this->getResource( ONAPP_GETRESOURCE_LOAD ) . "/autobackup_enable";
                break;

            case ONAPP_GETRESOURCE_AUTOBACKUP_DISABLE:
                $resource = $this->getResource( ONAPP_GETRESOURCE_LOAD ) . "/autobackup_disable";
                break;

            default:
                $resource = parent::getResource( $action );
                break;
        }

        $actions = array(
            ONAPP_GETRESOURCE_LIST,
            ONAPP_GETRESOURCE_ADD,
            ONAPP_GETRESOURCE_AUTOBACKUP_ENABLE,
            ONAPP_GETRESOURCE_AUTOBACKUP_DISABLE,
        );
        if( in_array( $action, $actions ) ) {
            $this->_loger->debug( "getResource($action): return " . $resource );
        }

        return $resource;
    }

    /**
     * Anables autobackup
     *
     * @access public
     */
    function enableAutobackup( ) {
        $this->setAPIResource( $this->getResource( ONAPP_GETRESOURCE_AUTOBACKUP_ENABLE ) );

        $response = $this->sendRequest( ONAPP_REQUEST_METHOD_POST, "" );

        $result = $this->_castResponseToClass( $response );

        $this->_obj = $result;
    }

    /**
     * Disables autobackup
     *
     * @access public
     */
    function disableAutobackup( ) {
        $this->setAPIResource( $this->getResource( ONAPP_GETRESOURCE_AUTOBACKUP_DISABLE ) );

        $response = $this->sendRequest( ONAPP_REQUEST_METHOD_POST, "" );

        $result = $this->_castResponseToClass( $response );

        $this->_obj = $result;
    }

    /**
     * Sends an API request to get the Objects. After requesting,
     * unserializes the received response into the array of Objects
     *
     * @param integer $vm_id VM ID
     *
     * @return mixed an array of Object instances on success. Otherwise false
     * @access public
     */
    function getList( $vm_id = null ) {
        if( $vm_id ) {
            $this->_virtual_machine_id = $vm_id;
        }
        return parent::getList( );
    }

    /**
     * The method saves an Object to your account
     *
     * @param integer $vm_id VM ID
     *
     * @return mixed Serialized API Response
     * @access private
     */
    function save( $vm_id = null ) {
        if( $vm_id ) {
            $this->_virtual_machine_id = $vm_id;
        }
        parent::save( );
    }
}

?>
