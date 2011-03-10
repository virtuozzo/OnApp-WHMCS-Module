<?PHP
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Managing Virtual Machine console
 *
 * Using this class You can get access to virtual machine console
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
 * Virtual Machine Console
 *
 */
class ONAPP_Console extends ONAPP {

    /**
     * the virtual machine ID
     *
     * @var integer
     */
    var $_id;

    /**
     * 
     *
     * @todo add description
     */

    var $_called_in_at;

    /**
     * 
     *
     * @todo add description
     */
    var $_created_at;

    /**
     * 
     *
     * @todo add description
     */
    var $_port;

    /**
     * 
     *
     * @todo add description
     */
    var $_updated_at;

    /**
     * 
     *
     * @todo add description
     */
    var $_virtual_machine_id;

    /**
     * 
     *
     * @todo add description
     */
    var $_remote_key;

    /**
     * root tag used in the API request
     *
     * @var string
     */
    var $_tagRoot  = 'remote_access_session';
    
    /**
     * alias processing the object data
     *
     * @var string
     */
    var $_resource = 'remote_access_session';
    
    /**
     * 
     * called class name
     * 
     * @var string
     */
    var $_called_class = 'ONAPP_Console';
    
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
            'called_in_at' => array(
                ONAPP_FIELD_MAP           => '_called_in_at',
                ONAPP_FIELD_TYPE          => 'datetime',
                ONAPP_FIELD_READ_ONLY     => true
            ),
            'created_at' => array(
                ONAPP_FIELD_MAP           => '_created_at',
                ONAPP_FIELD_TYPE          => 'datetime',
                ONAPP_FIELD_READ_ONLY     => true
            ),
            'port' => array(
                ONAPP_FIELD_MAP           => '_port',
                ONAPP_FIELD_TYPE          => 'integer',
                ONAPP_FIELD_READ_ONLY     => true
            ),
            'updated_at' => array(
                ONAPP_FIELD_MAP           => '_updated_at',
                ONAPP_FIELD_TYPE          => 'datetime',
                ONAPP_FIELD_READ_ONLY     => true
            ),
            'virtual_machine_id' => array(
                ONAPP_FIELD_MAP           => '_virtual_machine_id',
                ONAPP_FIELD_TYPE          => 'integer',
                ONAPP_FIELD_READ_ONLY     => true,
            ),
            'remote_key' => array(
                ONAPP_FIELD_MAP           => '_remote_key',
                ONAPP_FIELD_TYPE          => 'string',
                ONAPP_FIELD_READ_ONLY     => true,
            ),
        );

        break;
        case '2.0.1':
          $this->_init_fields = $this->_init_fields("2.0.0");
        break;
      };

      return $this->_fields;
    }

    /**
     * Returns the URL Alias for Load of objects of the API Class that inherits the Class ONAPP
     *
     * Can be redefined if the API for load objects does not use the default
     * alias (the alias consisting of few fields) the same way as {@link
     * getResource}.
     * 
     * @param string $action action name
     * 
     * @return string API resource
     * @access public
     *
     * @see getResource
     */

    function getResource($action = ONAPP_GETRESOURCE_DEFAULT) {
        switch ($action) {
            case ONAPP_GETRESOURCE_LOAD:
                $resource = "virtual_machines/" . $this->_virtual_machine_id . "/console";
                $this->_loger->debug("getResource($action): return ".$resource);
                break;

            default:
                $resource = parent::getResource($action);
                break;
        }

        return $resource;
    }

    /**
     * Sends an API request to get the Object after sending, 
     * unserializes the response into an object
     *
     * The key field Parameter ID is used to load the Object. You can re-set
     * this parameter in the class inheriting Class ONAPP.
     *
     * @param integer $id Object id
     *  
     * @return mixed serialized Object instance from API
     * @access public
     */

    function load( $virtual_machine_id = null ) {
        if ( is_null($virtual_machine_id) && ! is_null($this->_virtual_machine_id) )
            $virtual_machine_id = $this->_virtual_machine_id;

        if ( is_null($virtual_machine_id) &&
            isset($this->_obj) &&
            ! is_null($this->_obj->_virtual_machine_id)
        )
            $virtual_machine_id = $this->_obj->_virtual_machine_id;

        $this->_loger->add("load: Load class ( id => '$virtual_machine_id').");

        if ( ! is_null($virtual_machine_id) ) {

            $this->_virtual_machine_id = $virtual_machine_id;

            $this->setAPIResource( $this->getResource(ONAPP_GETRESOURCE_LOAD) );

            $response = $this->sendRequest(ONAPP_REQUEST_METHOD_GET);

            $result = $this->_castResponseToClass( $response );

            $this->_obj = $result;
#            $this->_virtual_machine_id = $this->_obj->_virtual_machine_id;

            return $result;
        } else {
            $this->_loger->error(
               'load: argument _virtual_machine_id not set.', 
                __FILE__, 
                __LINE__
            );
        }
    }

    /**
     * Activates action performed with object
     * 
     * @param string $action_name the name of action
     * 
     * @access public
     */
    function activate($action_name) {
        switch ($action_name) {
            case ONAPP_ACTIVATE_GETLIST:
            case ONAPP_ACTIVATE_SAVE:
            case ONAPP_ACTIVATE_DELETE:
                die("Call to undefined method ".__CLASS__."::$action_name()");
                break;
        }
    }
}

?>
