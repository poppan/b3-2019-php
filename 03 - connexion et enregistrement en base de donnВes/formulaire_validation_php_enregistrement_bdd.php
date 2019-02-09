<?php
// connection base mysql
$db_config = [
    'host'      => 'localhost', // machine, la machine locale s'appelle par convention "localhost"
    'schema'    => 'projet', // nom du schema
    'port'      => 3306, // 3306 est le port par defaut de mysql
    'user'      => 'mysqluser', // nom d'utilisateur
    'password'  => 'mysqlpassword', // mot de passe
    'charset'   => 'utf8', // le charset utilisé pour communiquer avec mysql via PDO
];

// try/catch pour lever les erreurs de connexion

try{
    // on se connecte avec les acces,  IL FAUT QUE LA BASE EXISTE POUR MANIPULER
    $dbh = new PDO(
        'mysql:host='. $db_config['host'] .':'. $db_config['port'] .';dbname='. $db_config['schema'] .";charset=". $db_config['charset'],
        $db_config['user'],
        $db_config['password']
    );

    /*
     *  check/validation du formulaire
    */

    // test simple pour verifier que le champ $_POST['user_id'] existe ET (&&) contient une valeur
    // verifier qu'il existe ca permet de ne pas avoir le message au premier chargement de page

    // si name existe
    if (isset($_POST['user_id'])){
        if (empty($_POST['user_id'])) {
            $errors[] = 'champ user_id vide';
            // si name > 50 chars
        } else if (mb_strlen($_POST['user_id']) > 50) {
            $errors[] = 'champ user_id trop long (50max)';
        }
    }

    // si email existe
    if (isset($_POST['user_email'])) {
        if (empty($_POST['user_email'])) {
            $errors[] = 'champ user_email vide';
        } else if (mb_strlen($_POST['user_email']) > 150) {
            $errors[] = 'champ user_email trop long (150max)';
            // filter_var
        } else if (!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'champ user_email non-valide';
        }
    }

    // si message existe et que le champ est non vide, on utilise trim() pour enlever les espaces/tabs en debut et fin de texte
    if (isset($_POST['content']) && empty(trim($_POST['content']))) {
        $errors[] = 'champ content vide';
    }




    /*
     *  insertion base de données
    */

    // si il existe un champ "user_id" fourni dans le $_POST, c-a-d qu'un formulaire ient d'etre valid� ET qu'il n'y a aucune erreur
    if(isset($_POST['user_id']) && count($errors) == 0){

        // ben on insere dans la table message
        // la synaxe ":user_id" ca veut dire qu'on prepare la requete et que juste quand on la lance, on va remplacer ":user_id" par la bonne valeur.

        /* syntaxe avec preparedStatements */
        $sql = "insert into messages (user_id, content) values(:user_id, :content )";
        $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        if( $sth->execute(array(
            ':user_id' => $_POST['user_id'],
            ':content' => $_POST['content']
        )) ){
            echo 'ayé c\'est inséré';
        }
    }


    /*
    * affiche toutes les entrées de la table projet.messages
    */

    //requete qui doit retourner des resultats
    $stmt = $dbh->query("select * from messages");
    // recupere les messages dans le connecteur
    $results = $stmt->fetchAll();
    foreach ($results as $item) {
        ?>
        <article>
            <h1><?= $item['content'] ?></h1>
            <date><?= $item['created'] ?></date>
            <p><?= $item['user_id'] ?></p>
        </article>
        <?php
    }

}catch (Exception $e){
    print_r($e);
}
?>

<html>
<head>
    <meta charset="UTF-8">

    <style>
        body {
            font-family: Arial, Helvetica, Sans serif;
        }

        .errors {
            color: #ff0000;
        }

        fieldset {
            border: 1px solid rgba(0, 0, 0, .125);
        }

        label {
            float: left;
            min-width: 33%;
            display: inline-block;
        }
    </style>
</head>

<body>

    <h1>debug</h1>
    $_GET
    <pre><?php print_r($_GET); ?></pre>

    <hr/>

    $_POST :
    <pre><?php print_r($_POST); ?></pre>

    <hr/>

    <h1>formulaire de la win</h1>
    <p>le formulaire va envoyer ses données a la page courante, on verifie la validité des champs en PHP et on remonte les erreurs dans le tableau $errors</p>
    <p>si pas d'erreur on enregistre dans la table message</p>

    <form method="post" action="">

        <div class="errors">
<?php
            foreach( $errors as $error) {
                echo("<p>". $error . "</p>");
            }
?>
        </div>

        <fieldset>
            <label for="user_id">user_id
                <input type="text" name="user_id" value="<?php echo !empty($_POST['user_id']) ? ($_POST['user_id']) : '' ?>">
            </label>
        </fieldset>
<!--
        <fieldset>
            <label for="email">email
                <input type="text" name="email" value="<?= !empty($_POST['email']) ? ($_POST['email']) : '' ?>">
            </label>
        </fieldset>
-->
        <fieldset>
            <label for="content">message content
                <textarea name="content">
                    <?= !empty($_POST['content']) ? ($_POST['content']) : '' ?>
                </textarea>
            </label>
        </fieldset>

        <input type="submit">
    </form>


</body>
</html>