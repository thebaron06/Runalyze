<?php
/**
 * Script to refactor equipment
 * 
 * You have to set your database connection within this file to enable the script.
 * Remember to delete your credentials afterwards to protect this script.
 */
$hostname = '';
$database = '';
$username = '';
$password = '';

define('PREFIX', 'runalyze_');
define('LIMIT', 100); // Limit number of accounts to refactor per request
define('CLI', false); // Set to true if running from command line
define('SET_GLOBAL_PROPERTIES', false); // Set to true to set max_allowed_packet and key_buffer_size for mysql

// Uncomment these lines to unset time/memory limits
#@ini_set('memory_limit', '-1');
#if (!ini_get('safe_mode')) { @set_time_limit(0); }



/*******************************************************************************
 * SCRIPT STARTS - YOU DON'T NEED TO CHANGE ANYTHING BELOW
 ******************************************************************************/

$starttime = microtime(true);

/**
 * Protect script
 */
define('NL', CLI ? PHP_EOL : '<br>'.PHP_EOL);

if (empty($database) && empty($hostname)) {
	echo 'Database connection has to be set within the file.'.NL;
	exit;
} else {
	date_default_timezone_set('Europe/Berlin');

	try {
		$PDO = new PDO('mysql:dbname='.$database.';host='.$hostname, $username, $password);
		$PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$PDO->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

		if (SET_GLOBAL_PROPERTIES) {
			$PDO->exec('SET GLOBAL max_allowed_packet=1073741824;');
			$PDO->exec('SET GLOBAL key_buffer_size=1073741824;');
		}
	} catch (Exception $E) {
		echo 'Database connection failed: '.$E->getMessage().NL;
		exit;
	}
}

/**
 * Check version
 */
$IsNotRefactored = $PDO->query('SHOW TABLES LIKE "'.PREFIX.'shoe"');

if (!$IsNotRefactored) {
	echo 'The database is already refactored.'.NL;
	exit;
}


/**
 * Overview for data
 */
$HasTableEq = $PDO->query('SHOW TABLES LIKE "'.PREFIX.'equipment"');
$HasTableEqT = $PDO->query('SHOW TABLES LIKE "'.PREFIX.'equipment_type"');
$HasTableEqS = $PDO->query('SHOW TABLES LIKE "'.PREFIX.'equipment_sport"');
$HasTableEqAE = $PDO->query('SHOW TABLES LIKE "'.PREFIX.'activity_equipment"');

if (!$HasTableEq OR !$HasTableEqT OR !$HasTableEqS OR !$HasTableEqAE) {
	echo 'Cannot find equipment tables. Please run latest sql updates.';
	exit;
} else {
    $countAccount = $PDO->query('SELECT COUNT(*) FROM `'.PREFIX.'account`')->fetchColumn();
}

echo NL;


$HasColumnAccount = $PDO->query('SHOW COLUMNS FROM `'.PREFIX.'account` LIKE "refactored"')->fetch();

if (!$HasColumnAccount) {
	$PDO->exec('ALTER TABLE `'.PREFIX.'account` ADD `refactored` TINYINT NOT NULL AFTER `id`');
	echo 'Added column \'refactored\' to table account.'.NL;     
}

$count = $PDO->query('SELECT COUNT(*) FROM `'.PREFIX.'account` WHERE `refactored`=1')->fetchColumn();

echo 'Table '.PREFIX.'account has column \'refactored\'.'.NL;
echo ' - refactored: '.$count.'/'.$countAccount.NL;
echo NL;

if ($count < $countAccount) {
	$accounts = $PDO->query('SELECT `id`, `language` FROM `'.PREFIX.'account` WHERE `refactored`=0 LIMIT '.LIMIT);

	$InsertShoeinType = $PDO->prepare('INSERT INTO '.PREFIX.'equipment_type (`name`, `input`, `max_km`, `accountid`) VALUES (:name, 0, 1000, :accountid)');
	$InsertShoeinEqp = $PDO->prepare('INSERT INTO '.PREFIX.'equipment (`name`, `typeid`, `accountid`, `notes`, `distance`, `time`, `additional_km`, `date_start`,`date_end`) VALUES (:name, :typeid, :accountid, :notes, :distance, :time, :additional_km, :date_start, :date_end)');
	$InsertClothesinType = $PDO->prepare('INSERT INTO '.PREFIX.'equipment_type (`name`, `input`, `accountid`) VALUES (:name, 1, :accountid)');
	$InsertSport = $PDO->prepare('INSERT INTO '.PREFIX.'equipment_sport (`sportid`, `equipment_typeid`) VALUES (:sportid, :typeid)');
	$InsertClotheinEqp = $PDO->prepare('INSERT INTO '.PREFIX.'equipment (`name`, `typeid`, `notes`, `accountid`) VALUES (:name, :typeid, "", :accountid)');
	$InsertEquipActivity = $PDO->prepare('INSERT INTO '.PREFIX.'activity_equipment (`activityid`, `equipmentid`) VALUES (:activityid, :equipmentid)');

    while ($Row = $accounts->fetch()) {
        $InsertShoeinType->execute(array(
            ':name' => __shoe($Row['language']),
            ':accountid' => $Row['id']
        ));
        $ShoeTypeId = $PDO->lastInsertId();
        
		$shoetable = $PDO->query('SELECT `id`, `name`, `since`, `weight`, `km`, `time`, `additionalKm`, `inuse`  FROM `'.PREFIX.'shoe` WHERE `accountid`='.$Row['id']); 
		$shoeMap = array();

		while ($shoe = $shoetable->fetch()) {
			$lastUse = !$shoe['inuse'] ? $PDO->query('SELECT MAX(`time`) FROM `'.PREFIX.'training` WHERE `shoeid`='.$shoe['id'])->fetchColumn() : '';
			$InsertShoeinEqp->execute(array(
				':name' => $shoe['name'],
				':typeid' => $ShoeTypeId,
				':accountid' => $Row['id'],
				':notes' => $shoe['weight'] > 0 ? __weight($Row['language']).': '.$shoe['weight'].'g' : '',
				':distance' => $shoe['km'],
				':time' => $shoe['time'],
				':additional_km' => $shoe['additionalKm'],
				':date_start' => strtotime($shoe['since']) ? date('Y-m-d', strtotime($shoe['since'])) : null,
				':date_end' => !$shoe['inuse'] ? date('Y-m-d', $lastUse) : null
			));    
			$shoeMap[$shoe['id']] = $PDO->lastInsertId(); 
		}

		// Refactor clothes table to equipment
		$InsertClothesinType->execute(array(
			':name' => __clothes($Row['language']),
			':accountid' => $Row['id']
		));
		$ClothesTypeId = $PDO->lastInsertId();

		// link every sport to every type id
		$userSport = $PDO->query('SELECT `id` FROM `'.PREFIX.'sport` WHERE `accountid`='.$Row['id']);
		while ($sport = $userSport->fetch()) {  
			$InsertSport->execute(array(
				':sportid' => $sport['id'],
				':typeid' => $ShoeTypeId
			));    
			$InsertSport->execute(array(
				':sportid' => $sport['id'],
				':typeid' => $ClothesTypeId
			));
		}

		$clothestable = $PDO->query('SELECT `id`, `name`, `accountid` FROM `'.PREFIX.'clothes` WHERE `accountid`='.$Row['id']);  
		$clothesMap = array();

		while ($clothes = $clothestable->fetch()) {    
			$InsertClotheinEqp->execute(array(
				':name' => $clothes['name'],
				':typeid' => $ClothesTypeId,
				':accountid' => $Row['id']
			)); 
		   $clothesMap[$clothes['id']] = $PDO->lastInsertId(); 
		}

		// Refactor training table to equipment
		$trainings = $PDO->query('SELECT `id`, `clothes`, `shoeid` FROM `'.PREFIX.'training` WHERE `accountid`='.$Row['id']);    
		while ($training = $trainings->fetch()) {
			if ($training['shoeid'] != 0) {
				$InsertEquipActivity->execute(array(
					':activityid' => $training['id'],
					':equipmentid' => $shoeMap[$training['shoeid']]
				)); 
			}

			if (!empty($training['clothes'])) {
				if (strpos($training['clothes'], ',')) {
					$clothes = explode(',', $training['clothes']);
				} else {
					$clothes = array($training['clothes']);
				}

				foreach ($clothes as $clot) {
					$InsertEquipActivity->execute(array(
						':activityid' => $training['id'],
						':equipmentid' => $clothesMap[trim($clot)]
					));
				}
			}
		}

		$PDO->query('UPDATE `'.PREFIX.'account` SET `refactored` = 1 WHERE `id`='.$Row['id']);

		echo '.'.(CLI ? '' : ' ');
	}

	echo 'done;'.NL;
	echo NL;
	echo 'Time: '.(microtime(true) - $starttime).'s'.NL;
	echo 'Memory peak: '.memory_get_peak_usage().'B'.NL;
	echo NL;

	if (CLI) {
		echo '... call the script again to continue'.NL;
	} else {
		echo '... <a href="javascript:location.reload()">reload to continue</a>';
	}
}


if ($count + LIMIT >= $countAccount) {
	echo 'You are done. All rows are refactored.'.NL;

	$PDO->exec('ALTER TABLE `'.PREFIX.'account` DROP `refactored`');

	$PDO->exec('DROP TABLE IF EXISTS `'.PREFIX.'shoe`, `'.PREFIX.'clothes`');
	echo 'All unused tables (shoe, clothe) have been dropped.'.NL;
        
	$PDO->exec('ALTER TABLE `'.PREFIX.'training` DROP `shoeid`, DROP `clothes`');
	echo 'All unused columns from training (shoeid, clothes) have been dropped.'.NL;

	// Recalculate all distance and time data of all usersq
	$PDO->exec('UPDATE runalyze_equipment 
					CROSS JOIN(SELECT eqp.id AS `eqpid`, SUM(tr.distance) AS `km`, SUM(tr.s)+eqp.additional_km AS `s` 
						FROM runalyze_equipment eqp 
							LEFT JOIN runalyze_activity_equipment aeqp ON eqp.id = aeqp.equipmentid 
							LEFT JOIN runalyze_training tr ON aeqp.activityid = tr.id GROUP BY eqp.id) 
				AS NEW SET distance = NEW.km, TIME = NEW.s WHERE id = NEW.eqpid;');

	echo NL;
	echo 'Remember to unset your credentials within this file.'.NL;
	echo '(Or simply delete this file if you are not working on our git repository)'.NL;
}

function notNull($value) {
	if (is_null($value))
		return '';

	return $value;
}

function __shoe($lang) {
	switch ($lang) {
		case 'pl':
			return 'Buty';
		case 'ca':
			return 'Sabatilles';
		case 'de':
		case '':
			return 'Laufschuhe';
		case 'en':
		default:
			return 'Running shoes';
	}
}

function __clothes($lang) {
	switch ($lang) {
		case 'pl':
			return 'Ubranie';
		case 'ca':
			return 'Roba';
		case 'de':
		case '':
			return 'Kleidung';
		case 'en':
		default:
			return 'Clothes';
	}
}

function __weight($lang) {
	switch ($lang) {
		case 'pl':
			return 'Waga';
		case 'ca':
			return 'Pes';
		case 'de':
		case '':
			return 'Gewicht';
		case 'en':
		default:
			return 'Weight';
	}
}