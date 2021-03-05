<?php
require_once('../models/Message.class.php');
//demarrage session
session_start();

// si pas loggué (pas de user en session alors on redirige vers users_controller.php)
if (!isset($_SESSION['user']) || !isset($_SESSION['user']->id)) {
	header('Location: users_controller.php');
}

// try/catch pour lever les erreurs de connexion
try {
	$action = isset($_GET['action']) ? $_GET['action'] : '';

	$message = new Message();

	switch ($action) {

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

		case 'edit';
			if ($message->save($_POST)) {
				$_SESSION['errors'] = [];
				header('Location: ?action=list');

			} else {


				// si y'a un id je le charge initialement
				$message_id = $_GET['id'];
				// si il existe pas en $_GET je cherche dans le $_POST
				if(empty($message_id)){
					$message_id = $_POST['id'];
				}
				// sinon tant pis , ballec
				$message = new Message($message_id);

// surcharge si fournie
				$message->user_id = !empty($_SESSION['user']->id) ? ($_SESSION['user']->id) : $message->user_id;
				$message->content = !empty($_POST['content']) ? ($_POST['content']) : $message->content;

				$_SESSION['errors'] = $message->errors;
				include('../views/messages_edit.php');
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
