<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-regex
 */
namespace Jungle\Regex;
include '../vendor/autoload.php';



echo '<pre>';
print_r( RegexUtils::analyze_groups(
	'/(?|(.)|(.)|(.)|(.))/gi'
));
echo '</pre>';


echo '<pre>';
print_r( RegexUtils::analyze_groups(
	'/(?<name>...)/gi'
));
echo '</pre>';


echo '<pre>';
print_r( RegexUtils::analyze_groups(
	'/(?P>name)/gi'
));
echo '</pre>';

echo '<pre>inline modifiers1';
print_r( RegexUtils::analyze_groups(
	'/(?aisnd:...)/gi'
));
echo '</pre>';

echo '<pre>inline modifiers2';
print_r( RegexUtils::analyze_groups(
	'/(?aisnd)/gi'
));
echo '</pre>';


echo '<pre>inline modifiers2';
print_r( RegexUtils::analyze_groups(
	'/(?>\w+)/gi'
));
echo '</pre>';

