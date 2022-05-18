<?php
//pour contrôle
	session_start();

	if(!array_key_exists('userId', $_SESSION))
	{
		http_response_code(401);
		header('Location: log-in.php');
		exit;
	}

	define('MIME_TYPES_ACCEPTED', ['image/png', 'image/jpeg']);
	define('MAX_FILE_SIZE', 3000000);
	define('UPLOADED_FILES_FOLDER_PATH', 'uploaded-files');

	if(!empty($_POST))
	{
		if(!array_key_exists('title', $_POST) OR !array_key_exists('content', $_POST) OR !array_key_exists('price', $_POST))
		{
			http_response_code(409);
			header('Location: ?error=all-fields-must-be-completed');
			exit;
		}

		$title = trim($_POST['title']);
		$content = trim($_POST['content']);
		$price = trim($_POST['price']);

		if(empty($title) OR empty($content) OR empty($price))
		{
			http_response_code(409);
			header('Location: ?error=all-fields-must-be-completed');
			exit;
		}

		if(array_key_exists('pictures', $_FILES) AND count($_FILES['pictures']['name']) > 0)
		{
			foreach(array_keys($_FILES['pictures']['name']) as $index)
			{
				if($_FILES['pictures']['error'][$index] == 0)
				{
					if(in_array(mime_content_type($_FILES['pictures']['tmp_name'][$index]), MIME_TYPES_ACCEPTED))
					{
						if($_FILES['pictures']['size'][$index] <= MAX_FILE_SIZE)
						{
							//pour ne pas avoir deux noms identiques
							do
							{
								$filePath = UPLOADED_FILES_FOLDER_PATH.'/'.uniqid().'.'.pathinfo($_FILES['pictures']['name'][$index], PATHINFO_EXTENSION);
							}
							while(file_exists($filePath));

							$filePaths[$index] = $filePath;
						}
						else
						{
							//	Erreur : Fichier trop volumineux
						}
					}
					else
					{
						//	Erreur : Type mime du fichier incorrect
					}
				}
				else
				{
					//	Erreur : Fichier non récupéré
				}
			}
		}

		$dbh = require 'database-connection.php';

		$query =
		'
			INSERT INTO
				Ads
				(title, content, price, userId)
			VALUES
				(:title, :content, :price, :userId)
		';
		$sth = $dbh->prepare($query);
		$sth->bindValue(':title', $title, PDO::PARAM_STR);
		$sth->bindValue(':content', $content, PDO::PARAM_STR);
		$sth->bindValue(':price', $price, PDO::PARAM_INT);
		$sth->bindValue(':userId', $_SESSION['userId'], PDO::PARAM_INT);
		$sth->execute();
		$adId = $dbh->lastInsertId();
//pour l'ajout de plusieurs images
		if(count($filePaths) > 0)
		{
			$query =
			'
				INSERT INTO
					AdPictures
					(filePath, adId)
				VALUES
					'.implode(', ', array_fill(0, count($filePaths), '(?, ?)'));
			$sth = $dbh->prepare($query);
			$i = 1;
			foreach($filePaths as $index => $filePath)
			{
				$sth->bindValue($i++, $filePath, PDO::PARAM_STR);
				$sth->bindValue($i++, $adId, PDO::PARAM_INT);
			}
			$sth->execute();
				
			foreach($filePaths as $index => $filePath)
			{
				move_uploaded_file($_FILES['pictures']['tmp_name'][$index], $filePath);
			}
		}

		header('Location: dashboard.php');
		exit;
	}

	if(array_key_exists('error', $_GET))
	{
		$errorMessages =
		[
			'all-fields-must-be-completed' => 'Tous les champs doivent être renseignés'
		];

		if(array_key_exists($_GET['error'], $errorMessages))
		{
			$errorMessage = $errorMessages[$_GET['error']];
		}
	}

	require 'views/post-ad.phtml';