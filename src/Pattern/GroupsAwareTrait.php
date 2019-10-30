<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: php-regex
 */

namespace Jungle\Regex\Pattern;


use Jungle\Regex\RegexUtils;

trait GroupsAwareTrait{
	
	/** @var  string|null */
	protected $payload;
	
	/** @var   */
	protected $metadata;
	
	/**
	 * @var  null|int - captures count
	 * @see getCapturedGroupsCount()
	 */
	protected $metaCapturedCount;
	
	/**
	 * @var  null|array - captures count
	 * @see getCapturedGroupsWithNames()
	 */
	protected $metaCapturedWithNames;
	
	/**
	 * @var  null|array - positions meta [N => [start, pos] ]
	 * @see getLinksToMatchesMeta()
	 */
	protected $metaLinksToMatches;
	
	/**
	 * @var  null|array - positions meta [N => [start, pos] ]
	 * @see getLinksToPatternsMeta()
	 */
	protected $metaLinksToPatterns;
	
	
	/**
	 * @return string
	 */
	abstract public function getPayload();
	
	/**
	 * @return array
	 */
	public function getMetadata(){
		if(!$this->metadata){
			$this->metadata = RegexUtils::analyzeRegexpMetadata($this->getPayload());
		}
		return $this->metadata;
	}
	
	/**
	 * @return mixed
	 */
	public function getCapturedGroupsCount(){
		$metadata = $this->getMetadata();
		return $metadata[RegexUtils::M_CAPTURED_COUNT];
	}
	
	/**
	 * @return array
	 */
	public function getCapturedGroupsWithNames(){
		if(!isset($this->metaCapturedWithNames)){
			$metadata = $this->getMetadata();
			
			$named      = array_keys($metadata[RegexUtils::M_TYPES], RegexUtils::A_CAPTURED, true);
			$indexed    = array_keys($metadata[RegexUtils::M_TYPES], RegexUtils::A_CAPTURED_INDEXED, true);
			
			
			$keys = array_replace($named, $indexed);
			
			$a = [];
			foreach($keys as $i){
				$data = $metadata[RegexUtils::M_DATA][$i];
				$offset = $data[RegexUtils::A_LINK_OFFSET];
				if(isset($data[RegexUtils::A_LINK_NAME])){
					$a[$offset] = $data[RegexUtils::A_LINK_NAME];
				}else{
					$a[$offset] = null;
				}
			}
			ksort($a);
			$this->metaCapturedWithNames = $a;
		}
		return $this->metaCapturedWithNames;
	}
	
	/**
	 * @return array
	 */
	public function getCapturedGroupsMeta(){
		
	}
	
	/**
	 * @return array
	 */
	public function getLinksToMatchesMeta(){
		
	}
	
	/**
	 * @return array
	 */
	public function getLinksToPatternsMeta(){
		
	}
	
}
