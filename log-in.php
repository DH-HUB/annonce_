<?php

	if(!empty($_POST))
	{
		if(!array_key_exists('email', $_POST) OR !array_key_exists('password', $_POST))
		{
			http_response_code(409);
			header('Location: ?error=all-fields-must-be-completed');
			exit;
		}

		$email = trim($_POST['email']);
		$password = trim($_POST['password']);

		if(empty($email) OR empty($password))
		{
			http_response_code(409);
			header('Location: ?error=all-fields-must-be-completed');
			exit;
		}

		if(!filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			http_response_code(409);
			header('Location: ?error=invalid-email');
			exit;
		}

		$dbh = require 'database-connection.php';

		$query =
		'
			SELECT
				id,
				hashedPassword
			FROM
				Users
			WHERE
				email = :email
		';
		$sth = $dbh->prepare($query);
		$sth->bindValue(':email', $email, PDO::PARAM_STR);
		$sth->execute();
		$user = $sth->fetch();

		if($user !== false AND password_verify($password, $user['hashedPassword']))
		{
			session_start();
			$_SESSION['userId'] = intval($user['id']);

			header('Location: dashboard.php');
			exit;
		}
		else
		{
			http_response_code(409);
			header('Location: ?error=incorrect-email-or-password');
			exit;
		}
	}

	if(array_key_exists('error', $_GET))
	{
		$errorMessages =
		[
			'all-fields-must-be-completed' => 'Tous les champs doivent être renseignés',
			'invalid-email' => 'Adresse électronique non valide',
			'incorrect-email-or-password' => 'Adresse électronique ou mot de passe incorrect'
		];

		if(array_key_exists($_GET['error'], $errorMessages))
		{
			$errorMessage = $errorMessages[$_GET['error']];
		}
	}

	session_start();

	require 'views/log-in.phtml';