<?php
/**
 * This file contains class::Inserter
 * @package Runalyze\Model\HRV
 */

namespace Runalyze\Model\HRV;

use Runalyze\Model;

/**
 * Insert hrv data to database
 * 
 * @author Hannes Christiansen
 * @package Runalyze\Model\HRV
 */
class Inserter extends Model\InserterWithAccountID {
	/**
	 * Object
	 * @var \Runalyze\Model\HRV\Object
	 */
	protected $Object;

	/**
	 * Construct inserter
	 * @param \PDO $connection
	 * @param \Runalyze\Model\HRV\Object $object [optional]
	 */
	public function __construct(\PDO $connection, Object $object = null) {
		parent::__construct($connection, $object);
	}

	/**
	 * Tablename without prefix
	 * @return string
	 */
	protected function table() {
		return 'hrv';
	}

	/**
	 * Keys to insert
	 * @return array
	 */
	protected function keys() {
		return array_merge(array(
				self::ACCOUNTID
			),
			Object::allProperties()
		);
	}
}