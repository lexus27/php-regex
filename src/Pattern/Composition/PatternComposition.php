<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: php-regex
 */

namespace Jungle\Regex\Pattern\Composition;
use Jungle\Regex\Pattern\Composition\Member\MemberNeighbor;
use Jungle\Regex\Pattern\Composition\Member\MemberPattern;
use Jungle\Regex\Pattern\Pattern;
use Jungle\Regex\Pattern\PatternInterface;
use Jungle\Regex\Result\ResultElement;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class PatternComposition
 * @package Jungle\Regex\Pattern
 */
abstract class PatternComposition extends Pattern{
	
	/** @var  MemberInterface[]|PatternInterface[] */
	protected $children = [];
	
	/**
	 * PatternComposition constructor.
	 * @param array ...$children
	 */
	public function __construct(...$children){
		if($children)$this->addChildren($children);
	}
	
	/**
	 * @param array $children
	 * @return $this
	 */
	public function addChildren(array $children){
		foreach($children as $key => $child){
			if(is_array($child)){
				call_user_func([$this,'addChildren'], $child);
			}else if($child){
				$this->addChild($child, is_string($key)?$key:null);
			}
		}
		return $this;
	}
	
	
	/**
	 * @param $pattern
	 * @param null $alias
	 * @return $this
	 */
	public function addChild($pattern, $alias = null){
		
		if(is_string($pattern)){
			$member = new MemberNeighbor($pattern);
		}else if(!$pattern instanceof MemberInterface){
			$member = new MemberPattern($pattern);
		}else{
			$member = $pattern;
		}
		
		if(is_string($alias)){
			$member->setAlias($alias);
			$this->children[$alias] = $member;
		}else{
			$this->children[count($this->children) + 1] = $member;
		}
		
		
		$this->metaCapturedCount = null;// reset count
		
		return $this;
	}
	
	/**
	 * @param $index
	 * @return \Jungle\Regex\Pattern\PatternInterface|PatternWrapper|null
	 */
	public function getChild($index){
		if(is_string($index)){
			return isset($this->children[$index])?$this->children[$index]:null;
		}
		$a = array_slice($this->children, $index-1, 1, true);
		
		if($a){
			return reset($a);
		}else{
			return null;
		}
	}
	
	/**
	 * @return int|null
	 */
	public function getCapturedGroupsCount(){
		if(!$this->metaCapturedCount){
			$c = 0;
			foreach($this->children as $pattern){
				$c+= $pattern->getCapturedGroupsCount();
			}
			$this->metaCapturedCount = $c;
		}
		return $this->metaCapturedCount;
	}
	
	
	
	
	/**
	 * @return mixed
	 */
	public function getPayload(){
		if(!$this->payload){
			$payloads = $this->_processChildren();
			$this->payload = $this->compositeChildrenPayloads($payloads);
		}
		return $this->payload;
	}
	
	/**
	 * @return array
	 */
	protected function _processChildren(){
		$accumulatedSubmaskCount = 0;
		$payloads    = [];
		foreach($this->children as $index => $pattern){
			$pattern->setOffset($accumulatedSubmaskCount + 1); // прибавили 1 т.к маски нумеруются от 1, а подсчет $submaskCount от 0
			$accumulatedSubmaskCount+= $pattern->getCapturedGroupsCount();
			$payloads[] = $pattern->getSketch();
		}
		
		return $payloads;
	}
	
	
	/**
	 * @param array $payloads
	 * @return string
	 */
	abstract protected function compositeChildrenPayloads(array $payloads);
	
	/**
	 * @param $offset
	 * @param $prefix
	 * @return ResultElement
	 */
	public function getResultMap($offset=0, $prefix=''){
		
		$this->getPayload();
		
		$map = $this->aliases;
		
		$el = new ResultElement();
		$el->setAccess($offset);
		$el->setPattern($this);
		
		$i = 1;
		foreach($this->children as $index => $child){
			/** @var ResultElement $childMap */
			$childMap = $child->getResultMap($offset, $prefix);
			
			if($child instanceof MemberPattern){
				
				// поиск дополнительных псевдонимов для ключей
				$aliasKeys = [];
				if(isset($map[$i])){
					$aliasKeys = array_merge($aliasKeys, $map[$i]);
				}
				// добавление реальной-маски в ключи
				if(is_string($index)){
					if(isset($map[$index])){
						$aliasKeys = array_merge($aliasKeys, $map[$index]);
					}
					$aliasKeys[] = $index;
				}
				$aliasKeys[] = $i;
				
				$el->setChild(array_unique($aliasKeys), $childMap);
				$i++;
				
			}else if($child instanceof MemberNeighbor){
				$chAliases = $childMap->getChildrenAliases();
				foreach($childMap->getChildren() as $__i => $childMapChild){
					
					// Поиск имени маски
					$maskName = array_keys($chAliases, $__i, true);
					foreach($maskName as $__){
						if(!is_numeric($__)){
							$maskName = $__;
							unset($__);
						}
					}
					if(is_array($maskName)){
						$maskName = null;
					}
					
					// поиск дополнительных псевдонимов для ключей
					$aliasKeys = [];
					if(isset($map[$i])){
						$aliasKeys = array_merge($aliasKeys, $map[$i]);
					}
					// добавление реальной-маски в ключи
					if(is_string($maskName)){
						if(isset($map[$maskName])){
							$aliasKeys = array_merge($aliasKeys, $map[$maskName]);
						}
						$aliasKeys[] = $maskName;
					}
					$aliasKeys[] = $i;
					
					$el->setChild(array_unique($aliasKeys), $childMapChild);
					$i++;
				}
			}
		}
		return $el;
	}
	
	
	
	/**
	 * @param $offset
	 * @param $prefix
	 * @return array
	 */
	public function getResultMapArray($offset=0, $prefix=''){
		$a = [];
		$map = [];
		$i = 1;
		foreach($this->children as $index => $child){
			$childMap = $child->getResultMapArray($offset, $prefix);
			
			$key = null;
			$gKey = null;
			$gI = $offset + $i;
			
			if(isset($map[$i])){
				$key = $map[$i];
				$gKey = $offset + $i;
			}elseif(is_string($index)){
				if(isset($map[$index])){
					$key = $map[$index];
					$gKey = $prefix . $index;
				}else{
					$key = $index;
					$gKey = $prefix . $index;
				}
			}
			
			if($key && $gKey){
				$a[$key] = [
					'access'=> $gKey,
					'map'  => $childMap
				];
			}
			
			$a[$i] = [
				'access'=> $gI,
				'map'   => $childMap
			];
			
			$i++;
		}
		return $a;
	}
	
	
}


