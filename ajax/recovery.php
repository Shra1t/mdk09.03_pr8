<?php
	session_start();
	include("../settings/connect_datebase.php");
	include("../settings/mail.php");
	
	$login = $_POST['login'];
	
	// ищем пользователя
	$query_user = $mysqli->query("SELECT * FROM `users` WHERE `login`='".$login."';");
	
	$id = -1;
	$email = $login;
	if($user_read = $query_user->fetch_assoc()) {
		// создаём новый пароль
		$id = $user_read['id'];
		if (isset($user_read['email'])) {
			$email = $user_read['email'];
		}
	}
	
	function PasswordGeneration() {
		// создаём пароль
		$chars="qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP"; // матрица
		$max=10; // количество
		$size=StrLen($chars)-1; // Определяем количество символов в $chars
		$password="";
		
		while($max--) {
			$password.=$chars[rand(0,$size)];
		}
		
		return $password;
	}
	
	if($id != 0) {
		//обновляем пароль
		$password = PasswordGeneration();;
		// проверяем не используется ли пароль 
		$query_password = $mysqli->query("SELECT * FROM `users` WHERE `password`= '".md5($password)."';");
		while($password_read = $query_password->fetch_row()) {
			// создаём новый пароль
			$password = PasswordGeneration();
		}
		// обновляем пароль
		$mysqli->query("UPDATE `users` SET `password`='".md5($password)."' WHERE `login` = '".$login."'");
		// отсылаем на почту
		$message = "Ваш пароль был только что изменён. Новый пароль: ".$password;
		sendMail($email, 'Безопасность web-приложений КГАПОУ \"Авиатехникум\"', $message);
	}
	
	echo $id;
?>