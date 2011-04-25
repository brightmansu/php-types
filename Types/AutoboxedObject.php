<?php

namespace Types;

	/**
	 * AutoBoxedObject  Every class needs to inherit this class in order to use PHP
	 * autoboxing.
	 * @author Artur Graniszewski
	 * @version 1.0
	 * @updated 25-Apr-2011 4:21:36 AM
	 */
abstract class AutoBoxedObject
{
    /**
     * C-like variable pointer.
     *
     * @var mixed
     */
    protected $ref;

    /**
    * Internal ID used by caching mechanism (but not used frequently).
    *
    * @var int
    */
    protected $internId;

    /**
     * Public constructor
     *
     * @return AutoBoxedObject
     */
    public function __construct() {

    }

    /**
     * Destructor used to datatype enforcing and final cleanups.
     *
     * @return void
     */
    public function __destruct() {
        if($this->ref === null) {
            return;
        }
        if(VariablesManager::$memory[$this->ref] instanceof self) {
            VariablesManager::$memory[$this->ref]->setPointer($this->ref);

        } else if(is_scalar(VariablesManager::$memory[$this->ref])){
            $val = VariablesManager::$memory[$this->ref];
            $class = get_class($this);;

            VariablesManager::$memory[$this->ref] = new $class($val);
            VariablesManager::$memory[$this->ref]->setPointer($this->ref);
        }
    }

    /**
     * Sets C-like pointer for this object.
     *
     * @param mixed $name
     */
    public function setPointer($name) {
        $this->ref = $name;
    }

    /**
     * Returns internal ID of this object.
     *
     * @return mixed
     */
    public function getIntern() {
        return $this->internId;
    }

    /**
     * Returns string representation of this object.
     *
     * @return string
     */
    public function toString() {
        return $this->__toString();
    }
}

