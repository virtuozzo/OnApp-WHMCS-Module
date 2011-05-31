<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Managing Roles
 *
 * A role is a set of actions users are allowed to perform. OnApp allows you to
 * assign users roles and permissions to define who has access to OnApp and what
 * actions they can perform. OnApp maps users to the certain roles, and you can
 * restrict which operations each user role can perform. Users are not assigned
 * permissions directly, but acquire them through the roles. So granting users
 * with the ability to perform actions becomes a matter of assigning them to the
 * specific role. Users are assigned roles during the creation process.
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
 * User Roles
 *
 * This class represents the roles assigned  to the users in this OnApp installation
 *
 * The ONAPP_Role class uses the following basic methods:
 * {@link load}, {@link save}, {@link delete}, and {@link getList}.
 *
 * <b>Use the following XML API requests:</b>
 *
 * Get the list of roles
 *
 *     - <i>GET onapp.com/settings/roles.xml</i>
 *
 * Get a particular role details
 *
 *     - <i>GET onapp.com/settings/roles/{ID}.xml</i>
 *
 * Add new role
 *
 *     - <i>POST onapp.com/settings/roles.xml</i>
 *
 * <code>
 * <?xml version="1.0" encoding="UTF-8"?>
 * <role>
 *    <identifier>{IDENTIFIER}</identifier>
 *    <label>{LABEL}</label>
 * </role>
 * </code>
 *
 * Edit existing role
 *
 *     - <i>PUT onapp.com/settings/roles/{ID}.xml</i>
 *
 * <code>
 * <?xml version="1.0" encoding="UTF-8"?>
 * <role>
 *    <identifier>{IDENTIFIER}</identifier>
 *    <label>{LABEL}</label>
 * </role>
 * </code>
 *
 * Delete role
 *
 *     - <i>DELETE onapp.com/settings/roles/{ID}.xml</i>
 *
 * <b>Use the following JSON API requests:</b>
 *
 * Get the list of roles
 *
 *     - <i>GET onapp.com/settings/roles.json</i>
 *
 * Get a particular role details
 *
 *     - <i>GET onapp.com/settings/roles/{ID}.json</i>
 *
 * Add new role
 *
 *     - <i>POST onapp.com/settings/roles.json</i>
 *
 * <code>
 * {
 *      role: {
 *          identifier:'{IDENTIFIER}',
 *          label:'{LABEL}'
 *      }
 * }
 * </code>
 *
 * Edit existing role
 *
 *     - <i>PUT onapp.com/settings/roles/{ID}.json</i>
 *
 * <code>
 * {
 *      role: {
 *          identifier:'{IDENTIFIER}',
 *          label:'{LABEL}'
 *      }
 * }
 * </code>
 *
 * Delete role
 *
 *     - <i>DELETE onapp.com/settings/roles/{ID}.json</i>
 */
class ONAPP_Role extends ONAPP {

    /**
     * the role ID
     *
     * @var integer
     */
    var $_id;

    /**
     * the role creation date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_created_at;

    /**
     * the role identifier
     *
     * @var integer
     */
    var $_identifier;

    /**
     * the role permissions
     *
     * @var array of permission
     */

    var $_permissions;

    /**
     * the role label used for organizational purposes
     *
     * @var integer
     */
    var $_label;

    /**
     * the Role update date in the [YYYY][MM][DD]T[hh][mm]Z format
     *
     * @var string
     */
    var $_updated_at;

    /**
     * root tag used in the API request
     *
     * @var string
     */
    var $_tagRoot = 'role';

    /**
     * alias processing the object data
     *
     * @var string
     */
    var $_resource = 'roles';

    /**
     *
     * called class name
     *
     * @var string
     */
    var $_called_class = 'ONAPP_Role';

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
                        ONAPP_FIELD_READ_ONLY => true,
                        //    ONAPP_FIELD_DEFAULT_VALUE => ''
                    ),
                    'created_at' => array(
                        ONAPP_FIELD_MAP => '_created_at',
                        ONAPP_FIELD_TYPE => 'datetime',
                        ONAPP_FIELD_READ_ONLY => true,
                        //    ONAPP_FIELD_DEFAULT_VALUE => ''
                    ),
                    'identifier' => array(
                        ONAPP_FIELD_MAP => '_identifier',
                        ONAPP_FIELD_REQUIRED => true,
                        //    ONAPP_FIELD_DEFAULT_VALUE => ''
                    ),
                    'permissions' => array(
                        ONAPP_FIELD_MAP => '_permissions',
                        ONAPP_FIELD_REQUIRED => true,
                        //    ONAPP_FIELD_DEFAULT_VALUE => ''
                    ),
                    'label' => array(
                        ONAPP_FIELD_MAP => '_label',
                        ONAPP_FIELD_REQUIRED => true,
                        //    ONAPP_FIELD_DEFAULT_VALUE => ''
                    ),
                    'updated_at' => array(
                        ONAPP_FIELD_MAP => '_updated_at',
                        ONAPP_FIELD_TYPE => 'datetime',
                        ONAPP_FIELD_READ_ONLY => true,
                        //    ONAPP_FIELD_DEFAULT_VALUE => ''
                    ),
                );
                break;
        }

        return $this->_fields;
    }
}