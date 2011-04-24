<?php

namespace Types;

abstract class Type
	extends PrimitiveTypeWrapper
	implements \Serializable {
	
	protected $data;
	
	public function __invoke(){
		return $this->__toString();
	}
	
	protected function setData($data){
		if ( $data instanceof PrimitiveTypeWrapper )
			$data = $data->data;
		
		$this->data = $data;
		
		return $this;
	}
	
	// Serializable
	public function serialize(){
		return serialize($this->data);
	}
	
	public function unserialize($serialized){
		$this->data = unserialize($serialized);
	}
	
	// Static
	public static function from($data = null){
		$name = static::getClassName();
		
		return $name ? new $name($data) : $name;
	}
	
	protected static function getClassName(){}
	
}