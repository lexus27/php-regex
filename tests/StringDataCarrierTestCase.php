<?php
/**
 * @Creator Alexey Kutuzov <lexus27.khv@gmail.com>
 * @Author: Alexey Kutuzov <lexus27.khv@gmai.com>
 * @Project: php-regex
 */

namespace Jungle\Regex\Tests;


use Jungle\Regex\StringDataCarrier;
use PHPUnit\Framework\TestCase;

class StringDataCarrierTestCase extends TestCase{
	
	public function testSimple(){
		$s = '[block1] [block2] [block3]';
		$m = [
			[ 0, 8 ],
			[ 9, 8 ],
			[ 18, 8 ]
		];
		$a = [
			'b1',
			'b2',
			'b3',
		];
		
		$sc = new StringDataCarrier($s,$m ,$a);
		
		
		$sc->set('b1', 'изменил строчку здесь');
		$this->assertEquals('изменил строчку здесь',$sc->get('b1'));
		
		
		$sc->set('b1', '[block]');
		$this->assertEquals('[block]',$sc->get('b1'));
		
		
		$sc->set('b1', '[b2312losdfsck1]');
		$this->assertEquals('[b2312losdfsck1]',$sc->get('b1'));
		
		
		$sc->set('b1', '[blosdfsdck2]');
		$this->assertEquals('[blosdfsdck2]',$sc->get('b1'));
		
		
		$sc->set('b1', '[XXXX');
		$this->assertEquals('[XXXX',$sc->get('b1'));
		
		
		
		$data = $sc->getData();
		$this->assertEquals([
			'b1' => '[XXXX',
			'b2' => '[block2]',
			'b3' => '[block3]',
		],$data);
		$this->assertEquals('[XXXX',  $sc->get('b1'));
		$this->assertEquals('[block2]',  $sc->get('b2'));
		$this->assertEquals('[block3]',  $sc->get('b3'));
		
		
		
		$sc->setData([ '','','' ]);
		$data = $sc->getData();
		$this->assertEquals([
			'b1' => '',
			'b2' => '',
			'b3' => '',
		],$data);
		$this->assertEquals('',  $sc->get('b1'));
		$this->assertEquals('',  $sc->get('b2'));
		$this->assertEquals('',  $sc->get('b3'));
		
		
		
		$sc->setData([ 'a','b','c' ]);
		$data = $sc->getData();
		$this->assertEquals([
			'b1' => 'a',
			'b2' => 'b',
			'b3' => 'c',
		],$data);
		$this->assertEquals('a',  $sc->get('b1'));
		$this->assertEquals('b',  $sc->get('b2'));
		$this->assertEquals('c',  $sc->get('b3'));
	}
	
	/**
	 *
	 */
	public function testReplace(){
		
		
		$string = '[block1] [block2] [block3]';
		
		$metadata = [
			[ 0, 8 ],
			[ 9, 8 ],
			[ 18, 8 ]
		];
		
		
		list($string, $metadata) = StringDataCarrier::replace($string, $metadata, [0 => '[ABC]']);
		
		
		
		
		
		
	}
	
	/**
	 *
	 */
	public function testReplaceNested(){
		
		$string = '[block1: [nested1] [nested2]] [block2] [block3]';
		
		$metadata = [
			[ 0, 29 ],
			[ 9, 9 ],
			[ 19, 9 ],
			[ 30, 8 ],
			[ 39, 8 ]
		];
		
		
		list($string, $metadata) = StringDataCarrier::replaceNested($string, $metadata, [
			1 => '[nested____]'
		]);
		
		
		list($string, $metadata) = StringDataCarrier::replaceNested($string, $metadata, [
			1 => '[nested0]'
		]);
		
		
		
		$e = null;
		try{
			list($string, $metadata) = StringDataCarrier::replaceNested($string, $metadata, [
				0 => '[nested0]'
			]);
		}catch(\Exception $e){}
		
		$this->assertEquals(
			"When the holder 0([0, 29]) changes, we can lose the sub positions: \r\n\t1([9, 9])\r\n\t2([19, 9]). ",
			$e->getMessage()
		);
		
	}
	
	
}


