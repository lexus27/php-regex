<?php
/**
 * @created Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Project: php-regex
 */
namespace Jungle\Regex;
include '../vendor/autoload.php';

/**
 * Analyze groups
 */
echo '<h3>Analyze groups</h3>';
$pattern = '/(?:hello worlds (?P\'name\'named pattern))(?<name>named pattern)(?P<name>named pattern)/ias';
$analyzed_groups = RegexUtils::analyze_groups($pattern);
echo '<pre>Pattern: ';
echo htmlspecialchars($pattern);
echo '</pre>';
echo '<pre>Result: ';
print_r($analyzed_groups);
echo '</pre>';


/**
 * Decomposite regex
 */
echo '<h3>Decomposite regex</h3>';
$decomposite = RegexUtils::decomposite_regex($pattern);
echo '<pre>Pattern: ';
echo htmlspecialchars($pattern);
echo '</pre>';
echo '<pre>Result: ';
echo htmlspecialchars(print_r($decomposite,1));
echo '</pre>';





/**
 * To group
 */
echo '<h3>ToGroup</h3>';
$group = RegexUtils::to_group($pattern);
echo '<pre>Pattern: ';
echo htmlspecialchars($pattern);
echo '</pre>';
echo '<pre>Result: ';
echo htmlspecialchars($group);
// (?ias:(?:hello worlds (?P'name'named pattern))(?<name>named pattern)(?P<name>named pattern))
echo '</pre>';


/**
 * Decomposite regex
 * -------------------------------
 * bug fix for : [+\-]?[1-9][0-9]*
 * -------------------------------
 */
echo '<h3>Decomposite regex</h3>';
$pattern = '[+\-]?[1-9][0-9]*';
$decomposite = RegexUtils::decomposite_regex($pattern);
echo '<pre>Pattern: ';
echo htmlspecialchars($pattern);
echo '</pre>';
echo '<pre>Result: ';
echo htmlspecialchars(print_r($decomposite,1));
echo '</pre>';


/**
 * To Pcre from a flagged submask
 */
echo '<h3>To Pcre from a flagged submask</h3>';
$pattern = '(?ias:(?:hello worlds (?P\'name\'named pattern))(?<name>named pattern)(?P<name>named pattern))';
$pcre = RegexUtils::to_pcre($pattern);
echo '<pre>Pattern: ';
echo htmlspecialchars($pattern);
echo '</pre>';
echo '<pre>Result: ';
echo htmlspecialchars($pcre);
// /(?:hello worlds (?P'name'named pattern))(?<name>named pattern)(?P<name>named pattern)/ias
echo '</pre>';


/**
 * To pcre from a simple piece
 */
echo '<h3>To pcre from a simple piece</h3>';
$pattern = '(?:hello worlds (?P\'name\'named pattern))(?<name>named pattern)(?P<name>named pattern)';
$pcre = RegexUtils::to_pcre($pattern);
echo '<pre>Pattern: ';
echo htmlspecialchars($pattern);
echo '</pre>';
echo '<pre>Result: ';
echo htmlspecialchars($pcre);
// /(?:hello worlds (?P'name'named pattern))(?<name>named pattern)(?P<name>named pattern)/
echo '</pre>';

