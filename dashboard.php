<?php

	session_start();

	if(!array_key_exists('userId', $_SESSION))
	{
		http_response_code(401);
		header('Location: log-in.php');
		exit;
	}

	$dbh = require 'database-connection.php';

	$query =
	'
		SELECT
			id,
			title,
			content,
			price,
			(
				SELECT
					filePath
				FROM
					AdPictures
				WHERE
					adId = Ads.id
				ORDER BY
					id
				LIMIT 1
			) AS mainPictureFilePath
		FROM
			Ads
		WHERE
			userId = :userId
		ORDER BY
			publicationDate
	';
	$sth = $dbh->prepare($query);
	$sth->bindValue(':userId', $_SESSION['userId'], PDO::PARAM_INT);
	$sth->execute();
	$ads = $sth->fetchAll();

	require 'views/dashboard.phtml';