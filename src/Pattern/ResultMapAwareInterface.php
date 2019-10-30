<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: php-regex
 */

namespace Jungle\Regex\Pattern;

use Jungle\Regex\Result\ResultElement;

/**
 * @Author: Alexey Kutuzov <lexus.1995@mail.ru>
 * Interface ResultMapAwareInterface
 * @package Jungle\Regex\Pattern
 */
interface ResultMapAwareInterface{
	
	/**
	 * @param $offset
	 * @param $prefix
	 * @return ResultElement
	 */
	public function getResultMap($offset=0, $prefix='');
	
	/**
	 * @param $offset
	 * @param $prefix
	 * @return array
	 */
	public function getResultMapArray($offset=0, $prefix='');
	
}

