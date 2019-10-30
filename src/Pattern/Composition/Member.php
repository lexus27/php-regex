<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: php-regex
 */

namespace Jungle\Regex\Pattern\Composition;

use Jungle\Regex\Pattern\GroupsAwareTrait;
use Jungle\Regex\RegexUtils;
use Jungle\Regex\Result\ResultElement;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class Member
 * @package Jungle\Regex\Pattern\Composition
 */
abstract class Member implements MemberInterface{
	
	use GroupsAwareTrait;
	use MemberTrait;
	
}


