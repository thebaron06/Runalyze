<?php

namespace Runalyze\Calculation\Performance;

class FakeModel extends Model {
	public function calculateArrays() {
		foreach ($this->TRIMP as $i => $Trimp) {
			$this->Fitness[$i] = 2*$Trimp;
			$this->Fatigue[$i] = 5*$Trimp;
		}
	}
}

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2014-10-26 at 21:47:41.
 */
class MaximumCalculatorTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testWrongClosure() {
		new MaximumCalculator(function(){}, array());
	}

	/**
	 * @expectedException \InvalidArgumentException
	 */
	public function testNoModelInClosure() {
		new MaximumCalculator(function(){return new \StdClass();}, array());
	}

	public function testTSBclosure() {
		new MaximumCalculator(function(array $array){
			return new TSB($array);
		}, array());
	}

	public function testFakeModel() {
		$Calc = new MaximumCalculator(function(array $array){
			return new FakeModel($array);
		}, array(10, 0, 50, 100, 0, 75, 13));

		$this->assertEquals( 200, $Calc->maxFitness() );
		$this->assertEquals( 500, $Calc->maxFatigue() );
		$this->assertEquals( 100, $Calc->maxTrimp() );
	}

}