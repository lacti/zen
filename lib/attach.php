<?php
@mkdir (ATTACH_BASE);

function dispatchAttach ($info) {
	switch ($info[0]) {
		case "thread":
		case "comment":
			downloadAttachment ($info[0], $info[1]);
			break;
		case "list":
			require "page/attach.list.php";
			break;
	}
}

function createAttachment ($table, $threadIndex) {
	if (!isset ($_FILES['upload'])) return true;

	global $database;
	for ($index = 0; $index < count ($_FILES['upload']['name']); $index++) {
		if (strlen ($_FILES['upload']['name'][$index]) == 0)
			continue;

		$name   = end (explode ("/", $_FILES['upload']['name'][$index]));
		// $name   = isWindows ()? mb_convert_encoding ($name, "UTF-8", "CP949"): $name;
		$hash   = md5 ($_FILES['upload']['tmp_name'][$index] . $_FILES['upload']['name'][$index] . time ());
		$target = ATTACH_BASE . $hash;
		$type   = $_FILES['upload']['type'][$index]? $_FILES['upload']['type'][$index]: "application/octet-stream";

		if (move_uploaded_file ($_FILES['upload']['tmp_name'][$index], $target)) {
			$statement = $database->createStatement ();
			$statement->set ("INSERT INTO `pzen_{$table}_attach` (`{$table}Index`, `hash`, `name`, `type`, `size`, `regdate`) VALUES (%1, %2, %3, %4, %5, NOW())",
				array ($threadIndex, $hash, $name, $type, $_FILES['upload']['size'][$index]));

			if (!$statement->update ()) {
				return false;
			}
		} else {
			return false;
		}
	}
	return true;
}

function deleteAttachment ($table, $index) {
	global $database;

	$statement = $database->createStatement ();
	$statement->set ("SELECT `hash` FROM `pzen_{$table}_attach` WHERE `{$table}Index` = %1",
		array ($index));

	while ($attach = $statement->fetchArray ()) {
		@unlink (ATTACH_BASE . $attach['hash']);
	}

	$statement = $database->createStatement ();
	$statement->set ("DELETE FROM `pzen_{$table}_attach` WHERE `{$table}Index` = %1",
		array ($index));
	return $statement->update ();
}

function deleteAttachmentArray ($table, $baseIndex, $indices) {
	global $database;

	$querySet = array ();
	if ($indices && is_array ($indices)) {
		foreach ($indices as $attachIndex) {
			array_push ($querySet, "`index` = {$attachIndex}");
		}
	}
	$notQuery = count ($querySet) == 0? "": " AND NOT (" . implode (" OR ", $querySet) . ")";
	
	$statement = $database->createStatement ();
	$statement->set ("SELECT `hash` FROM `pzen_{$table}_attach` WHERE `{$table}Index` = %1{$notQuery}",
		array ($baseIndex));
	while ($attach = $statement->fetchArray ()) {
		@unlink (ATTACH_BASE . $attach['hash']);
	}

	$statement = $database->createStatement ();
	$statement->set ("DELETE FROM `pzen_{$table}_attach` WHERE `{$table}Index` = %1{$notQuery}",
		array ($baseIndex));
	return $statement->update ();
}

function readAttachment ($table, $index) {
	global $database;
	$statement = $database->createStatement ();
	$statement->set ("SELECT * FROM `pzen_{$table}_attach` WHERE `index` = %1",
		array ($index));
	return $statement->fetchArray ();
}

function downloadAttachment ($table, $index) {
	global $database;

	$attach = readAttachment ($table, $index);
	if (!$attach) {
		error ("attachment doesn't exist.");
		return false;
	}

	$attach['name'] = isWindows ()? mb_convert_encoding ($attach['name'], "CP949", "UTF-8"): $attach['name'];

	// fix for IE catching or PHP bug issue
	header("Pragma: public");
	header("Expires: 0"); // set expiration time
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	// browser must download file from server instead of cache

	// force download dialog
	//header("Content-Type: application/force-download");
	header("Content-Type: " . $attach['type']);
	//header("Content-Type: application/download");

	// use the Content-Disposition header to supply a recommended filename and
	// force the browser to display the save dialog.
	header("Content-Disposition: attachment; filename=\"{$attach['name']}\";");

	/*
	The Content-transfer-encoding header should be binary, since the file will be read
	directly from the disk and the raw bytes passed to the downloading computer.
	The Content-length header is useful to set for downloads. The browser will be able to
	show a progress meter as a file downloads. The content-lenght can be determines by
	filesize function returns the size of a file.
	*/
	header("Content-Transfer-Encoding: binary");
	header("Content-Length: {$attach['size']}");

	ob_end_clean ();
	@readfile(ATTACH_BASE . $attach['hash']);
}
?>
