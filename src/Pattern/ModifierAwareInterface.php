<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: php-regex
 */

namespace Jungle\Regex\Pattern;

/**
 * @Author: Alexey Kutuzov <lexus.1995@mail.ru>
 * Interface ModifierAwareInterface
 * @package Jungle\Regex\Pattern
 */
interface ModifierAwareInterface{
	
	/**
	 * @return string
	 */
	public function getModifiers();
	
	/**
	 * @param string $modifiers
	 * @param bool $merge
	 * @return $this
	 */
	public function setModifiers($modifiers, $merge = false);
	
	/**
	 * @param bool|true $on
	 * @return $this
	 */
	public function multiline($on = null);
	
	/**
	 * @param bool|true $on
	 * @return $this
	 */
	public function dotAll($on = null);
	
	/**
	 * @param bool|true $on
	 * @return $this
	 */
	public function caseless($on = true);
	
	/**
	 * @param bool|true $on
	 * @return $this
	 */
	public function extended($on = null);
	
	/**
	 * @param bool|true $on
	 * @return $this
	 */
	public function anchored($on = null);
	
	/**
	 * @param bool|true $on
	 * @return $this
	 */
	public function dollarEndOnly($on = null);
	
	/**
	 * @param bool|true $on
	 * @return $this
	 */
	public function cached($on = null);
	
	/**
	 * @param bool|true $on
	 * @return $this
	 */
	public function ungreddy($on = null);
	
	/**
	 * @param bool|true $on
	 * @return $this
	 */
	public function extra($on = null);
	
	/**
	 * @param bool|true $on
	 * @return $this
	 */
	public function unicode($on = null);
	
	
}

