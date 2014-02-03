<?php
function dispatchTag ($info) {
	global $database, $user;

	require "page/tag.{$info[0]}.php";
}

function convertTagsDatabaseFormatFromUserInput ($tags) {
	$newTags = array ();
	foreach (explode (",", $tags) as $tag) {
		if (strlen (($tag = trim ($tag))) > 0 && !in_array ($tag, $newTags))
			array_push ($newTags, strtolower ($tag));
	}
	return "*". implode ("*", $newTags) ."*";
}

function convertTagsDisplayFormatFromDatabase ($tags) {
	return implode (", ", getTagsArrayFromDatabaseFormat ($tags));
}

function getTagsArrayFromDatabaseFormat ($tags) {
	$newTags = array ();
	foreach (explode ("*", $tags) as $tag) {
		if (strlen (($tag = trim ($tag))) > 0 && !in_array ($tag, $newTags))
			array_push ($newTags, strtolower ($tag));
	}
	return $newTags;
}

function appendTags ($tableName, $tags /* INPUT BY DB FORMAT */) {
	if (!$tags) return true;
	return appendTagArray ($tableName, getTagsArrayFromDatabaseFormat ($tags));
}

function appendTagArray ($tableName, $tagArray) {
	global $database;
	$success = true;

	foreach ($tagArray as $tag) {
		$statement = $database->createStatement ();
		$statement->set ("UPDATE `pzen_{$tableName}_tags` SET `count` = `count` + 1 WHERE `tag` = %1",
			array ($tag));
		if ($statement->update () && $statement->affectedRows () == 0) {
			$statement = $database->createStatement ();
			$statement->set ("INSERT INTO `pzen_{$tableName}_tags` (`tag`) VALUES (%1)",
				array ($tag));
			echo $statement;
			$success = $success && $statement->update ();
		}
	}
	return $success;
}

function deleteTags ($tableName, $tags /* INPUT BY DB FORMAT */) {
	if (!$tags) return true;
	return deleteTagArray ($tableName, getTagsArrayFromDatabaseFormat ($tags));
}

function deleteTagArray ($tableName, $tagArray) {
	global $database;
	$success = true;

	foreach ($tagArray as $tag) {
		$statement = $database->createStatement ();
		$statement->set ("UPDATE `pzen_{$tableName}_tags` SET `count` = `count` - 1 WHERE `tag` = %1",
			array ($tag));
		$success = $success && $statement->update ();
	}
	$statement = $database->createStatement ();
	$statement->set ("DELETE FROM `pzen_{$tableName}_tags` WHERE `count` < 1");
	return $statement->update () && $success;
}

function updateTags ($tableName, $oldTags, $newTags) {
	$oldTagArray = getTagsArrayFromDatabaseFormat ($oldTags);
	$newTagArray = getTagsArrayFromDatabaseFormat ($newTags);
	$addTagSet = array_diff ($newTagArray, $oldTagArray);
	$removeTagSet = array_diff ($oldTagArray, $newTagArray);

	$success = deleteTagArray ($tableName, $removeTagSet);
	$success = $success && appendTagArray ($tableName, $addTagSet);
	return $success;
}
?>
