<?php
namespace Types;

	/**
	 * JAVA Autoboxing (part of Lotos Framework)  Copyright (c) 2005-2010 Artur
	 * Graniszewski (aargoth@boo.pl) All rights reserved.
	 * @category   Library
	 * @package    Lotos
	 * @subpackage DataTypes
	 * @copyright  Copyright (c) 2005-2010 Artur Graniszewski (aargoth@boo.pl)
	 * @license    GNU LESSER GENERAL PUBLIC LICENSE Version 3, 29 June 2007
	 * @author bob
	 * @version $Id$
	 * @updated 25-Apr-2011 4:21:37 AM
	 */
final class VariablesManager
{
    /**
     * Security checks
     *
     * @var bool
     */
    private static $initFinished = false;

    /**
     * Minimal string size to cache.
     *
     * @var integer
     */
    private static $minStringSize;

    /**
     * Number of the most current pointer.
     *
     * @var int
     */
    private static $counter;

    public static $memory = array();

    /**
     * Internal pool
     * array($privateInternId =>
     *   array(
     *       [0] - Variable value
     *       [1] - Number of different instances sharing this variable
     *       [2] - Variable length
     *       [3] - Public Intern id
     *   )
     * )
     */
    private static $internPool = array();

    /**
     * Array of internal pools' names.
     *
     * @var string[]
     */
    private static $internPoolId = array();

    /**
     * Number of the most current internal pool id.
     *
     * @var int
     */
    private static $internPoolCounter = 1;

    /**
    * Initializes new variable of the given type and returns C-like pointer to it.
    *
    * It allows us to do variable type enforcing and silent object casting
    *
    * @param String Data type
    * @return mixed Pointer to variable
    * @todo change to array pool?
    */
    public static function & getNewPointer($dataType) {
        do {
            ++self::$counter;
            $name = 'vm_var_'.self::$counter;

        } while (isset(VariablesManager::$memory[$name]));

        VariablesManager::$memory[$name] = $dataType;
        if(is_object($dataType) && in_array('setPointer', get_class_methods($dataType))) {
            $dataType->setPointer($name);
        }
        return VariablesManager::$memory[$name];
    }

    /**
     * Returns intern reference of the existing string or creates and returns new one if it doesn't exist.
     *
     * @param string Simple type string
     * @param string Internal pool id reserved for given string
     *  @param integer String length if calculated in a cache or null otherwise
     * @return string String reference or null if string was invalid
     */
    public static function & getIntern(& $string, & $internId, & $length = null) {
        if(!$length) {
            $length = strlen($string);
        }
        // cache only big strings
        if($string === '' || !isset($string[self::$minStringSize]) || strlen($string) < self::$minStringSize) {
            $internId = 0;
            return $string;
        }

        $intern = & self::$internPool[$string];
        if(isset($intern)) {
            // variable exists in cache:
            // increase the 'variable in use' counter by one
            ++$intern[1];

            // set flags, etc
            $length = $intern[2];
            $internId = $intern[3];
            return $intern[0];
        } else {
            $internId = self::$internPoolCounter++;
            self::$internPool[$string] = array(0 => & $string, 1 => 1, 2 => & $length, 3 => $internId);
            self::$internPoolId[$internId] = & $string;
            return $string;
        }
    }

    /**
     * Unsets internal pool entity or decreases it's counter by one if variable is still in use by other instances.
     *
     * @param String intern
     */
    public static function unsetIntern($internId) {
        // is PHP shutting down? if it is, then avoid unnecesary cleaning, PHP will do it automatically
        if($internId === 0) {
            return;
        }

        $id = $internId;

        $intern = & self::$internPool[self::$internPoolId[$internId]];

        if(isset($intern)) {
            --$intern[1];
            if($intern[1] === 0) {
                unset(self::$internPool[self::$internPoolId[$internId]]);
                unset(self::$internPoolId[$internId]);
            }
        }
    }

    /**
    * Initialises LOTOS Variables Manager.
    * @access public
    * @static
    * @param integer Minimal variable size needed to put it in cache
    * @return void
    */
    public static function init(& $minStringSize = 2) {
        self::$minStringSize = & $minStringSize;
        if(self::$initFinished) {
            return null;
        }
        self::$initFinished = true;
        return new self();
    }

    /**
     * LOTOS VM constructor.
     *
     * @return void
     */
    private function __construct() {

    }

    /**
     * LOTOS VM destructor (not used).
     *
     * @return void
     */
    public function __destruct() {

    }

    /**
    * Returns LOTOS VM statistics.
    *
    * @return mixed[] Array containing VM statistics.
    */
    public function getPoolUsage() {
        $len = 0; $shared = 0; $real = 0; $count = 0;
        foreach(self::$internPool as $var) {
            $len = strlen($var[0]);
            $real += $len;
            $shared += $len * $var[1];
            $count += $var[1];
        }
        $segments = count(self::$internPool);
        $ret = array('count' => $count, 'segments' => $segments, 'ratio' => ($segments > 0 ? ($count / $segments) * 100 : 0), 'memory' => array('real' => $real, 'shared' => $shared, 'ratio' => $real > 0 ? ($shared/$real) * 100 : 0));
        return $ret;
    }
}

// Lotos VM auto init (taken from the Lotos frameworks' autostart and executed manually below)
VariablesManager::init();
