<?php

/**
 * Serialize and Unserialize Object to/from JSON for OnApp wrapper
 *
 * @category  OBJECT CAST
 * @package   ONAPP
 * @author    Lev Bartashevsky
 * @copyright 2011 OnApp
 * @link      http://www.onapp.com/
 */
class JSONObjectCast {

    /**
     * 
     *
     * @var array
     */
    private $map;

    /**
     * Class name
     *
     * @var string
     */
    private $classname;

    /**
     * Api version
     *
     * @var string
     */
    private $api_version;

    /**
     * Construct the object
     *
     * @param string $version OnApp API version
     */
    public function __construct( $version ) {
        $this->api_version = $version;
    }

    /**
     * Serialize wrapper object to JSON data
     *
     * @param object $obj object to serialize
     *
     * @return void
     */
    public function serialize( $root, $obj ) {
        $obj = array( $root => $obj );

        return json_encode( $obj );
    }

    /**
     * Unserialize JSON data to wrapper object(s)
     *
     * @param string        $classname  classname to cast into
     * @param string|array  $data       JSON or array containing nested data
     * @param array         $map        fields map
     * @param string        $root       root tag
     *
     * @return array|object
     */
    public function unserialize( $classname, $data, $map, $root ) {
        $this->map = $map;
        $this->classname = $classname;

        if( is_string( $data ) ) {
            $data = json_decode( $data );
        }

        try {
            if( empty( $data ) ) {
               // throw new Exception( 'Data for casting could not be empty' );
            }
        }
        catch( Exception $e ) {
            echo PHP_EOL, $e->getMessage( ), PHP_EOL;

            return null;
        }

        if( count( $data ) > 1 ) {
            foreach( $data as $item ) {
                $result[ ] = $this->process( $item->$root );
            }
        }
        else {
            if( is_array( $data ) ) {
                $data = $data[ 0 ];
            }

            $result = $this->process( $data->$root );
        }

        return $result;
    }

    /**
     * Cast data to wrapper object
     *
     * @param object $items data to cast
     *
     * @return object
     */
    private function process( $item ) {
        $obj = new $this->classname;
        foreach( $item as $key => $value ) {
            $field = $this->map[ $key ][ ONAPP_FIELD_MAP ];

            if( isset( $this->map[ $key ][ ONAPP_FIELD_TYPE ] ) ) {
                if( $this->map[ $key ][ ONAPP_FIELD_TYPE ] == 'array' ) {
                    if( empty( $value ) ) {
                        $value = null;
                    }
                    else {
                        $classname = 'ONAPP_' . $this->map[ $key ][ ONAPP_FIELD_CLASS ];
                        $tmp_obj = new $classname;
                        $tmp_obj->_init_fields( $this->api_version );
                        $tmp_parser = new JSONObjectCast( $this->api_version );

                        $value = $tmp_parser->unserialize( $classname, $value, $tmp_obj->_fields, $tmp_obj->_tagRoot );

                        if( !is_array( $value ) ) {
                            $value = array( $value );
                        }
                    }
                }
            }

            $obj->$field = $value;
        }

        return $obj;
    }
}

/**
 * Load JSON library if PHP version is below 5.2
 */
if( !function_exists( 'json_encode' ) ) {
    require_once '../libs/JSON.php';
}
