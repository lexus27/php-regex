<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: php-regex
 */

namespace Jungle\Regex\Pattern\Composition\Member;

use Jungle\Regex\Pattern\Composition\MemberInterface;
use Jungle\Regex\Pattern\Composition\MemberTrait;
use Jungle\Regex\Pattern\Composition\PatternDecorator;
use Jungle\Regex\Pattern\GroupsAwareTrait;
use Jungle\Regex\RegexUtils;
use Jungle\Regex\Result\ResultElement;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class MemberPattern
 * @package Jungle\Regex\Pattern\Composition
 */
class MemberPattern extends PatternDecorator implements MemberInterface{
	
	use MemberTrait;
	
	public function modifyBy($payload, $metadata){
		list($payload, $metadata) = RegexUtils::modifyRegexpByMetadata($payload, $metadata, $this->getOffset(), $this->getPrefix(), $this->getOffset());
		return $payload;
	}
	
	/**
	 * @return mixed
	 */
	public function getPayload(){
		if(!$this->payload){
			
			$payload = $this->wrapped->getPayload();
			$metadata = $this->wrapped->getMetadata();
			
			$payload = $this->modifyBy($payload, $metadata);
			
			$alias = $this->getAlias();
			if($alias){
				$payload = "(?<{$alias}>{$payload})";
			}else{
				$payload = "({$payload})";
			}
			$this->payload = $payload;
		}
		return $this->payload;
	}
	
	/**
	 * @return string
	 */
	public function getPcre(){
		if(!$this->pcre){
			$pattern = $this->wrapped->getPayload();
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
			$modifiers = $this->getModifiers();
			$this->sketch = ($modifiers? '(?'.$modifiers.':'.$payload.')' :$payload);
		}
		return $this->sketch;
		
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


