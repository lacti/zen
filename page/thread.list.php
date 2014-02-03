<?php
$statement = $database->createStatement ();
$statement->set ("SELECT `subject` FROM `pzen_thread`");
while ($data = $statement->fetchArray ()) {
	var_dump ($data);
}
?>