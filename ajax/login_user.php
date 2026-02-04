<?php
	session_start();
	include("../settings/connect_datebase.php");
	include("../settings/mail.php");
	
	$login = $_POST['login'];
	$password = $_POST['password'];
	
	// Хешируем пароль перед проверкой
	$passwordHash = md5($password);
	
	// ищем пользователя
	$query_user = $mysqli->query("SELECT * FROM `users` WHERE `login`='".$login."' AND `password`= '".$passwordHash."';");
	
	$id = -1;
	$email = $login; // по умолчанию
	if($user_read = $query_user->fetch_assoc()) {
		$id = $user_read['id'];
		if (isset($user_read['email'])) {
			$email = $user_read['email'];
		}
	}
	
	if($id != -1) {
		// Этап 1: пароль верный, формируем код и отправляем на почту
		$code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
		
		// сохраняем код и пользователя в сессии, чтобы проверить на следующем шаге
		$_SESSION['pending_user_id'] = $id;
		$_SESSION['pending_auth_code'] = $code;
		$_SESSION['pending_auth_time'] = time();
		
		// отправка кода на почту (используется email из профиля)
		$message = "Ваш код авторизации: <b>".$code."</b>";
		sendMail($email, 'Код авторизации', $message);
		
		// сообщаем фронтенду, что код отправлен
		echo "code_sent";
	} else {
		// Неверный логин или пароль
		echo "";
	}
?>