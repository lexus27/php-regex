<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: php-regex
 */

namespace Jungle\Regex\Tests;

use Jungle\Regex\Matcher;
use Jungle\Regex\Pattern\Composition\PatternAlternation;
use Jungle\Regex\Pattern\Composition\PatternLine;
use Jungle\Regex\Pattern\Composition\PatternTemplate;
use Jungle\Regex\Pattern\PatternSimple;
use Jungle\Regex\RegexUtils;
use Jungle\Regex\Result\MatchedData;
use Jungle\Regex\Result\ResultElement;
use PHPUnit\Framework\TestCase;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class PatternTestCase
 * @package Jungle\Regex\Tests
 */
class PatternTestCase extends TestCase{
	
	
	public function testBasic(){
		preg_match('@(1)|(2)|(3)|(4)@', '1', $matches );
		$this->assertEquals(['1', '1'], $matches);
		
		preg_match('@(1)|(2)|(3)|(4)@', '4', $matches );
		$this->assertEquals(['4', '', '', '', '4'], $matches);
		
		preg_match('@((1)|(2)|(3)|(4))(A)@', '1A', $matches );
		$this->assertEquals(['1A', '1', '1','','','', 'A'], $matches);
		
		preg_match('@((1)|(2)|(3)|(4))(A)@', '4A', $matches );
		$this->assertEquals(['4A', '4', '', '', '', '4', 'A'], $matches);
	}
	
	
	/**
	 *
	 */
	public function testAnalyze(){
		
		
		/**
		 *
		 */
		$analyzed = RegexUtils::analyze_groups('/(?|(.)|(.)|(.)|(.))/gi');
		$this->assertEquals([
			'total' => 5,
			'captured' => [
				1 => [ 1, 19, 0, NULL, true, ],
			],
			'transparent' => [
				[ 4, 6, 1, NULL, true, ],
				[ 8, 10, 2, NULL, true, ],
				[ 12, 14, 3, NULL, true, ],
				[ 16, 18, 4, NULL, true, ],
			],
		],$analyzed);
		
		/**
		 * @see RegexUtils::GRP_NAMED_ANG
		 */
		$analyzed = RegexUtils::analyze_groups('/(?<name>...)/gi');
		$this->assertEquals([
			'total'         => 1,
			'captured'      => [ 1 => [ 1, 12, 0, [ 'name', 4, 4, ], false, ], ],
			'transparent'   => [],
		],$analyzed);
		
		/**
		 * @see RegexUtils::GRP_RECURSE_AFTER_NAMED
		 */
		$analyzed = RegexUtils::analyze_groups('/(?P>name)/gi');
		$this->assertEquals([
			'total' => 1,
			'captured' =>[],
			'transparent' => [
				[ 1, 9, 0, NULL, false, ],
			],
		],$analyzed);
		
		/**
		 * @see RegexUtils::GRP_INLINE_MODIFIERS_WRAP
		 * inline modifiers 1
		 */
		$analyzed = RegexUtils::analyze_groups('/(?aisnd:...)/gi');
		$this->assertEquals([
			'total' => 1,
			'captured' =>[],
			'transparent' => [
				[ 1, 12, 0, NULL, false, ],
			],
		],$analyzed);
		
		/**
		 * @see RegexUtils::GRP_INLINE_MODIFIERS_POINT
		 * inline modifiers 2
		 */
		$analyzed = RegexUtils::analyze_groups('/(?aisnd)/gi');
		$this->assertEquals([
			'total'         => 1,
			'captured'      => [],
			'transparent'   => [
				[ 1, 8, 0, NULL, false, ],
			],
		],$analyzed);
		
		/**
		 * @see RegexUtils::GRP_ATOMIC
		 * inline modifiers 3
		 */
		$analyzed = RegexUtils::analyze_groups('/(?>\w+)/gi');
		$this->assertEquals([
			'total' => 1,
			'captured' => [],
			'transparent' => [
				[1, 7, 0, NULL, false, ],
			],
		],$analyzed);
		
		/**
		 * @see RegexUtils::GRP_NAMED_ANG
		 * @see RegexUtils::GRP_RECURSE_NTN_BEFORE
		 * Include references analyzing
		 */
		$analyzed = RegexUtils::analyze_groups('/(?<name>\d+) hello (?-1)/gi', true);
		$this->assertEquals([
			'total' => 1,
			'captured' => [
				1 => [ 1, 12, 0, [ 'name', 4, 4 ], false ],
			],
			'transparent'           => [],
			'links_to_matches'      => [],
			'links_to_patterns'     => [
				[
					'absolute'              => false,
					'name'                  => NULL,
					'offset'                => '-1',
					'reference_position'    => [ 20, 5 ],
				],
			],
		],$analyzed);
		
		/**
		 * @see RegexUtils::GRP_NAMED_ANG
		 * @see RegexUtils::MATCH_SUBPATTERN_NTN_FIG_BEFORE
		 * Include references analyzing
		 */
		$analyzed = RegexUtils::analyze_groups('/(?<name>\d+) hello \g{-1}/gi', true);
		$this->assertEquals([
			'total' => 1,
			'captured' => [
				1 => [ 1, 12, 0,[ 'name', 4, 4, ], false, ],
			],
			'transparent' =>[],
			'links_to_matches' => [
				[
					'absolute'              => false,
					'name'                  => NULL,
					'offset'                => '-1',
					'reference_position'    => [ 20, 6, ],
				],
			],
			'links_to_patterns' => [],
		],$analyzed);
		
	}
	
	/**
	 *
	 */
	public function testModify(){
		/**
		 * $offset - Указывает на сколько масок текущий шаблон смещен, относительно предыдущих
		 * $prefix - Указывает какой префикс выставить названиям масок и ссылкам
		 * $as     - Указывает в какую подмаску будет вложен этот шаблон
		 */
		$offset = 5;
		$prefix = 'prefix_';
		$as     = 'pattern_1_ctx';
		
		/**
		 * \g{1} - абсолютная ссылка
		 */
		$sourcePcre = '/(?<name>\d+) hello \g{1} (?&name) (?R)/gi';
		$this->assertEquals(
			'/(?<prefix_name>\d+) hello \g{6} (?&prefix_name) (?&pattern_1_ctx)/gi',
			RegexUtils::modify_identifiers($sourcePcre , $offset, $prefix, $as)
		);
		
		/**
		 * \g{-3} - Относительная ссылка
		 */
		$sourcePcre = '/(?<name>\d+) hello \g{-3} (?&name) (?R)/gi';
		$this->assertEquals(
			'/(?<prefix_name>\d+) hello \g{-3} (?&prefix_name) (?&pattern_1_ctx)/gi',
			RegexUtils::modify_identifiers($sourcePcre , $offset, $prefix, $as)
		);
		
		/**
		 * \g{+3} - Относительная ссылка
		 */
		$sourcePcre = '/(?<name>\d+) hello \g{+3} (?&name) (?R)/gi';
		$this->assertEquals(
			'/(?<prefix_name>\d+) hello \g{+3} (?&prefix_name) (?&pattern_1_ctx)/gi',
			RegexUtils::modify_identifiers($sourcePcre , $offset, $prefix, $as)
		);
		
	}
	
	public function testAnalyzeRegexp(){
		
		
		$metadata = RegexUtils::analyzeRegexpMetadata($pattern = '/(?|(.)|(.)|(.)|(.))/gi');
		
		$this->assertEquals([
			'capturedCount' => 1,
			'positions'     => [
				0 => null,
				1 => [ 4, 2 ],
				2 => [ 8, 2 ],
				3 => [ 12, 2 ],
				4 => [ 16, 2 ],
			],
			'types'         => [
				0 => 'capturedIndexed',
				1 => 'transparent',
				2 => 'transparent',
				3 => 'transparent',
				4 => 'transparent',
			],
			'data'          => [
				0 => [
					'offset'  => 1,
					'coverer' => true,
				],
				1 => null,
				2 => null,
				3 => null,
				4 => null,
			],
		], $metadata);
		
		$metadata = RegexUtils::analyzeRegexpMetadata($pattern = '/(?<name>\d+) hello \g{1} (?&name) (?R)/gi');
		
		$this->assertEquals([
			'capturedCount' => 1,
			'positions'     => [
				0 => [4, 4],
				1 => [20, 5],
				2 => [26, 8],
				3 => [35, 4],
			],
			'types'         => [
				0 => 'captured',
				1 => 'links_to_matches',
				2 => 'links_to_patterns',
				3 => 'links_to_patterns',
			],
			'data' => [
				0 => [
					'name'    => 'name',
					'offset'  => 1,
					'coverer' => false,
				],
				1 => [
					'absolute' => true,
					'name'     => null,
					'offset'   => '1',
				],
				2 => [
					'absolute' => true,
					'name'     => 'name',
					'offset'   => null,
				],
				3 => [
					'absolute' => true,
					'name'     => null,
					'offset'   => null,
				],
			],
		], $metadata);
		
	}
	
	public function testModifyRegexp(){
		/**
		 * $offset - Указывает на сколько масок текущий шаблон смещен, относительно предыдущих
		 * $prefix - Указывает какой префикс выставить названиям масок и ссылкам
		 * $as     - Указывает в какую подмаску будет вложен этот шаблон
		 */
		$offset = 5;
		$prefix = 'prefix_';
		$as     = 'pattern_1_ctx';
		
		
		$pattern = '/(?<name>\d+) hello \g{1} (?&name) (?R)/gi';
		$metadata = RegexUtils::analyzeRegexpMetadata($pattern);
		
		list($pattern, $metadata) = RegexUtils::modifyRegexpByMetadata($pattern , $metadata, $offset, $prefix, $as);
		
		list($pattern, $metadata) = RegexUtils::modifyRegexpByMetadata($pattern , $metadata, $offset, $prefix, $as);
	}
	
	
	public function testTemplate(){
		
		
		$registry = [
			'contact' => new PatternAlternation([
				'email'   => new PatternSimple('[a-zA-Z][a-zA-Z\.\-\_\d]+@[a-zA-Z][a-zA-Z\-\_\d]+(?:\.[a-zA-Z]+)?'),
				'phone'   => new PatternSimple('\+?\d\s*(?:\(\d{4}\)\s*\d{2}|\(\d{3}\)\s*\d{3})\s*\d{2}[\s\-]*\d{2}'),
				'card'    => new PatternAlternation([
					'maestro' => new PatternSimple('\d{4}\s*\d{4}\s*\d{4}\s*\d{4}\s*\d{4}'),
					'visa'    => new PatternSimple('\d{4}\s*\d{4}\s*\d{4}\s*\d{4}')
				])
			])
		];
		
		
		$template = new PatternTemplate('(?<name>\w+)\:\s*{{contact}}',  $registry);
		
		
		
		$matcher = new Matcher($template);
		$map = $matcher->matchIn("Alexey: +7(914)172 57 29");
		$data = $map->getDataAbsolute($map::KEYS_ONLY_NAMED);
		$name       = $map['name'];
		// а если там будет 2 contact в строку, тогда какой ключ будет у второго? ХЗ
		$contact    = $map['contact'];
		$contactEmail      = $map['contact.email'];
		$contactPhone      = $map['contact.phone'];
		$contactCard       = $map['contact.card'];
		$contactCardMaestro    = $map['contact.card.maestro'];
		$contactCardVisa       = $map['contact.card.visa'];
		
	}
	/**
	 *
	 *
	 * IMPLEMENTED: Наладить OffsetCapture для множественного результата
	 * IMPLEMENTED: Шаблоны регулярных выражений. Подстановка регулярки внутрь другой регулярки
	 * IMPLEMENTED: Сделать живые метаданные позиций подмасок, то есть Позиции которые в результате модификации, будут
	 *            : смещаться и обновляться в метаданных, такие метаданные можно будет кешировать
	 *
	 * TODO: Мини интерфейс для создания регулярки(Хелперы, Синтаксис, Тесты, Suitable)
	 * TODO: Suitable cases(Генерация строк соответствующих выражению, Генерация строк не-соответствующих выражению)
	 * TODO: Хранение шаблонов, облако шаблонов и т.п
	 * TODO: Кеширование карты результатов при хранении шаблона
	 * TODO: События шаблонов и влияние на результирующую строку вследствии обработки событий шаблонами, + возможное кеширование результатов сопоставления
	 * TODO: Формирование data-object(из строки, то есть считывание данных из строки)
	 *
	 *
	 */
	public function testMatcher(){
		// -- Шаблон адреса эл. почты
		// -- Шаблон номера телефона по крайней мере для росиии
		// -- Шаблон карты VISA
		// -- Шаблон карты MAESTRO
		// ---- Собераем шаблон для карт VISA или MAESTRO
		// ---- Собираем обобщенный шаблон контакта из комбинации(адрес эл. почты или номер телефона или номер карты)
		
		// ---- Формируем шаблон строки Имя: Контакт, используем нашу комбинацию для контакта
		$myPattern = new PatternLine([
			'name'     => new PatternSimple('\w+'),
						  new PatternSimple('\:\s*'),
			'contact'  => new PatternAlternation([
				'email'   => new PatternSimple('[a-zA-Z][a-zA-Z\.\-\_\d]+@[a-zA-Z][a-zA-Z\-\_\d]+(?:\.[a-zA-Z]+)?'),
				'phone'   => new PatternSimple('\+?\d\s*(?:\(\d{4}\)\s*\d{2}|\(\d{3}\)\s*\d{3})\s*\d{2}[\s\-]*\d{2}'),
				'card'    => new PatternAlternation([
					'maestro' => new PatternSimple('\d{4}\s*\d{4}\s*\d{4}\s*\d{4}\s*\d{4}'),
					'visa'    => new PatternSimple('\d{4}\s*\d{4}\s*\d{4}\s*\d{4}')
				])
			])
		]);
		
		
		$matcher = new Matcher($myPattern);
		
		$map = $matcher->matchIn("Alexey: +7(914)172 57 29");
		$data = $map->getDataAbsolute($map::KEYS_ONLY_NAMED);
		$name       = $map['name'];
		$contact    = $map['contact'];
		$contactEmail      = $map['contact.email'];
		$contactPhone      = $map['contact.phone'];
		$contactCard       = $map['contact.card'];
		$contactCardMaestro    = $map['contact.card.maestro'];
		$contactCardVisa       = $map['contact.card.visa'];
		
		$aggregator = $matcher->matchManyIn("
			 Alexey: +7(914)172 57 29, 
			 Anna: +7(914)172 57 29,
			 Senya: senya9000@mail.ru, Vasya: 7262 7000 1565 1980,
			 Petya: lexus27.khv@gmail.com,
			 John: 7262 7000 1565 1980 0910
		");
		
		$collection = [];
		/**
		 * @var  $i
		 * @var ResultElement $map
		 */
		foreach($aggregator as $i => $map){
			$data = $map->getDataAbsolute($map::KEYS_ONLY_NAMED);
			$name       = $map['name'];
			$contact    = $map['contact'];
			$contactEmail      = $map['contact.email'];
			$contactPhone      = $map['contact.phone'];
			$contactCard       = $map['contact.card'];
			$contactCardMaestro    = $map['contact.card.maestro'];
			$contactCardVisa       = $map['contact.card.visa'];
			
			if($map->in('contact')->getAlternationCase() === 'card'){
				$concrete = $map->query('contact.card',true)->getAlternationCase();
			}
			
			if($map['contact']){
				$switchPath = $map->query('contact',true)->getAlternationBranchPath();
			}
			
			$collection[$i] = $data;
		}
		
		
		
		$a = 0;
		
	}
	
	public function testBackReference(){
		
		
		//------------------------------------
		//    (  (\w+)  ({regex})  ) \g{-2}
		//    `  ^      `             <-2
		//------------------------------------
		
		
		
		//------------------------------------
		//    (\w+)  ( ({regex}) \g{-3} ) ;
		//    ^      ` `          <-3
		//------------------------------------
		
		
		
		
		
		//------------------------------------
		//    ({regex}) \g{-1} ;
		//    ^          <-1
		//------------------------------------
		
		
		
		
		
		
		$a = '({regex}) (\w+) (\w+) (\w+) (\w+) ({regex}) (\w+) ({regex}) \g{-2}';
		$meta = [
			4,
			0,
			0,
			0,
			0,
			4,
			0,
			4,
			0
		];
		
		
		$back = 2;
		$current = 8;
		$increment = 0;
		while($back-- && $current--){
			$increment+= $meta[$current];
		}
		//$increment === 4
		//$resultBack === ($increment + $back) === 6
		
		
		
		$a = '({regex}) (\w+) (\w+) (\w+) (\w+) ({regex}) (\w+) ({regex}) \g{-4}';
		
		$back = 4;
		$current = 8;
		$increment = 0;
		while($back-- && $current--){
			$increment+= $meta[$current];
		}
		//$increment === 8
		//$resultBack === ($increment + $back) === 12
		
	}
	
	public function testPatternLine(){
		
		
		//$emailWithStrictDomainZones = new PatternSimple('[a-zA-Z][a-zA-Z\.\-\_\d]+@[a-zA-Z][a-zA-Z\-\_\d]+(?:\.{{domainZoneIdentifiers}})?');
		
		/**
		 * @todo: preg_match($pattern, $subjectString, $matches, PREG_OFFSET_CAPTURE, $offset = 0);
		 * @todo: preg_match_all($pattern, $subjectString, $matches, PREG_OFFSET_CAPTURE, $offset = 0);
		 *
		 * @todo: preg_split($pattern,$subjectString, $limit = -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_OFFSET_CAPTURE); array chunks(with delimiter captured)
		 *
		 * @todo: preg_replace_callback($pattern, function(){},  $subjectString, $limit = -1, $countOfReplaced);
		 *
		 * existsIn():boolean
		 * existsMapping():boolean
		 *
		 * findIn():array
		 * findInMany():array
		 *
		 * compare():array
		 * compareMany():array
		 *
		 *
		 *
		 * split();
		 *
		 * matchWithCorrector([
		 *      'path.to.capture.or.child' => function($result){
		 *          return [
		 *              'a' => 'b',
		 *              'c' => 'f'
		 *          ],
		 *      }
		 * ], $subject);
		 *
		 * replace([
		 *      'rules' => [[
		 *          'when' => '\\\\',
		 *          'then' => '\\'
		 *      ],[
		 *          'when' => '\\{',
		 *          'then' => '{'
		 *      ],function($all, $result){
		 *          if($all === ' '){
		 *              return '_';
		 *          }
		 *          return $all;
		 *      }],
		 *      'map' => [
		 *          'path.to.capture.or.child' => function($result, $rootResult){
		 *              return str_replace( ' ' , '_' , $rootResult['path.to.capture']);
		 *          },
		 *          'path.to.capture.or.child' => '{str_replace(" ", "_", {path.to.capture})}',
		 *          'path.to.capture.or.child' => 'simple a text'
		 *      ],
		 * ], $subject);
		 *
		 */
		
		
		// todo Сделать компоновщик результатов
		// todo найти способ получения результатов по родному ключу в любом вложенном шаблоне
		// todo Вставка объекта настройщика (микроплагина) для конкретного шаблона по ключам относительно контейнера - нужно для regexp-replace
		
		
		
		
		
		
		/*
		$patternLine = new Pattern\PatternLine();
		
		$patternAlt = new Pattern\PatternLine();
		$patternAlt->addChild( new Pattern('[a-zA-Z]+(?<dot>\.)'),'string' );
		$patternAlt->addChild( new Pattern('\d+'),'int' );
		
		//$patternLine->addChild(new Pattern('^'));
		$patternLine->addChild($patternAlt, 'line');
		$patternLine->addChild( new Pattern('\\\\'),'BSlash');
		$patternLine->addChild( new Pattern('\d'),'intOne');
		//$patternLine->addChild(new Pattern('$'));
		
		$pcre = $patternLine->getPcre();
		*/
		
		
		
		/*
		// todo простые соседние вхождения
		// todo ставить префиксы, компилировать заного с подсчетом метаданных
		// todo replace
		// todo capture positions
		// todo Список параметров принадлежащщих шаблону
		// todo Line которые просто подставляют содержащиеся шаблоны, без обрамления в маску
		// todo PatternTemplate - шаблон в который подставляются другие шаблоны и компилятся потом.
		$matching = new MatchedElement($patternLine, 'abc.170\\1');
		$provideHierarchy = $matching->getResult();
		
		$a['line.int']        = $provideHierarchy['line.int'];
		$a['line.string']     = $provideHierarchy['line.string'];
		$a['line.string.dot'] = $provideHierarchy['line.string.dot'];
		
		$a['BSlash']          = $provideHierarchy['BSlash'];
		$a['intOne']          = $provideHierarchy['intOne'];
		
		*/
	}
	
	function presentFullQueryPaths($map,$p = null){
		$indexes = [];
		!$p && ($p = '');
		foreach($map as $key => $item){
			$k = ($p?$p.'.':'') . $key;
			$indexes[$k] = $item['access'];
			if($item['map']){
				$indexes = array_replace($indexes, $this->presentFullQueryPaths($item['map'], $k));
			}
		}
		return $indexes;
	}
	
	function importDataInFullQueryStructure(array $fullQueryPaths,$matches, $p = null){
		$values = [];
		!$p && ($p = '');
		foreach($fullQueryPaths as $key => $real){
			$k = ($p?$p.'.':'') . $key;
			$v = null;
			if(isset($matches[$real])){
				$v = $matches[$real];
			}
			$values[$k] = $v;
		}
		return $values;
	}
	
	function importDataInNestedStructure($map,$matches){
		$data = [];
		foreach($map as $key => $item){
			$v = [
				'that'      => null,
				'children'  => null
			];
			if(isset($matches[$item['access']])){
				$v['that'] = $matches[$item['access']];
			}
			if($item['map']){
				$v['children'] = $this->importDataInNestedStructure($item['map'], $matches);
			}
			$data[$key] = $v;
		}
		return $data;
	}
	function importDataInNestedStructureV2($map,$matches){
		$data = [];
		foreach($map as $key => $item){
			$v = [
				'that'      => null,
				'children'  => null
			];
			if(isset($matches[$item['access']])){
				$v['that'] = $matches[$item['access']]?:null;
			}
			if($item['map']){
				$v['children'] = $this->importDataInNestedStructureV2($item['map'], $matches);
			}else{
				$v = $v['that'];
			}
			$data[$key] = $v;
		}
		return $data;
	}
	
	
	public function testConceptSuitableGenerator(){
		
		$regexForParseRegex = '@(\[([^\\[\\]]+|(?<!\\\\)\])+\]|(\(((?-2)|.*)\))|\\\\.|[^\\[\\(]+)(\{(?:(\d*),)?(\d+|)\}|\+|\*|\?)?@';
		
		$subjectPattern = '[a-zA-Z][a-zA-Z\.\-\_\d]+@[a-zA-Z]{,1}[a-zA-Z\-\_\d]+(?:\.[a-zA-Z]+)?';
		$subjectPattern = '\d{4}\s*\d{4}\s*\d{4}\s*\d{4}\s*\d{4}';
		
		
		
		/**
		 * Это конвертация из сущностей маски в реальные символы, которые будут успешно сопоставляться с шаблоном
		 */
		$result = preg_replace_callback($regexForParseRegex, function($m){
			
			if(!empty($m[2])){          //symbol list
				$type = 'list';
				$v = $m[2];
			}else if(!empty($m[4])){    // group
				$type = 'group';
				$v = $m[4];
			}else if(!empty($m[1])){   //character
				
				if(substr($m[1],0,1) === '\\' || $m[1]==='.'){
					$type = 'identifier';
					$v = $m[1];
				}else{
					$type = 'text';
					$v = $m[0];
				}
				
			}else{
				$type = 'text';
				$v = $m[0];
			}
			
			$multiplier     = isset($m[5])?$m[5]:null;
			switch($multiplier){
				case '+':
					$multiplier = [1, null];
					break;
				case '?':
					$multiplier = [0,1];
					break;
				case '*':
					$multiplier = [0, null];
					break;
				default:
					if(isset($m[6]) && isset($m[7])){
						$max = $m[7]? intval($m[7]) : null;
						switch(trim($m[6])){
							case ',':
								$min = 0;
								break;
							case '':
								$min = $max;
								break;
							default:
								$min = intval(trim($m[6],' ,'));
								break;
						}
						
						$multiplier = [$min, $max];
					}else{
						$multiplier = [1, 1];
					}
					break;
			}
			
			switch($type){
				case 'list':
					
					$negative = false;
					if(substr($v,0,1) === '^'){
						$negative = true;
						$v = substr($v,1);
					}
					$v = preg_replace_callback('@\\\\(.)|(.)\-(.)|.@',function($m) use($negative){
						$v = '';
						if(!empty($m[1])){
							
							switch($m[1]){
								case 'w':
									$v = '0123456789QWERTYUIOPASDFGHJKLZXCVBNM_qwertyuiopasdfghjklzxcvbnm';
									break;
								case 'W':
									$v =  "~!#\$@\$%^&*()-+=?><:\";'\\/}{][., \r\n\t";
									break;
								case 'd':
									$v =  '0123456789';
									break;
								case 'D':
									$v =  "QWERTYUIOPASDFGHJKLZXCVBNM_qwertyuiopasdfghjklzxcvbnm~!#\$@%^&*()-+=?><:\";'\\/}{][., \r\n\t";
									break;
								case 's':
									$v =  " \r\n\t";
									break;
								case 'S':
									$v =  "0123456789QWERTYUIOPASDFGHJKLZXCVBNM_qwertyuiopasdfghjklzxcvbnm~!#\$@%^&*()-+=?><:\";'\\/}{][.,";
									break;
							}
							return $v;
						}else if(!empty($m[2]) && !empty($m[3])){
							return $m[2];
						}else{
							return $m[0];
						}
						
					}, $v);
					
					
					$v = str_split($v);
					$min = $multiplier[0];
					$max = $multiplier[1]===null?$min+5:$multiplier[1];
					$count = mt_rand($min,$max);
					$vv = '';
					if($count){
						while($count--){
							$vv.=$v[rand(0,count($v)-1)];
						}
					}
					return $vv;
					break;
				case 'group':
					return '(IMPLEMENT_GRP)';
					break;
				case 'identifier':
					$v = '';
					$charClass = substr($m[1],1);
					switch($charClass){
						case 'w':
							$v = '0123456789QWERTYUIOPASDFGHJKLZXCVBNM_qwertyuiopasdfghjklzxcvbnm';
							break;
						case 'W':
							$v =  "~!#\$@\$%^&*()-+=?><:\";'\\/}{][., \n\t";
							break;
						case 'd':
							$v =  '0123456789';
							break;
						case 'D':
							$v =  "QWERTYUIOPASDFGHJKLZXCVBNM_qwertyuiopasdfghjklzxcvbnm~!#\$@%^&*()-+=?><:\";'\\/}{][., \n\t";
							break;
						case 's':
							$v =  " \n\t";
							break;
						case 'S':
							$v =  "0123456789QWERTYUIOPASDFGHJKLZXCVBNM_qwertyuiopasdfghjklzxcvbnm~!#\$@%^&*()-+=?><:\";'\\/}{][.,";
							break;
					}
					$v = str_split($v);
					$min = $multiplier[0];
					$max = $multiplier[1]===null?$min+5:$multiplier[1];
					$count = mt_rand($min,$max);
					$vv = '';
					if($count){
						while($count--){
							$vv.=$v[rand(0,count($v)-1)];
						}
					}
					return $vv;
					
					break;
				case 'text':
					return $v;
					break;
			}
			
			return '';
			
		}, $subjectPattern);
		echo $result;
		
		
		
		preg_match('@'.addcslashes($subjectPattern,'@').'@', '9248
 

 4314 	
2840 


1142	 9240', $m);
		
		print_r($m);
		
	}
	
	public function testResultMap(){
		// -- Шаблон адреса эл. почты
		// -- Шаблон номера телефона по крайней мере для росиии
		// -- Шаблон карты VISA
		// -- Шаблон карты MAESTRO
		// ---- Собераем шаблон для карт VISA или MAESTRO
		// ---- Собираем обобщенный шаблон контакта из комбинации(адрес эл. почты или номер телефона или номер карты)
		
		// ---- Формируем шаблон строки Имя: Контакт, используем нашу комбинацию для контакта
		$myPattern = new PatternLine([
			'name'     => new PatternSimple('\w+'),
						  new PatternSimple('\:\s*'),
			'contact'  => new PatternAlternation([
				'email'   => new PatternSimple('[a-zA-Z][a-zA-Z\.\-\_\d]+@[a-zA-Z][a-zA-Z\-\_\d]+(?:\.[a-zA-Z]+)?'),
				'phone'   => new PatternSimple('\+?\d\s*(?:\(\d{4}\)\s*\d{2}|\(\d{3}\)\s*\d{3})\s*\d{2}[\s\-]*\d{2}'),
				'card'    => new PatternAlternation([
					'maestro' => new PatternSimple('\d{4}\s*\d{4}\s*\d{4}\s*\d{4}\s*\d{4}'),
					'visa'    => new PatternSimple('\d{4}\s*\d{4}\s*\d{4}\s*\d{4}')
				])
			])
		]);
		
		// > Компилируем
		$pcre = $myPattern->getPcre();
		
		// todo Сделать инкапсуляцию матчинга
		// todo Сделать события onMatched
		// > Вычисление случая: Номер телефона
		$subject = 'Alexey: +7(914)172 57 29';
		if(preg_match($pcre, $subject, $matches)){
			echo "Subject: '{$subject}'\r\n";
			echo "Matches: \r\n";
			print_r($matches);
			echo "\r\n\r\n";
			
			$matchedData = new MatchedData($matches);
			
			
			$map = $myPattern->getResultMap();
			
			
			
			$map->setMatchedData($matchedData);
			
			//todo alias-mapping in pattern
			$phone = $map->query('contact.2');
			$phone = $map->query('contact.phone');
			
			$email = $map->query('contact.1');
			$email = $map->query('contact.email');
			
			$case = $map->in('contact')->getAlternationBranchPath();
			
			
			//$dataNested = $map->getDataNested($map::PREFERRED_ONLY_NAMED);
			//$dataAbsolute = $map->getDataAbsolute($map::PREFERRED_ONLY_NAMED);
			
			
			
		}
	}
	
	public function testResultMapArray(){
		
		/**
		 * preg_match('@\d{0,3}@', '12,126', $m, PREG_OFFSET_CAPTURE);
		 * // массив как схождение
		 * $m === [
		 *      // маска [0] (т.е глобальная)
		 *      0 => [
	     *           0 => '12', // (текст схождения)
	     *           1 => 0,    // (начальная позиция)
	     *      ],
		 *
		 * ];
		 *
		 * preg_match_all('@\d{0,3}@', '12,126', $m, PREG_OFFSET_CAPTURE);
		 * $m === [
		 *      // маска [0] (т.е глобальная)
		 *		0 => [
		 *
		 *         // схождение [0]
		 *		   0 => [ 0 => '12', 1 => 0, ],  // [ (текст схождения) , (начальная позиция) ]
		 *
		 *         // схождение [1]
		 *		   1 => [ 0 => '', 1 => 2, ],    // [ (текст схождения) , (начальная позиция) ]
		 *
		 *         // схождение [2]
		 *		   2 => [ 0 => '126', 1 => 3, ], // [ (текст схождения) , (начальная позиция) ]
		 *
		 *         // схождение [3]
		 *		   3 => [ 0 => '', 1 => 6, ],    // [ (текст схождения) , (начальная позиция) ]
		 *
		 *		],
		 *	];
		 */
		
		
		
		// -- Шаблон адреса эл. почты
		$email = new PatternSimple('[a-zA-Z][a-zA-Z\.\-\_\d]+@[a-zA-Z][a-zA-Z\-\_\d]+(?:\.[a-zA-Z]+)?');
		// -- Шаблон номера телефона по крайней мере для росиии
		$phone = new PatternSimple('\+?\d\s*(?:\(\d{4}\)\s*\d{2}|\(\d{3}\)\s*\d{3})\s*\d{2}[\s\-]*\d{2}');
		
		
		// -- Шаблон карты VISA
		$cardNumberVisa     = new PatternSimple('\d{4}\s*\d{4}\s*\d{4}\s*\d{4}');
		// -- Шаблон карты MAESTRO
		$cardNumberMaestro  = new PatternSimple('\d{4}\s*\d{4}\s*\d{4}\s*\d{4}\s*\d{4}?');
		
		// ---- Собераем шаблон для карт VISA или MAESTRO
		$cardNumber = new PatternAlternation($cardNumberMaestro, $cardNumberVisa);
		
		
		// ---- Собираем обобщенный шаблон контакта из комбинации(адрес эл. почты или номер телефона или номер карты)
		$myVariableContact = new PatternAlternation($email, $phone, $cardNumber);
		
		
		// ---- Формируем шаблон строки Имя: Контакт, используем нашу комбинацию для контакта
		$myPattern = new PatternLine(
			new PatternSimple('\w+'),
			new PatternSimple('\:\s*')
		);
		$myPattern->addChild($myVariableContact, 'contact');
		
		
		// > Компилируем
		$pcre = $myPattern->getPcre();
		
		
		// > Вычисление случая: Номер телефона
		$subject = 'Alexey: +7(914)172 57 29';
		if(preg_match($pcre, $subject, $matches)){
			echo "Subject: '{$subject}'\r\n";
			echo "Matches: \r\n";
			print_r($matches);
			echo "\r\n\r\n";
			
			/*
			$indexes = [
				'1' => $myPattern->getChild(1)->getOffset(),
				'2' => $myPattern->getChild(2)->getOffset(),
				'3' => $myPattern->getChild(3)->getOffset(),
				'3.1' => $myPattern->getChild(3)->getOffset() + $myPattern->getChild(3)->getWrapped()->getChild(1)->getOffset(),    // email
				'3.2' => $myPattern->getChild(3)->getOffset() + $myPattern->getChild(3)->getWrapped()->getChild(2)->getOffset(),    // phone
				'3.3' => $myPattern->getChild(3)->getOffset() + $myPattern->getChild(3)->getWrapped()->getChild(3)->getOffset(),    // card
				'3.3.1' => $myPattern->getChild(3)->getOffset() + $myPattern->getChild(3)->getWrapped()->getChild(3)->getOffset() + $myPattern->getChild(3)->getWrapped()->getChild(3)->getWrapped()->getChild(1)->getOffset(),    // card Maestro
				'3.3.2' => $myPattern->getChild(3)->getOffset() + $myPattern->getChild(3)->getWrapped()->getChild(3)->getOffset() + $myPattern->getChild(3)->getWrapped()->getChild(3)->getWrapped()->getChild(2)->getOffset(),    // card Visa
			];
			
			$indexes = [
				'name'      => $myPattern->getChild(1)->getOffset(),
				'contact'   => $myPattern->getChild(3)->getOffset(),
				'contact.email'     => $myPattern->getChild(3)->getOffset() + $myPattern->getChild(3)->getWrapped()->getChild(1)->getOffset(),    // email
				'contact.phone'     => $myPattern->getChild(3)->getOffset() + $myPattern->getChild(3)->getWrapped()->getChild(2)->getOffset(),    // phone
				'contact.card'      => $myPattern->getChild(3)->getOffset() + $myPattern->getChild(3)->getWrapped()->getChild(3)->getOffset(),    // card
				'contact.card.maestro'  => $myPattern->getChild(3)->getOffset() + $myPattern->getChild(3)->getWrapped()->getChild(3)->getOffset() + $myPattern->getChild(3)->getWrapped()->getChild(3)->getWrapped()->getChild(1)->getOffset(),    // card Maestro
				'contact.card.visa'     => $myPattern->getChild(3)->getOffset() + $myPattern->getChild(3)->getWrapped()->getChild(3)->getOffset() + $myPattern->getChild(3)->getWrapped()->getChild(3)->getWrapped()->getChild(2)->getOffset(),    // card Visa
			];
			$data = [];
			foreach($indexes as $key => $offset){
				$data[$key] = isset($matches[$offset])?$matches[$offset]:null;
			}
			print_r($data);
			*/
			
			
			$map = $myPattern->getResultMapArray(0,'');
			$this->assertEquals([
				
				// name (indexed)
				1 => [ 'access' => 1, 'map' =>[] ],
				
				// delimiter (indexed)
				2 => [ 'access' => 2, 'map' =>[] ],
				
				// contact (indexed)
				3 => [
					'access' => 3,
					'map' => [
						1 => [ 'access' => 4, 'map' => [] ],
						2 => [ 'access' => 5, 'map' => [] ],
						3 => [
							'access' => 6,
							'map'   => [ 1 => [ 'access' => 7, 'map' =>[] ], 2 => [ 'access' => 8, 'map' => [] ], ],
						],
					],
				],
				
				// contact (aliased)
				'contact' =>[
					'access' => 'contact',
					'map' =>[
						1 => [ 'access' => 4, 'map' =>[] ],
						2 => [ 'access' => 5, 'map' =>[] ],
						3 => [
							'access' => 6,
							'map' =>[ 1 => [ 'access' => 7, 'map' =>[] ], 2 => [ 'access' => 8, 'map' =>[] ], ],
						],
					],
				],
			
			], $map);
			
			$paths = $this->presentFullQueryPaths($map);
			$this->assertEquals(array (
				
				// name (indexed)
				1           => 1,
				
				// delimiter (indexed)
				2           => 2,
				
				// contact (aliased)
				'contact'   => 'contact',
				'contact.1'     => 4,
				'contact.2'     => 5,
				'contact.3'     => 6,
				'contact.3.1'       => 7,
				'contact.3.2'       => 8,
				
				// contact (indexed)
				3           => 3,
				'3.1'           => 4,
				'3.2'           => 5,
				'3.3'           => 6,
				'3.3.1'             => 7,
				'3.3.2'             => 8,
			
			), $paths);
			
			echo "Nested: \r\n";
			$dataNested = $this->importDataInNestedStructure($map, $matches);
			$this->assertEquals([
				1 => [ 'that' => 'Alexey', 'children' => null, ],
				2 => [ 'that' => ': ', 'children' => null, ],
				'contact' => [
					'that'     => '+7(914)172 57 29',
					'children' => [
						1 => [ 'that' => '', 'children' => null, ],
						2 => ['that' => '+7(914)172 57 29','children' => null, ],
						3 => [
							'that'     => null,
							'children' => [
								1 => [ 'that' => null, 'children' => null, ],
								2 => [ 'that' => null, 'children' => null, ],
							],
						],
					],
				],
				3 => [
					'that' => '+7(914)172 57 29',
					'children' => [
						1 => [ 'that' => '', 'children' => null, ],
						2 => [ 'that' => '+7(914)172 57 29', 'children' => null, ],
						3 => [
							'that' => null,
							'children' => [
								1 => [ 'that' => null, 'children' => null, ],
								2 => [ 'that' => null, 'children' => null, ],
							],
						],
					],
				],
			], $dataNested);
			
			$dataNested2 = $this->importDataInNestedStructureV2($map, $matches);
			echo "Nested 2: \r\n";
			$this->assertEquals([
				1 => 'Alexey',
				2 => ': ',
				'contact' =>[
					'that' => '+7(914)172 57 29',
					'children' =>[
						1 => NULL,
						2 => '+7(914)172 57 29',
						3 =>[
							'that' => NULL,
							'children' =>[ 1 => NULL, 2 => NULL, ],
						],
					],
				],
				3 => [
					'that' => '+7(914)172 57 29',
					'children' => [
						1 => NULL,
						2 => '+7(914)172 57 29',
						3 => [
							'that' => NULL,
							'children' => [
								1 => NULL,
								2 => NULL,
							],
						],
					],
				],
			],$dataNested2);
			
			$dataSmooth = $this->importDataInFullQueryStructure($paths, $matches);
			echo "Smooth: \r\n";
			$this->assertEquals( array (
				1               => 'Alexey',
				2               => ': ',
				'contact'       => '+7(914)172 57 29',
				'contact.1'         => '',
				'contact.2'         => '+7(914)172 57 29',
				'contact.3'         => NULL,
				'contact.3.1'           => NULL,
				'contact.3.2'           => NULL,
				3               => '+7(914)172 57 29',
				'3.1'               => '',
				'3.2'               => '+7(914)172 57 29',
				'3.3'               => NULL,
				'3.3.1'                 => NULL,
				'3.3.2'                 => NULL,
			) ,$dataSmooth);
			
			
		}
	}
	
	public function testPatternCompositions(){
		
		/**
		 * Решил сделать условные обозначения и разделить на 2 зоны: Определение и далее Использование
		 *
		 * --   (2 тире) указывает на Определяющую логику
		 * ---- (4 тире) указывает на Определение благодаря использования другого ранее определенного
		 * >    (1 птичка) указывает на логику где используется то что мы ранее определили
		 *
		 */
		
		
		
		// -- Шаблон адреса эл. почты
		$email = new PatternSimple('[a-zA-Z][a-zA-Z\.\-\_\d]+@[a-zA-Z][a-zA-Z\-\_\d]+(?:\.[a-zA-Z]+)?');
		// -- Шаблон номера телефона по крайней мере для росиии
		$phone = new PatternSimple('\+?\d\s*(?:\(\d{4}\)\s*\d{2}|\(\d{3}\)\s*\d{3})\s*\d{2}[\s\-]*\d{2}');
		
		
		// -- Шаблон карты VISA
		$cardNumberVisa     = new PatternSimple('\d{4}\s*\d{4}\s*\d{4}\s*\d{4}');
		// -- Шаблон карты MAESTRO
		$cardNumberMaestro  = new PatternSimple('\d{4}\s*\d{4}\s*\d{4}\s*\d{4}\s*\d{4}?');
		
		// ---- Собераем шаблон для карт VISA или MAESTRO
		$cardNumber = new PatternAlternation($cardNumberMaestro, $cardNumberVisa);
		
		
		// ---- Собираем обобщенный шаблон контакта из комбинации(адрес эл. почты или номер телефона или номер карты)
		$myVariableContact = new PatternAlternation($email, $phone, $cardNumber);
		
		
		// ---- Формируем шаблон строки Имя: Контакт, используем нашу комбинацию для контакта
		$myPattern = new PatternLine(
			new PatternSimple('\w+'),
			new PatternSimple('\:\s*')
		);
		$myPattern->addChild($myVariableContact);
		
		
		// > Компилируем
		$pcre = $myPattern->getPcre();
		echo "Result pcre regexp: {$pcre}\r\n\r\n";
		$this->assertEquals(
			'@(\\w+)(\\:\\s*)(([a-zA-Z][a-zA-Z\\.\\-\\_\\d]+\\@[a-zA-Z][a-zA-Z\\-\\_\\d]+(?:\\.[a-zA-Z]+)?)|(\\+?\\d\\s*(?:\\(\\d{4}\\)\\s*\\d{2}|\\(\\d{3}\\)\\s*\\d{3})\\s*\\d{2}[\\s\\-]*\\d{2})|((\\d{4}\\s*\\d{4}\\s*\\d{4}\\s*\\d{4}\\s*\\d{4}?)|(\\d{4}\\s*\\d{4}\\s*\\d{4}\\s*\\d{4})))@',
			$pcre,
			'Pcre from $myPattern->getPcre()'
		);
		
		
		
		// > Вычисление случая: Номер телефона
		$subject = 'Alexey: +7(914)172 57 29';
		if(preg_match($pcre, $subject, $matches)){
			echo "Subject: '{$subject}'\r\n";
			echo "Matches: \r\n";
			print_r($matches);
			echo "\r\n\r\n";
		}
		$this->assertEquals(
			[
				0 => 'Alexey: +7(914)172 57 29',
				1 => 'Alexey',
				2 => ': ',
				3 => '+7(914)172 57 29',
				4 => '',
				5 => '+7(914)172 57 29',
			],
			$matches,
			'match alexey phone'
		);
		
		
		// > Вычисление случая: Номер карты
		$subject = 'Vasya: 7262 7000 1565 1980';
		if(preg_match($pcre, $subject, $matches)){
			echo "Subject: '{$subject}'\r\n";
			echo "Matches: \r\n";
			print_r($matches);
			echo "\r\n\r\n";
		}
		$this->assertEquals(
			[
				0 => 'Vasya: 7262 7000 1565 1980',
				1 => 'Vasya',
				2 => ': ',
				3 => '7262 7000 1565 1980',
				4 => '',
				5 => '',
				6 => '7262 7000 1565 1980',
				7 => '',
				8 => '7262 7000 1565 1980',
			],
			$matches,
			'Match vasya card visa'
		);
		
		
		// > Вычисление случая: Адрес Эл. Почты
		$subject = 'Petya: lexus27.khv@gmail.com';
		if(preg_match($pcre, $subject, $matches)){
			echo "Subject: '{$subject}'\r\n";
			echo "Matches: \r\n";
			print_r($matches);
			echo "\r\n\r\n";
		}
		$this->assertEquals(
			[
				0 => 'Petya: lexus27.khv@gmail.com',
				1 => 'Petya',
				2 => ': ',
				3 => 'lexus27.khv@gmail.com',
				4 => 'lexus27.khv@gmail.com',
			],
			$matches,
			'Match petya email'
		);
		
	}
	
	
	public function testPatternTemplate(){
		/*
		$pt = new Pattern\PatternTemplate('{{words}}\s*--\s*{{digits}}',[
			'digits' => new Pattern('\d+'),
			'words'  => new Pattern('[a-zA-Z]+'),
		]);
		$pcre = $pt->getPcre();
		
		
		echo $pcre;
		*/
	}
	
	
	public function test____Concept(){
		
		
		//$emailWithStrictDomainZones = new PatternSimple('[a-zA-Z][a-zA-Z\.\-\_\d]+@[a-zA-Z][a-zA-Z\-\_\d]+(?:\.{{domainZoneIdentifiers}})?');
		
		/**
		 * @todo: preg_match($pattern, $subjectString, $matches, PREG_OFFSET_CAPTURE, $offset = 0);
		 * @todo: preg_match_all($pattern, $subjectString, $matches, PREG_OFFSET_CAPTURE, $offset = 0);
		 *
		 * @todo: preg_split($pattern,$subjectString, $limit = -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_OFFSET_CAPTURE); array chunks(with delimiter captured)
		 *
		 * @todo: preg_replace_callback($pattern, function(){},  $subjectString, $limit = -1, $countOfReplaced);
		 *
		 * existsIn():boolean
		 * existsMapping():boolean
		 *
		 * findIn():array
		 * findInMany():array
		 *
		 * compare():array
		 * compareMany():array
		 *
		 *
		 *
		 * split();
		 *
		 * matchWithCorrector([
		 *      'path.to.capture.or.child' => function($result){
		 *          return [
		 *              'a' => 'b',
		 *              'c' => 'f'
		 *          ],
		 *      }
		 * ], $subject);
		 *
		 * replace([
		 *      'rules' => [[
		 *          'when' => '\\\\',
		 *          'then' => '\\'
		 *      ],[
		 *          'when' => '\\{',
		 *          'then' => '{'
		 *      ],function($all, $result){
		 *          if($all === ' '){
		 *              return '_';
		 *          }
		 *          return $all;
		 *      }],
		 *      'map' => [
		 *          'path.to.capture.or.child' => function($result, $rootResult){
		 *              return str_replace( ' ' , '_' , $rootResult['path.to.capture']);
		 *          },
		 *          'path.to.capture.or.child' => '{str_replace(" ", "_", {path.to.capture})}',
		 *          'path.to.capture.or.child' => 'simple a text'
		 *      ],
		 * ], $subject);
		 *
		 */
		
		
		// todo Сделать компоновщик результатов
		// todo найти способ получения результатов по родному ключу в любом вложенном шаблоне
		// todo Вставка объекта настройщика (микроплагина) для конкретного шаблона по ключам относительно контейнера - нужно для regexp-replace
		
		
		
		
		
		
		/*
		$patternLine = new Pattern\PatternLine();
		
		$patternAlt = new Pattern\PatternLine();
		$patternAlt->addChild( new Pattern('[a-zA-Z]+(?<dot>\.)'),'string' );
		$patternAlt->addChild( new Pattern('\d+'),'int' );
		
		//$patternLine->addChild(new Pattern('^'));
		$patternLine->addChild($patternAlt, 'line');
		$patternLine->addChild( new Pattern('\\\\'),'BSlash');
		$patternLine->addChild( new Pattern('\d'),'intOne');
		//$patternLine->addChild(new Pattern('$'));
		
		$pcre = $patternLine->getPcre();
		*/
		
		
		
		/*
		// todo простые соседние вхождения
		// todo ставить префиксы, компилировать заного с подсчетом метаданных
		// todo replace
		// todo capture positions
		// todo Список параметров принадлежащщих шаблону
		// todo Line которые просто подставляют содержащиеся шаблоны, без обрамления в маску
		// todo PatternTemplate - шаблон в который подставляются другие шаблоны и компилятся потом.
		$matching = new MatchedElement($patternLine, 'abc.170\\1');
		$provideHierarchy = $matching->getResult();
		
		$a['line.int']        = $provideHierarchy['line.int'];
		$a['line.string']     = $provideHierarchy['line.string'];
		$a['line.string.dot'] = $provideHierarchy['line.string.dot'];
		
		$a['BSlash']          = $provideHierarchy['BSlash'];
		$a['intOne']          = $provideHierarchy['intOne'];
		
		*/
	}
	
}


