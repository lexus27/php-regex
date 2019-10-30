<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: php-regex
 */

namespace Jungle\Regex\Result;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class MatchedWithOffsetCapture
 * @package Jungle\Regex\Result
 */
class MatchedWithOffsetCapture extends MatchedData{
	
	const RESULT_MATCHED  = 0;
	const RESULT_POSITION = 1;
	
	
	public function __construct(MatchedData $md){
		$this->data = $md;
	}
	
	public function setData($data){
		$this->data = $data;
	}
	
	
	public function offsetExists($offset){
		return isset($this->data[$offset][self::RESULT_MATCHED]);
	}
	
	public function offsetGet($offset){
		if(isset($this->data[$offset][self::RESULT_MATCHED])){
			return $this->data[$offset][self::RESULT_MATCHED];
		}
		return null;
	}
	
	/**
	 * @param $index
	 * @return int|null
	 */
	public function position($index){
		if(isset($this->data[$index][self::RESULT_POSITION])){
			return $this->data[$index][self::RESULT_POSITION];
		}
		return null;
	}
	
	/**
	 * @param mixed $offset
	 * @param mixed $value
	 */
	public function offsetSet($offset, $value){
		$this->data[$offset][self::RESULT_MATCHED] = $value;
		if(!isset($this->data[$offset][self::RESULT_POSITION])){
			$this->data[$offset][self::RESULT_POSITION] = null;
		}
	}
	
	/**
	 * @param mixed $offset
	 */
	public function offsetUnset($offset){
		unset($this->data[$offset]);
	}
	
	
}


