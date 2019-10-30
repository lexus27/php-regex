<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: php-regex
 */

namespace Jungle\Regex\Result;

use Jungle\Regex\Pattern\Composition\PatternAlternation;
use Jungle\Regex\Pattern\PatternInterface;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class ResultElement
 * @package Jungle\Regex\Result
 *
 * @todo
 */
class ResultElement implements \ArrayAccess{
	
	/** @var  MatchedData|MatchedDataOffsetCapture */
	protected $data;
	
	/** @var  null|ResultElement */
	protected $parent;
	
	/** @var  null|\Jungle\Regex\Pattern\PatternInterface */
	protected $pattern;
	
	/** @var  string|integer  */
	protected $access = 0;
	
	/** @var  ResultElement[] */
	protected $children = [];
	
	/** @var  int[] */
	protected $childrenAliases = [];
	
	/**
	 * @param $accessIndex
	 * @return $this
	 */
	public function setAccess($accessIndex){
		$this->access = $accessIndex;
		return $this;
	}
	
	public function getChildren(){
		return $this->children;
	}
	
	public function getChildrenAliases(){
		return $this->childrenAliases;
	}
	
	/**
	 * @param $key
	 * @param ResultElement $element
	 * @return $this
	 */
	public function setChild($key, ResultElement $element){
		if(!is_array($key)){
			$key = [$key];
		}
		
		$element->setParent($this);
		
		$i = count($this->children);
		$this->children[$i] = $element;
		
		foreach($key as $k){
			$this->childrenAliases[$k] = $i;
		}
		
		return $this;
	}
	
	/**
	 * @param ResultElement $parent
	 * @return $this
	 */
	public function setParent(ResultElement $parent){
		$this->parent = $parent;
		return $this;
	}
	
	/**
	 * @param \Jungle\Regex\Pattern\PatternInterface $pattern
	 * @return $this
	 */
	public function setPattern(PatternInterface $pattern){
		$this->pattern = $pattern;
		return $this;
	}
	
	/**
	 * @param MatchedData $data
	 * @return $this
	 */
	public function setMatchedData(MatchedData $data){
		$this->data = $data;
		foreach($this->children as $child){
			$child->setMatchedData($data);
		}
		return $this;
	}
	
	
	
	/**
	 * получает результат текущей маски
	 * @return null|string
	 */
	public function that(){
		return $this->data->item($this->access);
	}
	
	/**
	 * Проходит в объект под-шаблона
	 * @param $index
	 * @return ResultElement|null
	 */
	public function in($index){
		if(is_numeric($index)){
			$index = intval($index);
		}
		if(isset($this->childrenAliases[$index])){
			if(isset($this->children[$this->childrenAliases[$index]])){
				return $this->children[$this->childrenAliases[$index]];
			}
		}
		return null;
	}
	
	/**
	 * Получает результат дочернего объекта под-шаблона
	 * @param $index
	 * @return string|null
	 */
	public function get($index){
		if($this->data[$this->access] !== null){
			return $this->in($index)->that();
		}
		return null;
	}
	
	/**
	 * Производит дружественный запрос по пути до вложенного под-шаблона, разделенным специальным разделителем(например
	 * . (точка))
	 * @param $path - 'contact.card.visa'
	 * @return ResultElement|string|null
	 */
	public function query($path, $toSubElement = false){
		if(!is_array($path)){
			$path= explode('.', $path);
		}
		
		$tryFull = implode('.', $path);
		if($el = $this->in($tryFull)){
			if($toSubElement){
				return $el;
			}else{
				return $el->that();
			}
		}
		
		$chunk = array_shift($path);
		if($path){
			$child = $this->in($chunk);
			if(!$child){
				return null;
			}
			return $child->query($path, $toSubElement);
		}else{
			if($el = $this->in($chunk)){
				if($toSubElement){
					return $el;
				}else{
					return $el->that();
				}
			}
			return null;
		}
	}
	
	/**
	 * @return null|array [pos, len]
	 */
	public function position(){
		if($this->data instanceof MatchedWithOffsetCapture){
			return $this->data->position($this->access);
		}
		return null;
	}
	
	
	
	
	public function offsetExists($offset){
		return !!$this->query($offset);
	}
	
	public function offsetGet($offset){
		return $this->query($offset);
	}
	
	public function offsetSet($offset, $value){
		throw new \BadMethodCallException(__METHOD__ . ' not be used');
	}
	public function offsetUnset($offset){
		throw new \BadMethodCallException(__METHOD__ . ' not be used');
	}
	
	/**
	 * @return array
	 */
	public function exportMap(){
		
		$a = [];
		foreach($this->childrenAliases as $alias => $childIndex){
			if(isset($this->children[$childIndex])){
				$a[$alias] = $this->children[$childIndex]->exportMap();
			}
		}
		
		return [
			'access'    => $this->access,
			'children'  => $a?:null
		];
	}
	
	/**
	 * @param null $preferred
	 * @return array
	 */
	public function getDataNested($preferred = null){
		$a = [];
		
		
		if($preferred !== self::KEYS_ANY){
			
			foreach($this->children as $i => $child){
				$aliases = array_keys($this->childrenAliases, $i, true);
				$_aliases = null;
				switch($preferred){
					
					case self::KEYS_INDEXED:
						foreach($aliases as $alias){
							if(is_numeric($alias)){
								$_aliases = [$alias];
								break;
							}
						}
						if(!$_aliases){ $_aliases = [reset($aliases)]; }
						break;
					case self::KEYS_NAMED:
						foreach($aliases as $alias){
							if(!is_numeric($alias)){
								$_aliases = [$alias];
								break;
							}
						}
						if(!$_aliases){ $_aliases = [reset($aliases)]; }
						break;
					
					case self::KEYS_ONLY_INDEXED:
						foreach($aliases as $alias){
							if(is_numeric($alias)){
								$_aliases = [$alias];
								break;
							}
						}
						break;
					case self::KEYS_ONLY_NAMED:
						foreach($aliases as $alias){
							if(!is_numeric($alias)){
								$_aliases = [$alias];
								break;
							}
						}
						break;
				}
				if($_aliases){
					foreach($_aliases as $alias){
						$childMatched = $child->that()?:null;
						if($child->children){
							$a[$alias] = [
								'that'      => $childMatched,
								'children'  => $child->getDataNested(self::KEYS_ANY)
							];
						}else{
							$a[$alias] = $childMatched;
						}
					}
				}
			}
			
		}else{
			
			foreach($this->childrenAliases as $alias => $childIndex){
				if(isset($this->children[$childIndex])){
					$child = $this->children[$childIndex];
					$childMatched = $child->that()?:null;
					if($child->children){
						$a[$alias] = [
							'that'      => $childMatched,
							'children'  => $child->getDataNested()
						];
					}else{
						$a[$alias] = $childMatched;
					}
				}
			}
		}
		
		return $a;
	}
	
	const KEYS_ANY              = null;      // Включить в вывод все возможные ключи
	
	const KEYS_NAMED            = 'named';   // Включать в вывод приемущественно именованные
	const KEYS_INDEXED          = 'indexed'; // Включать в вывод приемущественно нумерованные
	
	const KEYS_ONLY_NAMED       = 'only-named';   // Включать в вывод только именованные
	const KEYS_ONLY_INDEXED     = 'only-indexed'; // Включать в вывод только нумерованные
	
	/**
	 * @param bool $keysMode
	 * @param null $p
	 * @return array
	 */
	public function getDataAbsolute($keysMode = null, $p = null){
		$a = [];
		
		if($keysMode !== self::KEYS_ANY){
			
			foreach($this->children as $i => $child){
				$aliases = array_keys($this->childrenAliases, $i, true);
				$_aliases = null;
				switch($keysMode){
					
					case self::KEYS_INDEXED:
						foreach($aliases as $alias){
							if(is_numeric($alias)){
								$_aliases = [$alias];
								break;
							}
						}
						if(!$_aliases){ $_aliases = [reset($aliases)]; }
						break;
					case self::KEYS_NAMED:
						foreach($aliases as $alias){
							if(!is_numeric($alias)){
								$_aliases = [$alias];
								break;
							}
						}
						if(!$_aliases){ $_aliases = [reset($aliases)]; }
						break;
					
					case self::KEYS_ONLY_INDEXED:
						foreach($aliases as $alias){
							if(is_numeric($alias)){
								$_aliases = [$alias];
								break;
							}
						}
						break;
					case self::KEYS_ONLY_NAMED:
						foreach($aliases as $alias){
							if(!is_numeric($alias)){
								$_aliases = [$alias];
								break;
							}
						}
						break;
				}
				if($_aliases){
					foreach($_aliases as $alias){
						$childMatched = $child->that()?:null;
						$key = $p . $alias;
						$a[$key] = $childMatched;
						
						if($child->children){
							$a = array_replace($a, $child->getDataAbsolute($keysMode, $key . '.'));
						}
					}
				}
			}
			
		}else{
			foreach($this->childrenAliases as $alias => $childIndex){
				if(isset($this->children[$childIndex])){
					$child = $this->children[$childIndex];
					$childMatched = $child->that()?:null;
					$k = $p . $alias;
					$a[$k] = $childMatched;
					if($child->children){
						$a = array_replace($a, $child->getDataAbsolute(self::KEYS_ANY, $k . '.'));
					}
				}
			}
		}
		return $a;
	}
	
	/**
	 * Найден ли шаблон
	 * @return bool
	 */
	public function isCompliant(){
		return !empty($this->data[$this->access]);
	}
	
	/**
	 * @return int|string|null
	 *
	 * return 'email'
	 * return 'phone'
	 * return 'card'
	 *
	 */
	public function getAlternationCase(){
		
		if(!$this->data->item($this->access)){
			return null;
		}
		
		if(!$this->pattern instanceof PatternAlternation){
			throw new \BadMethodCallException(
				__CLASS__ . ' contains "'.get_class($this->pattern).'", but must be pattern: PatternAlternation(method '.__FUNCTION__.' use only with PatternAlternation)'
			);
		}
		
		foreach($this->childrenAliases as $alias => $childIndex){
			if(isset($this->children[$childIndex])){
				if($this->children[$childIndex]->isCompliant()){
					return $alias;
				}
			}
		}
		return null;
	}
	
	/**
	 * Проверяет путь по альтернациям, чтобы узнать что именно выбранно.
	 * Здесь есть некая аналогия между switchCase
	 *
	 *
	 * @return int|string|null
	 *
	 *
	 * return ['email']
	 * return ['phone']
	 * return ['card','visa']
	 * return ['card','maestro']
	 *
	 */
	public function getAlternationBranchPath(){
		
		if(!$this->data->item($this->access)){
			return null;
		}
		
		if(!$this->pattern instanceof PatternAlternation){
			throw new \BadMethodCallException(
				__CLASS__ . ' contains "'.get_class($this->pattern).'", but must be pattern: PatternAlternation(method '.__FUNCTION__.' use only with PatternAlternation)'
			);
		}
		
		$a = [];
		foreach($this->childrenAliases as $alias => $childIndex){
			if(isset($this->children[$childIndex])){
				$child = $this->children[$childIndex];
				if($child->isCompliant()){
					if($child->pattern instanceof PatternAlternation){
						$a = array_merge($a, [$alias], (array)$child->getAlternationBranchPath());
					}else{
						$a = array_merge($a, [$alias]);
					}
					break;
				}
			}
		}
		return $a?:null;
	}
	
	public function __toString(){
		return $this->that();
	}
	
}


