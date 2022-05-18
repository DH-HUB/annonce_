<?php

	session_start();

	if(!array_key_exists('userId', $_SESSION))
	{
		http_response_code(401);
		header('Location: log-in.php');
		exit;
	}

	if(!array_key_exists('adId', $_GET) OR !array_key_exists('contactId', $_GET))
	{
		http_response_code(409);
		exit;
	}
//verif si l'annonce exciste
	$dbh = require 'database-connection.php';

	$query =
	'
		SELECT
			COUNT(*) > 0
		FROM
			Ads
		WHERE
			id = :adId
	';
	$sth = $dbh->prepare($query);
	$sth->bindValue(':adId', $_GET['adId'], PDO::PARAM_INT);
	$sth->execute();
	$existingAd = boolval($sth->fetchColumn());

	if(!$existingAd)
	{
		http_response_code(404);
		exit;
	}

	if(!empty($_POST))
	{
		if(!array_key_exists('message', $_POST))
		{
			http_response_code(409);
			header('Location: ?error=all-fields-must-be-completed');
			exit;
		}

		$message = trim($_POST['message']);

		if(empty($message))
		{
			http_response_code(409);
			header('Location: ?error=all-fields-must-be-completed');
			exit;
		}

		$query =
		'
			INSERT INTO
				Messages
				(adId, fromUser, toUser, content)
			VALUES
				(:adId, :fromUser, :toUser, :message)
		';
		$sth = $dbh->prepare($query);
		$sth->bindValue(':adId', $_GET['adId'], PDO::PARAM_INT);
		$sth->bindValue(':fromUser', $_SESSION['userId'], PDO::PARAM_INT);
		$sth->bindValue(':toUser', $_GET['contactId'], PDO::PARAM_INT);
		$sth->bindValue(':message', $_POST['message'], PDO::PARAM_STR);
		$sth->execute();

		header('Location: '.$_SERVER['REQUEST_URI']);
		exit;
	}
/* (fromUser = :userId AND toUser = :contactId OR fromUser = :contactId AND toUser = :userId)
		ORDER BY
		writingDate****gestion des message expe/recep*/
	$query =
	'
		SELECT
			content,
			fromUser,
			DATE_FORMAT(writingDate, \'%d.%m.%Y %H:%i\') AS writingDate
		FROM
			Messages
		WHERE
			adId = :adId
			AND
		
			(fromUser = :userId AND toUser = :contactId OR fromUser = :contactId AND toUser = :userId)
		ORDER BY
			writingDate
	';
	$sth = $dbh->prepare($query);
	$sth->bindValue(':adId', $_GET['adId'], PDO::PARAM_INT);
	$sth->bindValue(':userId', $_SESSION['userId'], PDO::PARAM_INT);
	$sth->bindValue(':contactId', $_GET['contactId'], PDO::PARAM_INT);
	$sth->execute();
	$messages = $sth->fetchAll();

	require 'views/conversation.phtml';