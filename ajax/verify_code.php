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
		$id = $_SESSION['pending_user_id'];
		$_SESSION['user'] = $id;
		
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

