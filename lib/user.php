<?php
function dispatchUser ($info) {
	if ($info[0] == "action") {
		switch ($info[1]) {
			case "create":
			case "delete":
			case "update":
			case "login":
			case "logout":
				$function = $info[1] . "User";
				$function ($_POST);
				break;

			case "read":
				readUser ($_GET);
				break;

			default:
				_404 ();
				break;
		}
	} else {
		require_once "page/user.{$info[0]}.php";
	}
}

function createUser ($data) {
	if (!$data['email'] || !$data['password'] || !$data['password2'] || !$data['name']) {
		error ("invalid access.");
		return false;
	}

	if ($data['password'] != $data['password2']) {
		error ("mismatch password.");
		return false;
	}

	global $database;
	$statement = $database->createStatement ();
	$statement->set ("SELECT * FROM `user` WHERE `email` = %1",
		array ($data['email']));
	if ($statement->hasTuple ()) {
		error ("sorry, already exists email address");
		return false;
	}

	$statement = $database->createStatement ();
	$statement->set ("INSERT INTO `user` (`email`, `password`, `name`, `regdate`) VALUES (%1, MD5(PASSWORD(MD5(%2))), %3, NOW())",
		array ($data['email'], $data['password'], $data['name']));
	if ($statement->update ()) {
		$_SESSION['userIndex'] = $database->lastInsertId ();
		goHome ();

	} else {
		error ("cannot create account.", HOME_URL);
	}
}

function deleteUser ($data) {
	global $database;
	$statement = $database->createStatement ();
	$statement->set ("DELETE FROM `user` WHERE `index` = %1",
		array ($data['index']));
	return $statement->update ();
}

function readUser ($index) {
	global $database;
	$statement = $database->createStatement ();
	$statement->set ("SELECT * FROM `user` WHERE `index` = %1",
		array ($index));
	$retVal = $statement->fetchArray ();
	return $retVal;
}

function autologinPassword ($password) {
	global $database;
	$statement = $database->createStatement ();
	$statement->set ("SELECT PASSWORD(MD5(%1)) AS `pw`", array ($password));
	return $statement->selectField ("pw");
}

function autologinUser () {
	if (!isset ($_COOKIE['zAutologin']) || !$_COOKIE['zAutologin']) return false;

	global $database;
	$statement = $database->createStatement ();
	$statement->set ("SELECT * FROM `user` WHERE `email` = %1 AND `password` = MD5(%2)",
		array ($_COOKIE['zEmail'], $_COOKIE['zPassword']));
	if ($statement->hasTuple ()) {
		$user = $statement->fetchArray ();
		$_SESSION['userIndex'] = $user['index'];
		return $user;

	} else {
		return false;
	}
}

function loginUser ($data) {
	if (!isset ($data) || !$data || !isset ($data['email']) || !isset ($data['password'])) {
		error ("invalid access.");
		return false;
	}

	global $database;
	$statement = $database->createStatement ();
	$statement->set ("SELECT `index` FROM `user` WHERE `email` = %1 AND `password` = MD5(PASSWORD(MD5(%2)))",
		array ($data['email'], $data['password']));
	if ($statement->hasTuple ()) {
		$user = $statement->fetchArray ();
		$_SESSION['userIndex'] = $user['index'];

		if (isset ($data['autologin']) && $data['autologin'] == "on") {
			setcookie ("zAutologin", "1", time () + 30 * 24 * 3600, "/");
			setcookie ("zEmail", $data['email'], time () + 30 * 24 * 3600, "/");
			setcookie ("zPassword", autologinPassword ($data['password']), time () + 30 * 24 * 3600, "/");

		} else {
			expireAutologin ();
		}
		redirectBack ();

	} else {
		error ("invalid access.");
	}
}

function updateUser ($data) {
	global $database;
	$statement = $database->createStatement ();
	$statement->set ("UPDATE `user` SET `name` = %2 WHERE `index` = %1",
		array ($data['index'], $data['name']));
	return $statement->update ();
}

function expireAutologin () {
	setcookie ("zAutologin", "", time () - 3600, "/");
	setcookie ("zEmail", "", time () - 3600, "/");
	setcookie ("zPassword", "", time () - 3600, "/");
}

function logoutUser () {
	unset ($_SESSION['userIndex']);
	expireAutologin ();
	redirectBack ();
}
?>
