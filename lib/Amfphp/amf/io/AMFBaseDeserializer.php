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

/**
 * Required classes
 */
require_once(AMFPHP_BASE . "shared/util/MessageBody.php");
require_once(AMFPHP_BASE . "shared/util/MessageHeader.php");
require_once(AMFPHP_BASE . "amf/util/DateWrapper.php");

class AMFBaseDeserializer {
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
	function AMFBaseDeserializer($rd) {
		$this->isBigEndian = AMFPHP_BIG_ENDIAN;
		$this->current_byte = 0;
		$this->raw_data = $rd; // store the stream in this object
		$this->content_length = strlen($this->raw_data); // grab the total length of this stream
		$this->charsetHandler = new CharsetHandler('flashtophp');
		$this->storedStrings = array();
		$this->storedObjects = array();
		$this->storedDefinitions = array();
		$this->native = $GLOBALS['amfphp']['native'] && function_exists('amf_decode');
		$this->decodeFlags = (AMFPHP_BIG_ENDIAN * 2) | 4;
	}

	/**
	 * deserialize invokes this class to transform the raw data into valid object
	 *
	 * @param object $amfdata The object to put the deserialized data in
	 */
	function deserialize(&$amfdata) {
		$time = microtime_float();
		$this->amfdata = & $amfdata;
		$this->readHeader(); // read the binary header
		$this->readBody(); // read the binary body
		if ($this->decodeFlags & 1 == 1) {
			//AMF3 mode
			$GLOBALS['amfphp']['encoding'] = "amf3";
		}
		global $amfphp;
		$amfphp['decodeTime'] = microtime_float() - $time;
	}

	/**
	 * returns the built AMFObject from the deserialization operation
	 *
	 * @return object The deserialized AMFObject
	 */
	function getAMFObject() {
		return $this->amfdata;
	}

	/**
	 * Decode callback is triggered when an object is encountered on decode
	 */
	function decodeCallback($event, $arg) {
		if ($event == 1) //Object
		{
			$type = $arg;
			return $this->mapClass($type);
		} else if ($event == 2) //Object post decode
		{
			$obj = $arg;
			if (method_exists($obj, 'init')) {
				$obj->init();
			}
			return $obj;
		} else if ($event == 3) //XML post-decode
		{
			return $arg;
		} else if ($event == 4) //Serializable post-decode
		{
			if ($type == 'flex.messaging.io.ArrayCollection' || $type == 'flex.messaging.io.ObjectProxy') {
				return;
			} else {
				trigger_error("Unable to read externalizable data type " . $type, E_USER_ERROR);
				return "error";
			}
		} else if ($event == 5) //ByteArray post decode
		{
			return new ByteArray($arg);
		}
	}

	/**
	 * readHeader converts that header section of the amf message into php obects.
	 * Header information typically contains meta data about the message.
	 */
	function readHeader() {

		$topByte = $this->readByte(); // ignore the first two bytes --  version or something
		$secondByte = $this->readByte(); //0 for Flash,
		//1 for FlashComm
		//Disable debug events for FlashComm
		$GLOBALS['amfphp']['isFlashComm'] = $secondByte == 1;

		//If firstByte != 0, then the AMF data is corrupted, for example the transmission
		//
		if (!($topByte == 0 || $topByte == 3)) {
			trigger_error("Malformed AMF message, connection may have dropped");
			exit();
		}
		$this->header_count = $this->readInt(); //  find the total number of header elements
		while ($this->header_count--) { // loop over all of the header elements
			$name = $this->readUTF();
			$required = $this->readByte() == 1; // find the must understand flag
			//$length   = $this->readLong(); // grab the length of  the header element
			$this->current_byte += 4; // grab the length of the header element
			if ($this->native) {
				$content = amf_decode($this->raw_data, $this->decodeFlags, $this->current_byte, array(& $this, "decodeCallback"));
			} else {
				$type = $this->readByte(); // grab the type of the element

				$content = $this->readData($type); // turn the element into real data

			}


			$this->amfdata->addHeader(new MessageHeader($name, $required, $content)); // save the name/value into the headers array
		}

	}

	/**
	 * readBody converts the payload of the message into php objects.
	 */
	function readBody() {
		$this->body_count = $this->readInt(); // find the total number  of body elements
		while ($this->body_count--) { // loop over all of the body elements

			$this->amf0storedObjects = array();
			$this->storedStrings = array();
			$this->storedObjects = array();
			$this->storedDefinitions = array();

			$target = $this->readUTF();
			$response = $this->readUTF(); //    the response that the client understands

			//$length = $this->readLong(); // grab the length of    the body element
			$this->current_byte += 4;

			if ($this->native)
				$data = amf_decode($this->raw_data, $this->decodeFlags, $this->current_byte, array(& $this, "decodeCallback"));
			else {
				$type = $this->readByte(); // grab the type of the element
				$data = $this->readData($type); // turn the element into real data
			}

			$this->amfdata->addBody(new MessageBody($target, $response, $data)); // add the body element to the body object

		}
	}

	/********************************************************************************
	 *                       This used to be in AmfInputStream
	 ********************************************************************************
	 *
	 * /**
	 * readByte grabs the next byte from the data stream and returns it.
	 *
	 * @return int The next byte converted into an integer
	 */
	function readByte() {
		return ord($this->raw_data[$this->current_byte++]); // return the next byte
	}

	/**
	 * readInt grabs the next 2 bytes and returns the next two bytes, shifted and combined
	 * to produce the resulting integer
	 *
	 * @return int The resulting integer from the next 2 bytes
	 */
	function readInt() {
		return ((ord($this->raw_data[$this->current_byte++]) << 8) |
			ord($this->raw_data[$this->current_byte++])); // read the next 2 bytes, shift and add
	}

	/**
	 * readUTF first grabs the next 2 bytes which represent the string length.
	 * Then it grabs the next (len) bytes of the resulting string.
	 *
	 * @return string The utf8 decoded string
	 */
	function readUTF() {
		$length = $this->readInt(); // get the length of the string (1st 2 bytes)
		//BUg fix:: if string is empty skip ahead
		if ($length == 0) {
			return "";
		} else {
			$val = substr($this->raw_data, $this->current_byte, $length); // grab the string
			$this->current_byte += $length; // move the seek head to the end of the string
			return $this->charsetHandler->transliterate($val); // return the string
		}
	}

	/**
	 * readLong grabs the next 4 bytes shifts and combines them to produce an integer
	 *
	 * @return int The resulting integer from the next 4 bytes
	 */
	function readLong() {
		return ((ord($this->raw_data[$this->current_byte++]) << 24) |
			(ord($this->raw_data[$this->current_byte++]) << 16) |
			(ord($this->raw_data[$this->current_byte++]) << 8) |
			ord($this->raw_data[$this->current_byte++])); // read the next 4 bytes, shift and add
	}

	/**
	 * readDouble reads the floating point value from the bytes stream and properly orders
	 * the bytes depending on the system architecture.
	 *
	 * @return float The floating point value of the next 8 bytes
	 */
	function readDouble() {
		$bytes = substr($this->raw_data, $this->current_byte, 8);
		$this->current_byte += 8;
		if ($this->isBigEndian) {
			$bytes = strrev($bytes);
		}
		$zz = unpack("dflt", $bytes); // unpack the bytes
		return $zz['flt']; // return the number from the associative array
	}

	/**
	 * readLongUTF first grabs the next 4 bytes which represent the string length.
	 * Then it grabs the next (len) bytes of the resulting in the string
	 *
	 * @return string The utf8 decoded string
	 */
	function readLongUTF() {
		$length = $this->readLong(); // get the length of the string (1st 4 bytes)
		$val = substr($this->raw_data, $this->current_byte, $length); // grab the string
		$this->current_byte += $length; // move the seek head to the end of the string
		return $this->charsetHandler->transliterate($val); // return the string
	}

	function mapClass($typeIdentifier) {
		//Check out if class exists
		if ($typeIdentifier == "") {
			return NULL;
		}
		$clazz = NULL;
		$mappedClass = str_replace('.', '/', $typeIdentifier);

		if ($typeIdentifier == "flex.messaging.messages.CommandMessage") {
			return new CommandMessage();
		}
		if ($typeIdentifier == "flex.messaging.messages.RemotingMessage") {
			return new RemotingMessage();
		}

		if (isset($GLOBALS['amfphp']['incomingClassMappings'][$typeIdentifier])) {
			$mappedClass = str_replace('.', '/', $GLOBALS['amfphp']['incomingClassMappings'][$typeIdentifier]);
		}

		$include = FALSE;
		if (file_exists($GLOBALS['amfphp']['customMappingsPath'] . $mappedClass . '.php')) {
			$include = $GLOBALS['amfphp']['customMappingsPath'] . $mappedClass . '.php';
		} elseif (file_exists($GLOBALS['amfphp']['customMappingsPath'] . $mappedClass . '.class.php')) {
			$include = $GLOBALS['amfphp']['customMappingsPath'] . $mappedClass . '.class.php';
		}

		if ($include !== FALSE) {
			include_once($include);
			$lastPlace = strrpos('/' . $mappedClass, '/');
			$classname = substr($mappedClass, $lastPlace);
			if (class_exists($classname)) {
				$clazz = new $classname;
			}
		}

		return $clazz; // return the object
	}
}

?>