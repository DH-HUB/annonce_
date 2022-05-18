<?php

	session_start();

	$dbh = require 'database-connection.php';

	$query =
	'
		SELECT
			id,
			title,
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
		ORDER BY
			publicationDate DESC
	';
	$sth = $dbh->query($query);
	$ads = $sth->fetchAll();

	require 'views/index.phtml';