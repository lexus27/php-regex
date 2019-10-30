<?php
namespace Jungle\Regex;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class RegexUtils
 * @package Jungle\Regex
 */
class RegexUtils{

	/**
	 * Version for this package
	 */
	const VERSION = '0.0.1';


	/**
	 * /s (@PCRE_DOTALL) Если данный модификатор используется, метасимвол "точка" в шаблоне соответствует всем
	 *     символам, включая перевод строк. Без него - всем, за исключением переводов строк. Этот модификатор
	 *     эквивалентен записи /s в Perl. Класс символов, построенный на отрицании, например [^a], всегда
	 *     соответствует переводу строки, независимо от наличия этого модификатора.
	 */
	const PCRE_DOTALL = 's';


	/**
	 * /m (@PCRE_MULTILINE) По умолчанию PCRE обрабатывает данные как однострочную символьную строку (даже если она
	 *     содержит несколько разделителей строк). Метасимвол начала строки '^' соответствует только началу
	 *     обрабатываемого текста, в то время как метасимвол "конец строки" '$' соответствует концу текста, либо
	 *     позиции перед завершающим текст переводом строки (в случае, если модификатор D не установлен). В Perl
	 *     ситуация полностью аналогична. Если этот модификатор используется, метасимволы "начало строки" и "конец
	 *     строки" также соответствуют позициям перед произвольным символом перевода и строки и, соответственно,
	 *     после, как и в самом начале и в самом конце строки. Это соответствует Perl-модификатору /m. В случае,
	 *     если обрабатываемый текст не содержит символов перевода строки, либо шаблон не содержит метасимволов '^'
	 *     или '$', данный модификатор не имеет никакого эффекта.
	 */
	const PCRE_MULTILINE = 'm';

	/**
	 * /i (@PCRE_CASELESS) Если этот модификатор используется, символы
	 *      в шаблоне соответствуют символам как верхнего, так и нижнего регистра.
	 *      Нужно понимать что по умолчанию регистр учитывается только по одному байту,
	 *      Это значит что для мультибайтовых кодировок , регистр будет учитываться только
	 *      в случае наличия модификатора \u @see RegexUtils::PCRE_UTF8
	 *      Иначе парсер не поймет что строчная "Б" эквивалентна прописной "б",
	 *      в регистронезависимом сравнении. Для него это будут просто разные байты
	 */
	const PCRE_CASELESS = 'i';

	/**
	 * /x (@PCRE_EXTENDED) Если используется данный модификатор, неэкранированные пробелы, символы табуляции и пустой
	 *     строки будут проигнорированы в шаблоне, если они не являются частью символьного класса. Также
	 *     игнорируются все символы между неэкранированным символом '#' (если он не является частью символьного
	 *     класса) и символом перевода строки (включая сами символы '\n' и '#'). Это эквивалентно Perl-модификатору
	 *     /x, и позволяет размещать комментарий в сложных шаблонах. Замечание: это касается только символьных
	 *     данных. Пробельные символы не фигурируют в служебных символьных последовательностях, к примеру, в
	 *     последовательности '(?(', открывающей условную подмаску.
	 */
	const PCRE_EXTENDED = 'x';

	/**
	 * /A (@PCRE_ANCHORED) Если используется данный модификатор, соответствие шаблону будет достигаться только в том
	 *     случае, если он "заякорен", т.е. соответствует началу строки, в которой производится поиск. Того же
	 *     эффекта можно достичь подходящей конструкцией с вложенным шаблоном, которая является единственным
	 *     способом реализации этого поведения в Perl.
	 */
	const PCRE_ANCHORED = 'A';


	/**
	 * /D (@PCRE_DOLLAR_ENDONLY) Если используется данный модификатор, метасимвол $ в шаблоне соответствует только
	 *     окончанию обрабатываемых данных. Без этого модификатора метасимвол $ соответствует также позиции перед
	 *     последним символом, в случае, если им является перевод строки (но не распространяется на любые другие
	 *     переводы строк). Данный модификатор игнорируется, если используется модификатор m. В языке Perl
	 *     аналогичный модификатор отсутствует.
	 */
	const PCRE_DOLLAR_END_ONLY = 'D';


	/**
	 * /S (@PCRE_CACHED)   В случае, если планируется многократно использовать шаблон, имеет смысл потратить немного больше времени
	 *     на его анализ, чтобы уменьшить время его выполнения. В случае, если данный модификатор используется,
	 *     проводится дополнительный анализ шаблона. В настоящем это имеет смысл только для "незаякоренных"
	 *     шаблонов, не начинающихся с какого-либо определенного символа.
	 */
	const PCRE_CACHED = 'S';


	/**
	 * /U (@PCRE_UNGREEDY) Этот модификатор инвертирует жадность квантификаторов, таким образом они по умолчанию не
	 *     жадные. Но становятся жадными, если за ними следует символ ?. Такая возможность не совместима с Perl.
	 *     Его также можно установить с помощью (?U) установки модификатора внутри шаблона или добавив знак вопроса
	 *     после квантификатора (например, .*?).
	 */
	const PCRE_UNGREDDY = 'U';



	/**
	 * /X (@PCRE_EXTRA) Этот модификатор включает дополнительную функциональность PCRE, которая не совместима с Perl:
	 *     любой обратный слеш в шаблоне, за которым следует символ, не имеющий специального значения, приводят к
	 *     ошибке. Это обусловлено тем, что подобные комбинации зарезервированы для дальнейшего развития. По
	 *     умолчанию же, как и в Perl, слеш со следующим за ним символом без специального значения трактуется как
	 *     опечатка. На сегодняшний день это все возможности, которые управляются данным модификатором
	 */
	const PCRE_EXTRA = 'X';


	/**
	 * /u (@PCRE_UTF8) Этот модификатор включает дополнительную функциональность PCRE, которая не совместима с Perl:
	 *     шаблон и целевая строка обрабатываются как UTF-8 строки. Модификатор u доступен в PHP 4.1.0 и выше для
	 *     Unix-платформ, и в PHP 4.2.3 и выше для Windows платформ. Валидность UTF-8 в шаблоне и целевой строке
	 *     проверяется начиная с PHP 4.3.5. Недопустимая целевая строка приводит к тому, что функции preg_* ничего
	 *     не находят, а неправильный шаблон приводит к ошибке уровня E_WARNING. Пятый и шестой октеты UTF-8
	 *     последовательности рассматриваются недопустимыми с PHP 5.3.4 (согласно PCRE 7.3 2007-08-28); ранее они
	 *     считались допустимыми.
	 */
	const PCRE_UTF8 = 'u';
	
	
	
	const M_CAPTURED_COUNT = 'capturedCount';
	const M_POSITIONS = 'positions';
	const M_TYPES = 'types';
	const M_DATA = 'data';
	
	
	const A_LINK_COVERER = 'coverer';
	
	
	static $pcre_modifiers = [
		self::PCRE_ANCHORED,
		self::PCRE_CACHED,
		self::PCRE_CASELESS,
		self::PCRE_DOLLAR_END_ONLY,
		self::PCRE_DOTALL,
		self::PCRE_EXTENDED,
		self::PCRE_EXTRA,
		self::PCRE_MULTILINE,
		self::PCRE_UNGREDDY,
		self::PCRE_UTF8
	];




	const SYM_SPACE         = '\s';// символьный-класс пробельные символы включая перевод строки или табуляцию
	const SYM_NOT_SPACE     = '\S';// символьный-класс не пробельные символы

	const SYM_WORD          = '\w';// символьный-класс словоформирующие символы, это буквы, цифры, знак нижнего подчеркивания
	const SYM_NOT_WORD      = '\W';// символьный-класс не словоформирующие символы

	const SYM_DIGIT         = '\d';// символьный-класс цифры
	const SYM_NOT_DIGIT     = '\D';// символьный-класс не цифры

	const SYM_BOUND         = '\b';// псевдо символьный-класс ограничитель слова
	const SYM_NOT_BOUND     = '\B';// псевдо символьный-класс не ограничитель слова

	const SYM_DATA_START    = '\A';// начало данных
	const SYM_DATA_END      = '\Z';// конец данных

	const SYM_LINE_START    = '^';// начало строки
	const SYM_LINE_END      = '$';// конец строки


	const GRP_COMMENT                       = '(?#...)';    // комментарий

	const GRP_POSITIVE_LOOKAHEAD            = '(?=...)';    //   ?=  указывает что после этой группы, должно стоять того что указано в шаблоне
	const GRP_NEGATIVE_LOOKAHEAD            = '(?!...)';    //   ?!  указывает что после этой группы, не должно стоять того что указано в шаблоне
	const GRP_POSITIVE_LOOKBEHIND           = '(?<=...)';   // ?<= указывает что до этой группы, должно стоять того что указано в шаблоне
	const GRP_NEGATIVE_LOOKBEHIND           = '(?<!...)';   // ?<! указывает что до этой группы, не должно стоять того что указано в шаблоне

	const GRP_ENCLOSED                      = '(?:...)';    // просто группа, которая не захватывается
	const GRP_ATOMIC                        = '(?>...)';    // атомическая группа

	const GRP_CONDITIONAL                   = '(?(...)yes|no)'; // условие где за булеву берется результат маски по ссылке

	const GRP_INLINE_MODIFIERS_POINT        = '(?imsxXU)';      // указывает модификаторы для последующих условностей
	const GRP_INLINE_MODIFIERS_WRAP         = '(?imsxXU:...)';  // указывает модификаторы для собственной незахватываемой группы, то есть образует группу где внутри себя действуют эти модификаторы


	const GRP_BASIC                         = '(...)';      // именованная подмаска/ устарело
	const GRP_BRANCH_RESET                  = '(?|...)';    // подмаска не захватывает все любые вложенные в неё подмаски

	const GRP_NAMED_ANG_OLD                 = '(?P<name>...)';  // именованная подмаска/ устарело
	const GRP_NAMED_QUOT                    = "(?'name'...)";   // именованная подмаска
	const GRP_NAMED_ANG                     = '(?<name>...)';   // именованная подмаска

	const GRP_RECURSE_ENTIRE                = '(?R)';       // проверяет в этом месте вхождения на соотвентствие шаблону по ссылке
	const GRP_RECURSE_NAMED                 = '(?&name)';   // проверяет в этом месте вхождения на соотвентствие шаблону по ссылке
	const GRP_RECURSE_AFTER_NAMED           = '(?P>name)';  // проверяет в этом месте вхождения на соотвентствие шаблону по ссылке

	const GRP_RECURSE_NTN_ABS               = '(?1)';       // проверяет в этом месте вхождения на соотвентствие шаблону по ссылке
	const GRP_RECURSE_NTN_BEFORE            = '(?-1)';      // проверяет в этом месте вхождения на соотвентствие шаблону по ссылке
	const GRP_RECURSE_NTN_AFTER             = '(?+1)';      // проверяет в этом месте вхождения на соотвентствие шаблону по ссылке



	const MATCH_SUBPATTERN_PREV_NTN_ABS     = "\\1"; // ссылка на результат уже проведенного сопоставления

	const MATCH_SUBPATTERN_PREV_NAMED_QUOT  = "\\g'name'"; // ссылка на результат уже проведенного сопоставления / только на предидущую
	const MATCH_SUBPATTERN_PREV_NAMED_ANG   = "\\g<name>"; // ссылка на результат уже проведенного сопоставления / только на предидущую
	const MATCH_SUBPATTERN_PREV_NAMED_FIG   = "\\g{name}"; // ссылка на результат уже проведенного сопоставления / только на предидущую

	const MATCH_SUBPATTERN_NAMED_GRP        = '(?P=name)'; // ссылка на результат уже проведенного сопоставления
	const MATCH_SUBPATTERN_NAMED_QUOT       = "\\k'name'"; // ссылка на результат уже проведенного сопоставления
	const MATCH_SUBPATTERN_NAMED_ANG        = "\\k<name>"; // ссылка на результат уже проведенного сопоставления
	const MATCH_SUBPATTERN_NAMED_FIG        = "\\k{name}"; // ссылка на результат уже проведенного сопоставления


	const MATCH_SUBPATTERN_NTN_ABS          = "\\g1";       // ссылка на результат уже проведенного сопоставления
	const MATCH_SUBPATTERN_NTN_FIG_ABS      = "\\g{1}";     // ссылка на результат уже проведенного сопоставления
	const MATCH_SUBPATTERN_NTN_FIG_AFTER    = "\\g{+1}";    // ссылка на результат уже проведенного сопоставления
	const MATCH_SUBPATTERN_NTN_FIG_BEFORE   = "\\g{-1}";    // ссылка на результат уже проведенного сопоставления

	const MATCH_SUBPATTERN_NTN_QUOT_ABS     = "\\g'1'";     // ссылка на результат уже проведенного сопоставления
	const MATCH_SUBPATTERN_NTN_QUOT_AFTER   = "\\g'+1'";    // ссылка на результат уже проведенного сопоставления
	const MATCH_SUBPATTERN_NTN_QUOT_BEFORE  = "\\g'-1'";    // ссылка на результат уже проведенного сопоставления

	const MATCH_SUBPATTERN_NTN_ANG_ABS      = "\\g<1>";     // ссылка на результат уже проведенного сопоставления
	const MATCH_SUBPATTERN_NTN_ANG_AFTER    = "\\g<+1>";    // ссылка на результат уже проведенного сопоставления
	const MATCH_SUBPATTERN_NTN_ANG_BEFORE   = "\\g<-1>";    // ссылка на результат уже проведенного сопоставления


// в tpl нужно будет игнорировать внутренние маски шаблонов типов
// как вставить один шаблон в другой, чтобы при этом имена масок не конфликтовали:
//      А отчищать маски подстановочного,
//      Б использовать смещение и подсчет индексных масок

// Темплейт - много под шаблонов, реплейсинг, матчинг, сопостовление
// проход по обработчиком каждого шаблона с передачей объекта-информатора
// который дает информацию только в контексте шаблона от нулевого индекса
// capture->getParam(0) -> params[ prev_offset + 0 ]
// pattern->onMatched(function(CallerContext $o){ $o[0] })
// template - match | match_all | replace(pattern-retain | pattern-replace)


	/**
	 * @param $char
	 * @return int
	 */
	public static function get_case($char){
		return strtolower($char) === $char ? CASE_LOWER : CASE_UPPER;
	}

	// todo: Поддержка модификации именованных шаблонов
	// индексные группы:        [ (...) ]
	// именованные группы:      [ (?<name>...), (?P<name>...), (?P'name'...) ] (?<name>...)
	// рекурсивные повторения:  [ (?P>name), (?&name), (?1), (?R) ]
	// ссылки на совпадение:    [ \1, \g{-1}, \k'name' ]

	// todo: Поддержка рекурсивных повторений: (?P>name) или (?&name), (?1), (?R)

	// todo: Изменение индексных масок offset для шаблона в котором используются индексные рекурсивные ссылки
	// (?1) > (?{1 + $offset})

	// todo: Изменение именованых масок (обратных и рекурсивных ссылок, и именованых груп)
	// (?<name>...) > (?<prefix+name>...)
	// (?P<name>...) > (?P<prefix+name>...)
	// (?P'name'...) > (?P'prefix+name'...)


	/** @var array */
	protected static $not_capturing_signs = [
		'?#',   //comment
		'?:',   // not capture
		'?>',   // atomic
		'?=',   // look ahead
		'?!',   // not look ahead
		'?<=',  // look behind
		'?<!',  // not look behind
		'?P=',  // named sub pattern link
		'?P>',  // recurse sub-pattern
		'?(',   // conditional sign
		'?R)',  // recursive global repeat pattern
		'?&',   // recursive sub pattern
		'*',    // special token for inline options
	];

	/** @var array */
	protected static $named_groups = [
		'?<>', '?P<>', '?\'\''
	];


	public static $pcre_delimiters = [
		'#', '~', '@',
		'/', '+', '%'
	];

	public static $brackets = [
		'{' => '}',
		'[' => ']',
		'(' => ')',
	];

	public static $special_chars = [
		
		'^', '~', '@', '#', '$', '&',       // specials
		
		'%', '*', '-', '+', '=',            // math
		
		'\\', '/', '|',                     // slashed backslashes and |
		
		';', ':', '?', '!', '.', ',',       // punctuation
		
		'"', '\'', '`',                     // quotation
		
		'{','}', '[',']','(',')','<', '>'   // brackets
	];

	public static $digit_chars = [
		'0', '1', '2', '3', '4',
		'5', '6', '7', '8', '9'
	];

	public static $punctuation_chars = [
		'.','.',';',
		':','!','?'
	];
	public static $quotation_chars = [
		'`','"','\'',
	];


	/**
	 * @param $pattern
	 * @param string $modifiers
	 * @param string $delimiter
	 * @return string
	 */
	public static function escape($pattern, $modifiers = '', $delimiter = '@'){
		return $delimiter . addcslashes($pattern, $delimiter) . $delimiter . $modifiers;
	}

	/**
	 * @param $pattern
	 * @param $value
	 * @param string $modifiers
	 * @return bool
	 */
	public static function check_value($pattern, $value, $modifiers = 'S'){
		return preg_match('@^' . addcslashes($pattern, '@') . '$@' . $modifiers, $value) > 0;
	}

	/**
	 * @param $pattern
	 * @return bool
	 */
	public static function has_captures($pattern){
		$len = strlen($pattern);
		for($i = 0; $i < $len; $i++){
			$token = $pattern{$i};
			if($token === '('){
				if(self::count_repeat_before($pattern, $i,'\\') % 2 == 0){
					$capture = self::_is_capturing_group($pattern, $i);
					if($capture) return true;
				}
			}
		}
		return false;

	}

	/**
	 * @param $pattern
	 * @param $position
	 * @return bool
	 */
	protected static function _is_capturing_group($pattern, $position){
		if(!self::byte_has_after($pattern, $position, self::$not_capturing_signs)){

			// inline modifiers
			if(substr($pattern,$position+1,1)==='?' &&  !in_array(self::byte_read_after($pattern, $position,1,1),self::$special_chars)){
				return false;
			}

			return true;
		}
		return false;
	}
	
	const A_TOTAL_GROUPS = 'total';
	
	const A_CAPTURED_INDEXED = 'capturedIndexed';
	const A_CAPTURED = 'captured';
	const A_SUBMASK_POS         = 0;
	const A_SUBMASK_LEN         = 1;
	const A_SUBMASK_INDEX       = 2;
	const A_SUBMASK_NAME        = 3;
	const A_SUBMASK_COVERER     = 4;
	
	const A_TRANSPARENT = 'transparent';
	const A_TRANSPARENT_POS         = 0;
	const A_TRANSPARENT_LEN         = 1;
	const A_TRANSPARENT_INDEX       = 2;
	const A_TRANSPARENT_COVERER     = 3;
	
	const A_LINKS_TO_MATCHES  = 'links_to_matches';
	const A_LINKS_TO_PATTERNS = 'links_to_patterns';
	
	const A_LINK_ABSOLUTE        = 'absolute';
	const A_LINK_NAME            = 'name';
	const A_LINK_OFFSET          = 'offset';
	const A_LINK_REF             = 'reference_position';
	
	/**
	 *
	 * todo: Доработать корректное отличие Захватываемой маски, от не захватываемой
	 * todo: Данная функция может работать некорректно для рекурсивных повторений (?P>name) или (?&name), (?1), (?R)
	 *
	 * @param $pattern
	 * @param bool $include_references
	 * @return array
	 *
	 *            total: All groups count
	 *            captured: indexInResult => [opened_pos, closed_pos, order_index, name, is_coverer(?|...)]
	 *            transparent: [opened_pos, closed_pos, order_index, covered(in coverer)]
	 *
	 *            links_to_matches: [
	 *                'absolute'              => true,
	 *                'name'                  => $name,
	 *                'offset'                => null,
	 *                'reference_position'    => [ $i , $length+1 ]
	 *            ]
	 *            links_to_patterns: [
	 *                'absolute'              => true,
	 *                'name'                  => null,
	 *                'offset'                => null,
	 *                'reference_position'    => [ $i , $length+1 ]
	 *            ];
	 *
	 * preg_match('....', 'some text', $matches)
	 * foreach(returned[captured] as $i => $c){
	 * $matches[$i]
	 * }
	 *
	 *
	 * $links_to_matches[] = [
	 *     'absolute'              => true,
	 *     'name'                  => $name,
	 *     'offset'                => null,
	 *     'reference_position'    => [ $i , $length+1 ]
	 * ];
	 * $links_to_patterns[] = [
	 *     'absolute'              => true,
	 *     'name'                  => null,
	 *     'offset'                => null,
	 *     'reference_position'    => [ $i , $length+1 ]
	 * ];
	 * $captured_groups[]       = [ $pos, $i, $absolute_index, $name, $is_coverer ];
	 * $transparent_groups[]    = [ $pos, $i, $absolute_index, null, !!$covering];
	 *
	 * @throws RegexException
	 */
	public static function analyze_groups($pattern, $include_references = false){
		$len = strlen($pattern);
		$total_opened = 0;
		$index = 0;
		$opened = [ ];
		$captured_groups = [ ];
		$transparent_groups = [ ];

		$links_to_patterns = [ ];
		$links_to_matches  = [ ];

		$covering = [];
		for($i = 0; $i < $len; $i++){
			$token = $pattern{$i};
			if($token === '('){
				if(self::count_repeat_before($pattern, $i,'\\') % 2 == 0){

					if($include_references){
						//--------------deep analyzing--------------------
						$__ = substr($pattern,$i+1);
						if(preg_match('@^\?P=(\w+)\)@', $__, $m, PREG_OFFSET_CAPTURE)){
							$name = $m[1][0];
							$length = strlen($m[0][0]);
							
							$links_to_matches[] = $linkToMatch = [
								self::A_LINK_ABSOLUTE => true,
								self::A_LINK_NAME     => $name,
								self::A_LINK_OFFSET   => null,
								self::A_LINK_REF      => [ $i , $length+1 ]
							];
							
							$i+= $length;
							continue;
						}elseif(preg_match('@^\?R\)@', $__, $m, PREG_OFFSET_CAPTURE)){
							$length = strlen($m[0][0]);
							$links_to_patterns[] = $linkToMatch = [
								self::A_LINK_ABSOLUTE => true,
								self::A_LINK_NAME     => null,
								self::A_LINK_OFFSET   => null,
								self::A_LINK_REF      => [ $i , $length+1 ]
							];
							
							$i+= $length;
							continue;
						}elseif(preg_match('@^\?(?:\&(\w+)|([-+]\d+))\)@', $__, $m, PREG_OFFSET_CAPTURE)){
							$length = strlen($m[0][0]);
							$name = isset($m[1][0]) && is_string($m[1][0])?$m[1][0]:null;
							$offset = isset($m[2][0]) && is_string($m[2][0])?$m[2][0]:null;
							if($name){
								$links_to_patterns[] = $linkToMatch = [
									self::A_LINK_ABSOLUTE => true,
									self::A_LINK_NAME     => $name,
									self::A_LINK_OFFSET   => null,
									self::A_LINK_REF      => [ $i , $length+1 ]
								];
							}else{
								$links_to_patterns[] = $linkToMatch = [
									self::A_LINK_ABSOLUTE => is_numeric(substr($offset,0,1)),
									self::A_LINK_NAME     => null,
									self::A_LINK_OFFSET   => $offset,
									self::A_LINK_REF      => [ $i , $length+1 ]
								];
								
							}
							
							$i+= $length;
							continue;
						}
						//----end----deep analyzing--------------------
					}

					$is_coverer = false;
					if(substr($pattern, $i+1, 2) === '?|'){
						// some indexed
						$capture = true;
						$is_coverer = true;
						$covering[] = true;
					}else{
						$capture = self::_is_capturing_group($pattern, $i);
					}
					$name = $capture? self::find_group_name($pattern, $i+1): null;
					$opened[] = [ $i, $capture, $total_opened, $name, $is_coverer];
					$total_opened++;
				}
			}elseif($token === ')'){
				if(self::count_repeat_before($pattern, $i,'\\') % 2 == 0){
					if($opened){
						list($pos, $capture, $absolute_index, $name, $is_coverer) = array_pop($opened);

						if($is_coverer){
							array_pop($covering);
						}

						if($capture && !$covering){
							$index++;
							$captured_groups[] = [ $pos, $i, $absolute_index, $name, $is_coverer ];
						}else{
							$transparent_groups[] = [ $pos, $i, $absolute_index, null, !!$covering];
						}
					}else{
						throw new RegexException('Error have not expected closed groups!');
					}
				}
			}elseif($include_references && $token === '\\'){
				//----start----deep analyzing--------------------
				
				$start = $i;
				$i++;
				
				if(isset($pattern{$i})){
					$token = $pattern{$i};
					
					if(is_numeric($token)){
						$offset = $token;
						$length = strlen($offset)+1;
						$links_to_matches[] = [
							self::A_LINK_ABSOLUTE => true,
							self::A_LINK_NAME     => null,
							self::A_LINK_OFFSET   => $offset,
							self::A_LINK_REF      => [ $start , $length ]
						];
						$i+=$length;
					}elseif($token==='k'){
						$__ = substr($pattern,$i);
						if(preg_match('@^k\{(\w+)\}@',$__, $m,PREG_OFFSET_CAPTURE)
						   || preg_match('@^k\'(\w+)\'@',$__, $m,PREG_OFFSET_CAPTURE)
						   || preg_match('@^k\<(\w+)\>@',$__, $m,PREG_OFFSET_CAPTURE)
						){
							$name = $m[1][0];
							$length = strlen($m[0][0]);
							$links_to_matches[] = [
								self::A_LINK_ABSOLUTE => null,
								self::A_LINK_NAME     => $name,
								self::A_LINK_OFFSET   => null,
								self::A_LINK_REF      => [ $start, $length + 1 ]
							];
							$i+=$length;
						}
						
					}elseif($token==='g'){
						$__ = substr($pattern,$i);
						if(preg_match('@^g([-+]?\d+)@',$__, $m,PREG_OFFSET_CAPTURE)
						   || preg_match('@^g\{([-+]?\d+)\}@',$__, $m,PREG_OFFSET_CAPTURE)
						   || preg_match('@^g\'([-+]?\d+)\'@',$__, $m,PREG_OFFSET_CAPTURE)
						   || preg_match('@^g\<([-+]?\d+)\>@',$__, $m,PREG_OFFSET_CAPTURE)
						){
							$offset = $m[1][0];
							$signing = substr($offset,0,1);
							$length = strlen($m[0][0]);
							$links_to_matches[] = [
								self::A_LINK_ABSOLUTE => !in_array($signing,['+','-'], true),
								self::A_LINK_NAME     => null,
								self::A_LINK_OFFSET   => $offset,
								self::A_LINK_REF      => [ $start , $length+1 ]
							];
							$i+=$length;
						}
					}else{
						$i--;
					}
					
				}else{
					$i--;
				}
				
				
				//----end----deep analyzing--------------------
			}
		}
		if($opened){
			throw new RegexException(
				'Error have not closed opened groups by offset at \'' .
				implode('\' and \'', array_column($opened, 0)) . '\''
			);
		}
		usort($captured_groups, [self::class, '_sort_groups']);
		usort($transparent_groups, [self::class, '_sort_groups']);
		
		array_unshift($captured_groups, null);
		unset($captured_groups[0]);
		
		
		$result = [
			self::A_TOTAL_GROUPS => $total_opened,
			self::A_CAPTURED     => $captured_groups,
			self::A_TRANSPARENT  => $transparent_groups,
		];
		if($include_references){
			$result = array_replace($result,[
				self::A_LINKS_TO_MATCHES => $links_to_matches,
				self::A_LINKS_TO_PATTERNS  => $links_to_patterns,
			]);
		}
		return $result;
	}
	
	/**
	 * @param $pattern
	 * @return array
	 */
	static function analyzeRegexpMetadata($pattern){
		
		$__ = self::analyze_groups($pattern, true);
		
		$captured_groups    = $__[self::A_CAPTURED];
		$transparent_groups = $__[self::A_TRANSPARENT];
		$links_to_matches   = $__[self::A_LINKS_TO_MATCHES];
		$links_to_patterns  = $__[self::A_LINKS_TO_PATTERNS];
		
		$i = 0;
		$positionsMeta = [];
		$positionsType = [];
		$positionsData = [];
		
		foreach($captured_groups as $index => list($pos, $endPos, $absoluteIndex, $name, $isCoverer)){
			// Учет только именованных
			if($name){
				list($name, $nPos, $nLen) = $name;
				
				$positionsMeta[$i] = [$nPos, $nLen];
				$positionsType[$i] = self::A_CAPTURED;
				$positionsData[$i] = [
					self::A_LINK_NAME       => $name,
					self::A_LINK_OFFSET     => $index,
					self::A_LINK_COVERER    => $isCoverer,
				];
				$i++;
			}else{
				$positionsMeta[$i] = null;
				$positionsType[$i] = self::A_CAPTURED_INDEXED;
				$positionsData[$i] = [
					self::A_LINK_OFFSET     => $index,
					self::A_LINK_COVERER    => $isCoverer,
				];
				$i++;
			}
		}
		foreach($transparent_groups as list($pos, $endPos, $absoluteIndex, $isCoverer)){
			$positionsMeta[$i] = [$pos, $endPos - $pos];
			$positionsType[$i] = self::A_TRANSPARENT;
			$positionsData[$i] = null;
			$i++;
		}
		
		foreach($links_to_matches as $data){
			$positionsMeta[$i] = $data[self::A_LINK_REF];
			$positionsType[$i] = self::A_LINKS_TO_MATCHES;
			$positionsData[$i] = [
				self::A_LINK_ABSOLUTE => $data[self::A_LINK_ABSOLUTE],
				self::A_LINK_NAME => $data[self::A_LINK_NAME],
				self::A_LINK_OFFSET => $data[self::A_LINK_OFFSET],
			];
			$i++;
		}
		foreach($links_to_patterns as $data){
			$positionsMeta[$i] = $data[self::A_LINK_REF];
			$positionsType[$i] = self::A_LINKS_TO_PATTERNS;
			$positionsData[$i] = [
				self::A_LINK_ABSOLUTE => $data[self::A_LINK_ABSOLUTE],
				self::A_LINK_NAME => $data[self::A_LINK_NAME],
				self::A_LINK_OFFSET => $data[self::A_LINK_OFFSET],
			];
			$i++;
		}
		
		$positionsMeta = StringDataCarrier::sortByPosition($positionsMeta, true);
		
		return [
			self::M_CAPTURED_COUNT => count($captured_groups), // подсчет всех захватываемых масок
			self::M_POSITIONS      => $positionsMeta,
			self::M_TYPES          => $positionsType,
			self::M_DATA           => $positionsData
		];
	}
	
	/**
	 * @param $pattern
	 * @param int $offset
	 * @param null $prefix
	 * @param null $global_link_pat
	 * @return array [pattern, metadata]
	 */
	static function modifyRegexpByMetadata($pattern, $metadata, $offset=0, $prefix=null, $global_link_pat = null){
		$data = &$metadata[self::M_DATA];
		
		$values = [];
		
		$ltm    = array_keys($metadata[self::M_TYPES], self::A_LINKS_TO_MATCHES, true);
		$ltp    = array_keys($metadata[self::M_TYPES], self::A_LINKS_TO_PATTERNS, true);
		$c      = array_keys($metadata[self::M_TYPES], self::A_CAPTURED, true);
		
		foreach($ltm as $i){
			$link = &$data[$i];
			if($link[self::A_LINK_ABSOLUTE]){
				if($link[self::A_LINK_NAME]){
					$value = $link[self::A_LINK_NAME] = (  $prefix . $link[self::A_LINK_NAME]  );
					$value = '\k{'.(  $value  ).'}';
				}else{
					$value = $link[self::A_LINK_OFFSET] = (  $offset + $link[self::A_LINK_OFFSET]  );
					$value = '\g{'.(  $value  ).'}';
				}
				$values[$i] = $value;
			}
		}
		foreach($ltp as $i){
			$link = &$data[$i];
			if($link[self::A_LINK_ABSOLUTE]){
				$value = null;
				if($link[self::A_LINK_NAME]){
					$value = $link[self::A_LINK_NAME] = (  $prefix . $link[self::A_LINK_NAME]  );
				}elseif($link[self::A_LINK_OFFSET]){
					$value = $link[self::A_LINK_OFFSET] = (  $offset + $link[self::A_LINK_OFFSET]  );
				}elseif($global_link_pat){
					$value = $global_link_pat;
					if(!is_numeric($global_link_pat)){
						$link[self::A_LINK_NAME]    = $value;
						$link[self::A_LINK_OFFSET]  = null;
					}else{
						$link[self::A_LINK_NAME]    = null;
						$link[self::A_LINK_OFFSET]  = $value;
					}
				}
				if($value!==null){
					if(is_numeric($value)){
						$value = '(?'.(  $value  ).'}';
					}else{
						$value = '(?&'.(  $value  ).'}';
					}
					$values[$i] = $value;
				}
			}
		}
		foreach($c as $i){
			$link = &$data[$i];
			$value = $link[self::A_LINK_NAME] = (  $prefix . $link[self::A_LINK_NAME]  );
			$values[$i] = $value;
		}
		
		list($pattern, $metadata[self::M_POSITIONS]) = StringDataCarrier::replace($pattern, $metadata[self::M_POSITIONS], $values);
		
		return [$pattern, $metadata];
		
	}


	/**
	 * @param $pattern
	 * @param int $offset
	 * @param null $prefix
	 * @param null $global_link_pat
	 * @return mixed
	 * @throws RegexException
	 */
	static function modify_identifiers($pattern, $offset=0, $prefix=null, $global_link_pat = null){
		$metadata = self::analyze_groups($pattern,true);
		return self::modify_identifiers_metadata($pattern, $metadata, $offset, $prefix, $global_link_pat);
	}
	
	
	/**
	 * @param $pattern
	 * @param $metadata
	 * @param int $offset
	 * @param null $prefix
	 * @param null $global_link_pat
	 * @return mixed
	 */
	static function modify_identifiers_metadata($pattern, $metadata, $offset=0, $prefix=null, $global_link_pat = null){
		
		//$metadata = self::analyze_groups($pattern,true);
		
		$to_modify = [];
		foreach($metadata['links_to_matches'] as &$link){
			if($link['absolute']){
				$to_modify[$link['reference_position'][0]] = array_replace([
					'type' => 'links_to_matches',
					//'_ref' => &$link
				],$link);
			}
		}
		foreach($metadata['links_to_patterns'] as $link){
			if($link['absolute']){
				$to_modify[$link['reference_position'][0]] = array_replace([
					'type' => 'links_to_patterns',
					//'_ref' => &$link
				],$link);
			}
		}
		foreach($metadata['captured'] as $link){
			if(is_array($link[3])){
				$to_modify[$link[0]] = array_replace([
					'type' => 'groups',
					'name' => $link[3][0],
					'name_pos' => [$link[3][1], $link[3][2]],
					//'_ref' => &$link
				],$link);
			}
		}
		ksort($to_modify);
		
		foreach(array_reverse($to_modify) as $link){
			$pos = [0,0];
			$val = null;
			switch($link['type']){
				case 'links_to_matches':
					$pos = $link['reference_position'];
					if($link['name']){
						$val = '\k{'.($prefix . $link['name']).'}';
					}elseif($link['offset']){
						$val = '\g{'.($offset + $link['offset']).'}';
					}
					break;
				case 'links_to_patterns':
					$pos = $link['reference_position'];
					if($link['name']){
						$val = '(?&'.($prefix . $link['name']).')';
					}elseif($link['offset']){
						$val = '(?'.($offset + $link['offset']).')';
					}elseif($global_link_pat){
						if(is_numeric($global_link_pat)){
							$val = '(?'.($global_link_pat).')';
						}else{
							$val = '(?&'.($global_link_pat).')';
						}
					}
					break;
				case 'groups':
					$pos = $link['name_pos'];
					$val = ($prefix . $link['name']);
					break;
			}
			if($val){
				$pattern = substr_replace( $pattern , $val ,$pos[0],$pos[1]);
				// (?<string>[a-zA-Z]+(?<string_dot>\.))(?<int>\d+)
			}
		}
		return $pattern;
	}
	
	
	/**
	 * @param $string
	 * @param $position
	 * @return null|string
	 */
	static function find_group_name($string, $position){
		$name = null;$start = null;
		if('?P<' === ($n = substr($string, $position, 3)) || $n === '?P\''){
			$name = '';
			for($start = $i = $position + 3, $l = strlen($string); $i < $l; $i++){
				if(in_array($string{$i}, self::$special_chars, true)){
					break;
				}
				$name.=$string{$i};
			}
		}elseif('?<' === ($n = substr($string, $position, 2))){
			$name = '';
			for($start = $i = $position + 2, $l = strlen($string); $i < $l; $i++){
				if(in_array($string{$i}, self::$special_chars, true)){
					break;
				}
				$name.=$string{$i};
			}
		}
		return $name?[$name, $start, strlen($name)]:null;
	}

	/**
	 * @param $regex
	 * @return bool
	 */
	static function is_pcre($regex){
		$first_letter = substr($regex, 0, 1);
		switch(true){
			case in_array($first_letter,self::$pcre_delimiters, true):
				$len = strlen($regex);
				for($i = $len; $i; $i--){
					$char = $regex{$i};
					if($char === $first_letter){
						return true;
					}
				}
				break;
			case in_array($first_letter, array_keys(self::$brackets)):
				$closed = self::$brackets[$first_letter];
				$len = strlen($regex);
				for($i = $len; $i; $i--){
					$char = $regex{$i};
					if($char === $closed){
						// match
						return true;
					}
				}
				break;
		}
		return false;
	}

	/**
	 * @param $regex
	 * @return string
	 */
	static function to_group($regex){
		$composition = self::decomposite_regex($regex);
		return "(?{$composition['modifiers']}:{$composition['pattern']})";
	}

	/**
	 * Pattern to Pcre
	 * @param $regex
	 * @param string $delimiter
	 * @return string
	 * @throws RegexException
	 */
	static function to_pcre($regex, $delimiter = null){
		$composition = self::decomposite_regex($regex);
		$delimiter = $delimiter?:$composition['delimiter'];
		$modifiers = $composition['modifiers'];
		$regex = $composition['pattern'];
		$side_delimiter = false;
		if($delimiter === null || in_array($delimiter, self::$pcre_delimiters, true)){
			$right = $left = $delimiter?:'/';
			$side_delimiter = true;
		}elseif(isset(self::$brackets[$delimiter])){
			$left = $delimiter;
			$right = self::$brackets[$delimiter];
		}elseif(false !== $i = array_search($delimiter, self::$brackets)){
			$left = $i;
			$right = $delimiter;
		}else{
			//non valid delimiter
			throw new RegexException("Invalid delimiter '{$delimiter}'");
		}
		if($side_delimiter) $regex = addcslashes($regex,$delimiter);
		return "{$left}{$regex}{$right}{$modifiers}";
	}

	/**
	 * @param $regex
	 * @return array
	 */
	static function decomposite_regex($regex){
		if(substr($regex, 0 , 2) === '(?' && substr($regex, -1) === ')'){
			$len = strlen($regex);
			$groups = self::analyze_groups($regex);
			if($groups['transparent'] && $groups['transparent'][0][1] === $len-1){
				$modifiers = '';

				// такая вот выборка модификаторов посимвольно пока не дойдет до :
				for(
					$i = 2, $char = $regex{$i};
					$char!==':' && $i < $len && ($modifiers.=$char);
					$i++, $char = $regex{$i}
				){}

				if($char === ':'){
					$pattern = substr($regex, $i+1 , -1);
				}else{
					$pattern = substr($regex, 2 , -1);
				}
				return [
					'pattern' => $pattern,
					'modifiers' => $modifiers,
					'delimiter' => null,
				];
			}

		}else{
			$left = substr($regex, 0, 1);
			$right = null;
			$len = strlen($regex);
			$delimiter_closed = null;
			$modifiers = null;
			switch(true){
				case in_array($left,self::$pcre_delimiters, true):
					$right = $left;
					for($i = $len-1; $i; $i--){
						if($regex{$i} === $right){
							$delimiter_closed = $i;
							$modifiers = substr($regex, $i+1);
						}
					}
					break;
				case in_array($left, array_keys(self::$brackets)):
					$right = self::$brackets[$left];
					for($i = $len-1; $i; $i--){
						$char = $regex{$i};
						if(in_array($char, self::$special_chars, true)){
							break;
						}
						if($char === $right){
							$delimiter_closed = $i;
							$modifiers = substr($regex, $i+1);
						}
					}
					break;
			}
			if($delimiter_closed !== null){
				$pattern = substr($regex, 1, $delimiter_closed - 1);
				if($left === $right){
					$pattern = self::strip_backslashes($pattern,$left);
				}
				return [
					'pattern' => $pattern,
					'modifiers' => $modifiers,
					'delimiter' => $left
				];
			}
		}
		return [
			'pattern' => $regex,
			'modifiers' => '',
			'delimiter' => null
		];
	}

	/**
	 * @param $pattern
	 * @return bool
	 */
	static function has_utf8_chars($pattern){
		return preg_match('@[А-Яа-я]+@', $pattern) > 0;
	}


	/**
	 * @param $string
	 * @param $position
	 * @param $needle
	 * @return int
	 */
	static function count_repeat_before($string, $position, $needle){
		$l = strlen($needle);
		for($repeat_at = 0; self::byte_read_before($string, $position, $l, $repeat_at) === $needle; $repeat_at+=$l){}

		return $repeat_at;
	}


	/**
	 * @param $string
	 * @param $position
	 * @param $needle
	 * @return int
	 */
	static function count_repeat_after($string, $position, $needle){
		$l = strlen($needle);
		for($repeat_at = 0; self::byte_read_after($string, $position, $l, $repeat_at) === $needle; $repeat_at+=$l){}
		return $repeat_at;
	}

	/**
	 * @param $string
	 * @param $position
	 * @param int $len
	 * @param int $offset
	 * @return string
	 */
	static function byte_read_after($string, $position, $len = 1, $offset = 0){
		return substr($string, $position + 1 + $offset, $len);
	}

	/**
	 * @param $string
	 * @param $position
	 * @param int $len
	 * @param int $offset
	 * @return string
	 */
	static function byte_read_before($string, $position, $len = 1, $offset = 0){
		$pos = $position - $offset;
		$start = $pos - $len;
		if($start < 0){
			$len += $start;
			if(!$len) return '';
			$start = 0;
		}
		return substr($string, $start, $len);
	}

	/**
	 * @param $string
	 * @param $position
	 * @param $needle
	 * @param int $offset
	 * @return bool
	 */
	static function byte_has_before($string, $position, $needle, $offset = 0){
		if(!is_array($needle)){
			$needle = [ $needle ];
		}
		$ll = null;
		foreach($needle as $item){
			$l = strlen($item);
			if(!isset($s) || $ll != $l){
				$s = self::byte_read_before($string, $position, $l, $offset);
				$ll = $l;
			}
			if($s === $item) return true;
		}
		return false;
	}

	/**
	 * @param $string
	 * @param $position
	 * @param $needle
	 * @param int $offset
	 * @return bool
	 */
	static function byte_has_after($string, $position, $needle, $offset = 0){
		if(!is_array($needle)){
			$needle = [ $needle ];
		}
		$ll = null;
		foreach($needle as $item){
			$l = strlen($item);
			if(!isset($s) || $ll != $l){
				$s = self::byte_read_after($string, $position, $l, $offset);
				$ll = $l;
			}
			if($s === $item) return true;
		}
		return false;
	}


	/**
	 *
	 * charsMerge('smi', 'Pi') -> 'smiP'
	 *
	 * @param array|string $modifiers
	 *                      if array: return array
	 *                      if string: return string
	 *
	 * @param ...$_modifiers
	 * @return array|string
	 */
	static function charsMerge($modifiers, ...$_modifiers){
		$is_arr = is_array($modifiers);
		$modifiers = $is_arr?$modifiers:str_split($modifiers);
		foreach($_modifiers as $m){
			if(!is_array($m)) str_split($m);
			$modifiers = array_merge($modifiers, $m);
		}
		return $is_arr?array_unique($modifiers):implode(array_unique($modifiers));
	}

	/**
	 * charsDiff('smi', 'i') -> 'sm'
	 * charsDiff('smi', 'id') -> 'sm'
	 * charsDiff('smi', 'id', 'sm') -> ''
	 *
	 * @param array|string $modifiers
	 *                      if array: return array
	 *                      if string: return string
	 *
	 * @param ...$_modifiers
	 * @return array|string
	 */
	static function charsDiff($modifiers, ...$_modifiers){
		$is_arr = is_array($modifiers);
		$modifiers = $is_arr?$modifiers:str_split($modifiers);
		$a = [ $modifiers ];
		foreach($_modifiers as $m){
			if(!is_array($m)) str_split($m);
			$a[] = $m;
		}
		return $is_arr?array_diff(...$a):implode(array_diff(...$a));
	}

	/**
	 * charsIntersect('smi', 'id') -> 'i'
	 * charsIntersect('smi', 'i') -> 'i'
	 * charsIntersect('smi', 'smid') -> 'smi'
	 *
	 * @param array|string $modifiers
	 *                      if array: return array
	 *                      if string: return string
	 *
	 * @param ...$_modifiers
	 * @return array|string
	 */
	static function charsIntersect($modifiers, ...$_modifiers){
		$is_arr = is_array($modifiers);
		$modifiers = $is_arr?$modifiers:str_split($modifiers);
		$a = [ $modifiers ];
		foreach($_modifiers as $m){
			if(!is_array($m)) str_split($m);
			$a[] = $m;
		}
		return $is_arr?array_intersect(...$a):implode(array_intersect(...$a));
	}








	/**
	 * @param $char
	 * @return bool
	 */
	static function is_punctuation_char($char){
		return in_array($char, self::$punctuation_chars, true);
	}

	/**
	 * @param $char
	 * @param bool|null $closed
	 * @return bool
	 */
	static function is_bracket_char($char, $closed = null){
		if($closed === null){
			return isset(self::$brackets[$char]) || in_array($char, self::$brackets, true);
		}

		if($closed){
			return in_array($char, self::$brackets, true);
		}else{
			return isset(self::$brackets[$char]);
		}
	}

	/**
	 * @param $char
	 * @return bool
	 */
	static function is_digit_char($char){
		return in_array($char, self::$digit_chars, true);
	}





	/**
	 * @param $bracket
	 * @return null
	 */
	static function invert_bracket($bracket){
		if(isset(self::$brackets[$bracket])){
			return self::$brackets[$bracket];
		}
		if(false !== ($i = array_search($bracket, self::$brackets, true)) ){
			return self::$brackets[$i];
		}
		return null;
	}

	/**
	 * @param $bracket
	 * @return null
	 */
	static function close_bracket($bracket){
		if(false !== ($i = array_search($bracket, self::$brackets, true)) ){
			return self::$brackets[$i];
		}
		return null;
	}

	/**
	 * @param $bracket
	 * @return mixed
	 */
	static function open_bracket($bracket){
		if(isset(self::$brackets[$bracket])){
			return self::$brackets[$bracket];
		}
		return null;
	}

	/**
	 * @param $string
	 * @param $charlist
	 * @return string
	 */
	static function strip_backslashes($string, $charlist){
		if(is_array($charlist)) $charlist = implode($charlist);
		for($i = 0, $l = strlen($string); $i < $l; $i++){
			$char = $string{$i};
			if( strpos($charlist, $char) !== false && substr($string,$i-1, 1) === '\\' ){
				$string = substr_replace($string, '', $i-1, 1);
				$i--;
				$l--;
			}
		}
		return $string;
	}









	/**
	 * @param $a
	 * @param $b
	 * @return int
	 */
	private static function _sort_groups($a, $b){
		$a = $a[0];
		$b = $b[0];
		return ($a === $b ? 0 : (($a > $b) ? 1 : -1));
	}

}