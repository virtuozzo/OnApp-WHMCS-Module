<?PHP
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * VM Backups
 *
 * @category  API WRAPPER
 * @package   ONAPP
 * @author    Vitaliy Kondratyuk
 * @copyright 2010 / OnApp
 * @link      http://www.onapp.com/
 * @see       ONAPP
 */

/**
 * require Base class
 */
require_once dirname(__FILE__).'/../ONAPP.php';

/**
 * @todo: Add description
 */
define('ONAPP_GETRESOURCE_BACKUP_CONVERT', 'convert');

/**
 * @todo: Add description
 */
define('ONAPP_GETRESOURCE_BACKUP_RESTORE', 'restore');

/**
 * VM Backups
 * 
 * This class represents the Backups which have been taken or are waiting to be taken for Virtual Machine.
 * 
 * The Backup class uses the following basic methods:
 * {@link load}, {@link save}, {@link delete}, {@link getList}, {@link convert} and {@link restore}.
 * 
 * <b>Use the following XML API requests:</b>
 *
 * Get the list of Backups
 *
 *     - <i>GET onapp.com/virtual_machines/{VM_ID}/backups.xml</i>
 *
 * Get a particular Backup details 
 *
 *     - <i>GET onapp.com/backups/{ID}.xml</i>
 *
 * Add new Backup
 *
 *     - <i>POST onapp.com/settings/disks/{DISK_ID}/backups.xml</i>
 *
 * Convert Backup
 * 
 *     - <i>POST onapp.com/backups/{ID}/convert.xml</i>
 * 
 * <code>
 * <?xml version="1.0" encoding="UTF-8"?>
 * <backup>
 *    <label>{LABEL}</label>
 * </backup>
 * </code>
 * 
 * Restore Backup
 * 
 *     - <i>POST onapp.com/backups/{ID}/restore.xml</i>
 * 
 * Delete Backup
 *
 *     - <i>DELETE onapp.com/backups/{ID}.xml</i>
 *
 * <b>Use the following JSON API requests:</b>
 *
 * Get the list of Backups
 *
 *     - <i>GET onapp.com/virtual_machines/{VM_ID}/backups.json</i>
 *
 * Get a particular Backup details 
 *
 *     - <i>GET onapp.com/backups/{ID}.json</i>
 *
 * Add new Backup
 *
 *     - <i>POST onapp.com/settings/disks/{DISK_ID}/backups.json</i>
 *
 * Convert Backup
 * 
 *     - <i>POST onapp.com/backups/{ID}/convert.json</i>
 *
 * <code>
 * { 
 *      backup: {
 *          label:'{LABEL}'
 *      }
 * }
 * </code>
 * 
 * Restore Backup
 * 
 *     - <i>POST onapp.com/backups/{ID}/restore.json</i>
 *
 * Delete Backup
 *
 *     - <i>DELETE onapp.com/backups/{ID}..json</i>
 */
class ONAPP_VirtualMachine_Backup extends ONAPP {

    /**
     * ID
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
     * the date in the [YYYY][MM][DD]T[hh][mm]Z format  
     *
     * @var datetime
     */
    var $_updated_at;

    /**
     * the date in the [YYYY][MM][DD]T[hh][mm]Z format  
     *
     * @var datetime
     */
    var $_built_at;

    /**
     * Disc ID
     *
     * @var integer
     */
    var $_disk_id;

    /**
     * the Operating System
     *
     * @var integer
     */
    var $_operating_system;
    
    /**
     * the Operating System distribution
     *
     * @var integer
     */
    var $_operating_system_distro;

    /**
     * template ID
     *
     * @var integer
     */
    var $_template_id;

    /**
     * true if the swap disk is allowed. Otherwise, false.
     *
     * @var boolean
     */
    var $_allowed_swap;

    /**
     * the type of backup
     *
     * @var string
     */
    var $_backup_type;

    /**
     * true if the VM resizing without rebooting is allowed. Otherwise, false.
     *
     * @var boolean
     */
    var $_allow_resize_without_reboot;

    /**
     * the size of backup in KB
     *
     * @var integer
     */
    var $_backup_size;

    /**
     * the identifier of backup
     *
     * @var string
     */
    var $_identifier;

    /**
     * the minimum disk size
     *
     * @var integer
     */
    var $_min_disk_size;

    /**
     * @todo: Add description
     * 
     * @var boolean
     */
    var $_built;

    /**
     * @todo: Add description
     * 
     * @var boolean
     */
    var $_locked;

    /**
     * the VM ID
     *
     * @var integer
     */
    var $_virtual_machine_id;

    /**
     * The label of template convert to
     * 
     * @var string
     */
    var $_label;

    /**
     * root tag used in the API request
     *
     * @var string
     */
    var $_tagRoot  = 'backup';
    
    /**
     * alias processing the object data
     *
     * @var string
     */
    var $_resource = 'backups';
    
    /**
     * 
     * called class name
     * 
     * @var string
     */
    var $_called_class = 'ONAPP_VirtualMachine_Backup';
    
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
            ),
            'updated_at' => array(
                ONAPP_FIELD_MAP           => '_updated_at',
                ONAPP_FIELD_TYPE          => 'datetime',
                ONAPP_FIELD_READ_ONLY     => true,
            ),
            'built_at' => array(
                ONAPP_FIELD_MAP           => '_built_at',
                ONAPP_FIELD_TYPE          => 'datetime',
                ONAPP_FIELD_READ_ONLY     => true,
            ),
            'disk_id' => array(
                ONAPP_FIELD_MAP           => '_disk_id',
                ONAPP_FIELD_TYPE          => 'integer',
                ONAPP_FIELD_READ_ONLY     => true,
            ),
            'operating_system' => array(
                ONAPP_FIELD_MAP           => '_operating_system',
                ONAPP_FIELD_READ_ONLY     => true,
            ),
            'operating_system_distro' => array(
                ONAPP_FIELD_MAP           => '_operating_system_distro',
                ONAPP_FIELD_READ_ONLY     => true,
            ),
            'template_id' => array(
                ONAPP_FIELD_MAP           => '_template_id',
                ONAPP_FIELD_TYPE          => 'integer',
                ONAPP_FIELD_READ_ONLY     => true,
            ),
            'allowed_swap' => array(
                ONAPP_FIELD_MAP           => '_allowed_swap',
                ONAPP_FIELD_TYPE          => 'boolean',
                ONAPP_FIELD_READ_ONLY     => true,
            ),
            'backup_type' => array(
                ONAPP_FIELD_MAP           => '_backup_type',
                ONAPP_FIELD_READ_ONLY     => true,
            ),
            'allow_resize_without_reboot' => array(
                ONAPP_FIELD_MAP           => '_allow_resize_without_reboot',
                ONAPP_FIELD_TYPE          => 'boolean',
                ONAPP_FIELD_READ_ONLY     => true,
            ),
            'backup_size' => array(
                ONAPP_FIELD_MAP           => '_backup_size',
                ONAPP_FIELD_TYPE          => 'integer',
                ONAPP_FIELD_READ_ONLY     => true,
            ),
            'identifier' => array(
                ONAPP_FIELD_MAP           => '_identifier',
                ONAPP_FIELD_READ_ONLY     => true,
            ),
            'min_disk_size' => array(
                ONAPP_FIELD_MAP           => '_min_disk_size',
                ONAPP_FIELD_TYPE          => 'integer',
                ONAPP_FIELD_READ_ONLY     => true,
            ),
            'built' => array(
                ONAPP_FIELD_MAP           => '_built',
                ONAPP_FIELD_TYPE          => 'boolean',
                ONAPP_FIELD_READ_ONLY     => true,
            ),
            'locked' => array(
                ONAPP_FIELD_MAP           => '_locked',
                ONAPP_FIELD_TYPE          => 'boolean',
                ONAPP_FIELD_READ_ONLY     => true,
            ),
        );
        break;
        case '2.0.1':
          $this->_fields = $this->_init_fields("2.0.0");
        break;
      };  

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
    function getResource($action = ONAPP_GETRESOURCE_DEFAULT) {
        $show_log_msg = true;
        switch ($action) {
            case ONAPP_GETRESOURCE_DEFAULT:
                if ( is_null($this->_virtual_machine_id) && is_null($this->_obj->_virtual_machine_id) ) {
                    $this->_loger->error(
                        "getResource($action): argument _virtual_machine_id not set.", 
                        __FILE__, 
                        __LINE__
                    );
                } else if ( is_null($this->_virtual_machine_id) ) {
                    $this->_virtual_machine_id = $this->_obj->_virtual_machine_id;
                };
                $resource = 'virtual_machines/' . $this->_virtual_machine_id . '/' . $this->_resource;
                break;

            case ONAPP_GETRESOURCE_ADD:
                if ( is_null($this->_disk_id) && is_null($this->_obj->_disk_id) ) {
                    $this->_loger->error(
                        "getResource($action): argument _disk_id not set.", 
                        __FILE__, 
                        __LINE__
                    );
                } else if ( is_null($this->_disk_id) ) {
                    $this->_disk_id = $this->_obj->_disk_id;
                };
                $resource = 'settings/disks/' . $this->_disk_id . '/' . $this->_resource;
                break;

            case ONAPP_GETRESOURCE_LOAD:
            case ONAPP_GETRESOURCE_DELETE:
                if ( is_null($this->_id) && is_null($this->_obj->_id) ) {
                    $this->_loger->error(
                        "getResource($action): argument _id not set.", 
                        __FILE__, 
                        __LINE__
                    );
                } else if ( is_null($this->_id) ) {
                    $this->_id = $this->_obj->_id;
                };
                $resource = $this->_resource. '/' . $this->_id;
                break;

            case ONAPP_GETRESOURCE_BACKUP_CONVERT:
                $resource = $this->getResource(ONAPP_GETRESOURCE_LOAD).'/convert';
                break;

            case ONAPP_GETRESOURCE_BACKUP_RESTORE:
                $resource = $this->getResource(ONAPP_GETRESOURCE_LOAD).'/restore';
                break;

            default:
                $resource = parent::getResource($action);
                $show_log_msg = false;
                break;
        }

        if ($show_log_msg)
            $this->_loger->debug("getResource($action): return ".$resource);

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
    function getList($virtual_machine_id = null) {
        if ( is_null($virtual_machine_id) && ! is_null($this->_virtual_machine_id) )
            $virtual_machine_id = $this->_virtual_machine_id;

        if ( ! is_null($virtual_machine_id) ) {
            $this->_virtual_machine_id = $virtual_machine_id;

            return parent::getList();
        } else {
            $this->_loger->error(
                'getList: argument _virtual_machine_id not set.', 
                __FILE__, 
                __LINE__
            );
        }
    }

    /**
     * Convert backup to template
     * 
     * @param string $label The label of new template
     * 
     * @return mixed serialized Object instance from API
     * @access public 
     */
    function convert($label) {

        $this->_loger->add("Convert backup to template." );
        
        $this->_label = $label;

        $this->_fields["label"] = array(
            ONAPP_FIELD_MAP           => '_label',
            ONAPP_FIELD_REQUIRED      => true,
        );

        switch ( $this->options[ONAPP_OPTION_API_TYPE] ) {
            case 'xml':
                require_once dirname(__FILE__).'/../XMLObjectCast.php';

                $this->_loger->add("convert: Load XMLObjectCast (serializer and unserializer functions).");

                $objCast = &new XMLObjectCast();

                $data = $objCast->serialize(
                    $this->_tagRoot ,
                    $this->_getRequiredData()
                );

                $this->_loger->debug(
                    "unserialize: Serialize Class in to String:\n$data"
                );

                $this->setAPIResource( $this->getResource(ONAPP_GETRESOURCE_BACKUP_CONVERT) );

                $response = $this->sendRequest(ONAPP_REQUEST_METHOD_POST, $data);

                if ( ! $this->error )
                    $result = $this->_castResponseToClass( $response );
                
                $this->_obj = $result;

                return $result;
            break;
            default:
                $this->error("convert: Can't find serialize and unserialize functions for type (apiVersion => '".$this->_apiVersion."').", __FILE__, __LINE__ );
        }
    }

    /**
     * Restore backup
     * 
     * @access public 
     */
    function restore() {
        $this->setAPIResource( $this->getResource(ONAPP_GETRESOURCE_BACKUP_RESTORE) );

        $response = $this->sendRequest(ONAPP_REQUEST_METHOD_POST);
    
        $result = $this->_castResponseToClass( $response );
    
        $this->_obj = $result;
    }
}

?>
