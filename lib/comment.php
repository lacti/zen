<?php
function dispatchComment ($info) {
	global $database, $user;

	if ($info[0] == "action") {
		switch ($info[1]) {
			case "create":
				createComment ($_POST);
				break;
			case "delete":
				deleteComment ($info[2]);
				break;
			case "update":
				updateComment ($_POST);
				break;
		}

	} else {
		require_once "page/comment.{$info[0]}.php";
	}
}

function createComment ($data) {
	global $database, $user;
	if (!$user) {
		error ("you should sign up poolc before comment.", $_SERVER['HTTP_REFERER']);
		return false;
	}

	if (!$data['content']) {
		error ("you should write content.");
		return false;
	}

	if ($data['tags']) $data['tags'] = convertTagsDatabaseFormatFromUserInput ($data['tags']);
	else $data['tags'] = "";

	$statement = $database->createStatement ();
	$statement->set ("INSERT INTO `pzen_comment` (`threadIndex`, `userIndex`, `name`, `content`, `tags`, `editdate`, `regdate`) VALUES (%1, %2, %3, %4, %5, NOW(), NOW())",
		array ($data['threadIndex'], $user['index'], $user['name'], $data['content'], $data['tags']));
	if ($statement->update ()) {
		$commentIndex = $database->lastInsertId ();
		createCommentAttachment ($commentIndex);

		appendCommentTags ($data['tags']);
		header ("location: " . HOME_URL . "thread/read/{$data['threadIndex']}#comment" . $commentIndex);

	} else {
		error ("cannot comment.");
		return false;
	}
}

function readComment ($index) {
	global $database;

	$statement = $database->createStatement ();
	$statement->set ("SELECT * FROM `pzen_comment` WHERE `index` = %1",
		array ($index));
	return $statement->fetchArray ();
}

function updateComment ($data) {
	global $database, $user;
	if (!$user) {
		error ("no authority.", $_SERVER['HTTP_REFERER']);
		return false;
	}

	if (!$data['content']) {
		error ("you should write content.");
		return false;
	}

	if ($data['tags']) $data['tags'] = convertTagsDatabaseFormatFromUserInput ($data['tags']);
	else $data['tags'] = "";

	$statement = $database->createStatement ();
	$statement->set ("UPDATE `pzen_comment` SET `content` = %1, `tags` = %2, `editdate` = NOW() WHERE `index` = %3",
		array ($data['content'], $data['tags'], $data['index']));
	if ($statement->update ()) {
		deleteCommentAttachmentArray ($data['index'], $data['oldFileIndex']);
		createCommentAttachment ($data['index']);
		updateCommentTags ($data['tags']);

		header ("location: " . HOME_URL . "thread/read/{$data['threadIndex']}#comment{$data['index']}");

	} else {
		error ("cannot update comment.");
		return false;
	}
}

function deleteComment ($index, $forcely = false) {
	global $database, $user;

	$comment = readComment ($index);
	if (!$forcely && !$comment || !$user || $comment['userIndex'] != $user['index']) {
		error ("invalid access.");
		return false;
	}

	$statement = $database->createStatement ();
	$statement->set ("DELETE FROM `pzen_comment` WHERE `index` = %1",
		array ($index));
	if ($statement->update ()) {
		deleteCommentAttachment ($index);
		deleteCommentTags ($comment['tags']);

		header ("Location: " . HOME_URL . "thread/read/{$comment['threadIndex']}");
	} else {
		error ("cannot delete comment.");
		return false;
	}
}

function deleteCommentInThread ($threadIndex) {
	global $database;
	$statement = $database->createStatement ();
	$statement->set ("DELETE FROM `pzen_comment` WHERE `threadIndex` = %1",
		array ($threadIndex));
	return $statement->update ();
}

function appendCommentTags ($tags /* INPUT BY DB FORMAT */) {
	return appendTags ("comment", $tags);
}

function deleteCommentTags ($tags /* INPUT BY DB FORMAT */) {
	return deleteTags ("comment", $tags);
}

function updateCommentTags ($oldTags, $newTags) {
	return updateTags ("comment", $oldTags, $newTags);
}

function countThreadComment ($threadIndex) {
	global $database;
	$statement = $database->createStatement ();
	$statement = $statement->set ("SELECT COUNT(`index`) AS `count` FROM `pzen_comment` WHERE `threadIndex` = %1",
		array ($threadIndex));
	return $statement->hasTuple ()? $statement->selectField ("count"): 0;
}

function createCommentAttachment ($index) {
	return createAttachment ("comment", $index);
}

function deleteCommentAttachment ($index) {
	return deleteAttachment ("comment", $index);
}

function deleteCommentAttachmentArray ($commentIndex, $indices) {
	return deleteAttachmentArray ("comment", $commentIndex, $indices);
}
?>