<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-regex
 */

namespace Jungle\Regex\Pattern;
use Jungle\Regex\RegexUtils;


/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class Pattern
 * @package Jungle\Regex
 *
 * Анализирование:
 *  Групп и позиций start end
 *
 *  Групп модификаторов, контейнеров(?i:...) и инлайновых(?i)
 *  Общих модификаторов /.../i
 *
 *  Захватываемых групп (...)
 *  Именнованных групп ?P<name> ?<name> ?'name'
 *
 *  Ссылок на рекурсию шаблонов именованных (?&name) общий (?R)
 *  Ссылок на рекурсию шаблонов индексных (?1) (?-1) (?+1)
 *
 *  Ссылок на совпадения именованных \k{name} \g{name}
 *  Ссылок на совпадения индексных \g{1} \g{-1} \g{+1}
 *
 * Модификация:
 *  Переименнование именованных ссылок и групп (выставление префиксов)
 *  Смещение нумерованных ссылок и групп
 *
 *  Конвертация шаблонов с сохранением модификаторов между общим и локальным определением модификаторов
 *
 * Контекст под шаблона с абсолютными ссылками (алиасы)
 * onMatchedData(function(Context ){ Context->get })
 * onReplace(function(Context ){  })
 *
 */
abstract class Pattern implements PatternInterface{
	
	use ModifierAwareTrait;
	use OutputAwareTrait;
	use GroupsAwareTrait;
	
	/** @var  null|string */
	protected $pcre;
	
	/** @var  null|string */
	protected $sketch;
	
	
	/** @var  array */
	protected $aliases = [];
	
	/**
	 * @return string
	 */
	public function getPcre(){
		if(!$this->pcre){
			$this->pcre = $this->wrapAsPcre($this->getPayload());
		}
		return $this->pcre;
	}
	
	/**
	 * @return string
	 */
	public function getSketch(){
		if(!$this->sketch){
			$this->sketch = $this->wrapAsSketch($this->getPayload());
		}
		return $this->sketch;
	}
	
	
	
	
	/**
	 * @param array $aliases
	 * @return $this
	 */
	public function setMappingAliases( array $aliases){
		$this->aliases = [];
		foreach($aliases as $i => $alias){
			if($alias){
				$this->setMappingAlias($i+1, $alias);
			}
		}
		return $this;
	}
	
	/**
	 * @param $offset
	 * @param $alias
	 * @param bool $merge
	 * @return $this
	 */
	public function setMappingAlias($offset, $alias, $merge = false){
		if(!isset($this->aliases[$offset])){
			$this->aliases[$offset] = [];
		}
		if(!is_array($alias)){
			$alias = [$alias];
		}
		$this->aliases[$offset] = array_unique(($merge? array_merge($this->aliases[$offset], $alias):$alias));
		
		ksort($this->aliases);
		
		return $this;
	}
	
	/**
	 * @return array
	 */
	public function getMappingAliases(){
		return $this->aliases;
	}
	
}


