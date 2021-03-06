<?php

namespace Runalyze\Model\Swimdata;

use PDO;
use DB;

class InvalidInserterObjectForSwimdata_MockTester extends \Runalyze\Model\Object {
	public function properties() {
		return array('foo');
	}
}

/**
 * Generated by hand
 */
class InserterTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @var \PDO
	 */
	protected $PDO;

	protected function setUp() {
		$this->PDO = DB::getInstance();
		$this->PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	protected function tearDown() {
		$this->PDO->exec('TRUNCATE `'.PREFIX.'swimdata`');
	}

	/**
	 * @expectedException \PHPUnit_Framework_Error
	 */
	public function testWrongObject() {
		new Inserter($this->PDO, new InvalidInserterObjectForSwimdata_MockTester);
	}

	public function testSimpleInsert() {
		$R = new Object(array(
			Object::ACTIVITYID => 1,
			Object::POOL_LENGTH => 2500,
			Object::STROKE => array(25, 20, 15, 20),
			Object::STROKETYPE => array(2, 2, 2, 2)
		));

		$I = new Inserter($this->PDO, $R);
		$I->setAccountID(1);
		$I->insert();

		$data = $this->PDO->query('SELECT * FROM `'.PREFIX.'swimdata` WHERE `activityid`=1')->fetch(PDO::FETCH_ASSOC);
		$N = new Object($data);

		$this->assertEquals(1, $data[Inserter::ACCOUNTID]);
		$this->assertEquals(2500, $N->poollength());
		$this->assertEquals(array(25, 20, 15, 20), $N->stroke());
		$this->assertEquals(array(2, 2, 2, 2), $N->stroketype());
	}

}
