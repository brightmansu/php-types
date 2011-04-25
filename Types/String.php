<?php

namespace Types;
//class String extends Iterable implements \IteratorAggregate, \ArrayAccess, \Countable {
class String extends PrimitiveTypeWrapper implements \IteratorAggregate, \ArrayAccess, \Countable {


	/**
	 * String's length.
	 * @var int
	 */
	protected $_length = null;

	/**
	 * Current position (Iterator).
	 * @var int
	 */
	private $_index = 0;
	
	public function __construct($data = null){
		if (is_array($data))
			$data = implode($data);

//		$this->setData('' . (method_exists($data, 'toString') ? $data->toString() : $data));
		$this->setData($data);
	}

	// Misc

	public function contains($value){
		return $this->indexOf($value) == -1 ? false : true;
	}

	public function split($separator = null){
		$data = $this->data;

		if ($separator === null) $data = array($this->data);
		else if ($separator === '') $data = $this->toArray();
		else $data = explode($separator, $this->data);

		return new ArrayObject($data);
	}

	public function clear(){
		return $this->setData('');
	}

	public function camelCase(){
		return static::from(str_replace('-', '', preg_replace_callback('/-\D/', function($matches){
			return strtoupper($matches[0]);
		}, $this->data)));
	}

	public function substitute($data){
		$keys = array();
		foreach ($data as $key => $value)
			$keys[] = '{' . $key . '}';

		$string = str_replace($keys, array_values($data), $this->data);
		return static::from(preg_replace('/\{([^{}]+)\}/', '', $string));
	}

	// IteratorAggregate
	public function getIterator(){
		return new \ArrayIterator($this->toArray());
	}

	// ArrayAccess
	public function offsetSet($key, $value){
		$this->data[$key] = $value;
	}

	public function offsetGet($key){
		return !empty($this->data[$key]) ? $this->data[$key] : null;
	}

	public function offsetExists($key){
		return !empty($this->data[$key]);
	}

	public function offsetUnset($key){
		$this->data[$key] = null;
	}

	// Countable
	public function count(){
		return $this->getLength();
	}

	/**
	 * Overload method. Provides length and encoding properties.
	 * Example:
	 * <code>
	 * <?php
	 * $string = new String('123456');
	 * echo $string->getLength(); // prints: 6
	 * echo $string->length; // prints: 6
	 * ?>
	 * </code>
	 * @param string $key
	 * @return mixed
	 * @throws BadMethodCallException
	 */
	public function __get($key)
	{
			$key = strtolower($key);
			if ($key === 'length') {
					return $this->getLength();
			}
			throw new \BadMethodCallException('Undefined property.');
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
	public function capitalize() {
			return new self(ucfirst($this->data));
	}

	/**
	 * Returns the character at index $index, counting from zero.
	 * If the index doesn't exists, returns null.
	 * @param int $index character index, counting from zero.
	 * @return String
	 */
	public function charAt($index) {
			return $this->substring($index, 1);
	}

	/**
	 * Compares this string to the provided string.
	 * Returns positive number if this string is greater than $string,
	 * negative number if this string is less than $string,
	 * and 0 in case the strings are equal.
	 * This method is case-sensitive. See also {@link compareToIgnoreCase()}
	 * @param string
	 * @param int $characters upper limit of characters to use in comparison (default null)
	 * @return int
	 */
	public function compareTo($string, $characters = null)
	{
			if ($characters === null) {
					return strcmp($this->data, (string)$string);
			}
			return strncmp($this->data, (string)$string, (int)$characters);
	}

	/**
	 * Similar to {@link compareTo()}, but case-insensitive.
	 * @param string $string
	 * @param int $characters upper limit of characters to use in comparison (default null)
	 * @return int
	 */
	public function compareToIgnoreCase($string, $characters = null)
	{
			if ($characters === null) {
					return strncasecmp($this->data, (string)$string, strlen($string));
			}
			return strncasecmp($this->data, (string)$string, (int)$characters);
	}

	/**
	 * Concats a string and returns the new one.
	 * Actually, it is the same as the dot operator.
	 * @param string $string
	 * @return String
	 */
	public function concat($string)
	{
			return new self($this->data.(string)$string);
	}

	/**
	 * Returns the current element.
	 * @return String
	 */
	public function current()
	{
			return $this->charAt($this->_index);
	}

	/**
	 * Checks if the string ends with a substring.
	 * @param string $substr substring
	 * @return bool true if the string ends with $substr.
	 */
	public function endsWith($substr)
	{
			$substr = new self($substr);
			return ($this->lastIndexOf($substr) === $this->length() - $substr->length());
	}

	/**
	 * Checks is this string is equal to the provided string.
	 * This method is case-sensitive. See also {@link equalsIgnoreCase()}
	 * @param string $string
	 * @return bool true if the strings are equal
	 */
	public function equals($string)
	{
			return ($this->compareTo($string) === 0);
	}

	/**
	 * Similar to {@link equals()}, but case-insensitive.
	 * @param string $string
	 * @return bool true if the strings are equal
	 */
	public function equalsIgnoreCase($string)
	{
			return ($this->compareToIgnoreCase($string) === 0);
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
			$this->_length = (int)strlen($this->data);
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
			return strpos($this->data, (string)$substr, (int)$offset);
	}

	public function insert($offset, $string)
	{
			return $this->splice($offset, 0, $string);
	}

	/**
	 * Checks if the string is empty or whitespace-only.
	 * @return bool true if the string is blank
	 */
	public function isBlank()
	{
			return ($this->trim()->_string === '');
	}

	/**
	 * Checks if the string is empty.
	 * @return bool true if the string is empty
	 */
	public function isEmpty()
	{
			return ($this->data === '');
	}

	/**
	 * Checks if the string is lower case.
	 * String is considered lower case if all the characters are lower case.
	 * @return bool true if the string is lower case
	 */
	public function isLowerCase()
	{
			return $this->equals($this->toLowerCase());
	}

	/**
	 * Checks if the string is not empty or whitespace-only.
	 * @return bool true if the string is not blank
	 */
	public function isNotBlank()
	{
			return ($this->trim()->_string !== '');
	}

	/**
	 * Checks if the string is not empty.
	 * @return bool true if the string is not empty
	 */
	public function isNotEmpty()
	{
			return ($this->data !== '');
	}

	/**
	 * Checks if the string is palindrome.
	 * @return bool true if the string is palindrome
	 */
	public function isPalindrome()
	{
			return ($this->equals($this->reverse()));
	}

	/**
	 * Checks is the string is unicase.
	 * Unicase string is one that has no case for its letters.
	 * @return bool true if the string is unicase
	 */
	public function isUnicase()
	{
			return $this->toLowerCase()->equals($this->toUpperCase());
	}

	/**
	 * Checks if the string is upper case.
	 * String is considered upper case if all the characters are upper case.
	 * @return bool true if the string is upper case
	 */
	public function isUpperCase()
	{
			return $this->equals($this->toUpperCase());
	}

	/**
	 * Return the key of the current element.
	 * @return int
	 */
	public function key()
	{
			return $this->_index;
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
			return strrpos($this->data, (string)$substr, (int)$offset);
	}

	/**
	 * Returns the leftmost $length characters of a string.
	 * @param int $length number of characters.
	 * @return String
	 */
	public function left($length)
	{
			return $this->substring(0, $length);
	}

	public function matches($pattern)
	{
			return preg_match((string)$pattern, $this->data);
	}

	public function naturalCompareTo($string)
	{
			return strnatcmp($this->data, (string)$string);
	}

	public function naturalCompareToIgnoreCase($string)
	{
			return strnatcasecmp($this->data, (string)$string);
	}

	/**
	 * Move forward to next element.
	 */
	public function next()
	{
			++$this->_index;
	}

	public function pad($length, $padding = self::SPACE)
	{
			return new self(str_pad($this->data, (int)$length, (string)$padding, STR_PAD_BOTH));
	}

	public function padEnd($length, $padding = self::SPACE)
	{
			return new self(str_pad($this->data, (int)$length, (string)$padding, STR_PAD_RIGHT));
	}

	public function padStart($length, $padding = self::SPACE)
	{
			return new self(str_pad($this->data, (int)$length, (string)$padding, STR_PAD_LEFT));
	}

	/**
	 * Removes all occurrences of a substring from the string.
	 * @param string $substr substring
	 * @param bool $regex whether $substr is a regular expression
	 * @return String
	 */
	public function remove($substr)
	{
			return $this->replace($substr, '');
	}

	public function removeDuplicates($substr)
	{
			$pattern = '/('.preg_quote($substr, '/').')+/';
			return $this->replaceRegex($pattern, $substr);
	}

	/**
	 * Removes first occurrence of a substring from the string.
	 * @param string $substr substring
	 * @param bool $regex whether $substr is a regular expression
	 * @return String
	 */
	public function removeOnce($substr)
	{
			return $this->removeRegex($substr, 1);
	}

	public function removeRegex($pattern, $limit = null)
	{
			$this->replaceRegex($pattern, '', $limit);
	}

	public function removeSpaces()
	{
			return $this->remove(array(" ", "\r", "\n", "\t", "\0", "\x0B"));
	}

	/**
	 * Repeats the string $multiplier times.
	 * If seperator is not null, it will seperate the repeated string.
	 * @param int $multiplier number of times the string should be repeated.
	 * @param String $separator
	 * @return String
	 */
	public function repeat($multiplier, $separator = null)
	{
			if ($multiplier === 0) {
					$string = '';
			} else if ($separator === null) {
					$string = str_repeat($this->data, $multiplier);
			} else {
					$string = str_repeat($this->data.(string)$separator, $multiplier - 1) . $this->data;
			}
			return new self($string);
	}

	public function replace($search, $replace)
	{
			$string = str_replace($search, $replace, $this->data);
			return new self($string);
	}

	public function replaceOnce($search, $replace)
	{
			return $this->replaceRegex($search, $replace, 1);
	}

	public function replaceRegex($search, $replace, $limit = null)
	{
			$limit = (($limit === null) ? -1 : (int)$limit);
			$string = preg_replace($search, $replace, $this->data, $limit);
			return new self($string);
	}

	/**
	 * Revereses a string.
	 * @return String
	 */
	public function reverse()
	{
			if ($this->getEncoding() === 'UTF-8' && function_exists('utf8_strrev')) {
					$string = utf8_strrev($this->data);
			} else {
					$string = strrev($this->data);
			}
			return new self($string);
	}

	/**
	 * Rewind the Iterator to the first element.
	 */
	public function rewind()
	{
			$this->_index = 0;
	}

	/**
	 * Returns the rightmost $length characters of a string.
	 * @param int $length number of characters.
	 * @return String
	 */
	public function right($length)
	{
			return $this->substring(-$length);
	}

	/**
	 * Shuffles a string randomly.
	 * One permutation of all possible is created.
	 * @return String
	 */
	public function shuffle()
	{
			return new self(str_shuffle($this->data));
	}

	/**
	 * Removes a part of the string and replace it with something else.
	 * Example:
	 * <code>
	 * $string = new String('The fox jumped over the lazy dog.');
	 * echo $string->splice(4, 0, 'quick brown ');
	 * </code>
	 * prints 'The quick brown fox jumped over the lazy dog.'
	 * @return String
	 */
	public function splice($offset, $length = null, $replacement = '')
	{
			$count = $this->length();

			// Offset handling (negative values measure from end of string)
			if ($offset < 0) {
					$offset += $count;
			}

			// Length handling (positive values measure from $offset; negative, from end of string; omitted = end of string)
			if ($length === null) {
					$length = $count;
			} else if ($length < 0) {
					$length += $count - $offset;
			}

			return new self($this->substring(0, $offset) .
											(string)$replacement .
											$this->substring($offset + $length)
											);
	}

	public function splitRegex($pattern)
	{
			$array = preg_split($pattern, $this->data);
			return $array;
	}

	/**
	 * Removes extra spaces and reduces string's length.
	 * Extra spaces are repeated, leading or trailing spaces.
	 * It will also convert all spaces to white-spaces.
	 * @return String
	 */
	public function squeeze()
	{
			return $this
						 ->replace(array("\r\n", "\r", "\n", "\t", "\0", "\x0B"), ' ')
						 ->removeDuplicates(' ')
						 ->trim()
						 ;
	}

	/**
	 * Checks if the string starts with a substring.
	 * @param string $substr substring
	 * @return bool true if the string starts with $substr.
	 */
	public function startsWith($substr)
	{
			return ($this->indexOf($substr) === 0);
	}

	/**
	 * Returns part of the string.
	 * @param int $start
	 * @param int $length
	 * @return String
	 */
	public function substring($start, $length = null)
	{
			return new self(substr($this->data, $start, $length));
	}

	/**
	 * Gets the substring after the first occurrence of a separator.
	 * If no match is found returns null.
	 * @param string $separator
	 * @param bool $inclusive whether to return the seperator (default false)
	 * @return String
	 */
	public function substringAfterFirst($separator, $inclusive = false)
	{
			$incString = strstr($this->data, $separator);
			if ($incString === false) {
					return null;
			}

			$string = new self($incString);
			if ($inclusive) {
					return $string;
			}
			return $string->substring(1);

	}

	/**
	 * Gets the substring after the last occurrence of a separator.
	 * If no match is found returns null.
	 * @param String $separator
	 * @param bool $inclusive whether to return the seperator (default false)
	 * @return String
	 */
	public function substringAfterLast($separator, $inclusive = false)
	{
			$incString = strrchr($this->data, $separator);
			if ($incString === false) {
					return null;
			}

			$string = new self($incString);
			if ($inclusive) {
					return $string;
			}
			return $string->substring(1);
	}

	/**
	 * Gets the substring before the first occurrence of a separator.
	 * If no match is found returns null.
	 * @param String $separator
	 * @param bool $inclusive whether to return the seperator (default false)
	 * @return String
	 */
	public function substringBeforeFirst($separator, $inclusive = false)
	{
			if (version_compare(PHP_VERSION, '5.3.0') < 0) {
					$pos = $this->indexOf($separator);
					if ($pos === false) {
							return null;
					}
					if ($inclusive) {
							++$pos;
					}
					return $this->substring(0, $pos);
			}

			$excString = strstr($this->data, $separator, true);
			if ($excString === false) {
					return null;
			}

			$string = new self($excString);
			if ($inclusive) {
					return $string->concat($separator);
			}
			return $string;
	}

	/**
	 * Gets the substring before the last occurrence of a separator.
	 * If no match is found returns null.
	 * @param String $separator
	 * @param bool $inclusive whether to return the seperator (default false)
	 * @return String
	 */
	public function substringBeforeLast($separator, $inclusive = false)
	{
			$pos = $this->lastIndexOf($separator);
			if ($pos === false) {
							return null;
			}
			if ($inclusive) {
					++$pos;
			}
			return $this->substring(0, $pos);
	}

	/**
	 * Gets the String that is nested in between two Strings.
	 * If one of the delimiters is null, it will use the other one.
	 * Only the first match will be returned. If no match is found returns null.
	 * @param String $left  left  delimiter
	 * @param String $right right delimiter
	 * @return String
	 */
	public function substringBetween($left, $right = null)
	{
			if ($left === null && $right === null) {
					return null;
			}
			if ($left === null) {
					$left  = $right;
			} else if ($right === null) {
					$right = $left;
			}

			if (!($left  instanceof self)) {
					$left  = new self($left);
			}
			if (!($right instanceof self)) {
					$right = new self($right);
			}

			$posLeft  = $this->indexOf($left);
			if ($posLeft === false) {
					return null;
			}
			$posLeft += $left->length();

			$posRight = $this->indexOf($right, $posLeft + 1);
			if ($posRight === false) {
					return null;
			}
			return $this->substring($posLeft, $posRight - $posLeft);
	}

	/**
	 * Count the number of substring occurrences.
	 * @param string $substr
	 * @return int
	 */
	public function substringCount($substr)
	{
			return substr_count($this->data, (string)$substr);
	}

	/**
	 * Converts uppercase characters lowercase and vice versa.
	 * @return String
	 */
	public function swapCase()
	{
			$string = '';
			$length = $this->length();
			for ($i = 0; $i < $length; $i++) {
					$char = $this->charAt($i);
					if ($char->isLowerCase()) {
							$string .= $char->toUpperCase();
					} else {
							$string .= $char->toLowerCase();
					}
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
		return wrap(str_split($this->data, 1));
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
			return new self(strtolower($this->data));
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
			return new self(strtoupper($this->data));
	}

	/**
	 * Removes characters from both parts of the string.
	 * If $charlist is not provided, the default is to remove spaces.
	 * @param string $charlist characters to remove (default space characters)
	 * @return String
	 */
	public function trim($charlist = null)
	{
			return new self(trim($this->data, $charlist));
	}

	/**
	 * Removes characters from the right part of the string.
	 * If $charlist is not provided, the default is to remove spaces.
	 * @param string $charlist characters to remove (default space characters)
	 * @return String
	 */
	public function trimEnd($charlist = null)
	{
			return new self(rtrim($this->data, $charlist));
	}

	/**
	 * Removes characters from the left part of the string.
	 * If $charlist is not provided, the default is to remove spaces.
	 * @param string $charlist characters to remove (default space characters)
	 * @return String
	 */
	public function trimStart($charlist = null)
	{
			return new self(ltrim($this->data, $charlist));
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
			return new self(lcfirst($this->data));
	}

	/**
	 * Checks if current position is valid.
	 * @return bool
	 */
	public function valid()
	{
			return ($this->_index >= 0 && $this->_index < $this->length());
	}

	/**
	 * Returns the literal value of the string.
	 * @return string
	 */
	public function valueOf()
	{
			return $this->data;
	}

	/**
	 * Constructs a string object with the first argument as the string.
	 * Returns the result of the instance method $name.
	 * @param mixed $name callback function
	 * @param array $args function arguments. the first argument is the string literal.
	 * @return mixed
	 * @throws BadFunctionCallException
	 */
	public static function callbackStatic($name, array $args)
	{
			if (empty($args)) {
					throw new \BadFunctionCallException('Static callback requires at least one parameter.');
			}
			$literal = array_shift($args);
			$string = new self($literal);
			return call_user_func_array(array($string, $name), $args);
	}

	/**
	 * Overload method. Proxies to {@link callbackStatic()}.
	 * Method name starts with an underscore to prevent name clashes.
	 * Example:
	 * <code>
	 * <?php
	 * echo String::_squeeze(' a  b c ') // prints: a b c
	 * ?>
	 * </code>
	 * @param mixed $name
	 * @param array $args
	 * @return mixed
	 * @throws BadFunctionCallException
	 */
	public static function __callStatic($name, $args)
	{
			$name = substr($name, 1);
			return self::callbackStatic($name, $args);
	}

	/**
	 * Returns String with the first string argument.
	 * If no string is found, returns an empty String.
	 * Example:
	 * <code>
	 * <?php
	 * echo String::first(array(), 0, 'first', null, 'second'); // prints: first
	 * ?>
	 * </code>
	 * @return String
	 */
	public static function first()
	{
			$args = func_get_args();
			foreach ($args as $arg) {
					if (is_string($arg) || $arg instanceof self) {
							return new self($arg);
					}
			}
			return new self();
	}

	/**
	 * Formats and returns String.
	 * @param string $string formatting string
	 * @param array $args
	 * @return String
	 */
	public static function format($string, array $args)
	{
			return new self(vsprintf((string)$string), $args);
	}

	/**
	 * Returns random String in length of $length.
	 * The String consists of characters in $charset.
	 * @param int $length String's length
	 * @param string $charset String's charset (default alpha-numeric characters)
	 * @return String
	 */
	public static function random($length, $charset = self::ALNUM)
	{
			$length = (int)$length;
			$count = (int)self::getLength($charset);
			$str = '';
			while ($length--) {
					$str .= $charset[mt_rand(0, $count - 1)];
			}
			return new self($str);
	}

}
