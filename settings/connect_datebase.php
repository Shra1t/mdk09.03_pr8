<?php
	$mysqli = new mysqli('127.0.0.1', 'root', '', 'security');
	
	// Одна сессия на пользователя: если залогинен с другого браузера — текущая сессия недействительна
	if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['user']) && $_SESSION['user'] != -1) {
		$uid = (int)$_SESSION['user'];
		$res = $mysqli->query("SELECT active_session_id FROM `users` WHERE id = " . $uid);
		if ($res && $row = $res->fetch_assoc()) {
			$stored = $row['active_session_id'];
			$current_sid = session_id();
			if ($stored !== null && $stored !== '' && $stored !== $current_sid) {
				session_destroy();
				if (php_sapi_name() !== 'cli') {
					header("Location: login.php?expired=1");
					exit;
				}
			}
		}
	}
	
	function getClientIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
    $user_ip = getClientIP();

	setcookie("IP", $user_ip);

	setcookie("Datetime", date("Y-m-d H:i:s"));
?>