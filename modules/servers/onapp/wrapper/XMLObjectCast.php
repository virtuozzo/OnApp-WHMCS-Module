<?PHP
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Serialize and Unserialize Object and XML for ONAPP Wrapper
 *
 * @category  OBJECT CAST
 * @package   ONAPP
 * @author    Andrew Yatskovets
 * @copyright 2010 / OnApp
 * @link      http://www.onapp.com/
 */

/**
 * Initialization of the Serialize class to serialize an Array to an XML
 */
require_once dirname( __FILE__ ) . "/../libs/Serializer.php";

/**
 * Initialization of the Unserialize class to unserialize XML to an Object
 */
require_once dirname( __FILE__ ) . "/../libs/Unserializer.php";

/**
 * This class changes the entity of one data type into another
 * This is done to take advantage of certain features of type hierarchies. For
 * instance, values from a more limited set, such as XML, can be stored in a
 * more compact format and later converted to a different format enabling
 * operations not previously possible, such as division with several decimal
 * places worth of accuracy. Type conversion allows programs to treat objects
 * of one type as one of their ancestor types to simplify interacting with them.
 *
 * @todo create XML parser code for PHP4
 */
class XMLObjectCast {

    /**
     * The list of options used to serialize the objects
     *
     * @access private
     * @var    array
     */
    var $_serialize_options = array(
        XML_SERIALIZER_OPTION_INDENT => "    ",
        XML_SERIALIZER_OPTION_LINEBREAKS => "\n",
        XML_SERIALIZER_OPTION_XML_DECL_ENABLED => true,
        XML_SERIALIZER_OPTION_XML_ENCODING => "UTF-8",
        XML_SERIALIZER_OPTION_DOCTYPE_ENABLED => false,
// this value will be used directly as content, instead of creating a new tag, may only be used in conjuction with attributesArray
//        XML_SERIALIZER_OPTION_CONTENT_KEY      => true,
        XML_SERIALIZER_OPTION_ATTRIBUTES_KEY => 'attributesArray'
    );

    /**
     * The list of options used to unserialize the objects
     *
     * @access private
     * @var    array
     */
    var $_unserialize_options = array( );

    /**
     * This method performs the process of converting
     *
     * This method converts the data structure or an object into a sequence of bits so
     * that it can be stored in a file or memory buffer, or transmitted across
     * a network connection link to be "resurrected" later in the same or
     * another computer environment. When the resulting series of bits is
     * reread according to the serialization format, it can be used to create
     * a semantically identical clone of the original object. For many complex
     * objects, such as those that make extensive use of references, this
     * process is not straightforward.
     *
     * The following example illustrates:
     * <code>
     *    var $serialize_options = array(
     *        XML_SERIALIZER_OPTION_INDENT           => "    ",
     *        XML_SERIALIZER_OPTION_LINEBREAKS       => "\n",
     *        XML_SERIALIZER_OPTION_XML_DECL_ENABLED => true,
     *        XML_SERIALIZER_OPTION_XML_ENCODING     => "UTF-8",
     *        XML_SERIALIZER_OPTION_DOCTYPE_ENABLED  => false,
     *    );
     *
     *    $cast = new XMLObjectCast($serialize_options);
     *
     *    $a = array(
     *        'a' => 'A',
     *        'b' => 'B'
     *    );
     *
     *    echo $cast->serialize('root', $a);
     * </code>
     *
     * The above example will output:
     *
     * <code>
     *    <?xml version="1.0" encoding="UTF-8"?>
     *    <root>
     *        <a>A</a>
     *        <b>B</b>
     *    </root>
     * </code>
     *
     * @param string $root XML root element name
     * @param array  $obj  XML data
     *
     * @return string serialized XML
     * @access public
     */
    function serialize( $root, $obj ) {
        $serializer = &new XML_Serializer( $this->_serialize_options );
        $serializer->setOption( "rootName", $root );
        $result = $serializer->serialize( $obj );

        if( $result === true ) {
            return $serializer->getSerializedData( );
        }
    }

    /**
     * This method performs the process of converting
     *
     * This method converts a sequence of bits into a data structure or object,
     * or transmitted across a network connection link to be "resurrected" later
     * in the same or another computer environment. When the resulting series
     * of bits is reread according to the serialization format, it can be used
     * to create a semantically identical clone of the original object. For
     * many complex objects, such as those that make extensive use of
     * references, this process is not straightforward.
     *
     * The following example illustrates:
     *
     * <code>
     *
     *    require_once 'php/ONAPP/XMLObjectCast.php';
     *
     *    class ClassA {
     *        var $_a;
     *        var $_b;
     *    }
     *
     *    $obj = new XMLObjectCast();
     *
     *    $tagMap = array(
     *        'a' => '_a',
     *        'b' => '_b'
     *    );
     *
     *    $xml = '<?xml version="1.0" encoding="UTF-8"?>'.
     *        "<root>".
     *        "    <a>A</a>".
     *        "    <b>B</b>".
     *        "</root>";
     *
     *    $r = $obj->unserialize('ClassA', $xml, $tagMap);
     *
     * </code>
     *
     * The above example will output:
     *
     * <code>
     *
     *    object(ClassA)#4 (2) {
     *        ["_a"]=>
     *            string(1) "A"
     *        ["_b"]=>
     *            string(1) "B"
     *    }
     *
     * </code>
     *
     * @param string $classname class name
     * @param string $xml       XML
     * @param array  $tagMap    XML tag in to Class fields mapping
     *
     * @return mixed unserialized XML in to class
     * @access public
     *
     * @todo check XML size before unserialization
     * @todo add test cases
     */
    function unserialize( $classname, $xml, $tagMap = null ) {
        $dom = new DomDocument;
        $dom->preserveWhiteSpace = FALSE;

        if( !@$dom->loadXML( $xml ) ) {
            return array( );
        }

        if( $dom->childNodes->length != 0 && $dom->childNodes->item( 0 )->childNodes->length != 0 ) {
            $node_name = $dom->childNodes->item( 0 )->childNodes->item( 0 )->nodeName;
            if( $node_name == 'error' ) {
                $obj = new $classname;
                $obj->error = array( );
                $params = $dom->getElementsByTagName( $node_name );
                foreach( $params as $param ) {
                    $obj->error[ ] = $param->nodeValue;
                }
                return $obj;
            }
        }

        $this->_unserialize_options = array(
            XML_UNSERIALIZER_OPTION_ATTRIBUTES_PARSE => false,
            XML_UNSERIALIZER_OPTION_ATTRIBUTES_ARRAYKEY => false,
            XML_UNSERIALIZER_OPTION_COMPLEXTYPE => 'object',
        );
        $unserializer = &new XML_Unserializer( $this->_unserialize_options );
        $unserializer->setOption(
            XML_UNSERIALIZER_OPTION_DEFAULT_CLASS,
            $classname
        );

        if( !is_null( $tagMap ) && is_array( $tagMap ) ) {
            $tm = array( );
            foreach( $tagMap as $key => $value )
                $tm[ $key ] = $value[ ONAPP_FIELD_MAP ];
            $unserializer->setOption( XML_UNSERIALIZER_OPTION_TAG_MAP, $tm );
        }
        ;

        $unserializer->unserialize( $xml, false );

        $unserializedData = $unserializer->getUnserializedData( );

        if ($unserializer->_root == "errors") {
            $error = $unserializer->_dataStack;
            if ( is_array($error) ) {
                $_error = array();
                foreach ($error as $err)
                    if (trim($err) != "")
                      $_error[] = $err;

                $error = $_error;
            }

            $unserializedData->error = $error;
        }

        return $unserializedData;
    }
    /**
     * This function unserializes XML to the list of objects
     *
     * The following example illustrates:
     *
     * <code>
     *
     *    require_once 'php/ONAPP/XMLObjectCast.php';
     *
     *    class ClassA {
     *        var $_a;
     *        var $_b;
     *    }
     *
     *    $obj = new XMLObjectCast();
     *
     *    $tagMap = array(
     *        'a' => '_a',
     *        'b' => '_b'
     *    );
     *
     *    $xml = '<?xml version="1.0" encoding="UTF-8"?>'.
     *        "<root>".
     *        "  <object>".
     *        "    <a>A1</a>".
     *        "    <b>B1</b>".
     *        "  </object>".
     *        "  <object>".
     *        "    <a>A2</a>".
     *        "    <b>B2</b>".
     *        "  </object>".
     *        "</root>";
     *
     *    $r = $obj->unserialize_list('ClassA', $xml, $tagMap);
     *
     * </code>
     *
     * The above example will output:
     *
     * <code>
     *
     *    array(2) {
     *      [0]=>
     *      object(ClassA)#8 (2) {
     *        ["_a"]=>
     *        string(2) "A1"
     *        ["_b"]=>
     *        string(2) "B1"
     *      }
     *      [1]=>
     *      object(ClassA)#11 (2) {
     *        ["_a"]=>
     *        string(2) "A2"
     *        ["_b"]=>
     *        string(2) "B2"
     *      }
     *    }
     *
     * </code>
     *
     * @param string $classname class name
     * @param array  $tagMap    XML tag in to Class fields mapping
     * @param string $xml       XML
     *
     * @return array list of Objects
     * @access public
     *
     * @todo add the first line the same as in $xml to $as_xml
     * @todo add test cases
     *
     * @subpackage php-xml
     */
/*
    function unserialize_list($classname, $xml, $tagMap=null) {

        $dom = new DomDocument;
        $dom->preserveWhiteSpace = FALSE;

        if (! @$dom->loadXML($xml))
            return array();

        if ($dom->childNodes->length != 0 &&
            $dom->childNodes->item(0)->nodeName != 'nil-classes'
            ) {

            $node_name = $dom->childNodes->item(0)->childNodes->item(0)->nodeName;

            $params = $dom->getElementsByTagName($node_name);

            $result = array();

            foreach ($params as $param) {
                $as_xml = simplexml_import_dom($param)->asXML();

                $result[] = $this->unserialize($classname, $as_xml, $tagMap);
            }

            return $result;
        } else {
            return array();
        }

    }
*/
}

?>
