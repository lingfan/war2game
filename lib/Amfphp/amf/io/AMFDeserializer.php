<?php
/**
 * AMFDeserializer takes the raw amf input stream and converts it PHP objects
 * representing the data.
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright (c) 2003 amfphp.org
 * @package flashservices
 * @subpackage io
 * @version $Id$
 */

include_once(AMFPHP_BASE . "amf/io/AMFBaseDeserializer.php");

class AMFDeserializer extends AMFBaseDeserializer {
	/**
	 * The raw data input
	 *
	 * @access private
	 * @var string
	 */
	var $raw_data;

	/**
	 * The current seek cursor of the stream
	 *
	 * @access private
	 * @var int
	 */
	var $current_byte;

	/**
	 * The length of the stream.  Since this class is not actually using a stream
	 * the entire content of the stream is passed in as the initial argument so the
	 * length can be determined.
	 *
	 * @access private
	 * @var int
	 */
	var $content_length;

	/**
	 * The number of headers in the packet.
	 *
	 * @access private
	 * @var int
	 */
	var $header_count;

	/**
	 * The content of the packet headers
	 *
	 * @access private
	 * @var string
	 */
	var $headers;

	/**
	 * The number of bodys in the packet.
	 *
	 * @access private
	 * @var int
	 */
	var $body_count;

	/**
	 * The content of the body elements
	 *
	 * @access private
	 * @var string
	 */
	var $body;

	/**
	 * The object to store the amf data.
	 *
	 * @access private
	 * @var object
	 */
	var $amfdata;

	/**
	 * The instance of the amfinput stream object
	 *
	 * @access private
	 * @var object
	 */
	var $inputStream;

	/**
	 * metaInfo
	 */
	var $meta;

	var $storedStrings;
	var $storedObjects;
	var $storedDefinitions;
	var $amf0storedObjects;

	var $native;

	/**
	 * Constructor method for the deserializer.  Constructing the deserializer converts the input stream
	 * content to a AMFObject.
	 *
	 * @param object $is The referenced input stream
	 */
	function AMFDeserializer($rd) {
		AMFBaseDeserializer::AMFBaseDeserializer($rd);
	}

	/**
	 * readObject reads the name/value properties of the amf message and converts them into
	 * their equivilent php representation
	 *
	 * @return array The php array with the object data
	 */
	function readObject() {
		$ret = array(); // init the array
		$this->amf0storedObjects[] = & $ret;
		$key = $this->readUTF(); // grab the key
		for ($type = $this->readByte(); $type != 9; $type = $this->readByte()) {
			$val = $this->readData($type); // grab the value
			$ret[$key] = $val; // save the name/value pair in the array
			$key = $this->readUTF(); // get the next name
		}
		Logger::debug(array(__METHOD__, $ret));
		return $ret; // return the array
	}

	/**
	 * readMixedObject reads the name/value properties of the amf message and converts
	 * numeric looking keys to numeric keys
	 *
	 * @return array The php array with the object data
	 */
	function readMixedObject() {
		$ret = array(); // init the array
		$this->amf0storedObjects[] = & $ret;
		$key = $this->readUTF(); // grab the key
		for ($type = $this->readByte(); $type != 9; $type = $this->readByte()) {
			$val = $this->readData($type); // grab the value
			if (is_numeric($key)) {
				$key = (float)$key;
			}
			$ret[$key] = $val; // save the name/value pair in the array
			$key = $this->readUTF(); // get the next name
		}
		return $ret; // return the array
	}

	/**
	 * readArray turns an all numeric keyed actionscript array into a php array.
	 *
	 * @return array The php array
	 */
	function readArray() {
		$ret = array(); // init the array object
		$this->amf0storedObjects[] = & $ret;
		$length = $this->readLong(); // get the length  of the array
		for ($i = 0; $i < $length; $i++) { // loop over all of the elements in the data
			$type = $this->readByte(); // grab the type for each element
			$ret[] = $this->readData($type); // grab each element
		}
		return $ret; // return the data

	}

	/**
	 * readMixedArray turns an array with numeric and string indexes into a php array
	 *
	 * @return array The php array with mixed indexes
	 */
	function readMixedArray() {
		//$length   = $this->readLong(); // get the length  property set by flash
		$this->current_byte += 4;
		return $this->readMixedObject(); // return the body of mixed array
	}

	/**
	 * readCustomClass reads the amf content associated with a class instance which was registered
	 * with Object.registerClass.  In order to preserve the class name an additional property is assigned
	 * to the object "_explicitType".  This property will be overwritten if it existed within the class already.
	 *
	 * @return object The php representation of the object
	 */
	function readCustomClass() {
		$typeIdentifier = str_replace('..', '', $this->readUTF());
		$obj = $this->mapClass($typeIdentifier);
		$isObject = true;
		if ($obj == NULL) {
			$obj = array();
			$isObject = false;
		}
		$this->amf0storedObjects[] = & $obj;
		$key = $this->readUTF(); // grab the key
		for ($type = $this->readByte(); $type != 9; $type = $this->readByte()) {
			$val = $this->readData($type); // grab the value
			if ($isObject) {
				$obj->$key = $val; // save the name/value pair in the array
			} else {
				$obj[$key] = $val; // save the name/value pair in the array
			}
			$key = $this->readUTF(); // get the next name
		}
		if (!$isObject) {
			$obj['_explicitType'] = $typeIdentifier;
		}
		return $obj; // return the array
	}

	/**
	 * readDate reads a date from the amf message and returns the time in ms.
	 * This method is still under development.
	 *
	 * @return long The date in ms.
	 */
	function readDate() {
		$ms = $this->readDouble(); // date in milliseconds from 01/01/1970
		$int = $this->readInt(); // nasty way to get timezone
		if ($int > 720) {
			$int = -(65536 - $int);
		}
		$int *= -60;
		//$int *= 1000;
		//$min = $int % 60;
		//$timezone = "GMT " . - $hr . ":" . abs($min);
		// end nastiness

		//We store the last timezone found in date fields in the request
		//FOr most purposes, it's expected that the timezones
		//don't change from one date object to the other (they change per client though)
		DateWrapper::setTimezone($int);
		return $ms;
	}

	/**
	 * readReference replaces the old readFlushedSO. It treats where there
	 * are references to other objects. Currently it does not resolve the
	 * object as this would involve a serious amount of overhead, unless
	 * you have a genius idea
	 *
	 * @return String
	 */
	function readReference() {
		$reference = $this->readInt();
		return $this->amf0storedObjects[$reference];
	}

	function readAny() {
		if ($this->native)
			return amfphp_decode($this->raw_data, $this->decodeFlags, $this->current_byte, array(& $this, "decodeCallback"));
		else {
			$type = $this->readByte(); // grab the type of the element
			return $this->readData($type); // turn the element into real data
		}
	}

	/**
	 * readData is the main switch for mapping a type code to an actual
	 * implementation for deciphering it.
	 *
	 * @param mixed $type The $type integer
	 * @return mixed The php version of the data in the message block
	 */
	function readData($type) {
		switch ($type) {
			case 0: // number
				$data = $this->readDouble();
				break;
			case 1: // boolean
				$data = $this->readByte() == 1;
				break;
			case 2: // string
				$data = $this->readUTF();
				break;
			case 3: // object Object
				$data = $this->readObject();
				break;
			case 5: // null
				$data = null;
				break;
			case 6: // undefined
				$data = null;
				break;
			case 7: // Circular references are returned here
				$data = $this->readReference();
				break;
			case 8: // mixed array with numeric and string keys
				$data = $this->readMixedArray();
				break;
			case 10: // array
				$data = $this->readArray();
				break;
			case 11: // date
				$data = $this->readDate();
				break;
			case 12: // string, strlen(string) > 2^16
				$data = $this->readLongUTF();
				break;
			case 13: // mainly internal AS objects
				$data = null;
				break;
			case 15: // XML
				$data = $this->readLongUTF();
				break;
			case 16: // Custom Class
				$data = $this->readCustomClass();
				break;
			case 17: //AMF3-specific
				$GLOBALS['amfphp']['encoding'] = "amf3";
				$data = $this->readAmf3Data();
				break;
			default: // unknown case
				trigger_error("Found unhandled type with code: $type");
				exit();
				break;
		}
		return $data;
	}

	/********************************************************************************
	 *                       This is the AMF3 specific stuff
	 ********************************************************************************/
	function readAmf3Data() {
		$type = $this->readByte();
		switch ($type) {
			case 0x00 :
				return null; //undefined
			case 0x01 :
				return null; //null
			case 0x02 :
				return false; //boolean false
			case 0x03 :
				return true; //boolean true
			case 0x04 :
				return $this->readAmf3Int();
			case 0x05 :
				return $this->readDouble();
			case 0x06 :
				return $this->readAmf3String();
			case 0x07 :
				return $this->readAmf3XmlString();
			case 0x08 :
				return $this->readAmf3Date();
			case 0x09 :
				return $this->readAmf3Array();
			case 0x0A :
				return $this->readAmf3Object();
			case 0x0B :
				return $this->readAmf3XmlString();
			case 0x0C :
				return $this->readAmf3ByteArray();
			default:
				trigger_error("undefined Amf3 type encountered: " . $type, E_USER_ERROR);
		}
	}

	/// <summary>
	/// Handle decoding of the variable-length representation
	/// which gives seven bits of value per serialized byte by using the high-order bit
	/// of each byte as a continuation flag.
	/// </summary>
	/// <returns></returns>
	function readAmf3Int() {
		$int = $this->readByte();
		if ($int < 128)
			return $int;
		else {
			$int = ($int & 0x7f) << 7;
			$tmp = $this->readByte();
			if ($tmp < 128) {
				return $int | $tmp;
			} else {
				$int = ($int | ($tmp & 0x7f)) << 7;
				$tmp = $this->readByte();
				if ($tmp < 128) {
					return $int | $tmp;
				} else {
					$int = ($int | ($tmp & 0x7f)) << 8;
					$tmp = $this->readByte();
					$int |= $tmp;

					// Check if the integer should be negative
					if (($int & 0x10000000) != 0) {
						// and extend the sign bit
						$int |= 0xe0000000;
					}
					return $int;
				}
			}
		}
	}

	function readAmf3Date() {
		$dateref = $this->readAmf3Int();
		if (($dateref & 0x01) == 0) {
			$dateref = $dateref >> 1;
			if ($dateref >= count($this->storedObjects)) {
				trigger_error('Undefined date reference: ' . $dateref, E_USER_ERROR);
				return false;
			}
			return $this->storedObjects[$dateref];
		}
		//$timeOffset = ($dateref >> 1) * 6000 * -1;
		$ms = $this->readDouble();

		//$date = $ms-$timeOffset;
		$date = $ms;

		$this->storedObjects[] = & $date;
		return $date;
	}

	/**
	 * readString
	 *
	 * @return string
	 */
	function readAmf3String() {

		$strref = $this->readAmf3Int();

		if (($strref & 0x01) == 0) {
			$strref = $strref >> 1;
			if ($strref >= count($this->storedStrings)) {
				trigger_error('Undefined string reference: ' . $strref, E_USER_ERROR);
				return false;
			}
			return $this->storedStrings[$strref];
		} else {
			$strlen = $strref >> 1;
			$str = "";
			if ($strlen > 0) {
				$str = $this->readBuffer($strlen);
				$this->storedStrings[] = $str;
			}
			return $str;
		}

	}

	function readAmf3XmlString() {
		$handle = $this->readAmf3Int();
		$inline = (($handle & 1) != 0);
		$handle = $handle >> 1;
		if ($inline) {
			$xml = $this->readBuffer($handle);
			$this->storedStrings[] = $xml;
		} else {
			$xml = $this->storedObjects[$handle];
		}
		return $xml;
	}

	function readAmf3ByteArray() {
		$handle = $this->readAmf3Int();
		$inline = (($handle & 1) != 0);
		$handle = $handle >> 1;
		if ($inline) {
			$ba = new ByteArray($this->readBuffer($handle));
			$this->storedObjects[] = $ba;
		} else {
			$ba = $this->storedObjects[$handle];
		}
		return $ba;
	}

	function readAmf3Array() {
		$handle = $this->readAmf3Int();
		$inline = (($handle & 1) != 0);
		$handle = $handle >> 1;
		if ($inline) {
			$hashtable = array();
			$this->storedObjects[] = & $hashtable;
			$key = $this->readAmf3String();
			while ($key != "") {
				$value = $this->readAmf3Data();
				$hashtable[$key] = $value;
				$key = $this->readAmf3String();
			}

			for ($i = 0; $i < $handle; $i++) {
				//Grab the type for each element.
				$value = $this->readAmf3Data();
				$hashtable[$i] = $value;
			}
			return $hashtable;
		} else {
			return $this->storedObjects[$handle];
		}
	}

	function readAmf3Object() {
		$handle = $this->readAmf3Int();
		$inline = (($handle & 1) != 0);
		$handle = $handle >> 1;

		if ($inline) {
			//an inline object
			$inlineClassDef = (($handle & 1) != 0);
			$handle = $handle >> 1;
			if ($inlineClassDef) {
				//inline class-def
				$typeIdentifier = $this->readAmf3String();
				$typedObject = !is_null($typeIdentifier) && $typeIdentifier != "";
				//flags that identify the way the object is serialized/deserialized
				$externalizable = (($handle & 1) != 0);
				$handle = $handle >> 1;
				$dynamic = (($handle & 1) != 0);
				$handle = $handle >> 1;
				$classMemberCount = $handle;

				$classMemberDefinitions = array();
				for ($i = 0; $i < $classMemberCount; $i++) {
					$classMemberDefinitions[] = $this->readAmf3String();
				}
				//string mappedTypeName = typeIdentifier;
				//if( applicationContext != null )
				//	mappedTypeName = applicationContext.GetMappedTypeName(typeIdentifier);

				$classDefinition = array("type" => $typeIdentifier, "members" => $classMemberDefinitions,
					"externalizable" => $externalizable, "dynamic" => $dynamic);
				$this->storedDefinitions[] = $classDefinition;
			} else {
				//a reference to a previously passed class-def
				$classDefinition = $this->storedDefinitions[$handle];
			}
		} else {
			//an object reference
			return $this->storedObjects[$handle];
		}


		$type = $classDefinition['type'];
		$obj = $this->mapClass($type);

		$isObject = true;
		if ($obj == NULL) {
			$obj = array();
			$isObject = false;
		}

		//Add to references as circular references may search for this object
		$this->storedObjects[] = & $obj;

		if ($classDefinition['externalizable']) {
			if ($type == 'flex.messaging.io.ArrayCollection') {
				$obj = $this->readAmf3Data();
			} else if ($type == 'flex.messaging.io.ObjectProxy') {
				$obj = $this->readAmf3Data();
			} else {
				trigger_error("Unable to read externalizable data type " . $type, E_USER_ERROR);
			}
		} else {
			$members = $classDefinition['members'];
			$memberCount = count($members);
			for ($i = 0; $i < $memberCount; $i++) {
				$val = $this->readAmf3Data();
				$key = $members[$i];
				if ($isObject) {
					$obj->$key = $val;
				} else {
					$obj[$key] = $val;
				}
			}

			if ($classDefinition['dynamic'] /* && obj is ASObject*/) {
				$key = $this->readAmf3String();
				while ($key != "") {
					$value = $this->readAmf3Data();
					if ($isObject) {
						$obj->$key = $value;
					} else {
						$obj[$key] = $value;
					}
					$key = $this->readAmf3String();
				}
			}

			if ($type != '' && !$isObject) {
				$obj['_explicitType'] = $type;
			}
		}

		if ($isObject && method_exists($obj, 'init')) {
			$obj->init();
		}

		return $obj;
	}

	/**
	 * Taken from SabreAMF
	 */
	function readBuffer($len) {
		$data = substr($this->raw_data, $this->current_byte, $len);
		$this->current_byte += $len;
		return $data;
	}
}

?>