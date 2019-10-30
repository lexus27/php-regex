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
 * Class MemberPlain
 * @package Jungle\Regex\Pattern\Composition\Member
 */
class MemberPlain extends MemberNeighbor{
	
	use GroupsAwareTrait;
	use MemberTrait;
	use OutputAwareTrait;
	use ModifierAwareTrait;
	
	/**
	 * MemberPlain constructor.
	 * @param $payload
	 */
	public function __construct($payload){
		$this->payload = preg_quote($payload);
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
	 * @param $offset
	 * @param $prefix
	 * @return ResultElement
	 */
	public function getResultMap($offset = 0, $prefix = ''){
		return null;
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


