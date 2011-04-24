<?php

namespace Types;

/**
 * Example class.
 *
 * Note: in order to use AutoBoxing, your class need to extend "AutoBoxedObject" class.
 */
class CachedString extends String
{
    public $value;

    public function __construct($value) {
        parent::__construct();
        $this->value = & VariablesManager::getIntern($value, $this->internId);
    }

    public function __destruct() {
        VariablesManager::unsetIntern($this->internId);
        parent::__destruct();
    }
}

/**
* Initializes a newly created String object.
* @return String created String object
*/
function & cachedString($value = null) {
    $x = & VariablesManager::getNewPointer(new CachedString($value));
    return $x;
}