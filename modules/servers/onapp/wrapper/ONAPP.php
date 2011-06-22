<?php
error_reporting( E_ALL );

ini_set( 'display_errors', 0 );

/**
 * API Wrapper for OnApp
 *
 * This API provides an interface to onapp.net allowing common virtual machine
 * and account management tasks
 *
 * @category  API WRAPPER
 * @package   ONAPP
 * @author    Andrew Yatskovets
 * @copyright 2010 / OnApp
 * @link      http://www.onapp.com/
 *
 * @todo Pack using the lib (http://pecl.php.net/)
 */

/**
 * Use this class for debugging and error validation
 */
require_once dirname( __FILE__ ) . '/Logger.php';

/**
 * Current OnApp PHP API wrapper version
 */
define( 'ONAPP_VERSION', '1.0' );

/**
 * The ONAPP class uses this variable to define the Proxy server used by cURL
 */
define( 'ONAPP_OPTION_CURL_PROXY', 'proxy' );

/**
 * The ONAPP class uses this variable to define the URL to the API used by cURL
 */
define( 'ONAPP_OPTION_CURL_URL', 'url' );

/**
 * The ONAPP class uses this variable to define the data type which would help transfer data between the client and the API server
 *
 * Possible values:
 *   - xml  (default)
 *   - json (will be available after the parcer is created)
 */
define( 'ONAPP_OPTION_API_TYPE', 'data_type' );

/**
 * The ONAPP class uses this variable to define the charsets used to transfer data between the client and the API server
 *
 * Possible values:
 *   - charset=utf-8 (default)
 */
define( 'ONAPP_OPTION_API_CHARSET', 'charset' );

/**
 * The ONAPP class uses this value to define the content type used to transfer data between the client and the API server
 *
 * Possible values:
 *   - application/xml (default)
 *   - application/json (will be available after the Json parcer is created)
 */
define( 'ONAPP_OPTION_API_CONTENT', 'content' );

/**
 * TODO add description
 */
define( 'ONAPP_OPTION_DEBUG_MODE', 'debug_mode' );

/**
 * The ONAPP class uses this field name to map this field in the API response and variable in the class
 * The field name is used to unserialize the API server response to the necessary class.
 */
define( 'ONAPP_FIELD_MAP', 'map' );

/**
 * The field name that stands for the mapped field type in the API response
 *
 * The field is used to unserialize the object into API request.
 * Possible values:
 *   - integer
 *   - ...
 */
define( 'ONAPP_FIELD_TYPE', 'type' );

/**
 * The field name that stands for the field access in the API response
 *
 * Used to unserialize the object into API request.
 * Possible values:
 *   - true
 *   - false
 */
define( 'ONAPP_FIELD_READ_ONLY', 'read_only' );

/**
 * The field name that specifies if it is necessary to be used in the API request when new objects are created or existing edited
 *
 * Possible values:
 *   - true
 *   - false
 */
define( 'ONAPP_FIELD_REQUIRED', 'required' );

/**
 * The field name that stands for the default field value
 *
 * The field name is used to unserialize if the field was changed or not loaded.
 */
define( 'ONAPP_FIELD_DEFAULT_VALUE', 'default' );

/**
 * Specify field type to serialize and unserialize obgect using their name
 */
define( 'ONAPP_FIELD_CLASS', 'class' );

/**
 *
 */
define( 'ONAPP_GETRESOURCE_DEFAULT', 'default' );

/**
 *
 */
define( 'ONAPP_GETRESOURCE_LOAD', 'load' );

/**
 *
 */
define( 'ONAPP_GETRESOURCE_LIST', 'list' );

/**
 *
 *
 */
define( 'ONAPP_GETRESOURCE_ADD', 'add' );

/**
 *
 *
 */
define( 'ONAPP_GETRESOURCE_EDIT', 'edit' );

/**
 *
 *
 */
define( 'ONAPP_GETRESOURCE_DELETE', 'delete' );

/**
 *
 *
 */
define( 'ONAPP_GETRESOURCE_VERSION', 'version' );

/**
 *
 *
 */
define( 'ONAPP_ACTIVATE_GETLIST', 'getList' );

/**
 *
 *
 */
define( 'ONAPP_ACTIVATE_LOAD', 'load' );

/**
 *
 *
 */
define( 'ONAPP_ACTIVATE_SAVE', 'save' );

/**
 *
 *
 */
define( 'ONAPP_ACTIVATE_DELETE', 'delete' );

/**
 * Specify the GET request
 *
 */
define( 'ONAPP_REQUEST_METHOD_GET', 'GET' );

/**
 * Specify the POST request
 *
 */
define( 'ONAPP_REQUEST_METHOD_POST', 'POST' );

/**
 * Specify the PUT request
 *
 */
define( 'ONAPP_REQUEST_METHOD_PUT', 'PUT' );

/**
 * Specify the DELETE request
 *
 */
define( 'ONAPP_REQUEST_METHOD_DELETE', 'DELETE' );

/**
 * Basic ONAPP API Wrapper
 *
 * The wrapper is used to describe the following basic methods: {@link load},
 * {@link save}, {@link delete} and {@link getList}.
 *
 * To create a new class inheriting this one, re-define the
 * following variables:
 * <code>
 *
 *    // root tag used in the API request
 *    var $_tagRoot  = '<root>';
 *
 *    // alias processing the object data
 *    var $_resource = '<alias>';
 *
 *    // the fields array used in the response and request to the API server
 *    var $_fields   = array(
 *     ...
 *    )
 * </code>
 *
 * To create a read-only class, close the save and delete methods.
 * To re-define the traditional API aliases to the non-traditional,
 * re-define the  {@link getResource},  {@link getResourceADD}, {@link getResourceEDIT},
 * {@link getResourceLOAD},  {@link getResourceDELETE} and  {@link getResourceLIST}
 * methods in the class that will be inheriting the ONAPP class.
 */
class ONAPP {

    /**
     * The list of all available options used in the class to create API requests and receive responses,
     * as well as to serialize and unserialize.
     *
     * @access private
     * @var    array
     */
    var $_knownOptions = array(
        ONAPP_OPTION_CURL_PROXY,
        ONAPP_OPTION_CURL_URL,
        ONAPP_OPTION_API_TYPE,
        ONAPP_OPTION_API_CHARSET,
        ONAPP_OPTION_API_CONTENT,
        ONAPP_OPTION_DEBUG_MODE
    );

    /**
     * Default options used in the class to create API requests and receive responses,
     * as well as serialize and unserialize objects.
     *
     * @access private
     * @var    array
     */
    var $_defaultOptions = array(
        // cURL proxy
        ONAPP_OPTION_CURL_PROXY => '',

        // cURL url
        ONAPP_OPTION_CURL_URL => '',

        // API request and response charset
        ONAPP_OPTION_API_CHARSET => 'charset=utf-8',

        // API request and response type
        ONAPP_OPTION_API_TYPE => 'xml',

        // API request and response content
        ONAPP_OPTION_API_CONTENT => 'application/xml',

        // Debug mode
        ONAPP_OPTION_DEBUG_MODE => false,
    );

    /**
     * The array of the options used to create API requests and receive responses,
     * as well as serialize and unserialize objects in the class
     *
     * By default equals to $_defaultOptions
     *
     * <code>
     *    var $options = array(
     *
     *        // cURL proxy
     *        ONAPP_OPTION_CURL_PROXY     => '',
     *
     *        // cURL url
     *        ONAPP_OPTION_CURL_URL       => '',
     *
     *        // API request and response type
     *        ONAPP_OPTION_API_TYPE       => 'xml',
     *
     *        // API request and response charset
     *        ONAPP_OPTION_API_CHARSET   => 'charset=utf-8',
     *
     *        // API request and response content
     *        ONAPP_OPTION_API_CONTENT   => 'application/xml',
     *
     *        // Debug mode
     *        ONAPP_OPTION_DEBUG_MODE => false
     *    );
     * </code>
     *
     * @access public
     * @var    array
     */
    var $options = array( );

    /**
     * The Object Logger used to log the processes in the basic and inherited classes
     * It is possible to use the debug add error log methods
     *
     * @access private
     * @var    Logger
     */
    var $_loger;

    /**
     * Object cURL
     * PHP supports libcurl, a library created by Daniel Stenberg,
     * that allows you to connect and communicate to many different types of servers with many different types of protocols.
     * libcurl currently supports the http, https, ftp, gopher, telnet, dict, file and ldap protocols.
     * libcurl also supports HTTPS certificates, HTTP POST, HTTP PUT, FTP uploading (this can also be done with PHP's ftp extension),
     * HTTP form based upload, proxies, cookies and user+password authentication.
     *
     * @access private
     * @var    cURL
     */
    var $_ch;

    /**
     * Variable storing the data loaded by the API request. The data is static and cannot be changed by the class setters
     *
     * @access private
     * @var    object
     */
    var $_obj;

    /**
     * Variable for error handling
     *
     * @access public
     * @var    string
     */
    var $error;

    /**
     * cURL Object alias used as the basic alias to the load, save, delete and getList methods
     *
     * @access private
     * @var    string
     */
    var $_resource = null;

    /**
     * @access private
     * @var    string
     */
    var $_tagRoot = null;

    /**
     * @access private
     * @var    array
     */
    var $_tagRequired = null;

    /**
     * @access private
     * @var    boolean
     * @todo move in to getter an setter
     */
    var $_is_auth = false;

    /**
     * @access private
     * @var    boolean
     * @todo move in to getter an setter
     */
    var $_is_changed = false;

    /**
     * @access private
     * @var    boolean
     * @todo move in to getter an setter
     */
    var $_is_deleted = false;

    /**
     * Return OnApp version
     *
     * @access private
     * @var    sting
     */
    var $_version;

    /**
     * Return OnApp release
     *
     * @access private
     * @var    sting
     */
    var $_release;

    /**
     * Return OnApp fields array mapping
     *
     * @access private
     * @var    array
     */
    var $_fields;

    /**
     * Returns API version
     *
     * @access private
     * @return string  $version API version
     */
    function _apiVersion( ) {
        return $this->_version;
    }

    /**
     * Resets all options to default options
     *
     * @return void
     * @access public
     */
    function resetOptions( ) {
        $this->options = $this->_defaultOptions;
    }

    /**
     * Sets an option
     *
     * Use this method if you do not want
     * to set all options in the constructor
     *
     * @param string $name  option name
     * @param mixed  $value option value
     *
     * @return void
     * @access public
     */
    function setOption( $name, $value ) {
        $this->_loger->debug( "setOption: Set option $name => $value" );
        $this->options[ $name ] = $value;
    }

    /**
     * Sets several options at once
     *
     * Use this method if you do not want
     * to set all options in the constructor
     *
     * @param array $options options array
     *
     * @return void
     * @access public
     */
    function setOptions( $options ) {
        $this->options = array_merge( $this->options, $options );
    }

    /**
     * Creates data fro API response to save or change the object data
     *
     * Returns the Hash of Object fields with values
     *
     * @return hash of string
     * @access private
     */
    function _getRequiredData( ) {
        $this->_loger->debug( "_getRequiredData: Prepare data array:" );
        $result = array( );

        foreach( $this->_fields as $key => $value )
            if( isset( $value[ ONAPP_FIELD_REQUIRED ] ) &&
                $value[ ONAPP_FIELD_REQUIRED ] ) {

                $property = $value[ ONAPP_FIELD_MAP ];
                if( isset( $this->$property ) ) {
                    $result[ $key ] = $this->$property;
                }
                else {
                    if( isset( $this->_obj->$property ) ) {
                        $result[ $key ] = $this->_obj->$property;
                    }
                    else {
                        if( isset( $value[ ONAPP_FIELD_DEFAULT_VALUE ] ) ) {
                            $result[ $key ] = $value[ ONAPP_FIELD_DEFAULT_VALUE ];
                        }
                        else
                        {
                            $this->_loger->error(
                                "_getRequiredData: Property $property not defined",
                                __FILE__,
                                __LINE__
                            );
                        }
                    }
                };

                if( isset($result[ $key ]) )
                    $this->_loger->debug( "_getRequiredData: set attribute ( $key => '" . $result[ $key ] . "')." );
            }
        ;

        return $result;
    }

    /**
     * Returns the URL Alias of the API Class that inherits the Class ONAPP
     *
     * Can be redefined if the API does not use the default alias (the alias
     * consisting of few fields).
     * The following example illustrates:
     *
     * <code>
     *    function getResource() {
     *        return "alias/" . $this->_field_name . "/" . $this->_resource;
     *    }
     * </code>
     *
     *
     * @return string API resource
     * @access public
     */
    function getResource( $action = ONAPP_GETRESOURCE_DEFAULT ) {
        switch( $action ) {
            case ONAPP_GETRESOURCE_LOAD:
            case ONAPP_GETRESOURCE_EDIT:
            case ONAPP_GETRESOURCE_DELETE:
                $resource = $this->getResource( ) . "/" . $this->_id;
                break;
            case ONAPP_GETRESOURCE_LIST:
            case ONAPP_GETRESOURCE_ADD:
                $resource = $this->getResource( );
                break;
            case ONAPP_GETRESOURCE_DEFAULT:
            default:
                $resource = $this->_resource;
                break;
        }
        $this->_loger->debug( "getResource($action): return " . $resource );

        return $resource;
    }

    /**
     * Returns true if the API instance has authentication information set and authentication was succesful
     *
     * @return boolean true if authenticated
     * @access public
     *
     * @todo move to the defaut getter
     */
    function isAuthenticate( ) {
        return $this->_is_auth;
    }

    /**
     * Returns true if the Object was changed and API response was succesfull
     *
     * @return boolean true if the Object was changed
     * @access public
     *
     * @todo move to the defaut getter
     */
    function isChanged( ) {
        return $this->_is_changed;
    }

    /**
     * Returns true if the Object was deleted in the API instance
     * and API response was succesfull
     *
     * @return boolean true if the Object was deleted
     * @access public
     *
     * @todo move to the defaut getter
     */
    function isDelete( ) {
        return $this->_is_deleted;
    }

    /**
     * Returns Text written to the full class logs
     *
     * When the log level is set to debug, the debug messages will be also
     * included
     *
     * @return string All formatted logs
     * @access public
     */
    function logs( ) {
        if( isset( $this->_loger ) ) {
            return $this->_loger->logs( );
        }
    }

    /**
     * Authorizes users in the system by the specified URL by means of cURL
     *
     * To authorize, set the user name and password. Specify the Proxy, if
     * needed. When authorized, {@link load}, {@link save}, {@link delete} and
     * {@link getList} methods can be used.
     *
     * @param string $url API URL
     * @param string $user user name
     * @param string $pass password
     * @param string $proxy (optional) proxy server
     *
     * @return void
     * @access public
     */
    function auth( $url, $user, $pass, $proxy = '' ) {
        $this->options = $this->_defaultOptions;

        $this->_loger = new ONAPP_Logger;

        $this->_loger->setDebug(  $this->options[ ONAPP_OPTION_DEBUG_MODE ] );

        $this->_loger->setTimezone( );

        $this->_loger->debug( "auth: Authorization(url => '$url', user => '$user', pass => '********')." );

        $this->setOption( ONAPP_OPTION_CURL_URL, $url );
        $this->setOption( ONAPP_OPTION_CURL_PROXY, $proxy );

        $this->_init_curl( $user, $pass );

        $this->setAPIResource( ONAPP_GETRESOURCE_VERSION );

        $response = $this->sendRequest( ONAPP_REQUEST_METHOD_GET );

        if( $response[ "info" ][ "http_code" ] == "200" ) {
            $this->_version = $response[ "response_body" ];
        }
        else {
            $this->error = "Can't get OnApp version.";
        }

        $this->_load_fields( );

        $this->_is_auth = true;
    }

    function _load_fields( ) {
        $this->setAPIResource( ONAPP_GETRESOURCE_VERSION );

        $response = $this->sendRequest( ONAPP_REQUEST_METHOD_GET );

        if( $response[ 'info' ][ 'http_code' ] == '200' ) {

            preg_match_all( '#([0-9]*)\.([0-9]*)\.(\w+)?#', $response[ 'response_body' ], $out );

            if( $out[ 1 ] != null && $out[ 2 ] != null ) {
                $this->_version = $out[ 1 ][ 0 ] . '.' . $out[ 2 ][ 0 ];
                $this->_release = $out[ 3 ][ 0 ];
            }
            else {
                $this->error = 'OnApp version does not match with "([0-9]*)\.([0-9]*)\.(\w+)?"';
            }
        }
        else {
            $this->error = 'Can\'t get OnApp version.';
        }

        $this->_init_fields( $this->_version );

     }

    function _init_fields( ) { }

    /**
     * Sets an option for a cURL transfer
     *
     * @param string $user user name
     * @param string $pass password
     * @param string $cookiedir Cookies directory
     *
     * @return void
     * @access private
     *
     * @todo check response from basic URL
     */
    function _init_curl( $user, $pass, $cookiedir = '' ) {
        $this->_loger->debug( "_init_curl: Init Curl (cookiedir => '$cookiedir')." );

        $this->_ch = curl_init( );

        $this->_is_auth = true;

        if( strlen( $this->options[ ONAPP_OPTION_CURL_PROXY ] ) > 0 ) {
            curl_setopt(
                $this->_ch,
                CURLOPT_PROXY,
                $this->options[ ONAPP_OPTION_CURL_PROXY ]
            );
        }

        curl_setopt( $this->_ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $this->_ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt(
            $this->_ch, CURLOPT_USERPWD,
                $user . ':' . $pass
        );
    }

    /**
     * Closes a cURL session
     *
     * @return void
     * @access public
     */
    function close_curl( ) {
        curl_close( $this->_ch );
    }

    /**
     * Sets full API path to the variable cURL
     *
     * @param string $resource API alias
     * @param boolean $append_api_version
     * @param string $queryString API request
     *
     * @return void
     * @access public
     */
    function setAPIResource( $resource, $append_api_version = true, $queryString = '' ) {
        $url = $this->options[ ONAPP_OPTION_CURL_URL ];
        $this->_loger->add(
            "setAPIResource: Set an option for a cURL transfer (" .
            " url => '$url'," .
            " resource => '$resource'," .
            " data_type => '" . $this->options[ ONAPP_OPTION_API_TYPE ] . "'" .
            " append_api_version => '$append_api_version'," .
            " queryString => '$queryString')."
        );

        if( $append_api_version ) {
            curl_setopt(
                $this->_ch,
                CURLOPT_URL,
                sprintf(
                    '%1$s/%2$s.%3$s?%4$s',
                    $url,
                    $resource,
                    $this->options[ ONAPP_OPTION_API_TYPE ],
                    $queryString )
            );
        }
        else {
            curl_setopt(
                $this->_ch,
                CURLOPT_URL,
                sprintf(
                    '%1$s/%2$s?%3$s',
                    $url,
                    $resource,
                    $queryString
                )
            );
        }
    }

    /**
     * Sends API request to the API server and gets response from it
     *
     * @return array cURL response
     * @access public
     */
    function sendRequest( $method, $data = null ) {
        $alowed_methods = array(
            ONAPP_REQUEST_METHOD_GET,
            ONAPP_REQUEST_METHOD_POST,
            ONAPP_REQUEST_METHOD_PUT,
            ONAPP_REQUEST_METHOD_DELETE,
        );
        if( !in_array( $method, $alowed_methods ) ) {
            $this->_loger->error( 'Wrong request method.' );
        }

        $debug_msg = "Send $method request.";
        if( $data ) {
            $debug_msg .= " Reqest:\n$data";
        }
        $this->_loger->debug( $debug_msg );

        $http_header = array(
            'Content-Type: ' . $this->options[ ONAPP_OPTION_API_CONTENT ],
            'Accept: ' . $this->options[ ONAPP_OPTION_API_CONTENT ]
        );

        curl_setopt( $this->_ch, CURLOPT_CUSTOMREQUEST, $method );
        switch( $method ) {
            case ONAPP_REQUEST_METHOD_GET:
                curl_setopt( $this->_ch, CURLOPT_HTTPGET, true );
                $http_header[ ] = 'Content-Length: 0';
                break;

            case ONAPP_REQUEST_METHOD_POST:
                curl_setopt( $this->_ch, CURLOPT_POST, true );

                if( !is_null( $data ) ) {
                    curl_setopt( $this->_ch, CURLOPT_POSTFIELDS, $data );
                }
                break;

            case ONAPP_REQUEST_METHOD_PUT:
                $http_header[ ] = 'Content-Length: ' . strlen( $data );

                if( !is_null( $data ) ) {
                    curl_setopt( $this->_ch, CURLOPT_POSTFIELDS, $data );
                }
                break;

            case ONAPP_REQUEST_METHOD_DELETE:
                break;
        }

        curl_setopt(
            $this->_ch,
            CURLOPT_HTTPHEADER,
            $http_header
        );

        $result = array( );
        $result[ 'response_body' ] = curl_exec( $this->_ch );
        $result[ 'info' ] = curl_getinfo( $this->_ch );

        if( !$result[ 'response_body' ] ) {
            return false;
        }

        $content_type = $result[ 'info' ][ 'content_type' ];

        if( $content_type == $this->options[ ONAPP_OPTION_API_CONTENT ] . "; " . $this->options[ ONAPP_OPTION_API_CHARSET ] ) {
            switch( $result[ 'info' ][ 'http_code' ] ) {
                case 200:
                case 201:
                    $this->last_errors = null;
                    break;
                case 422:
                    switch( $this->options[ ONAPP_OPTION_API_TYPE ] ) {
                        case 'xml':
                        case 'json':
                            $this->_loger->add( "Response (code => " . $result[ 'info' ][ 'http_code' ] . ", cast:\n" . $result[ 'response_body' ] );
                            break;
                    }
                    break;

                default:
                    $this->_loger->warning( "Response (code => " . $result[ 'info' ][ 'http_code' ] . ", body:\n" . $result[ 'response_body' ] );

                    $result[ 'errors' ] = $result[ 'response_body' ];
            }
        }
        else {
            $this->_loger->add( "sendRequest: Response:\n" . $result[ 'response_body' ] );
            $result[ 'errors' ] = "Bad response content type: $content_type";
        }

        $this->_errno_curl( $result[ 'response_body' ] );

        return $result;
    }

    /**
     * The method validates the API request errors
     *
     * You will get an error in case of the following error codes:
     *   - UNSUPPORTED PROTOCOL
     *   - FAILED INIT
     *   - URL MALFORMAT
     *   - COULDNT RESOLVE PROXY
     *   - COULDNT RESOLVE HOST
     *
     * @param  string $response_body API Response body
     *
     * @return void
     * @access private
     */
    function _errno_curl( $response_body ) {
        $error_no = curl_errno( $this->_ch );

        switch( $error_no ) {
            case CURLE_OK:
                // Note: the 0 code does not mean an error, but it means success
                $this->_loger->debug( "sendRequest: OK.\n$response_body" );
                break;
            case CURLE_UNSUPPORTED_PROTOCOL : // 1
            case CURLE_UNSUPPORTED_PROTOCOL: // 2
            case CURLE_FAILED_INIT: // 3
            case CURLE_URL_MALFORMAT: // 4
            case CURLE_URL_MALFORMAT_USER: // 5
            case CURLE_COULDNT_RESOLVE_PROXY: // 6
            case CURLE_COULDNT_RESOLVE_HOST: // 7
                $this->_loger->warning(
                    "sendRequest: Error #$error_no.",
                    __FILE__,
                    __LINE__
                );
                break;
            default:
                $this->_loger->warning( "sendRequest: unknown error number $error_no ." );
        }
        ;
    }

    /**
     * Casts an API response to the Array of Objects or an Object
     *
     * @param array $response API response
     * @param boolean $is_list is Object or Array
     *
     * @return mixed (Array of Object or Object)
     * @access private
     */
    function _castResponseToClass( $response, $is_list = false ) {
        $this->_loger->debug( "_castResponseToClass: Cast response in to Object (is_list => '$is_list')." );

        if( isset( $response[ 'response_body' ] ) ) {
            $http_code = $response[ 'info' ][ 'http_code' ];
            $response_body = $response[ "response_body" ];

            switch( $http_code ) {
                case 200:
                case 201:
                    return $this->castStringToClass(
                        $response_body,
                        $is_list
                    );
                    break;
                case 422:
                    return $this->castStringToClass(
                        $response_body
                    );
                    break;
                case 500:
                    $this->error = "We're sorry, but something went wrong.";
                    return $this;
                    break;
                default:
                    $this->error = "Bad response (code => '$http_code', response => $response_body)";
            }
        }
        else {
            $this->_loger->error(
                "castResponseToClass: Can't parse " . $response[ 'response_body' ],
                __FILE__,
                __LINE__
            );
        }
    }

    /**
     * Casts string (API response body content) to the Object
     *
     * @param string $content class string content
     * @param boolean $is_list is array of Class
     *
     * @return mixed array of Objects or Object
     * @access public
     */
    function castStringToClass( $content, $is_list = false ) {
        $classname = $this->_called_class;

        $tagMap = $this->_fields;

        $this->_loger->add( "castStringToClass: cast String in to Object ( classname => '" . $classname . "', is_list => '" . $is_list . "' )." );

        switch( $this->options[ ONAPP_OPTION_API_TYPE ] ) {
            case 'xml':
                require_once dirname( __FILE__ ) . '/XMLObjectCast.php';

                $this->_loger->add( "castStringToClass: Load XMLObjectCast (serializer and unserializer functions)." );

                if( strlen( $content ) > 0 ) {
                    if( !$is_list ) {
                        $objCast = &new XMLObjectCast( $this->_serialize_options );

                        $this->_loger->debug( "unserialize: Unserialize in to Class $classname XML:\n$content" );
                        $obj = $objCast->unserialize( $classname, $content, $tagMap );

                        $dom = new DomDocument;
                        $dom->preserveWhiteSpace = FALSE;

                        if( @$dom->loadXML( $content ) &&
                             $dom->childNodes->length != 0 &&
                             $dom->childNodes->item( 0 )->nodeName != 'nil-classes'
                        ) { 
// TODO fix // PHP Warning:  Invalid argument supplied for foreach() in /home/joker/Desktop/radar-customization/php/ONAPP/ONAPP.php on line 1005
                            foreach( $tagMap as $key => $tag ) {

//                                if( isset( $tag[ ONAPP_FIELD_TYPE ] ) && $tag[ ONAPP_FIELD_TYPE ] == "array" ) {
                                if( isset( $tag[ ONAPP_FIELD_CLASS ] ) ) {

                                    $node_name = $dom->childNodes;

                                    foreach( $dom->childNodes as $param ) {
                                        foreach( $param->getElementsByTagName( $key ) as $node ) {
                                            $childclassname = "ONAPP_" . $tag[ ONAPP_FIELD_CLASS ];
                                            $attr = $tag[ ONAPP_FIELD_MAP ];

                                            $xmlObj = new $childclassname;
                                            $xmlObj->options = $this->_defaultOptions;
                                            $xmlObj->_loger = new ONAPP_Logger;
                                            $xmlObj->_version = $this->_version;
                                            $xmlObj->_init_fields( $this->_version );
                                            $xmlObjcontent = simplexml_import_dom( $node )->asXML( );

                                            if ( isset($tag[ ONAPP_FIELD_TYPE ]) && $tag[ ONAPP_FIELD_TYPE ] == 'array')
                                                $obj->$attr = $xmlObj->castStringToClass( $xmlObjcontent, true);
                                            else
                                                $obj->$attr = $xmlObj->castStringToClass( $xmlObjcontent );
                                        }
                                    }
                                }
                            }
                        }
                    }
                    else {
                        $objCast = &new XMLObjectCast( $this->_serialize_options );
                        $this->_loger->debug( "unserialize: Unserialize list in to array of Class $classname XML:\n$content" );

                        $dom = new DomDocument;
                        $dom->preserveWhiteSpace = FALSE;

                        if( !@$dom->loadXML( $content ) ) {
                            $obj = array( );
                        }
                        else {
                            if( $dom->childNodes->length != 0 &&
                                $dom->childNodes->item( 0 )->nodeName != 'nil-classes' &&
                                $dom->childNodes->item( 0 )->childNodes->length != 0 &&
                                $dom->childNodes->item( 0 )->childNodes->item( 0 )->nodeName != 'nil-classes'
                            ) {
                                $node_name = $dom->childNodes->item( 0 )->childNodes->item( 0 )->nodeName;

                                $params = $dom->getElementsByTagName( $node_name );

                                $result = array( );

                                foreach( $params as $param ) {
                                    $result[ ] = $this->castStringToClass(
                                        simplexml_import_dom( $param )->asXML( )
                                    );
                                }

                                $obj = $result;
                            }
                            else {
                                $obj = array( );
                            }
                        }

                        $this->_loger->debug( 'Found ' . count( $obj ) . ' element(s) in an unserialized array.' );
                    }
                    return $obj;
                }
                else {
                    $this->_loger->warning( "Can't unserialize empty String." );
                }
                break;

            case 'json':
                require_once dirname( __FILE__ ) . '/JSONObjectCast.php';
                $objCast = new JSONObjectCast( $this->_version );
                return $objCast->unserialize( $classname, $content, $tagMap, $this->_tagRoot );
                break;

            default:
                $this->_loger->error( "castStringToClass: Can't find serialize and unserialize functions for type (apiVersion => '" . $this->_apiVersion( ) . "').", __FILE__, __LINE__ );
        }
    }

    /**
     * Activates action performed with object
     *
     * @param string $action_name the name of action
     *
     * @access public
     */
    function activate( $action_name ) {
    }

    /**
     * Sends an API request to get the Objects. After requesting,
     * unserializes the received response into the array of Objects
     *
     * @return the array of Object instances
     * @access public
     */
    function getList( ) {
        $this->activate( ONAPP_ACTIVATE_GETLIST );

        $this->_loger->add( "getList: Get Transaction list." );

        $this->setAPIResource( $this->getResource( ONAPP_GETRESOURCE_LIST ) );
        $response = $this->sendRequest( ONAPP_REQUEST_METHOD_GET );

        if( !empty( $response[ 'errors' ] ) ) {
            $this->error = $response[ 'errors' ];
            return false;
        }

        return $this->castStringToClass(
            $response[ "response_body" ],
            true
        );
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
    function load( $id = null ) {
        $this->activate( ONAPP_ACTIVATE_LOAD );

        if( is_null( $id ) && !is_null( $this->_id ) ) {
            $id = $this->_id;
        }

        if( is_null( $id ) &&
            isset( $this->_obj ) &&
            !is_null( $this->_obj->_id )
        ) {
            $id = $this->_obj->_id;
        }

        if( is_null( $id ) ) {
            $this->_loger->error(
                "load: Can't set variable " . $id,
                __FILE__,
                __LINE__
            );
        }

        $this->_loger->add( "load: Load class ( id => '$id')." );

        if( strlen( $id ) > 0 ) {
            $this->_id = $id;

            $this->setAPIResource( $this->getResource( ONAPP_GETRESOURCE_LOAD ) );

            $response = $this->sendRequest( ONAPP_REQUEST_METHOD_GET );

            $result = $this->_castResponseToClass( $response );

            $this->_obj = $result;
            $this->_id = $this->_obj->_id;

            return $result;
        }
        else {
            $this->_loger->error(
                "load: argument id not set.",
                __FILE__,
                __LINE__
            );
        }
    }

    /**
     * The method saves an Object to your account
     *
     * After sending an API request to create an object or change the data in
     * the existing object, the method checks the response and loads the
     * exisitng object with the new data.
     *
     * This method can be closed for read only objects of the inherited class
     * <code>
     *    function save() {
     *        $this->_loger->error(
     *            "Call to undefined method ".__CLASS__."::save()",
     *            __FILE__,
     *            __LINE__
     *        );
     *    }
     * </code>
     *
     * @return void
     * @access public
     */
    function save( ) {
        $this->activate( ONAPP_ACTIVATE_SAVE );

        if( is_null( $this->_id ) ) {
            $obj = $this->_create( );
        }
        else
        {
            $obj = $this->_edit( );
        }

        if( isset( $obj ) && !isset( $obj->error ) ) {
            $this->load( );
        }
    }

    /**
     * The method creates a new Object
     *
     * @return object Serialized API Response
     * @access private
     */
    function _create( ) {
        $this->_loger->add( "Create new Object." );

        switch( $this->options[ ONAPP_OPTION_API_TYPE ] ) {
            case 'json':
                require_once dirname( __FILE__ ) . '/JSONObjectCast.php';
                $this->_loger->add( '_create: Load JSONObjectCast (serializer and unserializer functions).' );
                $objCast = new JSONObjectCast( $this->_version );

                $data = $objCast->serialize(
                    $this->_tagRoot,
                    $this->_getRequiredData( )
                );

                $this->_loger->debug(
                    'serialize: Serialize Class in to String:' . PHP_EOL . $data
                );

                $this->setAPIResource( $this->getResource( ONAPP_GETRESOURCE_ADD ) );

                $response = $this->sendRequest( ONAPP_REQUEST_METHOD_POST, $data );

                if( !$this->error ) {
                    $result = $this->_castResponseToClass( $response );
                }

                $this->_obj = $result;

                return $result;
                break;

            case 'xml':
                require_once dirname( __FILE__ ) . '/XMLObjectCast.php';

                $this->_loger->add( "_create: Load XMLObjectCast (serializer and unserializer functions)." );

                $objCast = &new XMLObjectCast( );

                $data = $objCast->serialize(
                    $this->_tagRoot,
                    $this->_getRequiredData( )
                );

                $this->_loger->debug(
                    "unserialize: Serialize Class in to String:\n$data"
                );

                $this->setAPIResource( $this->getResource( ONAPP_GETRESOURCE_ADD ) );

                $response = $this->sendRequest( ONAPP_REQUEST_METHOD_POST, $data );

                if( !$this->error ) {
                    $result = $this->_castResponseToClass( $response );
                }

                $this->_obj = $result;

                return $result;
                break;
            default:
                $this->error( "_create: Can't find serialize and unserialize functions for type (apiVersion => '" . $this->_apiVersion( ) . "').", __FILE__, __LINE__ );
        }
    }

    /**
     * The method edits an existing Object
     *
     * @return object Serialized API Response
     * @access private
     */
    function _edit( ) {
        switch( $this->options[ ONAPP_OPTION_API_TYPE ] ) {
            case 'json':
                require_once dirname( __FILE__ ) . '/JSONObjectCast.php';
                $this->_loger->add( '_edit: Load JSONObjectCast (serializer and unserializer functions).' );
                $objCast = new JSONObjectCast( $this->_version );

                $data = $objCast->serialize(
                    $this->_tagRoot,
                    $this->_getRequiredData( )
                );

                $this->_loger->debug(
                    'serialize: Serialize Class in to String:' . PHP_EOL . $data
                );

                $this->setAPIResource( $this->getResource( ONAPP_GETRESOURCE_EDIT ) );

                $this->sendRequest( ONAPP_REQUEST_METHOD_PUT, $data );

                $this->load( $this->_id );
                break;

            case 'xml':
                require_once dirname( __FILE__ ) . '/XMLObjectCast.php';

                $this->_loger->add( "_edit: Load XMLObjectCast (serializer and unserializer functions)." );

                $objCast = &new XMLObjectCast( );

                $data = $objCast->serialize(
                    $this->_tagRoot,
                    $this->_getRequiredData( )
                );

                $this->_loger->debug(
                    "serialize: Serialize Class in to String:\n$data"
                );

                $this->setAPIResource( $this->getResource( ONAPP_GETRESOURCE_EDIT ) );

                $this->sendRequest( ONAPP_REQUEST_METHOD_PUT, $data );

                $this->load( $this->_id );
                break;
            default:
                $this->error( "_edit: Can't find serialize and unserialize functions for type (apiVersion => '" . $this->_apiVersion( ) . "').", __FILE__, __LINE__ );
        }
    }

    /**
     * Sends an API request to delete an Object from your account
     *
     * This method can be closed for read only objects of the inherited class
     * <code>
     *    function delete() {
     *        $this->_loger->error(
     *            "Call to undefined method ".__CLASS__."::delete()",
     *            __FILE__,
     *            __LINE__
     *        );
     *    }
     * </code>
     *
     * @return boolean the Object deleted
     * @access public
     */
    function delete( ) {
        $this->activate( ONAPP_ACTIVATE_DELETE );

        $this->_loger->add( "Delete existing Object ( id => " . $this->_id . " )." );

        $this->setAPIResource( $this->getResource( ONAPP_GETRESOURCE_DELETE ) );

        $this->sendRequest( ONAPP_REQUEST_METHOD_DELETE );

        $this->_is_deleted = true;
    }

    /**
     * The basic PHP5 getter used to auto reload the class GET methods
     *
     * @access private
     *
     * @todo implement functionality
     */
    function __get( $strName ) {
        //TODO
    }

    /**
     * The basic PHP5 setter used to auto reload the class Set methods
     *
     * @access private
     *
     * @todo implement functionality
     */
    function __set( $strName, $mixValue ) {
        //TODO
    }
}
