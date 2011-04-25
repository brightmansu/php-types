<?php
namespace Types;


/**
 * @author bob
 * @version 1.0
 * @created 25-Apr-2011 4:25:53 AM
 */
class StringWithEncoding extends String
{

	/**
	 * Default string encoding. Will be used if no encoding is specified. Value can
	 * changed at run-time with the static method {@link setDefaultEncoding()}. Use
	 * null for auto-detection of encoding.
	 * @var string
	 */
	private static $_defaultEncoding = null;
	/**
	 * String's encoding.
	 * @var string uppercase
	 */
	private $_encoding = null;
	/**
	 * Whether the iconv extension is installed and loaded.
	 * @access private
	 * @var bool
	 */
	private static $_extIconv = null;
	/**
	 * Whether the mbstring extension is installed and loaded.
	 * @access private
	 * @var bool
	 */
	private static $_extMbstring = null;
	/**
	 * Whether the utf8 package is installed and loaded.
	 * @access private
	 * @var bool
	 */
	private static $_extUtf8 = null;
	const ALNUM = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
	const ALPHA = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	const NUMERIC = '0123456789';
	const SPACE = ' ';

	function __construct($data = null, $encoding = null) {
		if ($encoding !== null) {
				$this->_encoding = strtoupper(str_replace(' ', '-', (string)$encoding));
		} else if (self::$_defaultEncoding !== null) {
				$this->_encoding = self::$_defaultEncoding;
		}
		parent::__construct($data);
	}

	function __destruct()
	{

	}

	public function __get($key)
	{
			$key = strtolower($key);
			if ($key === 'encoding') {
					return $this->getEncoding();
			}
			return parent::__get($key);
	}


	/**
	 * Capitalizes a string.
	 * Changes the first letter to uppercase.
	 * Example:
	 * <code>
	 * <?php
	 * $string = new String('aBc');
	 * echo $string->capitalize(); // prints: ABc
	 * ?>
	 * </code>
	 * @return String
	 */
	public function capitalize()
	{
			if (function_exists('mb_ucfirst')) {
					$string = mb_ucfirst($this->data, $this->getEncoding());
			} else if (function_exists('mb_substr')) {
					$encoding = $this->getEncoding();
					$string = mb_strtoupper(mb_substr($this->data, 0, 1, $encoding), $encoding) .
										mb_substr($this->data, 1, null, $encoding);
			} else {
					$string = ucfirst($this->data);
			}
			return new self($string);
	}



	/**
	 * Returns String's encoding, or false in failure.
	 * @return string|bool
	 */
	public function getEncoding()
	{
			if ($this->_encoding === null) {
					if (function_exists('mb_detect_encoding')) {
							$this->_encoding = mb_detect_encoding($this->data);
					} else if (function_exists('utf8_compliant') && utf8_compliant($this->data)) {
							$this->_encoding = 'UTF-8';
					} else {
							$this->_encoding = false;
					}
			}
			return $this->_encoding;
	}

	/**
	 * Returns string's length.
	 * Counts the number of characters in the string.
	 * Example:
	 * <code>
	 * <?php
	 * $string = new String('123456');
	 * echo $string->getLength(); // prints: 6
	 * ?>
	 * </code>
	 * @return int
	 */
	public function getLength()
	{
			if ($this->_length === null) {
					if (function_exists('mb_strlen')) {
							$this->_length = (int)mb_strlen($this->data, $this->getEncoding());
					} else if ($this->getEncoding() === 'UTF-8' && function_exists('utf8_strlen')) {
							$this->_length = (int)utf8_strlen($this->data);
					} else if (function_exists('iconv_strlen')) {
							$this->_length = (int)iconv_strlen($this->data, $this->getEncoding());
					} else {
							$this->_length = (int)strlen($this->data);
					}
			}
			return $this->_length;
	}

	/**
	 * Returns the index of the first occurance of $substr in the string.
	 * In case $substr is not a substring of the string, returns false.
	 * @param String $substr substring
	 * @param int $offset
	 * @return int|bool
	 */
	public function indexOf($substr, $offset = 0)
	{
			if (function_exists('mb_strpos')) {
					$pos = mb_strpos($this->data, (string)$substr, (int)$offset, $this->getEncoding());
			} else if ($this->getEncoding() === 'UTF-8' && function_exists('utf8_strpos')) {
					$pos = utf8_strpos($this->data, (string)$substr, ($offset === 0 ? null : $offset));
			} else if (function_exists('iconv_strpos')) {
					$pos = iconv_strpos($this->data, (string)$substr, (int)$offset, $this->getEncoding());
			} else {
					$pos = strpos($this->data, (string)$substr, (int)$offset);
			}
			return $pos;
	}

	/**
	 * Returns the index of the last occurance of $substr in the string.
	 * In case $substr is not a substring of the string, returns false.
	 * @param String $substr substring
	 * @param int $offset
	 * @return int|bool
	 */
	public function lastIndexOf($substr, $offset = 0)
	{
			if (function_exists('mb_strrpos')) {
					$pos = mb_strrpos($this->data, (string)$substr, (int)$offset, $this->getEncoding());
			} else if ($this->getEncoding() === 'UTF-8' && function_exists('utf8_strrpos')) {
					$pos = utf8_strrpos($this->data, (string)$substr, ($offset === 0 ? null : $offset));
			} else if (function_exists('iconv_strrpos')) {
					$pos = iconv_strrpos($this->data, (string)$substr, (int)$offset, $this->getEncoding());
			} else {
					$pos = strrpos($this->data, (string)$substr, (int)$offset);
			}
			return $pos;
	}

	public function pad($length, $padding = self::SPACE)
	{
			$func = (($this->getEncoding() === 'UTF-8' && function_exists('utf8_str_pad')) ? 'utf8_str_pad' : 'str_pad');
			return new self($func($this->data, (int)$length, (string)$padding, STR_PAD_BOTH));
	}

	public function padEnd($length, $padding = self::SPACE)
	{
			$func = (($this->getEncoding() === 'UTF-8' && function_exists('utf8_str_pad')) ? 'utf8_str_pad' : 'str_pad');
			return new self($func($this->data, (int)$length, (string)$padding, STR_PAD_RIGHT));
	}

	public function padStart($length, $padding = self::SPACE)
	{
			$func = (($this->getEncoding() === 'UTF-8' && function_exists('utf8_str_pad')) ? 'utf8_str_pad' : 'str_pad');
			return new self($func($this->data, (int)$length, (string)$padding, STR_PAD_LEFT));
	}

	/**
	 * Returns part of the string.
	 * @param int $start
	 * @param int $length
	 * @return String
	 */
	public function substring($start, $length = null)
	{
			if (function_exists('mb_substr')) {
					$string = mb_substr($this->data, $start, $length, $this->getEncoding());
			} else if ($this->getEncoding() === 'UTF-8' && function_exists('utf8_substr')) {
					$string = utf8_substr($this->data, $start, $length);
			} else if (function_exists('iconv_substr')) {
					$string = iconv_substr($this->data, $start, $length, $this->getEncoding());
			} else {
					$string = substr($this->data, $start, $length);
			}
			return new self($string);
	}

	/**
	 * Converts the string to array.
	 * Each element in the array contains one character.
	 * @return array
	 */
	public function toArray()
	{
		if ($this->getEncoding() === 'UTF-8' && function_exists('utf8_str_split')) {
				$array = utf8_str_split($this->data, 1);
		} else
			$array = str_split($this->data, 1);
		
		return wrap($array);
	}

	/**
	 * Converts a string to lower case.
	 * Example:
	 * <code>
	 * <?php
	 * $string = new String('aBc');
	 * echo $string->toLowerCase(); // prints: abc
	 * ?>
	 * </code>
	 * @return String
	 */
	public function toLowerCase()
	{
			if (function_exists('mb_strtolower')) {
					$string = mb_strtolower($this->data, $this->getEncoding());
			} else if ($this->getEncoding() === 'UTF-8' && function_exists('utf8_strtolower')) {
					$string = utf8_strtolower($this->data);
			} else {
					$string = strtolower($this->data);
			}
			return new self($string);
	}

	/**
	 * Converts a string to upper case.
	 * Example:
	 * <code>
	 * <?php
	 * $string = new String('aBc');
	 * echo $string->toUpperCase(); // prints: ABC
	 * ?>
	 * </code>
	 * @return String
	 */
	public function toUpperCase()
	{
			if (function_exists('mb_strtoupper')) {
					$string = mb_strtoupper($this->data, $this->getEncoding());
			} else if ($this->getEncoding() === 'UTF-8' && function_exists('utf8_strtoupper')) {
					$string = utf8_strtoupper($this->data);
			} else {
					$string = strtoupper($this->data);
			}
			return new self($string);
	}

	/**
	 * Removes characters from both parts of the string.
	 * If $charlist is not provided, the default is to remove spaces.
	 * @param string $charlist characters to remove (default space characters)
	 * @return String
	 */
	public function trim($charlist = null)
	{
			if ($charlist !== null && $this->getEncoding() === 'UTF-8' && function_exists('utf8_trim')) {
					$string = utf8_trim($this->data, $charlist);
			} else {
					$string = trim($this->data, $charlist);
			}
			return new self($string);
	}

	/**
	 * Removes characters from the right part of the string.
	 * If $charlist is not provided, the default is to remove spaces.
	 * @param string $charlist characters to remove (default space characters)
	 * @return String
	 */
	public function trimEnd($charlist = null)
	{
			if ($charlist !== null && $this->getEncoding() === 'UTF-8' && function_exists('utf8_rtrim')) {
					$string = utf8_rtrim($this->data, $charlist);
			} else {
					$string = rtrim($this->data, $charlist);
			}
			return new self($string);
	}

	/**
	 * Removes characters from the left part of the string.
	 * If $charlist is not provided, the default is to remove spaces.
	 * @param string $charlist characters to remove (default space characters)
	 * @return String
	 */
	public function trimStart($charlist = null)
	{
			if ($charlist !== null && $this->getEncoding() === 'UTF-8' && function_exists('utf8_ltrim')) {
					$string = utf8_ltrim($this->data, $charlist);
			} else {
					$string = ltrim($this->data, $charlist);
			}
			return new self($string);
	}

	/**
	 * Uncapitalizes a string.
	 * Changes the first letter to lowercase.
	 * Example:
	 * <code>
	 * <?php
	 * $string = new String('ABCdE');
	 * echo $string->uncapitalize(); // prints: aBCdE
	 * ?>
	 * </code>
	 * @return String
	 */
	public function uncapitalize()
	{
			if (function_exists('mb_lcfirst')) {
					$string = mb_lcfirst($this->data, $this->getEncoding());
			} else if (function_exists('mb_substr')) {
					$encoding = $this->getEncoding();
					$string = mb_strtolower(mb_substr($this->data, 0, 1, $encoding), $encoding) .
										mb_substr($this->data, 1, null, $encoding);
			} else if (function_exists('lcfirst')) {
					$string = lcfirst($this->data);
			} else {
					$string = strtolower(substr($this->data, 0, 1)) .
										substr($this->data, 1);
			}
			return new self($string);
	}

	/**
	 * Returns an array with the string extensions that the class uses.
	 * Possible values: standard, mbstring, iconv, utf8.
	 * @return array
	 */
	public static function getLoadedExtensions()
	{
			$ext = array('standard');
			if (self::_mbstringLoaded()) {
					$ext[] = 'mbstring';
			}
			if (self::_iconvLoaded()) {
					$ext[] = 'iconv';
			}
			if (self::_utf8Loaded()) {
					$ext[] = 'utf8';
			}
			return $ext;
	}

	/**
	 * Sets default encoding.
	 * Use null for auto-detection.
	 * @param string $encoding encoding (default null)
	 */
	public static function setDefaultEncoding($encoding = null)
	{
			if ($encoding === null) {
					self::$_defaultEncoding = null;
			} else {
					self::$_defaultEncoding = strtoupper(str_replace(' ', '-', (string)$encoding));
			}
	}

	/**
	 * Returns default encoding.
	 * @return string
	 */
	public static function getDefaultEncoding()
	{
			return self::$_defaultEncoding;
	}

	/**
	 * Checks if the mbstring extension is installed and loaded.
	 * @access private
	 * @return bool true if mbstring is available
	 */
	private static function _mbstringLoaded()
	{
			if (self::$_extMbstring === null) {
					self::$_extMbstring = (bool)extension_loaded('mbstring');
			}
			return self::$_extMbstring;
	}

	/**
	 * Checks if the iconv extension is installed and loaded.
	 * @access private
	 * @return bool true if iconv is available
	 */
	private static function _iconvLoaded()
	{
			if (self::$_extIconv === null) {
					self::$_extIconv = (bool)extension_loaded('iconv');
			}
			return self::$_extIconv;
	}

	/**
	 * Checks if the utf8 package is installed and loaded.
	 * @access private
	 * @return bool true if utf8 package is available
	 */
	private static function _utf8Loaded()
	{
			if (self::$_extUtf8 === null) {
					self::$_extUtf8 = (bool)(defined('UTF8_CORE') && UTF8_CORE === true);
			}
			return self::$_extUtf8;
	}
}
?>