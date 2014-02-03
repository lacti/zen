<?php
error_reporting(E_ALL); 

if (ini_get ("register_globals")) {
	ini_set ("register_globals", 0);
}

define ("HOME_URL", "/");
define ("ATTACH_BASE", "attach/");
$HEAD_TITLE = 'iMaso';

include_once "lib/database.inc";
include_once "lib/text.inc";
include_once "lib/common.php";
include_once "lib/attach.php";
include_once "lib/tag.php";
include_once "lib/thread.php";
include_once "lib/user.php";
include_once "lib/category.php";
include_once "lib/comment.php";

if(!ini_get("session.auto_start")) {
	@session_start();
}

$database = new CMySQLDatabase ($dbconf['host'], $dbconf['user'], $dbconf['pw'], $dbconf['dbname']);
$info = isset ($_SERVER['PATH_INFO'])? array_slice (explode ("/", $_SERVER['PATH_INFO']), 1): array ();

if (isset ($_SESSION['userIndex']) && $_SESSION['userIndex'] > 0) {
	$user = readUser ($_SESSION['userIndex']);
	if (!$user)
		logoutUser ();
} else {
	$user = autologinUser ();
}

ob_start ();
require "page/index.header.php";

if (!$info) {
	$statement = $database->createStatement ();
	$statement->set ("SELECT `index` FROM `pzen_thread` ORDER BY `regdate` DESC LIMIT 0, 1");
	$info = array ("thread", "read", $statement->selectField ("index"));
}

$dispatchFunction = "dispatch" . ucfirst ($info[0]);
$dispatchFunction (array_slice ($info, 1));

require "page/index.footer.php";
ob_end_flush ();
?>
