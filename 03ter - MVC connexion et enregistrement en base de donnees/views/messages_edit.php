<?php
session_start();
$errors = isset($_SESSION['errors']) ? $_SESSION['errors'] : [];
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
<?php require_once('../components/nav.php') ?>
<div class="container">

    <div class="row">

        <ul class="errors">
            <?php
            foreach ($errors as $error) {
                echo("<li>" . $error . "</li>");
            }
            ?>
        </ul>

        <form method="post" action="?action=edit" id="messageCreateForm">
            <fieldset>
                <legend>message edit</legend>

                <label for="id">id</label>
                <input type="text" id="id" name="id" value="<?php echo !empty($message->id) ? ($message->id) : '' ?>">

                <label for="userId">user_id</label>
                <input type="text" id="user_id" name="user_id" value="<?php echo !empty($message->user_id) ? ($message->user_id) : '' ?>">


                <label for="messageContent">content</label>
                <input type="text" id="content" name="content" value="<?php echo !empty($message->content) ? ($message->content) : '' ?>">

            </fieldset>
            <input type="submit" value="Envoyer" class="button-primary">
        </form>
    </div>

    <div class="row">
        <div class="column">
            $_SESSION
            <pre><?php print_r($_SESSION) ?></pre>
        </div>

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
