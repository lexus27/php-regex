<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: php-regex
 */

namespace Jungle\Regex\Pattern\Composition;

/**
 * @Author: Alexey Kutuzov <lexus.1995@mail.ru>
 * Interface ParentAwareInterface
 * @package Jungle\Regex\Pattern\Composition
 */
interface ParentAwareInterface{
	
	/**
	 * @return PatternComposition|null
	 */
	public function getParent();
	
}

