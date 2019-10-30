<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-regex
 */

namespace Jungle\Regex;
use Traversable;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class Result
 * @package Jungle\Regex
 */
class Result implements \ArrayAccess{

	protected $result;

	protected $offset = 0;

	protected $prefix = '';

	protected $global_alias = 0;

	protected $prism_enabled = true;

	/**
	 * Result constructor.
	 * @param $result
	 */
	public function __construct($result){
		$this->result = $result;
	}

	/**
	 * @param int $offset
	 * @param string $prefix
	 * @param int $global_alias
	 * @return $this
	 */
	public function prism($offset = 0, $prefix = '', $global_alias = 0){
		$this->offset       = $offset;
		$this->prefix       = $prefix;
		$this->global_alias = $global_alias;
		$this->prism_enabled= true;
		return $this;
	}

	public function prismDisable(){
		$this->prism_enabled = false;
	}

	public function prismEnable(){
		$this->prism_enabled = true;
	}




	public function __get($name){
		$offset = $this->_in_prism( $name );
		return $this->_get($offset);
	}

	public function get($name = null){
		$offset = $this->_in_prism( $name );
		return $this->_get($offset);
	}



	public function offsetExists($offset){
		$offset = $this->_in_prism($offset);
		return $this->_has($offset);
	}

	public function offsetGet($offset){
		$offset = $this->_in_prism( $offset );
		return $this->_get($offset);
	}


	public function offsetSet($offset, $value){
		throw new \BadMethodCallException(__METHOD__. ' Not usable in this class');
	}

	public function offsetUnset($offset){
		throw new \BadMethodCallException(__METHOD__. ' Not usable in this class');
	}


	protected function _in_prism($offset = null){
		if($this->prism_enabled){
			if(!isset($offset) || $offset === 0){
				$offset = $this->global_alias;
			}elseif(is_string($offset)){
				$offset = $this->prefix . $offset;
			}else{
				$offset = $this->offset + $offset;
			}
		}elseif(!isset($offset)){
			$offset = 0;
		}
		return $offset;
	}

	protected function _has($offset){
		return isset($this->result[$offset]);
	}

	protected function _get($offset){
		if(isset($this->result[$offset])){
			return $this->result[$offset];
		}else{
			return null;
		}
	}

}


