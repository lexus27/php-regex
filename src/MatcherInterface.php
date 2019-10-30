<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: php-regex
 */

namespace Jungle\Regex;

/**
 * @Author: Alexey Kutuzov <lexus.1995@mail.ru>
 * Interface MatcherInterface
 * @package Jungle\Regex
 */
interface MatcherInterface{
	
	
	/**
	 * @param $subject
	 * @return mixed
	 */
	public function match($subject);
	
	/**
	 * @param $subject
	 * @return mixed
	 */
	public function matchIn($subject);
	
	/**
	 * @param $subject
	 * @return mixed
	 */
	public function matchManyIn($subject);
	
	/**
	 * @param $subject
	 * @param $rules
	 * @return string
	 */
	public function replace($subject, $rules);
	
	/**
	 * @param $subject
	 * @return array
	 */
	public function split($subject);
	
	
}

