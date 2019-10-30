<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: php-regex
 */

namespace Jungle\Regex\Pattern;

/**
 * @Author: Alexey Kutuzov <lexus.1995@mail.ru>
 * Interface SuitableAwareInterface
 * @package Jungle\Regex\Pattern
 */
interface SuitableAwareInterface{
	
	/**
	 * @return mixed
	 */
	public function getSuitableCases();
	
	/**
	 * @return mixed
	 */
	public function getRandomSuitable();
	
	/**
	 * @return mixed
	 */
	public function generateSuitable();
	
}

