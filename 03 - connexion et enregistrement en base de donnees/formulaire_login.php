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
            if (isset($_POST['password'])) {
                if (empty($_POST['password'])) {
                    $errors[] = 'champ password vide';
                    // si name > 50 chars
//                } else if (mb_strlen($_POST['password']) < 8) {
//                    $errors[] = 'champ password trop court (8 min)';
//                } else if (mb_strlen($_POST['password']) > 20) {
//                    $errors[] = 'champ password trop long (20 max)';
                }else{

                    $sql = "select password from users where login = :login limit 1";
                    $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
                    $sth->execute(array(
                        ':login' => $_POST['login']
                    ));
                    $storedPassword = $sth->fetchColumn();
                    if(!password_verify($_POST['password'], $storedPassword)){
                        $errors[] = 'CASSE TOI !';
                    }else{
                        echo('YOUPI');
                    }
                }
            }
        }

    }



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

        <ul class="errors">
            <?php
            foreach ($errors as $error) {
                echo("<li>" . $error . "</li>");
            }
            ?>
        </ul>

        <form method="post" action="" id="messageForm">
            <fieldset>
                <legend>LOGIN</legend>
                <label for="userLogin">login</label>
                <input type="text" id="userLogin" name="login" value="<?php echo !empty($_POST['login']) ? ($_POST['login']) : '' ?>">
                <label for="userPassword">password</label>
                <input type="password" id="userLogin" name="password" value="">
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


</div>
</body>
</html>