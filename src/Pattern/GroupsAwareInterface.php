<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: php-regex
 */

namespace Jungle\Regex\Pattern;

/**
 * @Author: Alexey Kutuzov <lexus.1995@mail.ru>
 * Interface GroupsAwareInterface
 * @package Jungle\Regex\Pattern
 */
interface GroupsAwareInterface{
	
	/**
	 * @return string
	 */
	public function getPayload();
	
	/**
	 * @return array
	 */
	public function getMetadata();
	
	/**
	 * @return mixed
	 */
	public function getCapturedGroupsCount();
	
	/**
	 * @return array
	 */
	public function getCapturedGroupsWithNames();
	
	/**
	 * @return array
	 */
	public function getCapturedGroupsMeta();
	
	/**
	 * @return array
	 */
	public function getLinksToMatchesMeta();
	
	/**
	 * @return array
	 */
	public function getLinksToPatternsMeta();
	
}

