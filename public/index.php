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
echo htmlspecialchars(print_r( RegexUtils::analyze_groups(
	'/(?>\w+)/gi'
),1));
echo '</pre>';



echo '<pre>Include references analyzing';
echo htmlspecialchars(print_r( RegexUtils::analyze_groups(
	'/(?<name>\d+) hello (?-1)/gi' , true
),1));
echo '</pre>';


echo '<pre>Include references analyzing';
echo htmlspecialchars(print_r( print_r( RegexUtils::analyze_groups(
	'/(?<name>\d+) hello \g{-1}/gi' , true
)),1));
echo '</pre>';


echo '<pre>modify_identifiers<br/>';
echo htmlspecialchars(print_r( RegexUtils::modify_identifiers(
	'/(?<name>\d+) hello \g{1} (?&name) (?R)/gi' , 5, 'herova_', 'pattern_1_ctx'
),1));
echo '</pre>';


echo '<pre>strip_backslashes  \'\A \\\\B\' <br/>';
echo htmlspecialchars(print_r( RegexUtils::strip_backslashes('\A \\\\B','AB'),1));
echo '</pre>';
