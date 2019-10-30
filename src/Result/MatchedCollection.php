<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: php-regex
 */

namespace Jungle\Regex\Result;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class MatchedCollection
 * @package Jungle\Regex\Result
 */
class MatchedCollection extends MatchedAggregator{
	
	
	public function valid(){
		return isset($this->data[$this->i][0]);
	}
	
	public function rewind(){
		$this->i = 0;
	}
	
	
	public function offsetExists($offset){
		return isset($this->data[$this->i][$offset]);
	}
	
	public function offsetGet($offset){
		if(isset($this->data[$this->i][$offset])){
			return $this->data[$this->i][$offset];
		}
		return null;
	}
	
	public function offsetSet($offset, $value){
		$this->data[$this->i][$offset] = $value;
	}
	
	public function offsetUnset($offset){
		unset($this->data[$this->i][$offset]);
	}
	
	public function count(){
		return count($this->data);
	}
}


