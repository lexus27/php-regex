<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: php-regex
 */

namespace Jungle\Regex\Pattern;

use Jungle\Regex\RegexUtils;
use Jungle\Regex\Result\ResultElement;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class PatternSimple
 * @package Jungle\Regex\Pattern
 */
class PatternSimple extends Pattern{
	
	public function suitableCases(){
		
	}
	
	public function suitableGen(){
		
	}
	
	public function suitableRandom(){
		
	}
	
	
	/**
	 * @param string $input
	 */
	public function __construct($input){
		$decomposition      = RegexUtils::decomposite_regex($input);
		$this->payload      = $decomposition['pattern'];
		$this->modifiers    = $decomposition['modifiers'];
		$this->delimiter    = $decomposition['delimiter'];
	}
	
	/**
	 * @return mixed
	 */
	public function getPayload(){
		return $this->payload;
	}
	
	/**
	 * @param $offset
	 * @param $prefix
	 * @return ResultElement
	 */
	public function getResultMap($offset=0, $prefix=''){
		
		$names = $this->getCapturedGroupsWithNames();
		$count = $this->getCapturedGroupsCount();
		
		$map = $this->aliases;
		
		$mapElement = (new ResultElement())->setAccess($offset)->setPattern($this);
		
		/**
		 * [name => globalAccessName]
		 * [index => globalAccessIndex]
		 */
		for($i=1;$i<$count;$i++){
			
			$accessIndex = $offset + $i;
			$aliasKeys = [];
			
			if(isset($map[$i])){
				$aliasKeys = array_merge($aliasKeys, $map[$i]);
			}
			if(isset($names[$i])){
				if(isset($map[$names[$i]])){
					$aliasKeys = array_merge($aliasKeys, $map[$names[$i]]);
				}
				$aliasKeys[] = $names[$i];
			}
			
			$aliasKeys[] = $i;
			
			$mapElement->setChild( array_unique($aliasKeys), (new ResultElement())->setAccess($accessIndex) );
			
		}
		
		return $mapElement;
	}
	
	
	/**
	 * @param $offset
	 * @param $prefix
	 * @return array
	 */
	public function getResultMapArray($offset=0, $prefix=''){
		
		//$offset = $offset + $this->getOffset();
		//$prefix = $prefix . $this->getPrefix();
		
		$names = $this->getCapturedGroupsWithNames();
		$count = $this->getCapturedGroupsCount();
		
		$map = [];
		
		$a = [];
		/**
		 * [name => globalAccessName]
		 * [index => globalAccessIndex]
		 */
		for($i=1;$i<$count;$i++){
			if(isset($map[$i])){
				$a[$map[$i]] = $offset + $i;
			}elseif(isset($names[$i])){
				if(isset($map[$names[$i]])){
					$a[$map[$names[$i]]] = $prefix . $names[$i];
				}else{
					$a[$names[$i]] = $prefix . $names[$i];
				}
			}
			$a[$i] = $offset + $i;
		}
		
		return $a;
	}
	
}


