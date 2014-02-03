<?php
function dispatchCategory ($info) {
	global $database, $user;
	require_once "page/category.threads.php";
}

function getCategoryName ($index) {
	global $database;
	
	$statement = $database->createStatement ();
	$statement->set ("SELECT `name` FROM `pzen_category` WHERE `index` = %1",
		array ($index));
	return $statement->selectField ("name");
}

function getCategoryIndex ($name) {
	global $database;
	
	$statement = $database->createStatement ();
	$statement->set ("SELECT `index` FROM `pzen_category` WHERE `name` = %1",
		array ($name));
	return $statement->selectField ("index");
}

function getCategoryInformation ($index) {
	global $database;
	
	$statement = $database->createStatement ();
	$statement->set ("SELECT * FROM `pzen_category_view` WHERE `index` = %1",
		array ($index));
	return $statement->fetchArray ();
}

function createOrReadCategory ($name) {
	global $database;
	if (!$name) return /* 기타 */1;

	$name = strtolower ($name); /* allow only lower case */
	$statement = $database->createStatement ();
	$statement->set ("SELECT `index` FROM `pzen_category` WHERE `name` = %1",
		array ($name));
	if (!$statement->hasTuple ()) {
		$statement = $database->createStatement ();
		$statement->set ("INSERT INTO `pzen_category` (`name`) VALUES (%1)",
			array ($name));
		$index = $statement->update ()? $database->lastInsertId (): 1;
		
	} else {
		$index = $statement->selectField ("index");
	}
	return $index;
}

function deleteUnusedCategory ($index) {
	if (!$index || $index <= 1) return true;

	global $database;
	$statement = $database->createStatement ();
	$statement->set ("SELECT `count` FROM `pzen_category_view` WHERE `index` = %1",
		array ($index));
	if (!$statement->hasTuple () || $statement->selectField ("count") == 0) {
		$statement = $database->createStatement ();
		$statement->set ("DELETE FROM `pzen_category` WHERE `index` = %1",
			array ($index));
		$statement->update ();
	}
}
?>
