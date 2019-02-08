<?php
// connection base mysql

$dbhost = 'localhost'; // machine, la machine locale s'appelle par convention "localhost"
$dbname = 'projet'; // nom de la base de donn�es
$dbuser = 'root'; // nom d'utilisateur base de donn�es
$dbpassword = ''; // mot de passe base de donn�es

// on se connecte avec les acces,  IL FAUT QUE LA BASE EXISTE POUR MANIPULER
$dbh = new PDO(
    'mysql:host='. $dbhost .';dbname='. $dbname,
    $dbuser,
    $dbpassword
);
/*
 *
 *
 *
 *
 *



gallerie_list_view.php
gallerie_detail_view.php
gallerie_edit_view.php

gallerie_controller.php









---------------------------------------------------------

	ON affiche toutes les entr�es de la table projet

---------------------------------------------------------
*/

//requete qui doit retourner des resultats
$results = $dbh->query("select * from galleries");
// recupere les messages dans le connecteur
$galleries = $results->fetchAll();

for ($i = 0; $i < count($galleries); $i++) {
?>
    <article>
        <h1><?= $galleries[$i]['titre'] ?></h1>
        <date><?= $galleries[$i]['description'] ?></date>
        <p><?= $galleries[$i]['categorie'] ?></p>
        <p><?= $galleries[$i]['illustration'] ?></p>
    </article>
<?php
}

/*
---------------------------------------------------------

	check du formulaire
	
---------------------------------------------------------
*/

// test simple pour verifier que le champ $_POST['pouet'] existe ET (&&) contient une valeur
// verfier qu'il existe ca permet de ne pas avoir le message au premier chargement de page

// si name existe
if (isset($_POST['name'])){
    if (empty($_POST['name'])) {
        $errors[] = 'champ name vide';
    // si name > 50 chars
    } else if (mb_strlen($_POST['name']) > 50) {
        $errors[] = 'champ name trop long (50max)';
    }
}

// si email existe
if (isset($_POST['email'])) {
    if (empty($_POST['email'])) {
        $errors[] = 'champ email vide';
    } else if (mb_strlen($_POST['email']) > 150) {
        $errors[] = 'champ email trop long (150max)';
    // filter_var
    } else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'champ email non-valide';
    }
}

// si message existe et que le champ est non vide, on utilise trim() pour enlever les espaces/tabs en debut et fin de texte
if (isset($_POST['message']) && empty(trim($_POST['message']))) {
    $errors[] = 'champ message vide';
}




/*
---------------------------------------------------------

	insertion base de donn�es
	
---------------------------------------------------------
*/

// si il existe un champ "name" fourni dans le $_POST, c-a-d qu'un formulaire ient d'etre valid� ET qu'il n'y a aucune erreur
if(isset($_POST['name']) && count($errors) == 0){

// ben on insere dans la table message
// la synaxe ":name" ca veut dire qu'on prepare la requete et que juste quand on la lance, on va remplacer ":name" par la bonne valeur.

/* syntaxe avec preparedStatements */
    $sql = "insert into message (name, email, message) values(:name, :email, :message )";
    $sth = $dbh->prepare($sql, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY));
    $sth->execute(array(
        ':name' => $_POST['name'],
        ':email' => $_POST['email'],
        ':message' => $_POST['message']
    ));
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
            color: ff0000;
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
    <pre>
<?php
echo '$_GET' . "\r\n";
print_r($_GET);
?>
    </pre>
<hr/>
    <pre>
<?php
echo '$_POST' . "\r\n";
print_r($_POST);
?>
    </pre>
<hr/>




<h1>formulaire de la win</h1>
<p>
	le formulaire va envoyer ses donn�es a la page courante, on verifie la validit� des champs en PHP et on remonte les erreurs dans le tableau $errors	
</p>
<p>
	si pas d'erreur on enregistre dans la table message
</p>
<form method="post" action="">

    <div class="errors">
        <?php
        for ($i = 0; $i < count($errors); $i++) {
            echo($errors[$i] . "<br />");
        }
        ?>
    </div>

    <fieldset>
        <label for="name">name</label>
        <input type="text" name="name" value="<?php echo !empty($_POST['name']) ? ($_POST['name']) : '' ?>">
    </fieldset>

    <fieldset>
        <label for="email">email</label>
        <input type="text" name="email" value="<?= !empty($_POST['email']) ? ($_POST['email']) : '' ?>">
    </fieldset>

    <fieldset>
        <label for="message">message</label>
                <textarea name="message">
                    <?= !empty($_POST['truc']) ? ($_POST['truc']) : '' ?>
                </textarea>
    </fieldset>

    <input type="submit">
</form>


</body>
</html>