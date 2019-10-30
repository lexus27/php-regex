<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: php-regex
 */

namespace Jungle\Regex\Pattern;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class OutputAwareTrait
 * @package Jungle\Regex\Pattern
 */
trait OutputAwareTrait{
	
	/**
	 * @return string
	 */
	abstract public function getPcre();
	
	/**
	 * @return string
	 */
	abstract public function getSketch();
	
	/**
	 * @return mixed
	 */
	abstract public function getModifiers();
	
	/**
	 * @return mixed
	 */
	abstract public function getDelimiter();
	
	
	/**
	 * @param $pattern
	 * @param null $prepend
	 * @param null $append
	 * @return string
	 */
	public function coverPattern($pattern, $prepend = null, $append = null){
		return $this->_cover($pattern, $prepend, $append);
	}
	
	/**
	 * @param $pattern
	 * @return string
	 */
	public function wrapAsPcre($pattern){
		$delimiter = $this->getDelimiter();
		$modifiers = $this->getModifiers();
		return "{$delimiter}".addcslashes($pattern,$delimiter)."{$delimiter}{$modifiers}";
	}
	
	/**
	 * @param $pattern
	 * @return string
	 */
	public function wrapAsSketch($pattern){
		$modifiers = $this->getModifiers();
		return $modifiers? "(?{$modifiers}:{$pattern})" : $pattern;
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
	
	
}
