<?php

namespace Types;

/**
 * Example class.
 *
 * Note: in order to use AutoBoxing, your class need to extend "AutoBoxedObject" class.
 */
class Integer extends PrimitiveTypeWrapper
{
    public $value = 0;

    public function __construct($value) {
        $this->value = $value;
    }

    public function __toString() {
        // NOTE: this must be a string, PHP forbids returning different type of variables in __toString() methods.
        return "{$this->value}";
    }
}