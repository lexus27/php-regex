<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-regex
 */

namespace Jungle\Regex;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class ResultMany
 * @package Jungle\Regex
 */
class ResultMany extends Result implements \Iterator{

	protected $current_match = 0;

	/**
	 * @param $offset
	 * @return bool
	 */
	protected function _has($offset){
		return isset($this->result[$offset][$this->current_match]);
	}

	/**
	 * @param $offset
	 * @return null
	 */
	protected function _get($offset){
		if(isset($this->result[$offset][$this->current_match])){
			return $this->result[$offset][$this->current_match];
		}else{
			return null;
		}
	}

	public function current(){
		return $this;
	}

	public function next(){
		$this->current_match++;
	}

	public function key(){
		return $this->current_match;
	}

	public function valid(){
		return isset($this->result[0][$this->current_match]);
	}

	public function rewind(){
		$this->current_match = 0;
	}
}


