<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: php-regex
 */

namespace Jungle\Regex\Pattern\Composition;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class PatternTemplate
 * @package Jungle\Regex\Pattern\Composition
 */
class PatternTemplate extends PatternLine{
	
	protected $templateArguments = [];
	
	protected $template;
	
	protected $templateProcessed = false;
	
	/**
	 * PatternTemplate constructor.
	 * @param string $template
	 * @param array $patterns
	 */
	public function __construct($template, array $patterns = null){
		$this->template = $template;
		$this->templateArguments = (array)$patterns;
	}
	
	/**
	 * @param $template
	 * @return $this
	 */
	public function setTemplate($template){
		$this->template = $template;
		return $this;
	}
	
	/**
	 * @return string
	 */
	public function getTemplate(){
		return $this->template;
	}
	
	/**
	 *
	 */
	public function _processTemplate(){
		if(!$this->templateProcessed){
			if(preg_match_all(
				'@\\\\\\\\|\\\\\{|[^\{]+|\{\{([^\}]+)\}\}@',
				$this->template,
				$matches,
				PREG_SET_ORDER
			)){
				foreach($matches as $m){
					if(isset($m[1])){
						$ph = trim($m[1]);
						
						$capture = true;
						if(substr($ph, 0, 2) === '?:'){
							$ph = substr($ph, 2);
							$capture = false;
						}
						
						$isDefault = false;
						switch(substr($ph, -1)){
							case '+':
								$mMod = [ 1, null ];
								break;
							case '*':
								$mMod = [ 0, null ];
								break;
							case '?':
								$mMod = [ 0, 1 ];
								break;
							default:
								$mMod = [ 1, 1 ];
								$isDefault = true;
								break;
						}
						
						if(!$isDefault){
							$ph = substr($ph, 0, -1);
						}
						$ph = trim($ph);
						
						
						$ph = explode(':',$ph);
						
						$alias = isset( $ph[1])? $ph[1]:$ph[0];
						if(is_numeric($alias)) $alias = intval($alias);
						
						$ph = $ph[0];
						
						
						$subpattern = $this->getPatternById($ph);
						$this->addChild($subpattern, $alias);
					}else{
						$this->addChild($m[0]);
					}
				}
			}
		}
	}
	
	protected function _processChildren(){
		$this->_processTemplate();
		return parent::_processChildren();
	}
	
	
	protected function getPatternById($id){
		if(isset($this->templateArguments[$id])){
			return $this->templateArguments[$id];
		}
		return '';
	}
	
	
}


