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
				COUNT(*) > 0
			FROM
				Users
			WHERE
				email = :email
		';
		$sth = $dbh->prepare($query);
		$sth->bindValue(':email', $email, PDO::PARAM_STR);
		$sth->execute();
		$alreadyExistingEmail = boolval($sth->fetchColumn());

		if($alreadyExistingEmail)
		{
			http_response_code(409);
			header('Location: ?error=already-existing-email');
			exit;
		}

		$query =
		'
			INSERT INTO
				Users
				(email, hashedPassword)
			VALUES
				(:email, :hashedPassword)
		';
		$sth = $dbh->prepare($query);
		$sth->bindValue(':email', $email, PDO::PARAM_STR);
		$sth->bindValue(':hashedPassword', password_hash($password, PASSWORD_BCRYPT), PDO::PARAM_STR);
		$sth->execute();

		session_start();
		$_SESSION['userId'] = $dbh->lastInsertId();

		header('Location: dashboard.php');
		exit;
	}

	if(array_key_exists('error', $_GET))
	{
		$errorMessages =
		[
			'all-fields-must-be-completed' => 'Tous les champs doivent être renseignés',
			'invalid-email' => 'Adresse électronique non valide',
			'already-existing-email' => 'Adresse électronique déjà existante'
		];

		if(array_key_exists($_GET['error'], $errorMessages))
		{
			$errorMessage = $errorMessages[$_GET['error']];
		}
	}

	session_start();

	require 'views/sign-up.phtml';