<?php
// connection base mysql
$db_config = [
    'host'      => 'localhost', // machine, la machine locale s'appelle par convention "localhost"
    'schema'    => 'projet', // nom du schema
    'port'      => 3306, // 3306 est le port par defaut de mysql
    'user'      => 'mysqluser', // nom d'utilisateur
    'password'  => 'mysqlpassword', // mot de passe
    'charset'   => 'utf8mb4', // le charset utilisé pour communiquer avec mysql via PDO
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
    // tableau d'erreurs initial, vide
    $errors = [];
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
        $sql = "insert into messages (user_id, content) values (:user_id, :content )";
        $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
        if($sth->execute(array(
            ':user_id' => $_POST['user_id'],
            ':content' => $_POST['content']
        ))){
            // success
        }
    }


    //requete qui doit retourner des resultats
    $stmt = $dbh->query("select * from users");
    // recupere les users et fout le resultat dans une variable sous forme de tableau de tableaux
    $users = $stmt->fetchAll();


    //requete qui doit retourner des resultats
    $stmt = $dbh->query("select * from messages");
    // recupere les messages et fout le resultat dans une variable mais sous forme de tableau d'objets
    /*
    // possibilité de mapper sur une classe existante si elle est declarée
    class message {
        public $id;
        public $created;
        public $user_id;
        public $login;
    }
    // i.e la je map les champs sur une classe "message"
    $messages = $stmt->fetchAll(PDO::FETCH_CLASS, 'message');
    */
    $messages = $stmt->fetchAll(PDO::FETCH_CLASS);


}catch (Exception $e){
    echo('cacaboudin exception');
    print_r($e);
}
?>

<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://puteborgne.sexy/_css/normalize.css" />
    <link rel="stylesheet" href="https://puteborgne.sexy/_css/skeleton.css" />
    <style>
        fieldset {
            border: 0.25rem solid rgba(225,225,225,0.5);
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
            <p>le formulaire va envoyer ses données a la page courante, on verifie la validité des champs en PHP et on remonte les erreurs dans le tableau $errors</p>
            <p>si pas d'erreur on enregistre dans la table message</p>

            <ul class="errors">
                <?php
                foreach( $errors as $error) {
                    echo("<li>". $error . "</li>");
                }
                ?>
            </ul>

            <form method="post" action="" id="messageForm">
                <fieldset>
                    <legend>message</legend>
                    <label for="messageUserId">user_id</label>
                    <select id="messageUserId" name="user_id">
                        <?php
                        foreach ($users as $user) {
                            ?>
                            <option value="<?= $user['id'] ?>"><?= $user['login'] ?></option>
                            <?php
                        }
                        ?>
                    </select>
                    <!--
                    <input type="text" id="messageUserId" name="user_id" value="<?php echo !empty($_POST['user_id']) ? ($_POST['user_id']) : '' ?>">
                    -->
                    <label for="messageContent">message content</label>
                    <textarea id="messageContent" name="content"><?= !empty($_POST['content']) ? trim($_POST['content']) : '' ?></textarea>
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
            <h2>Messages</h2>
            <table class="u-full-width">
                <thead>
                <tr>
                    <th>content</th>
                    <th>created</th>
                    <th>user_id</th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($messages as $message) {
                    ?>
                    <tr>
                        <td><?= $message->content ?></td>
                        <td><?= $message->created ?></td>
                        <td><?= $message->user_id ?></td>
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