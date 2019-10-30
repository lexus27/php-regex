<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: php-regex
 */

namespace Jungle\Regex\Pattern\Composition\Member;

use Jungle\Regex\Pattern\Composition\MemberInterface;
use Jungle\Regex\Pattern\Composition\MemberTrait;
use Jungle\Regex\Pattern\GroupsAwareTrait;
use Jungle\Regex\Pattern\ModifierAwareTrait;
use Jungle\Regex\Pattern\OutputAwareTrait;
use Jungle\Regex\Result\ResultElement;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class MemberNeighbor
 * @package Jungle\Regex\Pattern\Composition\Member
 */
class MemberNeighbor implements MemberInterface{
	
	use GroupsAwareTrait;
	use MemberTrait;
	use OutputAwareTrait;
	use ModifierAwareTrait;
	
	/**
	 * MemberPlain constructor.
	 * @param $payload
	 */
	public function __construct($payload){
		$this->payload = $payload;
	}
	
	/**
	 * @return string
	 */
	public function getPayload(){
		return $this->payload;
	}
	
	/**
	 * @return string
	 */
	public function getPcre(){
		return $this->wrapAsPcre($this->getPayload());
	}
	
	/**
	 * @return string
	 */
	public function getSketch(){
		return $this->wrapAsSketch($this->getPayload());
	}
	/**
	 * @param $pattern
	 * @return string
	 */
	public function wrapAsSketch($pattern){
		$modifiers = $this->getModifiers();
		return "(?{$modifiers}:{$pattern})";
	}
	
	/**
	 * @param $offset
	 * @param $prefix
	 * @return ResultElement
	 */
	public function getResultMap($offset = 0, $prefix = ''){
		
		$names = $this->getCapturedGroupsWithNames();
		$count = $this->getCapturedGroupsCount();
		
		$map = [];
		
		$mapElement = new ResultElement();
		
		/**
		 * [name => globalAccessName]
		 * [index => globalAccessIndex]
		 */
		for($i=0;$i<$count;$i++){
			$__i = $i + 1;
			$accessIndex = $offset + $__i;
			$keys = [];
			
			if(isset($names[$__i])){
				$keys[] = $names[$__i];
			}
			$keys[] = $__i;
			
			$el = new ResultElement();
			$el->setAccess($accessIndex);
			$mapElement->setChild($keys, $el);
			
		}
		
		return $mapElement;
	}
	
	/**
	 * @param $offset
	 * @param $prefix
	 * @return array
	 */
	public function getResultMapArray($offset = 0, $prefix = ''){
		return null;
	}
	
}


