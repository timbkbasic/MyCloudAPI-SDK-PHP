<?php

namespace MyCloud\Api\Core;

/**
 * Class MyCloudModel
 *
 * Generic Model class that all Model classes extend.
 * Stores all member data in a Hashmap that enables easy
 * JSON encoding/decoding.
 *
 * @package MyCloud\Api\Core
 */
class MyCloudModel
{
	/**
	 * The array into which all of our magic properties are stored.
	 */
    private $_propMap = array();

    /**
     * Default Constructor
     *
     * You can pass data as a json representation or array object.
     *
     * @param null $data
     * @throws \InvalidArgumentException
     */
    public function __construct( $data = null )
    {
        switch ( gettype($data) )
		{
            case "NULL":
                break;

            case "string":
                if ( $this->validate_json( $data ) ) {
					$this->fromJson($data);
				} else {
					
				}
                break;

            case "array":
                $this->fromArray($data);
                break;

            default:
        }
    }

    /**
     * Magic Get Method
     *
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        if ( $this->__isset($key) ) {
            return $this->_propMap[$key];
        }
        return null;
    }

    /**
     * Magic Set Method
     *
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        if ( ! is_array($value) && $value === null ) {
            $this->__unset($key);
        } else {
            $this->_propMap[$key] = $value;
        }
    }

    /**
     * Magic isSet Method
     *
     * @param $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset( $this->_propMap[$key] );
    }

    /**
     * Magic Unset Method
     *
     * @param $key
     */
    public function __unset($key)
    {
        unset( $this->_propMap[$key] );
    }

    /**
     * Converts Params to Array
     *
     * @param $param
     * @return array
     */
    private function _convertToArray($param)
    {
        $result = array();
        foreach ( $param as $k => $v ) {
            if ( $v instanceof MyCloudModel ) {
                $result[$k] = $v->toArray();
            } elseif ( sizeof($v) <= 0 && is_array($v) ) {
                $result[$k] = array();
            } elseif ( is_array($v) ) {
                $result[$k] = $this->_convertToArray($v);
            } else {
                $result[$k] = $v;
            }
        }

        // If the array is empty, which means an empty object,
        // we need to convert array to StdClass object to properly
        // represent JSON String
        if ( sizeof($result) <= 0 ) {
            $result = new MyCloudModel();
        }

        return $result;
    }

    /**
     * Fills object value from Array list
     *
     * @param $arr
     * @return $this
     */
    public function fromArray($arr)
    {
        if ( ! empty($arr) && is_array($arr) ) {
            // Iterate over each element in array
            foreach ( $arr as $k => $v ) {
                // If the value is an array, it means it will be an object after conversion
                if ( is_array($v) ) {
                    // Determine the class of the object
                    if (($clazz = ReflectionUtil::getPropertyClass(get_class($this), $k)) != null) {
                        // If the value is an associative array, it means, its an object. Recurse...
                        if ( empty($v) ) {
                            if ( ReflectionUtil::isPropertyClassArray(get_class($this), $k) ) {
                                // It means, it is an array of objects.
                                $this->assignValue($k, array());
                                continue;
                            }
                            $o = new $clazz();
                            //$arr = array();
                            $this->assignValue($k, $o);
                        } elseif ( $this->isAssocArray($v) ) {
                            /** @var self $o */
                            $o = new $clazz();
                            $o->fromArray($v);
                            $this->assignValue($k, $o);
                        } else {
                            // Else, value is an array of object/data
                            $arr = array();
                            // Iterate through each element in that array.
                            foreach ( $v as $nk => $nv ) {
                                if ( is_array($nv) ) {
                                    $o = new $clazz();
                                    $o->fromArray($nv);
                                    $arr[$nk] = $o;
                                } else {
                                    $arr[$nk] = $nv;
                                }
                            }
                            $this->assignValue($k, $arr);
                        }
                    } else {
                        $this->assignValue($k, $v);
                    }
                } else {
                    $this->assignValue($k, $v);
                }
            }
        }
        return $this;
    }

    private function assignValue( $key, $value )
    {
        $setter = 'set'. $this->convertToCamelCase($key);
        // If we find the setter, use that, otherwise use magic method.
        if ( method_exists($this, $setter) ) {
            $this->$setter($value);
        } else {
            $this->__set($key, $value);
        }
    }

    /**
     * Fills object value from Json string
     *
     * @param $json
     * @return $this
     */
    public function fromJson( $json )
    {
        return $this->fromArray( json_decode($json, true) );
    }

    /**
     * Returns array representation of object
     *
     * @return array
     */
    public function toArray()
    {
        return $this->_convertToArray( $this->_propMap );
    }

    /**
     * Returns object JSON representation
     *
     * @param int $options http://php.net/manual/en/json.constants.php
     * @return string
     */
    public function toJSON( $options = 0 )
    {
		// Use JSON_UNESCAPED_SLASHES option (requires PHP >= 5.4.0)
		return json_encode( $this->toArray(), ($options | 64) );
    }

    /**
     * Magic Method for toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJSON( 128 );
    }

    /**
     * Helper method for validating if string provided is a valid json.
     *
     * @param string $string String representation of Json object
     * @param bool $silent Flag to not throw \InvalidArgumentException
     * @return bool
     */
    public function validate_json( $string )
    {
        @json_decode( $string );
        if ( json_last_error() != JSON_ERROR_NONE ) {
            if ( $string !== '' && $string !== null ) {
				return false;
			}
        }
        return true;
    }

    /**
     * Determine if array is a (100%) associate array.
	 *
     * @param array $arr
     * @return true if $arr is an associative array
     */
    public function isAssocArray(array $arr)
    {
        foreach ( $arr as $k => $v ) {
            if ( is_int($k) ) {
                return false;
            }
        }
        return true;
    }

    /**
     * Converts the input key into a valid Setter Method Name
     *
     * @param $key
     * @return mixed
     */
    private function convertToCamelCase($key)
    {
        return str_replace(' ', '', ucwords(str_replace(array('_', '-'), ' ', $key)));
    }

    /**
     * Execute SDK Call to MyCloud services
     *
     * @param string      $url
     * @param string      $method
     * @param string      $payLoad
     * @param array $headers
     * @param ApiContext      $apiContext
     * @return string json response of the object
     */
    protected static function executeCall( $url, $method, $payLoad, $headers = array(), $apiContext = null )
    {
        //Initialize the context and rest call object if not provided explicitly
        $apiContext = $apiContext ? $apiContext : new ApiContext( null );
        $config = $apiContext->getConfig();
		$token = $apiContext->getToken();

		if ( ! empty($token) ) {
			$httpConfig = new MCHttpConfig( $url, $method, $config );
			$http = new MCHttpConnection( $apiContext, $httpConfig, $token );
	        $json = $http->execute( $url, $method, $payLoad, $headers );
		} else {
			$json = ""; // UNDONE
		}

		return $json;
    }

}
