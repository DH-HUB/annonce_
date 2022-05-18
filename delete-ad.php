<?php

	session_start();

	if(!array_key_exists('userId', $_SESSION))
	{
		http_response_code(401);
		header('Location: log-in.php');
		exit;
	}

	if(!array_key_exists('id', $_GET))
	{
		http_response_code(409);
		header('Location: dashboard.php');
		exit;
	}

	$dbh = require 'database-connection.php';

	$query =
	'
		SELECT
			filePath
		FROM
			AdPictures
		WHERE
			adId =
			(
				SELECT
					id
				FROM
					Ads
				WHERE
					id = :id
					AND
					userId = :userId
			)
	';
	$sth = $dbh->prepare($query);
	$sth->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
	$sth->bindValue(':userId', $_SESSION['userId'], PDO::PARAM_INT);
	$sth->execute();
	$filePaths = array_column($sth->fetchAll(), 'filePath');

	if(count($filePaths) > 0)
	{
		foreach($filePaths as $filePath)
		{
			unlink($filePath);
		}
	}
//il faut supprimer l'annonce du user conn 'userId = :userId'
	$query =
	'
		DELETE FROM
			Ads
		WHERE
			id = :id
			AND
			userId = :userId
	';
	$sth = $dbh->prepare($query);
	$sth->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
	$sth->bindValue(':userId', $_SESSION['userId'], PDO::PARAM_INT);
	$sth->execute();

	header('Location: dashboard.php');
	exit;