<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: php-regex
 */

namespace Jungle\Regex\Pattern\Composition;


use Jungle\Regex\Pattern\Pattern;
use Jungle\Regex\Pattern\PatternInterface;
use Jungle\Regex\Pattern\PatternLine as PLold;
use Jungle\Regex\Pattern\PatternSimple;
use Jungle\Regex\RegexUtils;

abstract class PatternDecorator extends Pattern{
	
	/** @var  PatternInterface */
	protected $wrapped;
	
	/**
	 * PatternDecorator constructor.
	 * @param PatternInterface $pattern
	 */
	public function __construct($pattern=null){
		$this->wrapped = $pattern;
	}
	
	/**
	 * @return PatternInterface|PatternSimple|PatternComposition|PatternLine
	 */
	public function getWrapped(){
		return $this->wrapped;
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
	
	/**
	 *
	 * get captured groups count
	 * @return mixed
	 */
	public function getCapturedGroupsCount(){
		return count($this->getMetadata()[RegexUtils::M_CAPTURED_COUNT]);
	}
	
	/**
	 * @return mixed
	 */
	public function getPayload(){
		return $this->wrapped->getPayload();
	}
	
	/**
	 * @return mixed
	 */
	public function getPcre(){
		return $this->wrapped->getPcre();
	}
	
	/**
	 * @return mixed
	 */
	public function getSketch(){
		return $this->wrapped->getPcre();
	}
	
	public function getModifiers(){
		return $this->modifiers;
	}
	
	public function multiline($on = null){
		$r = $this->wrapped->multiline($on);
		return is_null($on)?$r:$this;
	}
	
	public function dotAll($on = null){
		$r = $this->wrapped->dotAll($on);
		return is_null($on)?$r:$this;
	}
	
	public function caseless($on = true){
		$r = $this->wrapped->caseless($on);
		return is_null($on)?$r:$this;
	}
	
	public function extended($on = null){
		$r = $this->wrapped->extended($on);
		return is_null($on)?$r:$this;
	}
	
	public function anchored($on = null){
		$r = $this->wrapped->anchored($on);
		return is_null($on)?$r:$this;
	}
	
	public function dollarEndOnly($on = null){
		$r = $this->wrapped->dollarEndOnly($on);
		return is_null($on)?$r:$this;
	}
	
	public function cached($on = null){
		$r = $this->wrapped->cached($on);
		return is_null($on)?$r:$this;
	}
	
	public function ungreddy($on = null){
		$r = $this->wrapped->dollarEndOnly($on);
		return is_null($on)?$r:$this;
	}
	
	public function extra($on = null){
		$r = $this->wrapped->dollarEndOnly($on);
		return is_null($on)?$r:$this;
	}
	
	public function unicode($on = null){
		$r = $this->wrapped->dollarEndOnly($on);
		return is_null($on)?$r:$this;
	}
	
}


