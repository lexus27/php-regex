<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: php-regex
 */

namespace Jungle\Regex;
use Jungle\Regex\Pattern\Composition\PatternWrapper;
use Jungle\Regex\Pattern\PatternInterface;
use Jungle\Regex\Result\MatchedAggregator;
use Jungle\Regex\Result\MatchedCollection;
use Jungle\Regex\Result\MatchedData;
use Jungle\Regex\Result\MatchedWithOffsetCapture;
use Jungle\Regex\Result\ResultElement;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class Matcher
 * @package Jungle\Regex
 */
class Matcher implements MatcherInterface{
	
	
	/** @var  PatternInterface */
	protected $pattern;
	
	/** @var  PatternWrapper */
	protected $strictPattern;
	
	
	
	public function __construct(PatternInterface $pattern){
		$this->pattern = $pattern;
	}
	
	/**
	 * @return PatternWrapper
	 */
	public function getStrictPattern(){
		if(!$this->strictPattern){
			$this->strictPattern =  new PatternWrapper($this->pattern);
			$this->strictPattern->pcreCover('^', '$');
		}
		return $this->strictPattern;
	}
	
	/**
	 * @param $subject
	 * @param bool $capturePositions
	 * @return ResultElement|null
	 */
	public function match($subject, $capturePositions = false){
		$pattern = $this->getStrictPattern();
		$pcre = $pattern->getPcre();
		
		$countOfMatches = preg_match($pcre, $subject, $matchesList);
		if($countOfMatches){
			$md = new MatchedData($matchesList);
			$map = $pattern->getResultMap();
			$map->setMatchedData($md);
			return $map;
		}
		return null;
	}
	
	/**
	 * @param $subject
	 * @param bool $capturePositions
	 * @return ResultElement|null
	 */
	public function matchIn($subject, $capturePositions = false){
		$pattern = $this->pattern;
		$pcre = $pattern->getPcre();
		
		$countOfMatches = preg_match($pcre, $subject, $matchesList);
		if($countOfMatches){
			$md = new MatchedData($matchesList);
			$map = $pattern->getResultMap();
			if($capturePositions){
				$md = new MatchedWithOffsetCapture($md);
			}
			$map->setMatchedData($md);
			return $map;
		}
		return null;
	}
	
	/**
	 * @param $subject
	 * @param bool $capturePositions
	 * @return array|MatchedAggregator|null|\Traversable
	 */
	public function matchManyIn($subject, $capturePositions = false){
		$pattern = $this->pattern;
		$pcre = $pattern->getPcre();
		
		if($capturePositions){
			$countOfMatches = preg_match_all($pcre, $subject, $matchesList, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
		}else{
			$countOfMatches = preg_match_all($pcre, $subject, $matchesList, PREG_SET_ORDER);
		}
		
		if($countOfMatches){
			$map = $pattern->getResultMap();
			
			$md = $aggregator = new MatchedCollection($matchesList);
			
			if($capturePositions){
				$md = new MatchedWithOffsetCapture($md);
			}
			
			$map->setMatchedData($md);
			$aggregator->setMap($map);
			
			
			return $aggregator;
		}
		return [];
	}
	
	/**
	 * @param $subject
	 * @param $rules
	 * @return string
	 */
	public function replace($subject, $rules){
		$pattern = $this->pattern;
		$pcre = $pattern->getPcre();
		
		$matched = new MatchedData();
		$map = $pattern->getResultMap();
		$map->setMatchedData($matched);
		
		return preg_replace_callback($pcre, function($matchesList) use($matched, $map, $rules){
			$matched->setData($matchesList);
			
			if(is_callable($rules)){
				return call_user_func($rules, $map, $matched);
			}else{
				return $matchesList[0];
			}
		},$subject);
	}
	
	/**
	 * @param $subject
	 * @return array
	 */
	public function split($subject){
		$pattern = $this->pattern;
		$pcre = $pattern->getPcre();
		
		$resultArray = preg_split($pcre, $subject);
		
		return $resultArray;
	}
	
}


