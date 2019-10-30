<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: php-regex
 */

namespace Jungle\Regex\Pattern;

/**
 * @Author: Alexey Kutuzov <lexus.1995@mail.ru>
 * Interface OutputAwareInterface
 * @package Jungle\Regex\Pattern\Composition
 */
interface OutputAwareInterface{
	
	/**
	 * @return string
	 */
	public function getPcre();
	
	/**
	 * @return string
	 */
	public function getSketch();
	
	/**
	 * @param $pattern
	 * @return string
	 */
	public function wrapAsPcre($pattern);
	
	/**
	 * @param $pattern
	 * @return string
	 */
	public function wrapAsSketch($pattern);
	
}

