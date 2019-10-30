<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: php-regex
 */

namespace Jungle\Regex\Pattern\Composition;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class PatternLine
 * @package Jungle\Regex\Pattern\Composition
 */
class PatternLine extends PatternComposition{
	
	/**
	 * @param array $payloads
	 * @return string
	 */
	protected function compositeChildrenPayloads(array $payloads){
		return implode('', $payloads);
	}
}


