<?php

	session_start();

	if(!array_key_exists('userId', $_SESSION))
	{
		http_response_code(401);
		header('Location: log-in.php');
		exit;
	}

	$dbh = require 'database-connection.php';
//trier les conversations
	$query =
	'
		SELECT
			adId,
			IF(fromUser = :userId, toUser, fromUser) AS contactId,
			COUNT(*) AS messageCount
		FROM
			Messages
		WHERE
			(fromUser = :userId OR toUser = :userId)
		GROUP BY
			adId,
			IF(fromUser = :userId, toUser, fromUser)
		ORDER BY
			MAX(writingDate) DESC
	';
	$sth = $dbh->prepare($query);
	$sth->bindValue(':userId', $_SESSION['userId'], PDO::PARAM_INT);
	$sth->execute();
	$conversations = $sth->fetchAll();

	require 'views/messages.phtml';