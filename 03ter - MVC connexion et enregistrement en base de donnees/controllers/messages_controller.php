<?php
require_once('../models/Message.class.php');
//demarrage session
session_start();

// try/catch pour lever les erreurs de connexion
try {
	$action = isset($_GET['action']) ? $_GET['action'] : '';

	$message = new Message();

	switch ($action){

		case 'list':
			$messages = $message->findAll();
			include('../views/messages_list.php');
			break;
		case 'jsonlist':
			$messages = $message->findAll();
			header("Access-Control-Allow-Origin: *");
			header('Content-type: application/json; charset=UTF-8');
			echo json_encode($messages);
			break;

		case 'create';
			if ($message->save($_POST)){
				$_SESSION['errors'] = [];
				header('Location: ?action=list');

			}else{
				$_SESSION['errors'] = $message->errors;
				include('../views/messages_create.php');
			}
			break;
		default:
			header('Location: ?action=list');
			break;
	}
} catch (Exception $e) {
	echo('cacaboudin exception');
	print_r($e);
}
?>
