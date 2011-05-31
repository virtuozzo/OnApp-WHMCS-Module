<?PHP
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Provisioning Templates
 *
 * In OnApp, a template is a pre-configured OS image that you can immediately build a virtual machine on.
 * There are two types of templates for virtual machine deployment in
 * OnApp:
 *  - downloadable templates provisioned by the OnApp team
 *  - templates you can create by means of backing up and duplicating the existing virtual machine
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
 * Templates
 *
 * This class represents the Templates of the OnApp installation that you can build VMs on.
 *
 * The ONAPP_Template class uses the following basic methods:
 * {@link load}, {@link delete}, and {@link getList}.
 *
 * <b>Use the following XML API requests:</b>
 *
 * Get the list of templates
 *
 *     - <i>GET onapp.com/templates.xml</i>
 *
 * Get a particular template details
 *
 *     - <i>GET onapp.com/templates/{ID}.xml</i>
 *
 * Add new template
 *
 *     - <i>POST onapp.com/templates.xml</i>
 *
 * <code>
 * <?xml version="1.0" encoding="UTF-8"?>
 * <image-template>
 *     <!-- TODO add description -->
 * </image-template>
 * </code>
 *
 * Edit existing template
 *
 *     - <i>PUT onapp.com/templates/{ID}.xml</i>
 *
 * <code>
 * <?xml version="1.0" encoding="UTF-8"?>
 * <image-template>
 *     <!-- TODO add description -->
 * </image-template>
 * </code>
 *
 * Delete template
 *
 *     - <i>DELETE onapp.com/templates/{ID}.xml</i>
 *
 * <b>Use the following JSON API requests:</b>
 *
 * Get the list of templates
 *
 *     - <i>GET onapp.com/templates.json</i>
 *
 * Get a particular template details
 *
 *     - <i>GET onapp.com/templates/{ID}.json</i>
 *
 * Add new template
 *
 *     - <i>POST onapp.com/templates.json</i>
 *
 * <code>
 * {
 *      image-template: {
 *          # TODO add description
 *      }
 * }
 * </code>
 *
 * Edit existing template
 *
 *     - <i>PUT onapp.com/templates/{ID}.json</i>
 *
 * <code>
 * {
 *      image-template: {
 *          # TODO add description
 *      }
 * }
 * </code>
 *
 * Delete template
 *
 *     - <i>DELETE onapp.com/templates/{ID}.json</i>
 */
class ONAPP_Template extends ONAPP {

    /**
     * the template ID
     *
     * @var integer
     */
    var $_id;

    /**
     * true if the VM resizing without rebooting is allowed. Otherwise, false.
     *
     * @var integer
     */
    var $_allow_resize_without_reboot;

    /**
     * true if the swap disk is allowed. Otherwise, false.
     *
     * @var integer
     */
    var $_allowed_swap;

    /**
     * set this parameter to true if you wish to check the template for integrity. Otherwise, set to false.
     *
     * @var integer
     */
    var $_checksum;

    /**
     * the template creation date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_created_at;

    /**
     * the template installation file name
     *
     * @var integer
     */
    var $_file_name;

    /**
     * the Template label
     *
     * @var integer
     */
    var $_label;

    /**
     * the minimum disk size required to install a template
     *
     * @var integer
     */
    var $_min_disk_size;

    /**
     * the Operating System installed with this template
     *
     * @var integer
     */
    var $_operating_system;

    /**
     * the Operating System distribution installed with this template
     *
     * @var integer
     */
    var $_operating_system_distro;

    /**
     * active or not
     *
     * @var integer
     */
    var $_state;

    /**
     * the template update date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_updated_at;

    /**
     * the ID of the user who downloaded/created this template
     *
     * @var integer
     */
    var $_user_id;

    /**
     * the template version
     *
     * @var integer
     */
    var $_template_version;

    /**
     * the template size
     *
     * @var integer
     */
    var $_template_size;

    /**
     * shows whether hot migrate is allowed
     *
     * @var boolean
     */
    var $_allowed_hot_migrate;

    /**
     * opetating system arch
     *
     * @var string
     */
    var $_operating_system_arch;

    /**
     * operating system edition
     *
     * @var string
     */
    var $_operating_system_edition;

    /**
     * operating system tail
     *
     * @var string
     */
    var $_operating_system_tail;

    /**
     * virtualization type
     *
     * @var string
     */
    var $_virtualization;

    /**
     * parent template id
     *
     * @var integer
     */
    var $_parent_template_id;

    /**
     * root tag used in the API request
     *
     * @var string
     */
    var $_tagRoot = 'image-template';

    /**
     * alias processing the object data
     *
     * @var string
     */
    var $_resource = 'templates';

    /**
     *
     * called class name
     *
     * @var string
     */
    var $_called_class = 'ONAPP_Template';

    /**
     * API Fields description
     *
     * @access private
     * @var    array
     */
    function _init_fields( $version = NULL ) {
        if( !isset( $this->options[ ONAPP_OPTION_API_TYPE ] ) || ( $this->options[ ONAPP_OPTION_API_TYPE ] == 'json' ) ) {
            $this->_tagRoot = 'image_template';
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
                    'allow_resize_without_reboot' => array(
                        ONAPP_FIELD_MAP => '_allow_resize_without_reboot',
                        ONAPP_FIELD_TYPE => 'boolean',
                        ONAPP_FIELD_READ_ONLY => true,
                        ONAPP_FIELD_REQUIRED => true,
                    ),
                    'allowed_swap' => array(
                        ONAPP_FIELD_MAP => '_allowed_swap',
                        ONAPP_FIELD_TYPE => 'boolean',
                        ONAPP_FIELD_READ_ONLY => true,
                        ONAPP_FIELD_REQUIRED => true,
                    ),
                    'checksum' => array(
                        ONAPP_FIELD_MAP => '_checksum',
                        ONAPP_FIELD_READ_ONLY => true,
                        ONAPP_FIELD_REQUIRED => true,
                    ),
                    'created_at' => array(
                        ONAPP_FIELD_MAP => '_created_at',
                        ONAPP_FIELD_TYPE => 'datetime',
                        ONAPP_FIELD_READ_ONLY => true,
                        ONAPP_FIELD_REQUIRED => true,
                    ),
                    'file_name' => array(
                        ONAPP_FIELD_MAP => '_file_name',
                        ONAPP_FIELD_READ_ONLY => true,
                        ONAPP_FIELD_REQUIRED => true,
                    ),
                    'label' => array(
                        ONAPP_FIELD_MAP => '_label',
                        ONAPP_FIELD_READ_ONLY => true,
                        ONAPP_FIELD_REQUIRED => true,
                    ),
                    'min_disk_size' => array(
                        ONAPP_FIELD_MAP => '_min_disk_size',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                        ONAPP_FIELD_REQUIRED => true,
                    ),
                    'operating_system' => array(
                        ONAPP_FIELD_MAP => '_operating_system',
                        ONAPP_FIELD_READ_ONLY => true,
                        ONAPP_FIELD_REQUIRED => true,
                    ),
                    'operating_system_distro' => array(
                        ONAPP_FIELD_MAP => '_operating_system_distro',
                        ONAPP_FIELD_READ_ONLY => true,
                        ONAPP_FIELD_REQUIRED => true,
                    ),
                    'state' => array(
                        ONAPP_FIELD_MAP => '_state',
                        ONAPP_FIELD_READ_ONLY => true,
                        ONAPP_FIELD_REQUIRED => true,
                    ),
                    'updated_at' => array(
                        ONAPP_FIELD_MAP => '_updated_at',
                        ONAPP_FIELD_TYPE => 'datetime',
                        ONAPP_FIELD_READ_ONLY => true,
                        ONAPP_FIELD_REQUIRED => true,
                    ),
                    'user_id' => array(
                        ONAPP_FIELD_MAP => '_user_id',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                        ONAPP_FIELD_REQUIRED => true,
                    ),
                    'version' => array(
                        ONAPP_FIELD_MAP => '_template_version',
                        ONAPP_FIELD_READ_ONLY => true,
                        ONAPP_FIELD_REQUIRED => true,
                    ),
                    'template_size' => array(
                        ONAPP_FIELD_MAP => '_template_size',
                        ONAPP_FIELD_TYPE => 'integer',
                        ONAPP_FIELD_READ_ONLY => true,
                    ),
                );

                break;

            case '2.1':
                $this->_fields = $this->_init_fields('2.0');

                $this->_fields[ 'allowed_hot_migrate' ] = array(
                    ONAPP_FIELD_MAP => '_allowed_hot_migrate',
                    ONAPP_FIELD_TYPE => 'boolean',
                    ONAPP_FIELD_REQUIRED => true
                );

                $this->_fields[ 'operating_system_arch' ] = array(
                    ONAPP_FIELD_MAP => '_operating_system_arch',
                    ONAPP_FIELD_TYPE => 'string',
                    ONAPP_FIELD_REQUIRED => true
                );

                $this->_fields[ 'operating_system_edition' ] = array(
                    ONAPP_FIELD_MAP => '_operating_system_edition',
                    ONAPP_FIELD_TYPE => 'string',
                    ONAPP_FIELD_REQUIRED => true
                );

                $this->_fields[ 'operating_system_tail' ] = array(
                    ONAPP_FIELD_MAP => '_operating_system_tail',
                    ONAPP_FIELD_TYPE => 'string',
                    ONAPP_FIELD_REQUIRED => true
                );

                $this->_fields[ 'virtualization' ] = array(
                    ONAPP_FIELD_MAP => '_virtualization',
                    ONAPP_FIELD_TYPE => 'string',
                    ONAPP_FIELD_REQUIRED => true
                );

                $this->_fields[ 'parent_template_id' ] = array(
                    ONAPP_FIELD_MAP => '_template_size',
                    ONAPP_FIELD_TYPE => 'integer',
                    ONAPP_FIELD_READ_ONLY => true,
                );

                break;
        }

        return $this->_fields;
    }

    function activate( $action_name ) {
        switch( $action_name ) {
            case ONAPP_ACTIVATE_SAVE:
                die( "Call to undefined method " . __CLASS__ . "::$action_name()" );
                break;
        }
    }
}

?>
