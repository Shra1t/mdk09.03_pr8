<?php
	session_start();
	include("./settings/connect_datebase.php");
	
	if (isset($_SESSION['user'])) {
		if($_SESSION['user'] != -1) {
			
			$user_query = $mysqli->query("SELECT * FROM `users` WHERE `id` = ".$_SESSION['user']);
			while($user_read = $user_query->fetch_row()) {
				// Структура: id(0), login(1), email(2), password(3), roll(4)
				if($user_read[4] == 0) header("Location: user.php");
				else if($user_read[4] == 1) header("Location: admin.php");
			}
		}
 	}
?>
<html>
	<head> 
		<meta charset="utf-8">
		<title> Авторизация </title>
		
		<script src="https://code.jquery.com/jquery-1.8.3.js"></script>
		<link rel="stylesheet" href="style.css">
	</head>
	<body>
		<div class="top-menu">
			<a href=#><img src = "img/logo1.png"/></a>
			<div class="name">
				<a href="index.php">
					<div class="subname">БЗОПАСНОСТЬ  ВЕБ-ПРИЛОЖЕНИЙ</div>
					Пермский авиационный техникум им. А. Д. Швецова
				</a>
			</div>
		</div>
		<div class="space"> </div>
		<div class="main">
			<div class="content">
				<div class = "login" id="login-step">
					<div class="name">Авторизация</div>
				
					<div class = "sub-name">Логин:</div>
					<input name="_login" type="text" placeholder="" onkeypress="return PressToEnter(event)"/>
					<div class = "sub-name">Пароль:</div>
					<input name="_password" type="password" placeholder="" onkeypress="return PressToEnter(event)"/>
					
					<a href="regin.php">Регистрация</a>
					<br><a href="recovery.php">Забыли пароль?</a>
					<input type="button" class="button" value="Войти" onclick="LogIn()"/>
					<img src = "img/loading.gif" class="loading"/>
				</div>
				
				<div class="login" id="code-step" style="display: none;">
					<div class="name">Подтверждение входа</div>
					
					<div class="sub-name">Код из письма:</div>
					<input name="_code" type="text" maxlength="6" placeholder="Введите 6-значный код"/>
					
					<input type="button" class="button" value="Подтвердить" onclick="VerifyCode()"/>
					<img src="img/loading.gif" class="loading-code" style="display: none;"/>
				</div>
				
				<div class="footer">
					© КГАПОУ "Авиатехникум", 2020
					<a href=#>Конфиденциальность</a>
					<a href=#>Условия</a>
				</div>
			</div>
		</div>
		
		<script>
			function LogIn() {
				var loading = document.getElementsByClassName("loading")[0];
				var button = document.getElementsByClassName("button")[0];
				
				var _login = document.getElementsByName("_login")[0].value;
				var _password = document.getElementsByName("_password")[0].value;
				loading.style.display = "block";
				button.className = "button_diactive";
				
				var data = new FormData();
				data.append("login", _login);
				data.append("password", _password);
				
				// AJAX запрос
				$.ajax({
					url         : 'ajax/login_user.php',
					type        : 'POST', // важно!
					data        : data,
					cache       : false,
					dataType    : 'html',
					// отключаем обработку передаваемых данных, пусть передаются как есть
					processData : false,
					// отключаем установку заголовка типа запроса. Так jQuery скажет серверу что это строковой запрос
					contentType : false, 
					// функция успешного ответа сервера
					success: function (_data) {
						console.log("Ответ сервера: " + _data);
						if(_data == "") {
							loading.style.display = "none";
							button.className = "button";
							alert("Логин или пароль не верный.");
						} else if (_data == "code_sent") {
							// Переходим на шаг ввода кода
							loading.style.display = "none";
							button.className = "button";
							document.getElementById("login-step").style.display = "none";
							document.getElementById("code-step").style.display = "block";
							alert("На вашу почту отправлен код авторизации.");
						} else {
							// На случай, если в будущем будет прямой вход без кода
							localStorage.setItem("token", _data);
							location.reload();
							loading.style.display = "none";
							button.className = "button";
						}
					},
					// функция ошибки
					error: function( ){
						console.log('Системная ошибка!');
						loading.style.display = "none";
						button.className = "button";
					}
				});
			}
			
			function VerifyCode() {
				var loadingCode = document.getElementsByClassName("loading-code")[0];
				var button = document.querySelector("#code-step .button");
				var _code = document.getElementsByName("_code")[0].value;
				
				if (_code.length != 6) {
					alert("Введите 6-значный код.");
					return;
				}
				
				loadingCode.style.display = "block";
				button.className = "button_diactive";
				
				var data = new FormData();
				data.append("code", _code);
				
				$.ajax({
					url         : 'ajax/verify_code.php',
					type        : 'POST',
					data        : data,
					cache       : false,
					dataType    : 'html',
					processData : false,
					contentType : false,
					success: function (_data) {
						console.log("Проверка кода, ответ: " + _data);
						if (_data == "") {
							alert("Неверный код авторизации.");
							loadingCode.style.display = "none";
							button.className = "button";
						} else {
							localStorage.setItem("token", _data);
							location.reload();
						}
					},
					error: function () {
						console.log('Системная ошибка!');
						loadingCode.style.display = "none";
						button.className = "button";
					}
				});
			}
			
			function PressToEnter(e) {
				if (e.keyCode == 13) {
					var _login = document.getElementsByName("_login")[0].value;
					var _password = document.getElementsByName("_password")[0].value;
					
					if(_password != "") {
						if(_login != "") {
							LogIn();
						}
					}
				}
			}
			
		</script>
	</body>
</html>