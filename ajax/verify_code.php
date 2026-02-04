<?php
	session_start();
	include("../settings/connect_datebase.php");
	
	$code = isset($_POST['code']) ? $_POST['code'] : '';
	
	// Проверяем, есть ли ожидающий подтверждения пользователь и код
	if (!isset($_SESSION['pending_user_id']) || !isset($_SESSION['pending_auth_code'])) {
		echo "";
		exit;
	}
	
	if ($code === $_SESSION['pending_auth_code']) {
		// Код верный — авторизуем пользователя
		$id = (int)$_SESSION['pending_user_id'];
		$_SESSION['user'] = $id;
		
		// одна сессия на пользователя: запоминаем текущую сессию (вход с другого браузера отменит предыдущую)
		$sid = $mysqli->real_escape_string(session_id());
		$mysqli->query("UPDATE `users` SET active_session_id = '" . $sid . "' WHERE id = " . $id);
		
		// очищаем временные значения
		unset($_SESSION['pending_user_id']);
		unset($_SESSION['pending_auth_code']);
		unset($_SESSION['pending_auth_time']);
		
		// возвращаем тот же токен, что и раньше при успешной авторизации
		echo md5(md5($id));
	} else {
		// Неверный код
		echo "";
	}
?>

