<?php
// connection base mysql
$db_config = [
    'host' => 'localhost', // machine, la machine locale s'appelle par convention "localhost"
    'schema' => 'projet', // nom du schema
    'port' => 3306, // 3306 est le port par defaut de mysql
    'user' => 'mysqluser', // nom d'utilisateur
    'password' => 'mysqlpassword', // mot de passe
    'charset' => 'utf8mb4', // le charset utilisé pour communiquer avec mysql via PDO
];

// try/catch pour lever les erreurs de connexion

try {
    // on se connecte avec les acces,  IL FAUT QUE LA BASE EXISTE POUR MANIPULER
    $dbh = new PDO(
        'mysql:host=' . $db_config['host'] . ':' . $db_config['port'] . ';dbname=' . $db_config['schema'] . ";charset=" . $db_config['charset'],
        $db_config['user'],
        $db_config['password']
    );

    /*
     *  check/validation du formulaire
    */
    // tableau d'erreurs initial, vide
    $errors = [];
    // test simple pour verifier que le champ $_POST['user_id'] existe ET (&&) contient une valeur
    // verifier qu'il existe ca permet de ne pas avoir le message au premier chargement de page

    // si name existe
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

    /*
     *  insertion base de données
    */

    // si il existe un champ "user_id" fourni dans le $_POST, c-a-d qu'un formulaire ient d'etre valid� ET qu'il n'y a aucune erreur
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
            // success
        }
    }


    //requete qui doit retourner des resultats
    $stmt = $dbh->query("select * from users");
    // recupere les users et fout le resultat dans une variable sous forme de tableau de tableaux
    $users = $stmt->fetchAll(PDO::FETCH_CLASS);


    //requete qui doit retourner des resultats
    $stmt = $dbh->query("select * from messages");
    $messages = $stmt->fetchAll(PDO::FETCH_CLASS);


} catch (Exception $e) {
    echo('cacaboudin exception');
    print_r($e);
}
?>

<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://puteborgne.sexy/_css/normalize.css"/>
    <link rel="stylesheet" href="https://puteborgne.sexy/_css/skeleton.css"/>
    <style>
        fieldset {
            border: 0.25rem solid rgba(225, 225, 225, 0.5);
            border-radius: 4px;
            padding: 1rem 2rem;
        }

        .errors {
            color: #ff5555;
        }
    </style>
</head>

<body>
<div class="container">

    <div class="row">
        <h1>formulaire de la win</h1>
        <p>le formulaire va envoyer ses données a la page courante, on verifie la validité des champs en PHP et on
            remonte les erreurs dans le tableau $errors</p>
        <p>si pas d'erreur on enregistre dans la table message</p>

        <ul class="errors">
            <?php
            foreach ($errors as $error) {
                echo("<li>" . $error . "</li>");
            }
            ?>
        </ul>

        <form method="post" action="" id="messageForm">
            <fieldset>
                <legend>user</legend>
                <label for="userLogin">login</label>
                <input type="text" id="userLogin" name="login" value="<?php echo !empty($_POST['login']) ? ($_POST['login']) : '' ?>">
                <label for="userPassword">password</label>
                <input type="password" id="userLogin" name="password" value="">
                <label for="userFirstname">firstname</label>
                <input type="text" id="userFirstname" name="firstname" value="<?php echo !empty($_POST['firstname']) ? ($_POST['firstname']) : '' ?>">
                <label for="userLastname">lastname</label>
                <input type="text" id="userLastname" name="lastname" value="<?php echo !empty($_POST['lastname']) ? ($_POST['lastname']) : '' ?>">
            </fieldset>
            <input type="submit" value="Envoyer" class="button-primary">
        </form>
    </div>

    <div class="row">
        <div class="one-half column">
            $_GET
            <pre><?php print_r($_GET) ?></pre>
        </div>
        <div class="one-half column">
            $_POST :
            <pre><?php print_r($_POST) ?></pre>
        </div>
    </div>

    <div class="row">
        <h2>Users</h2>
        <table class="u-full-width">
            <thead>
            <tr>
                <th>id</th>
                <th>login</th>
                <th>password</th>

            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($users as $user) {
                ?>
                <tr>
                    <td><?= $user->id ?></td>
                    <td><?= $user->login ?></td>
                    <td><?= $user->password ?></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>