<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: php-regex
 */

namespace Jungle\Regex\Pattern;


use Jungle\Regex\RegexUtils;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class ModifierAwareTrait
 * @package Jungle\Regex\Pattern
 */
trait ModifierAwareTrait{
	
	/** @var  null|string */
	protected $modifiers = '';
	
	/** @var  null|string */
	protected $delimiter = '@';
	
	/**
	 * @param $modifiers
	 * @param bool $merge
	 * @return $this
	 */
	public function setModifiers($modifiers, $merge = false){
		if(!$merge){
			$this->modifiers = '';
		}
		
		if(!is_array($modifiers))$modifiers = str_split($modifiers);
		
		foreach($modifiers as $modifier){
			$this->_mod($modifier, true);
		}
		
		return $this;
	}
	
	/**
	 * @return mixed
	 */
	public function getModifiers(){
		return $this->modifiers;
	}
	/**
	 * @return mixed
	 */
	public function getDelimiter(){
		return $this->delimiter;
	}
	
	/**
	 * @param $delimiter
	 * @return $this
	 */
	public function setDelimiter($delimiter){
		$this->delimiter = $delimiter;
		return $this;
	}
	
	
	/**
	 * @param bool|true $on
	 * @return bool
	 */
	public function multiline($on = null){
		return $this->_mod(RegexUtils::PCRE_MULTILINE, $on);
	}
	
	/**
	 * @param bool|true $on
	 * @return bool|Pattern
	 */
	public function dotAll($on = null){
		return $this->_mod(RegexUtils::PCRE_DOTALL, $on);
	}
	
	/**
	 * @param bool|true $on
	 * @return  bool|Pattern
	 */
	public function caseless($on = true){
		return $this->_mod(RegexUtils::PCRE_CASELESS, $on);
	}
	
	/**
	 * @param bool|true $on
	 * @return  bool|Pattern
	 */
	public function extended($on = null){
		return $this->_mod(RegexUtils::PCRE_EXTENDED, $on);
	}
	
	/**
	 * @param bool|true $on
	 * @return  bool|Pattern
	 */
	public function anchored($on = null){
		return $this->_mod(RegexUtils::PCRE_ANCHORED, $on);
	}
	
	/**
	 * @param bool|true $on
	 * @return  bool|Pattern
	 */
	public function dollarEndOnly($on = null){
		return $this->_mod(RegexUtils::PCRE_DOLLAR_END_ONLY, $on);
	}
	
	/**
	 * @param bool|true $on
	 * @return  bool|Pattern
	 */
	public function cached($on = null){
		return $this->_mod(RegexUtils::PCRE_CACHED, $on);
	}
	
	/**
	 * @param bool|true $on
	 * @return  bool|Pattern
	 */
	public function ungreddy($on = null){
		return $this->_mod(RegexUtils::PCRE_UNGREDDY, $on);
	}
	
	/**
	 * @param bool|true $on
	 * @return  bool|Pattern
	 */
	public function extra($on = null){
		return $this->_mod(RegexUtils::PCRE_EXTRA, $on);
	}
	
	/**
	 * @param bool|true $on
	 * @return  bool|Pattern
	 */
	public function unicode($on = null){
		return $this->_mod(RegexUtils::PCRE_UTF8, $on);
	}
	
	/**
	 * @param $modifier
	 * @param null $mode
	 *
	 * NULL: return boolean (has modifier)
	 * TRUE: return Pattern (add modifier)
	 * FALSE: return Pattern (remove modifier)
	 *
	 * @return ModifierAwareTrait|boolean
	 *
	 * if $mode == true: Pattern
	 * if $mode == false: Pattern
	 * if $mode === null: boolean
	 */
	private function _mod($modifier, $mode = null){
		if($mode === null){
			return strpos($this->modifiers,$modifier) !== false;
		}elseif($mode){
			if(strpos($this->modifiers,$modifier)===false){
				
				$this->modifiers.=$modifier;
				
				$this->_onModifiersChange();
				
			}
		}else{
			if(($i = strpos($this->modifiers,$modifier)) !== false){
				
				$this->modifiers = substr_replace($this->modifiers,'',$i,1);
				
				$this->_onModifiersChange();
				
			}
		}
		return $this;
	}
	
	protected function _onModifiersChange(){
		$this->pcre = null;
		$this->sketch = null;
		
	}
	
}


