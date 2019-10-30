<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: php-regex
 */

namespace Jungle\Regex\Result;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class MatchedAggregator
 * @package Jungle\Regex\Result
 */
class MatchedAggregator extends MatchedData implements \Iterator, \Countable{
	
	protected $i = 0;
	
	protected $map;
	
	
	public function setMap(ResultElement $map){
		$this->map = $map;
		return $this;
	}
	
	public function current(){
		return $this->map;
	}
	
	public function next(){
		$this->i++;
	}
	
	public function key(){
		return $this->i;
	}
	
	public function valid(){
		return isset($this->data[0][$this->i]);
	}
	
	public function rewind(){
		$this->i = 0;
	}
	
	
	
	
	public function offsetExists($offset){
		return isset($this->data[$offset][$this->i]);
	}
	
	public function offsetGet($offset){
		if(isset($this->data[$offset][$this->i])){
			return $this->data[$offset][$this->i];
		}
		return null;
	}
	
	public function offsetSet($offset, $value){
		$this->data[$offset][$this->i] = $value;
	}
	
	public function offsetUnset($offset){
		unset($this->data[$offset][$this->i]);
	}
	
	public function count(){
		return count($this->data[0]);
	}
}


