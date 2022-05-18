<?php

	if(!array_key_exists('id', $_GET))
	{
		http_response_code(409);
		exit;
	}

	session_start();

	$dbh = require 'database-connection.php';

	$query =
	'
		SELECT
			id,
			title,
			content,
			price,
			(SELECT GROUP_CONCAT(filePath ORDER BY id SEPARATOR \',\') FROM AdPictures WHERE adId = Ads.id) AS pictureFilePaths,
			userId
		FROM
			Ads
		WHERE
			id = :id
	';
	$sth = $dbh->prepare($query);
	$sth->bindValue(':id', $_GET['id'], PDO::PARAM_INT);
	$sth->execute();
	$ad = $sth->fetch();

	if($ad === false)
	{
		http_response_code(404);
		exit;
	}

	$ad['pictureFilePaths'] = explode(',', $ad['pictureFilePaths']);

	require 'views/ad.phtml';