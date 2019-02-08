<h1>formulaire de la win</h1>
<a href="?"> appeller le controller courant sans action dans la Query String</a>
<p>
    on va modifier l'action du formulaire pour rajouter "?action=save" dans &lt;form method="post" action="?action=save"
    id="contact_form"&gt;
</p>
<p>
    on rajoute ce cas dans le controller pour valider le formulaire
</p>

<p>
    comme cette view est incluse par le controller, il est possible qu'un tableau d'erreurs soit disponible, du coup si
    il existe on affiche
</p>

<form method="post" action="?action=save" id="contact_form">
    <div class="errors">
        <?php
        if (isset($errors)) {
            // si il y a des erreurs on les affiche.
            for ($i = 0; $i < count($errors); $i++) {
                echo($errors[$i] . "<br />");
            }
        }
        ?>
    </div>


    <fieldset>
        <label for="name">name</label>
        <input type="text" id="name" name="name"
               value="<?php echo !empty($postdata['name']) ? ($postdata['name']) : '' ?>">
    </fieldset>

    <fieldset>
        <label for="email">email</label>
        <input type="text" id="email" name="email"
               value="<?= !empty($postdata['email']) ? ($postdata['email']) : '' ?>">
    </fieldset>

    <fieldset>
        <label for="message">message</label>
                <textarea id="message" name="message">
                    <?= !empty($postdata['message']) ? ($postdata['message']) : '' ?>
                </textarea>
    </fieldset>


    <input type="submit" id="ajax_form_check" value="click click">

</form>

