<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: php-regex
 */

namespace Jungle\Regex\Pattern\Composition;


trait MemberTrait{
	
	/** @var  string|null */
	protected $alias;
	
	/** @var  int|null */
	protected $offset;
	
	/** @var  string|null */
	protected $prefix;
	
	/**
	 * @return string
	 */
	public function getPrefix(){
		if(!$this->prefix){
			$this->prefix = $this->alias? "{$this->alias}_" : "i{$this->offset}_" ;
		}
		return $this->prefix;
	}
	
	/**
	 * @return null|string
	 */
	public function getAlias(){
		return $this->alias;
	}
	
	/**
	 * @return int|null
	 */
	public function getOffset(){
		return $this->offset;
	}
	
	/**
	 * @param null|string $alias
	 * @return $this
	 */
	public function setAlias($alias){
		$this->alias = $alias?:null;
		return $this;
	}
	
	/**
	 * @param null|int|string $offset
	 * @return $this
	 */
	public function setOffset($offset){
		$this->offset = $offset!==null ? intval($offset) : $offset;
		return $this;
	}
	
}
