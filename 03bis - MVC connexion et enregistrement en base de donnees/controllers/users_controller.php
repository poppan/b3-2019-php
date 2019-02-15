<?php
require_once('../config/db_config.php');
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

    switch ($action){
        case 'login':
            if (isset($_POST['login'])) {
                if (empty($_POST['login'])) {
                    $errors[] = 'champ login vide';
                }else if (mb_strlen($_POST['login']) > 45) {
                    $errors[] = 'champ login trop long (45max)';
                }else{
                    if (isset($_POST['password'])) {
                        if (empty($_POST['password'])) {
                            $errors[] = 'champ password vide';
                        }else{

                            $sql = "select password from users where login = :login limit 1";
                            $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                            $sth->execute(array(
                                ':login' => $_POST['login']
                            ));
                            $storedPassword = $sth->fetchColumn();
                            if(!password_verify($_POST['password'], $storedPassword)){
                                $errors[] = 'CASSE TOI !';
                                // ERROR

                            }else{
                                // SUCCESS
                                // redirect to some other controller page
                                header('Location: ../controllers/users_controller.php?action=list');
                                die;
                            }
                        }
                    }
                }

            }
            // put errors in $session
            $_SESSION['errors'] = $errors;
            // redirect to login
            // on *redirige* vers la VIEW par defaut
            header('Location: ../views/users_login.php');
            break;
        case 'register':
            if (isset($_POST['login'])) {
                if (empty($_POST['login'])) {
                    $errors[] = 'champ login vide';
                    // si name > 50 chars
                } else if (mb_strlen($_POST['login']) > 45) {
                    $errors[] = 'champ login trop long (45max)';
                }else{
                    $sql = "select count(id) from users where login = :login";
                    $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                    $sth->execute(array(
                        ':login' => $_POST['login']
                    ));
                    if($sth->fetchColumn() > 0){
                        $errors[] = 'login deja pris blaireau';
                    }
                }

            }
            if (isset($_POST['password'])) {
                if (empty($_POST['password'])) {
                    $errors[] = 'champ password vide';
                    // si name > 50 chars
                } else if (mb_strlen($_POST['password']) < 8) {
                    $errors[] = 'champ password trop court (8 min)';
                } else if (mb_strlen($_POST['password']) > 20) {
                    $errors[] = 'champ password trop long (20 max)';
                }
            }

            if (isset($_POST['firstname'])) {
                if (empty($_POST['firstname'])) {
                    $errors[] = 'champ firstname vide';
                    // si name > 50 chars
                } else if (mb_strlen($_POST['firstname']) < 2) {
                    $errors[] = 'champ firstname trop court (8 min)';
                } else if (mb_strlen($_POST['firstname']) > 45) {
                    $errors[] = 'champ firstname trop long (20 max)';
                }
            }
            if (isset($_POST['lastname'])) {
                if (empty($_POST['lastname'])) {
                    $errors[] = 'champ lastname vide';
                    // si name > 50 chars
                } else if (mb_strlen($_POST['lastname']) < 2) {
                    $errors[] = 'champ lastname trop court (8 min)';
                } else if (mb_strlen($_POST['lastname']) > 45) {
                    $errors[] = 'champ lastname trop long (20 max)';
                }
            }
            if (isset($_POST['login']) && count($errors) == 0) {
                // ben on insere dans la table message
                // la synaxe ":user_id" ca veut dire qu'on prepare la requete et que juste quand on la lance, on va remplacer ":user_id" par la bonne valeur.

                $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
                /* syntaxe avec preparedStatements */
                $sql = "insert into users (login, password, firstname, lastname) values (:login, :password , :firstname, :lastname)";
                $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                if ($sth->execute(array(
                    ':login' => $_POST['login'],
                    ':password' => $hashedPassword,
                    ':firstname' => $_POST['firstname'],
                    ':lastname' => $_POST['lastname']
                ))) {
                    // SUCCESS
                    // redirect to list
                    // on *redirige* vers la VIEW list

                    header('Location: ../controllers/users_controller.php?action=list');
                    die;
                }else{
                    // ERROR
                    // put errors in $session
                    $errors['pas reussi a creer le user'];
                }
            }
            $_SESSION['errors'] = $errors;
            header('Location: ../views/users_register.php');
            break;

        case 'list':
            // load users
            //requete qui doit retourner des resultats
            $stmt = $dbh->query("select * from users");
            // recupere les users et fout le resultat dans une variable sous forme de tableau de tableaux
            $users = $stmt->fetchAll(PDO::FETCH_CLASS);
            $_SESSION['users'] = $users;
            header('Location: ../views/users_list.php');
            break;
        default;
            header('Location: ../views/users_login.php');
            break;
    }
} catch (Exception $e) {
    echo('cacaboudin exception');
    print_r($e);
}
?>