<?php 
	function sendMail($to, $subject, $message) {
		// Настройки Gmail SMTP
		$gmail_user = 'sovestnesovest@gmail.com';  // УКАЖИ СВОЙ GMAIL АДРЕС ЗДЕСЬ
		$gmail_password = 'tebwiwsblrdmsgny';   // Пароль приложения из Gmail
		
		$smtp_host = 'smtp.gmail.com';
		$smtp_port = 587;
		
		// Создаем соединение
		$socket = @fsockopen($smtp_host, $smtp_port, $errno, $errstr, 30);
		if (!$socket) {
			return false;
		}
		
		// Читаем приветствие сервера
		$response = fgets($socket, 515);
		if (substr($response, 0, 3) != '220') {
			fclose($socket);
			return false;
		}
		
		// Отправляем EHLO
		fputs($socket, "EHLO " . $smtp_host . "\r\n");
		$response = '';
		while ($line = fgets($socket, 515)) {
			$response .= $line;
			if (substr($line, 3, 1) == ' ') break;
		}
		
		// Начинаем TLS
		fputs($socket, "STARTTLS\r\n");
		$response = fgets($socket, 515);
		if (substr($response, 0, 3) != '220') {
			fclose($socket);
			return false;
		}
		
		// Включаем шифрование
		stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
		
		// Повторяем EHLO после TLS
		fputs($socket, "EHLO " . $smtp_host . "\r\n");
		$response = '';
		while ($line = fgets($socket, 515)) {
			$response .= $line;
			if (substr($line, 3, 1) == ' ') break;
		}
		
		// Авторизация
		fputs($socket, "AUTH LOGIN\r\n");
		$response = fgets($socket, 515);
		if (substr($response, 0, 3) != '334') {
			fclose($socket);
			return false;
		}
		
		// Отправляем логин (base64)
		fputs($socket, base64_encode($gmail_user) . "\r\n");
		$response = fgets($socket, 515);
		if (substr($response, 0, 3) != '334') {
			fclose($socket);
			return false;
		}
		
		// Отправляем пароль (base64)
		fputs($socket, base64_encode($gmail_password) . "\r\n");
		$response = fgets($socket, 515);
		if (substr($response, 0, 3) != '235') {
			fclose($socket);
			return false;
		}
		
		// Указываем отправителя
		fputs($socket, "MAIL FROM: <" . $gmail_user . ">\r\n");
		$response = fgets($socket, 515);
		if (substr($response, 0, 3) != '250') {
			fclose($socket);
			return false;
		}
		
		// Указываем получателя
		fputs($socket, "RCPT TO: <" . $to . ">\r\n");
		$response = fgets($socket, 515);
		if (substr($response, 0, 3) != '250') {
			fclose($socket);
			return false;
		}
		
		// Начинаем отправку данных
		fputs($socket, "DATA\r\n");
		$response = fgets($socket, 515);
		if (substr($response, 0, 3) != '354') {
			fclose($socket);
			return false;
		}
		
		// Формируем заголовки письма
		$headers = "From: WEB-безопасность <" . $gmail_user . ">\r\n";
		$headers .= "To: <" . $to . ">\r\n";
		$headers .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=UTF-8\r\n";
		$headers .= "Content-Transfer-Encoding: base64\r\n";
		
		// Тело письма в base64
		$body = base64_encode($message);
		
		// Отправляем письмо
		fputs($socket, $headers . "\r\n" . chunk_split($body) . ".\r\n");
		$response = fgets($socket, 515);
		if (substr($response, 0, 3) != '250') {
			fclose($socket);
			return false;
		}
		
		// Закрываем соединение
		fputs($socket, "QUIT\r\n");
		fclose($socket);
		
		return true;
	}
?>