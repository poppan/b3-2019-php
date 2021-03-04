<?php
require_once('../models/User.class.php');
//demarrage session
session_start();

// try/catch pour lever les erreurs de connexion
try {
    $action = isset($_GET['action']) ? $_GET['action'] : '';

    $user = new User();

    switch ($action){
        case 'login':
            if ($user->login($_POST)){
                $_SESSION['user'] = $user;
                header('Location: ?action=list');
            }else{
	            include('../views/users_login.php');
            }
            break;
        case 'list':
            $users = $user->findAll();
            include('../views/users_list.php');
            break;
        case 'jsonlist':
            $users = $user->findAll();
            header("Access-Control-Allow-Origin: *");
            header('Content-type: application/json; charset=UTF-8');
            echo json_encode($users);
            break;

        case 'register';
            if ($user->save($_POST)){
                $_SESSION['errors'] = [];
                header('Location: ?action=login');

            }else{
                $_SESSION['errors'] = $user->errors;
                include('../views/users_register.php');
            }
            break;
        default:
            header('Location: ?action=login');
            break;
    }
} catch (Exception $e) {
    echo('cacaboudin exception');
    print_r($e);
}
?>
