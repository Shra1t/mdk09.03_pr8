<?php
	session_start();
	include("../settings/connect_datebase.php");
	
	$login = $_POST['login'];
	$email = $_POST['email'];
	$password = $_POST['password'];
	
	// Проверка сложности пароля на стороне сервера
	function isStrongPassword($password) {
		// длина более 8 символов
		if (strlen($password) <= 8) return false;
		// латинские буквы
		if (!preg_match('/[A-Za-z]/', $password)) return false;
		// цифры
		if (!preg_match('/[0-9]/', $password)) return false;
		// хотя бы один символ, который не буква и не цифра
		if (!preg_match('/[^A-Za-z0-9]/', $password)) return false;
		// хотя бы одна заглавная буква
		if (!preg_match('/[A-Z]/', $password)) return false;
		
		return true;
	}
	
	if (!isStrongPassword($password)) {
		// -2 — пароль не соответствует требованиям
		echo -2;
		exit;
	}
	
	// Хешируем пароль перед сохранением
	$passwordHash = md5($password);
	
	// ищем пользователя по логину
	$query_user = $mysqli->query("SELECT * FROM `users` WHERE `login`='".$login."'");
	$id = -1;
	
	if($user_read = $query_user->fetch_row()) {
		echo $id;
	} else {
		$mysqli->query("INSERT INTO `users`(`login`, `email`, `password`, `roll`) VALUES ('".$login."', '".$email."', '".$passwordHash."', 0)");
		
		$query_user = $mysqli->query("SELECT * FROM `users` WHERE `login`='".$login."' AND `password`= '".$passwordHash."';");
		$user_new = $query_user->fetch_row();
		$id = $user_new[0];
			
		if($id != -1) $_SESSION['user'] = $id; // запоминаем пользователя
		echo $id;
	}
?>