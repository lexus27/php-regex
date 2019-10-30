<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: php-regex
 */

namespace Jungle\Regex\Pattern\Composition;

use Jungle\Regex\Pattern\GroupsAwareInterface;
use Jungle\Regex\Pattern\OutputAwareInterface;
use Jungle\Regex\Pattern\ResultMapAwareInterface;

/**
 * @Author: Alexey Kutuzov <lexus.1995@mail.ru>
 * Interface MemberInterface
 * @package Jungle\Regex\Pattern\Composition
 */
interface MemberInterface extends ResultMapAwareInterface, GroupsAwareInterface, OutputAwareInterface{
	
	/**
	 * @return string
	 */
	public function getPrefix();
	
	/**
	 * @return null|string
	 */
	public function getAlias();
	
	/**
	 * @return int|null
	 */
	public function getOffset();
	
	
	/**
	 * @return null|string
	 */
	public function setAlias($alias);
	
	/**
	 * @return int|null
	 */
	public function setOffset($offset);
	
	
}

