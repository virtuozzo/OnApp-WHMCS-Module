<?PHP
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Managing Virtual Machines
 *
 * When creating a virtual machine, users can select a Hypervisor server with
 * Data Store attached if they wish. If not, the system will find a list of
 * hypervisors with sufficient RAM and available storage and choose the one with
 * the least available RAM.
 *
 * OnApp provides complete management for your virtual machines. You can start,
 * stop, reboot, and delete virtual machines. You can also move VM's between the
 * hypervisors with no downtime. Automatic and manual backups will help you
 * restore the VM’s in case of failure.
 * With OnApp you have an integrated console and complete root access to your
 * virtual machines that provides full control over all files and processes
 * running on the machines.
 *
 * With OnApp you can monitor the CPU usage for each virtual machine and Network
 * Utilization for each network interface. This lets you know when to consider
 * increasing resources available to the system. Also the cloud engine provides
 * the detailed log records of all the tasks which are currently running,
 * pending, failed or completed.
 * 
 * @category  API WRAPPER
 * @package   ONAPP
 * @author    Andrew Yatskovets
 * @copyright 2010 / OnApp
 * @link      http://www.onapp.com/
 * @see       ONAPP
 */

/**
 * require Base class
 */
require_once 'ONAPP.php';
require_once 'IpAddress.php';

/**
 * 
 * 
 */
define('ONAPP_GETRESOURCE_REBOOT',  'reboot');

/**
 * 
 * 
 */
define('ONAPP_GETRESOURCE_SHUTDOWN',  'shutdown');

/**
 * 
 * 
 */
define('ONAPP_GETRESOURCE_STARTUP',  'startup');

/**
 * 
 * 
 */
define('ONAPP_GETRESOURCE_UNLOCK',  'unlock');

/**
 * 
 * 
 */
define('ONAPP_GETRESOURCE_BUILD',  'build');


/**
 *
 *
 */
define('ONAPP_ACTIVATE_GETLIST_USER', 'getUserVMsList');

/**
 * Virtual Machines
 *
 * The Virtual Machine class represents the Virtual Machines of the OnAPP installation. 
 * 
 * The Virtual Machine class uses the following basic methods:
 * {@link load}, {@link save}, {@link delete}, and {@link getList}.
 * 
 * <b>Use the following XML API requests:</b>
 *
 * Get the list of virtual machines
 *
 *     - <i>GET onapp.com/virtual_machines.xml</i>
 *
 * Get a particular virtual machine details 
 *
 *     - <i>GET onapp.com/virtual_machines/{ID}.xml</i>
 *
 * Add new virtual machine
 *
 *     - <i>POST onapp.com/virtual_machines.xml</i>
 *
 * <code>
 * <?xml version="1.0" encoding="UTF-8"?>
 * <virtual-machine>
 *     <cpu-shares>{NUMBER}</cpu-shares>
 *     <cpus>{NUMBER}</cpus>
 *     <hostname>{HOSTNAME}</hostname>
 *     <hypervisor-id>{ID}</hypervisor-id>
 *     <initial-root-password>{PASSWORD}</initial-root-password>
 *     <memory>{SIZE}</memory>
 *     <template-id>{ID}</template-id>
 *     <primary-disk-size>{SIZE}</primary-disk-size>
 *     <swap-disk-size>{SIZE}</swap-disk-size>
 * </virtual-machine>
 * </code>
 *
 * Edit existing virtual machine
 *
 *     - <i>PUT onapp.com/virtual_machines/{ID}.xml</i>
 *
 * <code>
 * <?xml version="1.0" encoding="UTF-8"?>
 * <virtual-machine>
 *     <cpu-shares>{NUMBER}</cpu-shares>
 *     <cpus>{NUMBER}</cpus>
 *     <hostname>{HOSTNAME}</hostname>
 *     <hypervisor-id>{ID}</hypervisor-id>
 *     <initial-root-password>{PASSWORD}</initial-root-password>
 *     <memory>{SIZE}</memory>
 *     <template-id>{ID}</template-id>
 *     <primary-disk-size>{SIZE}</primary-disk-size>
 *     <swap-disk-size>{SIZE}</swap-disk-size>
 * </virtual-machine>
 * </code>
 *
 * Delete virtual machine
 *
 *     - <i>DELETE onapp.com/virtual_machines/{ID}.xml</i>
 *
 * <b>Use the following JSON API requests:</b>
 *
 * Get the list of virtual machines
 *
 *     - <i>GET onapp.com/virtual_machines.json</i>
 *
 * Get a particular virtual machine details 
 *
 *     - <i>GET onapp.com/virtual_machines/{ID}.json</i>
 *
 * Add new virtual machine
 *
 *     - <i>POST onapp.com/virtual_machines.json</i>
 *
 * <code>
 * { 
 *     virtual-machine: {
 *         cpu-shares:{NUMBER},
 *         cpus:{NUMBER},
 *         hostname:'{HOSTNAME}',
 *         hypervisor-id:{ID},
 *         initial-root-password:'{PASSWORD}',
 *         memory:{SIZE},
 *         template-id:{ID},
 *         primary-disk-size:{SIZE},
 *         swap-disk-size:{SIZE}
 *      }
 * }
 * </code>
 *
 * Edit existing virtual machine
 *
 *     - <i>PUT onapp.com/virtual_machines/{ID}.json</i>
 *
 * <code>
 * { 
 *      virtual-machine: {
 *         cpu-shares:{NUMBER},
 *         cpus:{NUMBER},
 *         hostname:'{HOSTNAME}',
 *         hypervisor-id:{ID},
 *         initial-root-password:'{PASSWORD}',
 *         memory:{SIZE},
 *         template-id:{ID},
 *         primary-disk-size:{SIZE},
 *         swap-disk-size:{SIZE}
 *      }
 * }
 * </code>
 *
 * Delete virtual machine
 *
 *     - <i>DELETE onapp.com/virtual_machines/{ID}.json</i>
 */
class ONAPP_VirtualMachine extends ONAPP {

    /**
     * the virtual machine ID
     *
     * @var integer
     */
    var $_id;
    
    /**
     * true if booted. Otherwise false
     *
     * @var integer
     */
    var $_booted;
    
    /**
     * true if built. Otherwise false
     *
     * @var integer
     */
    var $_built;
    
    /**
     * the number of CPU Shares
     *
     * @var integer
     */
    var $_cpu_shares;
    
    /**
     * the number of CPUs
     *
     * @var integer
     */
    var $_cpus;

    /**
     * the date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var datetime
     */
    var $_created_at;
    
    /**
     * the name of your host
     *
     * @var integer
     */
    var $_hostname;
    
    /**
     * the ID of the hypervisor used by this VM
     *
     * @var integer
     */
    var $_hypervisor_id;
    
    /**
     * the VM identifier
     *
     * @var integer
     */
    var $_identifier;
    
    /**
     * the VM root password
     *
     * @var integer
     */
    var $_initial_root_password;
    
    /**
     * the VM label
     *
     * @var integer
     */
    var $_label;
    
    /**
     * The port ID used for console access
     *
     * @var integer
     */
    var $_local_remote_access_port;
    
    /**
     * true if the VM is locked. Otherwise false
     *
     * @var integer
     */
    var $_locked;
    
    /**
     * the memory size
     *
     * @var integer
     */
    var $_memory;
    
    /**
     * the bandwidth used this month
     *
     * @var integer
     */
    var $_monthly_bandwidth_used;

    /**
     * true if recovery mode allowed. Otherwise false
     *
     * @var integer
     */
    var $_recovery_mode;
    
    /**
     * the password for the remote access
     *
     * @var integer
     */
    var $_remote_access_password;
    
    /**
     * the ID of the template the VM is based on
     *
     * @var integer
     */
    var $_template_id;

    /**
     * the date when the Virtual Machine was updated in the [YYYY][MM][DD]T[hh][mm]Z format  
     *
     * @var datetime
     */
    var $_updated_at;
    
    /**
     * the ID of the Xen virtualizing this VM
     *
     * @var integer
     */
    var $_xen_id;
    
    /**
     * true if swap alowed. Otherwise false
     *
     * @var integer
     */
    var $_allowed_swap;

    /**
     * true if resize without reboot alowed. Otherwise false
     *
     * @var integer
     */
    var $_allow_resize_without_reboot;

    /**
     * the VM IP addresses
     *
     * @var string
     */
    var $_ip_addresses;

    /**
     * the minimal size of the disk
     *
     * @var integer
     */
    var $_min_disk_size;

    /**
     * the Operating System installed with this VM
     *
     * @var string
     */
    var $_operating_system;

    /**
     * the Operating System distribution installed with this VM
     *
     * @var integer
     */
    var $_operating_system_distro;

    /**
     * the label of the template the VM is based on
     *
     * @var string
     */
    var $_template_label;

    /**
     * The User ID
     * 
     * @var integer
     */
    var $_user_id;

    /**
     * the size of the primary disk
     *
     * @var integer
     */
    var $_primary_disk_size;

    /**
     * the size of the swap disk
     *
     * @var integer
     */
    var $_swap_disk_size;

    /**
     * the primary network ID
     *
     * @var integer
     */
    var $_primary_network_id;

    /**
     * true if automatic backup required. Otherwise false
     * 
     * @var boolean
     */
    var $_required_automatic_backup;

    /**
     * the rate limit
     * 
     * @var integer
     */
    var $_rate_limit;

    /**
     * true if IP address assigment required. Otherwise false
     * 
     * @var boolean
     */
    var $_required_ip_address_assignment;

    /**
     * true if VM build after creation required. Otherwise false
     * 
     * @var boolean
     */
    var $_required_virtual_machine_build;

    /**
     * show total disks size
     *
     * @var string
     */
    var $_total_disk_size;

    /**
     *
     */
    var $_required_startup;

    /**
     * root tag used in the API request
     *
     * @var string
     */
    var $_tagRoot  = 'virtual-machine';
    
    /**
     * alias processing the object data
     *
     * @var string
     */
    var $_resource = 'virtual_machines';
    
    /**
     * 
     * called class name
     * 
     * @var string
     */
    var $_called_class = 'ONAPP_VirtualMachine';
    
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
                  ONAPP_FIELD_MAP          => '_id',
                  ONAPP_FIELD_TYPE         => 'integer',
                  ONAPP_FIELD_READ_ONLY    => true
              ),
              'booted' => array(
                  ONAPP_FIELD_MAP           => '_booted',
                  ONAPP_FIELD_TYPE          => 'boolean',
                  ONAPP_FIELD_READ_ONLY     => true,
              ),
              'built' => array(
                  ONAPP_FIELD_MAP           => '_built',
                  ONAPP_FIELD_TYPE          => 'boolean',
                  ONAPP_FIELD_READ_ONLY     => true,
              ),
              'cpu_shares' => array(
                  ONAPP_FIELD_MAP           => '_cpu_shares',
                  ONAPP_FIELD_TYPE          => 'integer',
                  ONAPP_FIELD_REQUIRED      => true,
                  ONAPP_FIELD_DEFAULT_VALUE => 1
              ),
              'cpus' => array(
                  ONAPP_FIELD_MAP           => '_cpus',
                  ONAPP_FIELD_TYPE          => 'integer',
                  ONAPP_FIELD_REQUIRED      => true,
                  ONAPP_FIELD_DEFAULT_VALUE => 1
              ),
              'created_at' => array(
                  ONAPP_FIELD_MAP           => '_created_at',
                  ONAPP_FIELD_TYPE          => 'datetime',
                  ONAPP_FIELD_READ_ONLY     => true,
              ),
              'hostname' => array(
                  ONAPP_FIELD_MAP           => '_hostname',
                  ONAPP_FIELD_REQUIRED      => true,
              ),
              'hypervisor_id' => array(
                  ONAPP_FIELD_MAP           => '_hypervisor_id',
                  ONAPP_FIELD_TYPE          => 'integer',
                  ONAPP_FIELD_REQUIRED      => true,
                  ONAPP_FIELD_DEFAULT_VALUE => ''
              ),
              'identifier' => array(
                  ONAPP_FIELD_MAP           => '_identifier',
                  ONAPP_FIELD_READ_ONLY     => true,
              ),
              'initial_root_password' => array(
                  ONAPP_FIELD_MAP           => '_initial_root_password',
                  ONAPP_FIELD_REQUIRED      => true,
                  ONAPP_FIELD_DEFAULT_VALUE => ''
              ),
              'label' => array(
                  ONAPP_FIELD_MAP           => '_label',
                  ONAPP_FIELD_REQUIRED      => true,
              ),
              'local_remote_access_port' => array(
                  ONAPP_FIELD_MAP           => '_local_remote_access_port',
                  ONAPP_FIELD_TYPE          => 'integer',
                  ONAPP_FIELD_READ_ONLY     => true,
              ),
              'locked' => array(
                  ONAPP_FIELD_MAP           =>'_locked',
                  ONAPP_FIELD_TYPE          => 'boolean',
                  ONAPP_FIELD_READ_ONLY     => true,
              ),
              'memory' => array(
                  ONAPP_FIELD_MAP           => '_memory',
                  ONAPP_FIELD_TYPE          => 'integer',
                  ONAPP_FIELD_REQUIRED      => true,
                  ONAPP_FIELD_DEFAULT_VALUE => 256
              ),
              'recovery_mode' => array(
                  ONAPP_FIELD_MAP           => '_recovery_mode',
                  ONAPP_FIELD_TYPE          => 'boolean',
                  ONAPP_FIELD_READ_ONLY     => true,
              ),  
              'remote_access_password' => array(
                  ONAPP_FIELD_MAP           => '_remote_access_password',
                  ONAPP_FIELD_READ_ONLY     => true,
              ),
              'template_id' => array(
                  ONAPP_FIELD_MAP           => '_template_id',
                  ONAPP_FIELD_TYPE          => 'integer',
                  ONAPP_FIELD_REQUIRED      => true,
                  ONAPP_FIELD_DEFAULT_VALUE => ''
              ),
              'updated_at' => array(
                  ONAPP_FIELD_MAP           => '_updated_at',
                  ONAPP_FIELD_TYPE          => 'datetime',
                  ONAPP_FIELD_READ_ONLY     => true,
              ),
              'user_id' => array(
                  ONAPP_FIELD_MAP           => '_user_id',
                  ONAPP_FIELD_TYPE          => 'integer',
                  ONAPP_FIELD_READ_ONLY     => true,
              ),
              'xen_id' => array(
                  ONAPP_FIELD_MAP           => '_xen_id',
                  ONAPP_FIELD_TYPE          => 'integer',
                  ONAPP_FIELD_READ_ONLY     => true,
              ),
              'allowed_swap' => array(
                  ONAPP_FIELD_MAP           => '_allowed_swap',
                  ONAPP_FIELD_TYPE          => 'boolean',
                  ONAPP_FIELD_READ_ONLY     => true,
              ),
              'allow_resize_without_reboot' => array(
                  ONAPP_FIELD_MAP           => '_allow_resize_without_reboot',
                  ONAPP_FIELD_TYPE          => 'boolean',
                  ONAPP_FIELD_READ_ONLY     => true,
              ),
              'ip_addresses' => array(
                  ONAPP_FIELD_MAP           => '_ip_addresses',
                  ONAPP_FIELD_TYPE          => 'array',
                  ONAPP_FIELD_READ_ONLY     => true,
                  ONAPP_FIELD_CLASS         => 'IpAddress',
              ),
              'min_disk_size' => array(
                  ONAPP_FIELD_MAP           => '_min_disk_size',
                  ONAPP_FIELD_TYPE          => 'integer',
                  ONAPP_FIELD_READ_ONLY     => true,
              ),
              'monthly_bandwidth_used' => array(
                  ONAPP_FIELD_MAP           => '_monthly_bandwidth_used',
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
              'template_label' => array(
                  ONAPP_FIELD_MAP           => '_template_label',
                  ONAPP_FIELD_READ_ONLY     => true,
              ),
              'total_disk_size' => array(
                  ONAPP_FIELD_MAP           => '_total_disk_size',
                  ONAPP_FIELD_READ_ONLY     => true,
              ),
          );

          if ( is_null($this->_id) ) {
              $this->_fields["primary_disk_size"] = array(
                  ONAPP_FIELD_MAP           => '_primary_disk_size',
                  ONAPP_FIELD_TYPE          => 'integer',
                  ONAPP_FIELD_REQUIRED      => true,
                  ONAPP_FIELD_DEFAULT_VALUE => 1
              );
              $this->_fields["swap_disk_size"] = array(
                  ONAPP_FIELD_MAP           => '_swap_disk_size',
                  ONAPP_FIELD_TYPE          => 'integer',
                  ONAPP_FIELD_REQUIRED      => true,
                  ONAPP_FIELD_DEFAULT_VALUE => 0
              );
              $this->_fields["primary_network_id"] = array(
                  ONAPP_FIELD_MAP           => '_primary_network_id',
                  ONAPP_FIELD_TYPE          => 'integer',
                  ONAPP_FIELD_REQUIRED      => true,
                  ONAPP_FIELD_DEFAULT_VALUE => ''
              );
              $this->_fields["required_automatic_backup"] = array(
                  ONAPP_FIELD_MAP           => '_required_automatic_backup',
                  ONAPP_FIELD_TYPE          => 'boolean',
                  ONAPP_FIELD_REQUIRED      => true,
                  ONAPP_FIELD_DEFAULT_VALUE => ''
              );
              $this->_fields["rate_limit"] = array(
                  ONAPP_FIELD_MAP           => '_rate_limit',
                  ONAPP_FIELD_TYPE          => 'integer',
                  ONAPP_FIELD_REQUIRED      => true,
                  ONAPP_FIELD_DEFAULT_VALUE => ''
              );
              $this->_fields["required_ip_address_assignment"] = array(
                  ONAPP_FIELD_MAP           => '_required_ip_address_assignment',
                  ONAPP_FIELD_TYPE          => 'boolean',
                  ONAPP_FIELD_REQUIRED      => true,
                  ONAPP_FIELD_DEFAULT_VALUE => ''
              );
              $this->_fields["required_virtual_machine_build"] = array(
                  ONAPP_FIELD_MAP           => '_required_virtual_machine_build',
                  ONAPP_FIELD_TYPE          => 'boolean',
                  ONAPP_FIELD_REQUIRED      => true,
                  ONAPP_FIELD_DEFAULT_VALUE => ''
              );
          };

        break;
        case '2.0.1':
          $this->_fields = $this->_init_fields("2.0.0");
        break;
      };

      return $this->_fields;

    }

    function getResource($action = ONAPP_GETRESOURCE_DEFAULT) {
        switch($action) {
            case ONAPP_GETRESOURCE_REBOOT:
                $resource .= $this->getResource(ONAPP_GETRESOURCE_LOAD).'/reboot';;
                break;

            case ONAPP_GETRESOURCE_SHUTDOWN:
                $resource = $this->getResource(ONAPP_GETRESOURCE_LOAD).'/shutdown';
                break;

            case ONAPP_GETRESOURCE_STARTUP:
                $resource = $this->getResource(ONAPP_GETRESOURCE_LOAD).'/startup';
                break;

            case ONAPP_GETRESOURCE_UNLOCK:
                $resource = $this->getResource(ONAPP_GETRESOURCE_LOAD).'/unlock';
                break;

            case ONAPP_GETRESOURCE_BUILD:
                $resource = $this->getResource(ONAPP_GETRESOURCE_LOAD).'/build';
                break;

            case ONAPP_ACTIVATE_GETLIST_USER:
                $resource = "/users/" . $this->_user_id . "/virtual_machines";
                break;

            default:
                $resource = parent::getResource($action);
                break;
        }
        
        $actions = array(
            ONAPP_GETRESOURCE_REBOOT,
            ONAPP_GETRESOURCE_SHUTDOWN,
            ONAPP_GETRESOURCE_STARTUP,
            ONAPP_GETRESOURCE_UNLOCK,
            ONAPP_GETRESOURCE_BUILD,
            ONAPP_ACTIVATE_GETLIST_USER
        );
        if (in_array($action, $actions))
            $this->_loger->debug("getResource($action): return ".$resource);

        return $resource;
    }

    /**
     * @return string console output
     *
     * @access private
     */
    function _POSTAction($resource) {
        switch ( $this->options[ONAPP_OPTION_API_TYPE] ) {
            case 'xml':

                require_once dirname(__FILE__).'/XMLObjectCast.php';

                $this->_loger->add("_POSTAction: Load XMLObjectCast (serializer and unserializer functions).");

                $objCast = &new XMLObjectCast();

                $data = $objCast->serialize(
                    $this->_tagRoot,
                    $this->_getRequiredData()
                );
/*
                if ( $resource == ONAPP_GETRESOURCE_BUILD ) {
                    $data = '';

                    $template_id  = is_null($this->_template_id) ? $this->_obj->_template_id : $this->_template_id;

                    if( $template_id )
                        $data = sprintf('template_id=%d&', $template_id);

                    if ($this->_required_startup)
                        $data .= sprintf("required_startup=%s", $this->_required_startup);

//                    curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
                };
*/
                $this->_loger->debug(
                    "serialize: Serialize Class in to String:\n$data"
                );

                $this->setAPIResource( $this->getResource($resource) );
    
                $response = $this->sendRequest(ONAPP_REQUEST_METHOD_POST, $data);

                $result = $this->_castResponseToClass( $response );

                if (! is_null($this->error) )
                    $this->_obj = $result;
                else
                    $this->_obj->error = $this->error;

            break;
            default:
                $this->error("_POSTAction: Can't find serialize and unserialize functions for type (apiVersion => '".$this->_apiVersion."').", __FILE__, __LINE__ );
        }
    }

    /**
     * Reboot Virtual machine
     *
     * @access public
     */
    function reboot() {
            $this->_POSTAction(ONAPP_GETRESOURCE_REBOOT);
    }

    /**
     * Stop Virtual Machine
     *
     * @access public
     */
    function shutdown() {
            $this->_POSTAction(ONAPP_GETRESOURCE_SHUTDOWN);
    }

    /**
     * Start Virtual machine
     *
     * @access public
     */
    function startup() {
            $this->_POSTAction(ONAPP_GETRESOURCE_STARTUP);
            $this->_obj = $this;
    }

    /**
     * Unlock Virtual machine
     *
     * @access public
     */
    function unlock() {
            $this->_POSTAction(ONAPP_GETRESOURCE_UNLOCK);
    }

    /**
     * Build or rebuild Virtual machine
     *
     * @access public
     */
    function build() {
            $this->_POSTAction(ONAPP_GETRESOURCE_BUILD);
    }


    /**
     * Sends an API request to get the Objects. After requesting, 
     * unserializes the received response into the array of Objects
     *
     * @return the array of Object instances
     * @access public
     */
    function getList($user_id = NULL) {

        if ( is_null($user_id) )
           return parent::getList(); 
        else {
            $this->activate(ONAPP_ACTIVATE_GETLIST);

            $this->_loger->add("getList: Get Transaction list.");

            $this->_user_id = $user_id;

            $this->setAPIResource( $this->getResource(ONAPP_ACTIVATE_GETLIST_USER) );

            $response = $this->sendRequest(ONAPP_REQUEST_METHOD_GET);

            if (!empty($response['errors'])) {
                $this->error = $response['errors'];
                return false;
            }

            return $this->castStringToClass(
                $response["response_body"],
                true
            );
        };
    }

    /**
     * Save Object in to your account.
     */
    function save() {
        $fields = $this->_fields;

        $this->_fields["primary_disk_size"] = array(
            ONAPP_FIELD_MAP           => '_primary_disk_size',
            ONAPP_FIELD_TYPE          => 'integer',
            ONAPP_FIELD_REQUIRED      => true,
            ONAPP_FIELD_DEFAULT_VALUE => 1
        );
        $this->_fields["swap_disk_size"] = array(
            ONAPP_FIELD_MAP           => '_swap_disk_size',
            ONAPP_FIELD_TYPE          => 'integer',
            ONAPP_FIELD_REQUIRED      => true,
            ONAPP_FIELD_DEFAULT_VALUE => 0
        );
        $this->_fields["primary_network_id"] = array(
            ONAPP_FIELD_MAP           => '_primary_network_id',
            ONAPP_FIELD_TYPE          => 'integer',
            ONAPP_FIELD_REQUIRED      => true,
            ONAPP_FIELD_DEFAULT_VALUE => ''
        );
        $this->_fields["required_automatic_backup"] = array(
            ONAPP_FIELD_MAP           => '_required_automatic_backup',
            ONAPP_FIELD_TYPE          => 'boolean',
            ONAPP_FIELD_REQUIRED      => true,
            ONAPP_FIELD_DEFAULT_VALUE => ''
        );
        $this->_fields["rate_limit"] = array(
            ONAPP_FIELD_MAP           => '_rate_limit',
            ONAPP_FIELD_TYPE          => 'integer',
            ONAPP_FIELD_REQUIRED      => true,
            ONAPP_FIELD_DEFAULT_VALUE => ''
        );
        $this->_fields["required_ip_address_assignment"] = array(
            ONAPP_FIELD_MAP           => '_required_ip_address_assignment',
            ONAPP_FIELD_TYPE          => 'boolean',
            ONAPP_FIELD_REQUIRED      => true,
            ONAPP_FIELD_DEFAULT_VALUE => ''
        );
        $this->_fields["required_virtual_machine_build"] = array(
            ONAPP_FIELD_MAP           => '_required_virtual_machine_build',
            ONAPP_FIELD_TYPE          => 'boolean',
            ONAPP_FIELD_REQUIRED      => true,
            ONAPP_FIELD_DEFAULT_VALUE => ''
        );

        parent::save();

        $this->_fields = $fields;
    }

}

?>
