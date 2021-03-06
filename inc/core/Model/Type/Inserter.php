<?php
/**
 * This file contains class::Inserter
 * @package Runalyze\Model\Type
 */

namespace Runalyze\Model\Type;

use Runalyze\Model;

/**
 * Insert type to database
 * 
 * @author Hannes Christiansen
 * @package Runalyze\Model\Type
 */
class Inserter extends Model\InserterWithAccountID {
	/**
	 * Object
	 * @var \Runalyze\Model\Type\Object
	 */
	protected $Object;

	/**
	 * Construct inserter
	 * @param \PDO $connection
	 * @param \Runalyze\Model\Type\Object $object [optional]
	 */
	public function __construct(\PDO $connection, Object $object = null) {
		parent::__construct($connection, $object);
	}

	/**
	 * Tablename without prefix
	 * @return string
	 */
	protected function table() {
		return 'type';
	}

	/**
	 * Keys to insert
	 * @return array
	 */
	protected function keys() {
		return array_merge(array(
				self::ACCOUNTID
			),
			Object::allDatabaseProperties()
		);
	}
}