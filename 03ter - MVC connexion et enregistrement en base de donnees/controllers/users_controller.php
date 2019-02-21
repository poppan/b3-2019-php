<?php
require_once('../config/db_config.php');
require_once('../models/User.class.php');
//demarrage session
session_start();

// try/catch pour lever les erreurs de connexion
try {
    // on se connecte avec les acces,  IL FAUT QUE LA BASE EXISTE POUR MANIPULER
    $dbh = new PDO(
        'mysql:host=' . $db_config['host'] . ':' . $db_config['port'] . ';dbname=' . $db_config['schema'] . ";charset=" . $db_config['charset'],
        $db_config['user'],
        $db_config['password']
    );
    // tableau d'erreurs initial, vide
    $errors = [];

    $action = isset($_GET['action']) ? $_GET['action'] : '';

    $user = new User();

    switch ($action){
        case 'login':
            if ($user->login($_POST)){
                $_SESSION['errors'] = [];
                header('Location: ../controllers/users_controller.php?action=list');
                die;
            }
            // put errors in $session
            $_SESSION['errors'] = $errors;
            header('Location: ../views/users_login.php');
            break;

        case 'list':
            $_SESSION['errors'] = [];
            $users = $user->findAll();
            $_SESSION['users'] = $users;
            header('Location: ../views/users_list.php');
            break;

        case 'register';
            if ($user->save($_POST)){
                $_SESSION['errors'] = [];
                header('Location: ../views/users_list.php');
                die;
            }
            $_SESSION['errors'] = $user->errors;
            header('Location: ../views/users_register.php');
            break;
        default:
            header('Location: ../views/users_login.php');
            break;
    }
} catch (Exception $e) {
    echo('cacaboudin exception');
    print_r($e);
}
?>