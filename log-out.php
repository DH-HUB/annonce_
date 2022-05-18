<?php

	session_start();

	unset($_SESSION['userId']);//supprimer uniquement la session user

	header('Location: ./');
	exit;