<?php
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.0 on 2014-01-28 at 14:46:11.
 */
class ElevationCorrectorGoogleMapsTest extends PHPUnit_Framework_TestCase {

	/**
	 * @var ElevationCorrectorGoogleMaps
	 */
	protected $object;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 */
	protected function setUp() {}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 */
	protected function tearDown() {}

	/**
	 * @covers ElevationCorrectorGoogleMaps::canHandleData
	 * @covers ElevationCorrectorGoogleMaps::correctElevation
	 */
	public function testSimpleData() {
		$Corrector = new ElevationCorrectorGoogleMaps(
			array(49.444722), 
			array(7.768889)
		);

		$this->assertTrue( $Corrector->canHandleData() );

		$Corrector->correctElevation();

		$this->assertEquals( array(237), $Corrector->getCorrectedElevation() );
	}

	/**
	 * @covers ElevationCorrectorGoogleMaps::canHandleData
	 * @covers ElevationCorrectorGoogleMaps::correctElevation
	 */
	public function testSimplePath() {
		$Corrector = new ElevationCorrectorGoogleMaps(
			array(49.440, 49.441, 49.442, 49.443, 49.444, 49.445, 49.446, 49.447, 49.448, 49.449, 49.450), 
			array(7.760, 7.761, 7.762, 7.763, 7.764, 7.765, 7.766, 7.767, 7.768, 7.769, 7.770)
		);

		$this->assertTrue( $Corrector->canHandleData() );

		$Corrector->correctElevation();

		$this->assertEquals( array(238, 238, 238, 238, 238, 237, 237, 237, 237, 237, 263), $Corrector->getCorrectedElevation() );
	}
}
