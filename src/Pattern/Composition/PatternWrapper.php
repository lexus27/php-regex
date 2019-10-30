<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: php-regex
 */

namespace Jungle\Regex\Pattern\Composition;

use Jungle\Regex\RegexUtils;
use Jungle\Regex\Result\ResultElement;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class PatternWrapper
 * @package Jungle\Regex\Pattern\Composition
 */
class PatternWrapper extends PatternDecorator{
	
	protected $alias;
	
	protected $offset;
	
	
	/** @var  PatternComposition */
	protected $parent;
	
	
	/**
	 * @param PatternComposition $parent
	 * @return $this
	 */
	public function setParent(PatternComposition $parent){
		$this->parent = $parent;
		return $this;
	}
	
	/**
	 * @return PatternComposition
	 */
	public function getParent(){
		return $this->parent;
	}
	
	
	public function getPrefix(){
		return $this->alias? "{$this->alias}_" : "i{$this->offset}_" ;
	}
	
	public function setMappingAlias($alias = null){
		$this->alias = $alias?:null;
		return $this;
	}
	
	public function setOffset($offset = null){
		$this->offset = $offset?:0;
		return $this;
	}
	
	public function getAlias(){
		return $this->alias;
	}
	
	public function getOffset(){
		return intval($this->offset);
	}
	
	/**
	 * @return array
	 */
	public function getMetadata(){
		if(!$this->metadata){
			$this->metadata = RegexUtils::analyzeRegexpMetadata($this->getPayload());
		}
		return $this->metadata;
	}
	
	public function getCapturedGroupsCount(){
		return count($this->getMetadata()['captured']);
	}
	
	/**
	 * @todo Задать стандарт компиляции шаблона - "Для подстановки"
	 * @todo Сейчас метаданные вычисляются по payload, а иногда нужно по sketch(Для подстановки)
	 * @return mixed
	 */
	public function getPayload(){
		if(!$this->payload){
			$p = $this->wrapped->getPayload();
			// todo: сделать спец метод $pattern->modifiedIdentifiers для ленивого анализирования груп
			$p = RegexUtils::modify_identifiers($p, $this->getOffset(), $this->getPrefix(), $this->getOffset());
			
			$alias = $this->getAlias();
			if($alias){
				$p = "(?<{$alias}>{$p})";
			}else{
				$p = "({$p})";
			}
			$this->payload = $p;
		}
		return $this->payload;
	}
	
	protected $pcre_prepend = [];
	
	protected $pcre_append = [];
	
	/**
	 * @return string
	 */
	public function getPcre(){
		if(!$this->pcre){
			$pattern = $this->wrapped->getPayload();
			if($this->pcre_prepend || $this->pcre_append){
				$pattern = $this->_cover($pattern,
					!is_array($this->pcre_prepend)?[$this->pcre_prepend]:$this->pcre_prepend,
					!is_array($this->pcre_append)?[$this->pcre_append]:$this->pcre_append
				);
			}
			$this->pcre = $this->wrapAsPcre($pattern);
		}
		return $this->pcre;
	}
	
	/**
	 * @return string
	 */
	public function getSketch(){
		if(!$this->sketch){
			$payload = $this->getPayload();
			$modifiers = $this->wrapped->getModifiers();
			$this->sketch = ($modifiers? '(?'.$modifiers.':'.$payload.')' :$payload);
		}
		return $this->sketch;
		
	}
	
	
	public function reset(){
		$this->metadata = null;
		$this->sketch = null;
		$this->payload = null;
		return $this;
	}
	
	public function pcreCover($prepend=null,$append=null){
		if($prepend)$this->pcre_prepend[] = $prepend;
		if($append)$this->pcre_append[] = $append;
		return $this;
	}
	
	public function pcrePrepend($prepend){
		$this->pcre_prepend[] = $prepend;
		return $this;
	}
	
	public function pcreAppend($append){
		$this->pcre_append[] = $append;
		return $this;
	}
	
	
	
	
	/**
	 * @param $string
	 * @param array $prepend
	 * @param array $append
	 * @return string
	 */
	protected function _cover($string, array $prepend, array $append){
		$length = strlen($string);
		$_a = [];
		foreach($prepend as $item){
			if(strpos($string, $item) !== 0){
				$_a[] = $item;
				
			}
		}
		foreach($_a as $item) $string = $item . $string;
		
		$_a = [];
		foreach($append as $item){
			$item_length = strlen($item);
			if(strpos($string, $item) !== $length - $item_length){
				$_a[] = $item;
			}
		}
		foreach($_a as $item) $string = $string. $item;
		
		return $string;
	}
	
	/**
	 * @param $offset
	 * @param $prefix
	 * @return ResultElement
	 */
	public function getResultMap($offset = 0, $prefix=''){
		
		$offset = $offset + $this->getOffset();
		$prefix = $prefix . $this->getPrefix();
		
		return $this->wrapped->getResultMap($offset, $prefix);
	}
	
	
	
	/**
	 * @param $offset
	 * @param $prefix
	 * @return array
	 */
	public function getResultMapArray($offset=0, $prefix=''){
		
		$offset = $offset + $this->getOffset();
		$prefix = $prefix . $this->getPrefix();
		
		return $this->wrapped->getResultMapArray($offset, $prefix);
	}
}


