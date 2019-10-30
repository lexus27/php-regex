<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: php-regex
 */

namespace Jungle\Regex;

/**
 * @Author: Alexey Kutuzov <lexus27.khv@gmail.com>
 * Class StringDataCarrier
 * @package Jungle\Regex
 *
 * Изменение текста по метаданным [pos, len], и изменение позиций и размеров в метаданных - после изменения
 * На возврат отдается массив где первый элемент строка(после замены), вторым элементом - актуальные метаданные
 *
 * TODO: Использовать в RegexUtils::modify_identifiers для сохранения актуальности метаданных после модификаций
 * TODO: Учитывать вложенные метапозиции например: ( [0, 10], [7, 2], [11, 20], [14, 5] )
 */
class StringDataCarrier{
	
	protected $string;
	protected $metadata;
	protected $aliases = [];
	
	
	public function __construct($string, $metadata, $aliases){
		$this->string   = $string;
		$this->metadata = $metadata;
		
		if(is_array($aliases)){
			foreach($aliases as $i => $alias){
				if(!is_array($alias)){
					$alias = [$alias];
				}
				foreach($alias as $name){
					$this->aliases[$name] = $i;
				}
				
			}
		}
		
	}
	
	/**
	 * @param $key
	 * @param $value
	 * @return $this
	 */
	public function set($key, $value){
		
		if(isset($this->aliases[$key])){
			$key = $this->aliases[$key];
		}
		
		list($this->string, $this->metadata) = self::replace($this->string, $this->metadata, [
			$key => $value
		]);
		
		return $this;
	}
	
	/**
	 * @param $key
	 * @return null|string
	 */
	public function get($key){
		
		if(isset($this->aliases[$key])){
			$key = $this->aliases[$key];
		}
		
		if(isset($this->metadata[$key])){
			return substr($this->string, $this->metadata[$key][0], $this->metadata[$key][1]);
		}
		return null;
	}
	
	/**
	 * @param array $data
	 * @return $this
	 */
	public function setData(array $data){
		
		$a = [];
		foreach($data as $key => $value){
			if(isset($this->aliases[$key])){
				$key = $this->aliases[$key];
			}
			$a[$key] = $value;
		}
		
		list($this->string, $this->metadata) = self::replace($this->string, $this->metadata, $a);
		
		return $this;
	}
	
	/**
	 * @param bool $toAliases
	 * @return array
	 */
	public function getData($toAliases = true){
		
		$a = [];
		foreach($this->metadata as $key => list($pos, $len)){
			
			if($toAliases){
				$keys = array_keys($this->aliases, $key, true);
				if(!$keys){
					$keys = [$key];
				}
			}else{
				$keys = [$key];
			}
			
			$value = substr($this->string, $pos, $len);
			
			foreach($keys as $key){
				$a[$key] = $value;
			}
		}
		
		return $a;
	}
	
	
	/**
	 * @param $string
	 * @param $metadata
	 * @param $values
	 * @return array
	 */
	public static function replace($string, $metadata, $values){
		if(!is_array($values))$values = array_fill(0, count($metadata), $values);
		
		$previousOffsets = 0;  // main hook matching a real offset for next elements for offset his
		foreach($metadata as $i => &$meta){
			
			$pos        = $meta[0] + $previousOffsets; // for right string position (because prev operations modify string insertion)
			
			if(isset($values[$i])){
				$value          = $values[$i];
				
				$valueLength   = strlen($value);
				
				$len    = $meta[1];
				
				$string = substr_replace($string, $value , $pos, $len);
				
				// 10 - 5 = 5;      стало на 5 больше
				// 5 - 10 = -5;     стало на 5 меньше
				// 0 - 10 = -10;    стало на 10 меньше
				// 10 - 0 = 10;     стало на 10 больше
				$difference = ($valueLength - $len);
				
				$previousOffsets+= $difference;
				
				$meta[1] = $valueLength;
			}
			
			$meta[0] = $pos;
			
		}
		
		return [$string, $metadata];
	}
	
	/**
	 * @param $string
	 * @param $metadata
	 * @param $values
	 * @return array
	 */
	public static function replaceNested($string, $metadata, $values){
		if(!is_array($values))$values = array_fill(0, count($metadata), $values);
		
		$previousOffsets = 0;  // main hook matching a real offset for next elements for offset his
		$endPosMaxPrevChanged   = 0;
		$endPosMaxHolder            = null;
		$endPosMaxHolderReference   = null;
		$e = [];
		foreach($metadata as $i => &$meta){
			
			$pos        = $meta[0] + $previousOffsets; // for right string position (because prev operations modify string insertion)
			$len        = $meta[1];
			$endPos     = $pos + $len;
			
			if($endPosMaxPrevChanged > $meta[0]){
				$e[$endPosMaxHolder . "([{$endPosMaxHolderReference[0]}, {$endPosMaxHolderReference[1]}])"][$i] = [$meta[0], $len];
			}
			
			if(isset($values[$i])){
				$value          = $values[$i];
				$valueLength   = strlen($value);
				
				$string = substr_replace($string, $value , $pos, $len);
				
				// 10 - 5 = 5;      стало на 5 больше
				// 5 - 10 = -5;     стало на 5 меньше
				// 0 - 10 = -10;    стало на 10 меньше
				// 10 - 0 = 10;     стало на 10 больше
				$difference = ($valueLength - $len);
				self::_processPreviousMeta($i, $pos, $len, $difference, $metadata);
				
				$previousOffsets+= $difference;
				
				if($endPos > $endPosMaxPrevChanged){
					$endPosMaxHolder = $i;
					$endPosMaxHolderReference = [$meta[0], $meta[1]];
					$endPosMaxPrevChanged = $endPos;
				}
				
				$meta[1] = $valueLength;
			}
			
			
			$meta[0] = $pos;
			
		}
		
		if($e){
			$a = [];
			foreach($e as $holder => $subPositions){
				$b = "When the holder {$holder} changes, we can lose the sub positions: \r\n";
				$bb = [];
				foreach($subPositions as $i => list($pos, $len) ){
					$bb[] = "\t{$i}([{$pos}, {$len}])";
				}
				$b.= implode("\r\n", $bb) . '. ';
				$a[] = $b;
			}
			throw new \Exception(implode("\r\n", $a));
		}
		
		return [$string, $metadata];
	}
	
	protected static function _processPreviousMeta($currentMetaIndex, $currentPos, $currentLen, $difference, &$metadata){
		if($difference!=0){
			$i = $currentMetaIndex - 1;
			
			if(isset($metadata[$i])){
				$_meta = &$metadata[$i];
				
				$pos = &$_meta[0];
				$len = &$_meta[1];
				
				if(($pos + $len) > $currentPos){
					$len+= $difference;
					self::_processPreviousMeta($i, $pos, $len, $difference, $metadata);
				}
			}
			
		}
	}
	
	/**
	 * @param array $array [ [pos, len], [pos, len], [pos, len] ]
	 * @param bool $preserveKeys
	 * @return array [ [pos, len], [pos, len], [pos, len] ]
	 */
	public static function sortByPosition(array $array, $preserveKeys = false){
		if($preserveKeys){
			uasort($array, [__CLASS__, 'comparePositions']);
		}else{
			usort($array, [__CLASS__, 'comparePositions']);
		}
		return $array;
	}
	
	/**
	 * @param array $a  [pos, len]
	 * @param array $b  [pos, len]
	 * @return int
	 */
	public static function comparePositions($a, $b){
		
		if($a == $b){
			return 0;
		}else if($a===null ^ $b===null){
			if($b===null){
				return 1;
			}else{
				return -1;
			}
		}
		
		if($a[0] == $b[0]){
			return 0;
		}
		return $a[0] > $b[0]? 1 : -1;
	}
	
	
	public function __toString(){
		return $this->string;
	}
	
	
}