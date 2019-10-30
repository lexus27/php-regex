<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: php-regex
 */

namespace Jungle\Regex\Result;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class MatchedData
 * @package Jungle\Regex\Result
 */
class MatchedData implements \ArrayAccess{
	
	/** @var  array */
	protected $data;
	
	public function __construct($data = null){
		if(isset($data))$this->setData($data);
	}
	
	public function setData($data){
		$this->data = $data;
		return $this;
	}
	
	/**
	 * @return mixed|null
	 */
	public function getFull(){
		return $this->offsetGet(0);
	}
	
	/**
	 * @param $index
	 * @return mixed|null
	 */
	public function item($index){
		return $this->offsetGet($index);
	}
	
	public function has($index){
		return $this->offsetExists($index);
	}
	
	public function offsetExists($offset){
		return isset($this->data[$offset]);
	}
	
	public function offsetGet($offset){
		if(isset($this->data[$offset])){
			return $this->data[$offset];
		}
		return null;
	}
	
	public function offsetSet($offset, $value){
		$this->data[$offset] = $value;
	}
	
	public function offsetUnset($offset){
		unset($this->data[$offset]);
	}
}


