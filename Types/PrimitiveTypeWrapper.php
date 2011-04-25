<?php

namespace Types;

/**
 * @throws Exception
 * TODO inherit from Type
 */
class PrimitiveTypeWrapper extends Iterable
{
	protected $data = null;

	protected $allowedCasting = array();

	/**
	 * Overload method. Proxies to {@link callback()}.
	 * Example:
	 * <code>
	 * <?php
	 * $string = new String('123456');
	 * echo $string->md5(); // prints: e10adc3949ba59abbe56e057f20f883e
	 * ?>
	 * </code>
	 * @param mixed $name
	 * @param array $args
	 * @return mixed
	 * @throws BadFunctionCallException
	 */
	public function __call($name, $args)
	{
			return $this->callback($name, $args);
	}

	/**
	 * Returns the result of the callback function $name.
	 * The literal string will be sent as the first argument.
	 * @param mixed $name callback function
	 * @param array $args additional function arguments (default empty)
	 * @return mixed
	 * @throws BadFunctionCallException
	 * @see http://php.net/manual/en/language.pseudo-types.php#language.types.callback
	 */
	public function callback($name, array $args = array())
	{
			if (!is_callable($name)) {
					throw new \BadFunctionCallException("$name is not a valid callback.");
			}
			array_unshift($args, $this->data);
			$result = call_user_func_array($name, $args);
			return wrap($result);
	}

	public function __toString()
	{
		// NOTE: this must be a string, PHP forbids returning different type of variables in __toString() methods.
		return "{$this->data}";
	}

	/**
	 * Converts this variable to Integer object.
	 *
	 * @return Integer
	 */
	public function & toInt()
	{
		$x = integer((int)$this->data);
		return $x;
	}

	/**
	 * Converts this variable to Float object.
	 *
	 * @return Float
	 */
	public function & toFloat()
	{
		$x = float((int)$this->data);
		return $x;
	}

	/**
	 * Converts this variable to String object.
	 *
	 * @return String
	 */
	public function & toString()
	{
		$x = string((string)$this->data);
		return $x;
	}

	/**
	 * Converts this variable to Array object.
	 *
	 * @return ArrayObject
	 */
	public function & toArray()
	{
		$x = new ArrayObject($this->data);
		return $x;
	}

	/**
	 * Converts this variable to native array.
	 *
	 * @return Array
	 */
	public function & toArrayNative()
	{
		$x = new ArrayObject($this->data);
		return $x->toArrayNative();
	}

	public function & toPrimitiveType() {
		return $this->data;
	}

	/**
	 * Destructor used to datatype enforcing and final cleanups.
	 *
	 * This time we are overwritting default Lotos VariablesManager behaviour and use
	 * strong data type enforcing
	 *
	 * @return void
	 */
	public function __destruct()
	{
		if ($this->ref === null) {
			return;
		}
		if (is_object(VariablesManager::$memory[$this->ref]) && get_class(VariablesManager::$memory[$this->ref]) === get_class($this) && in_array('setPointer', get_class_methods(VariablesManager::$memory[$this->ref]))) {
			VariablesManager::$memory[$this->ref]->setPointer($this->ref);

		} else if (is_scalar(VariablesManager::$memory[$this->ref])) {
			$val = VariablesManager::$memory[$this->ref];
			$class = get_class($this);

			VariablesManager::$memory[$this->ref] = new $class($val);
			VariablesManager::$memory[$this->ref]->setPointer($this->ref);
		} else if (is_object(VariablesManager::$memory[$this->ref])) {
			foreach ($this->allowedCasting as $dataType) {
				if (is_a(VariablesManager::$memory[$this->ref], $dataType)) {
					return;
				}
			}

			// TODO
			throw new Exception('Cannot cast ' . get_class(VariablesManager::$memory[$this->ref]) . ' data type to ' . get_class($this) . '!');
		}
	}
}

