<?php
	session_start();
	$uid = isset($_SESSION['user']) ? (int)$_SESSION['user'] : 0;
	if ($uid) {
		include("../settings/connect_datebase.php");
		$mysqli->query("UPDATE `users` SET active_session_id = NULL WHERE id = " . $uid);
	}
	session_destroy();
?>