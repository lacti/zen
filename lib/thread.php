<?php
function dispatchThread ($info) {
	global $database, $user;

	if ($info[0] == "action") {
		switch ($info[1]) {
			case "create":
				createThread ($_POST);
				break;
			case "delete":
				deleteThread ($info[2]);
				break;
			case "update":
				updateThread ($_POST);
				break;
		}

	} else {
		require_once "page/thread.{$info[0]}.php";
	}
}

function createThread ($data) {
	global $database, $user;
	if (!$user) {
		error ("you should sign up poolc before posting.", $_SERVER['HTTP_REFERER']);
		return false;
	}

	if (!$data['subject'] || !$data['content']) {
		error ("you should write subject and content.");
		return false;
	}

	if ($data['tags']) $data['tags'] = convertTagsDatabaseFormatFromUserInput ($data['tags']);
	else $data['tags'] = "";

	$categoryIndex = createOrReadCategory ($data['category']);
	$statement = $database->createStatement ();
	$statement->set ("INSERT INTO `pzen_thread` (`categoryIndex`, `userIndex`, `name`, `subject`, `content`, `tags`, `editdate`, `regdate`, `searchHint`) VALUES (%1, %2, %3, %4, %5, %6, NOW(), NOW(), %7)",
		array ($categoryIndex, $user['index'], $user['name'], $data['subject'], $data['content'], $data['tags'], $data['searchHint']));
	if ($statement->update ()) {
		$threadIndex = $database->lastInsertId ();
		if (!createThreadAttachment ($threadIndex)) {
			error ("cannot upload attachments.");
			deleteThreadAttachment ($threadIndex);
		}

		appendThreadTags ($data['tags']);
		header ("location: " . HOME_URL . "thread/read/" . $threadIndex);

	} else {
		error ("cannot post thread.");
		return false;
	}
}

function readThread ($index) {
	global $database;
	$statement = $database->createStatement ();
	$statement->set ("SELECT *, UNIX_TIMESTAMP(`regdate`) AS `regdate`, UNIX_TIMESTAMP(`editdate`) AS `date` FROM `pzen_thread` WHERE `index` = %1",
		array ($index));
	return $statement->fetchArray ();
}

function deleteThread ($index, $forcely = false) {
	global $database, $user;

	$thread = readThread ($index);

	if (!$forcely && !$thread || !$user || $user['index'] != $thread['userIndex']) {
		error ("invalid access.");
		return false;
	}

	$statement = $database->createStatement ();
	$statement->set ("DELETE FROM `pzen_thread` WHERE `index` = %1",
		array ($index));
	if (!$statement->update ()) {
		error ("no authority.");
		return false;
	}

	deleteUnusedCategory ($thread['categoryIndex']);
	deleteCommentInThread ($index);
	deleteThreadTags ($thread['tags']);
	deleteThreadAttachment ($index);
	goHome ();
}

function updateThread ($data) {
	global $database, $user;
	
	if (!$user) {
		error ("you should sign up poolc before posting.", $_SERVER['HTTP_REFERER']);
		return false;
	}

	if (!$data['subject'] || !$data['content']) {
		error ("you should write subject and content.");
		return false;
	}

	$oldThread = readThread ($data['index']);
	if (!$oldThread) {
		error ("invalid access.");
		return false;
	}

	if ($data['tags']) $data['tags'] = convertTagsDatabaseFormatFromUserInput ($data['tags']);
	else $data['tags'] = "";

	$oldCategoryIndex = $oldThread['categoryIndex'];
	$categoryIndex = createOrReadCategory ($data['category']);

	$statement = $database->createStatement ();
	$statement->set ("UPDATE `pzen_thread` SET `subject` = %1, `content` = %2, `categoryIndex` = %3, `tags` = %4, `editdate` = NOW() WHERE `index` = %5 AND `userIndex` = %6",
		array ($data['subject'], $data['content'], $categoryIndex, $data['tags'], $data['index'], $user['index']));
	if ($statement->update ()) {
		deleteThreadAttachmentArray ($data['index'], $data['oldFileIndex']);
		createThreadAttachment ($data['index']);

		updateThreadTags ($oldThread['tags'], $data['tags']);
		if ($oldCategoryIndex != $categoryIndex)
			deleteUnusedCategory ($oldCategoryIndex);

		header ("location: " . HOME_URL . "thread/read/" . $data['index']);

	} else {
		error ("no authority.");
		return false;
	}
}

function appendThreadTags ($tags /* INPUT BY DB FORMAT */) {
	return appendTags ("thread", $tags);
}

function deleteThreadTags ($tags /* INPUT BY DB FORMAT */) {
	return deleteTags ("thread", $tags);
}

function updateThreadTags ($oldTags, $newTags) {
	return updateTags ("thread", $oldTags, $newTags);
}

function createThreadAttachment ($index) {
	return createAttachment ("thread", $index);
}

function deleteThreadAttachment ($index) {
	return deleteAttachment ("thread", $index);
}

function deleteThreadAttachmentArray ($threadIndex, $indices) {
	return deleteAttachmentArray ("thread", $threadIndex, $indices);
}
?>
