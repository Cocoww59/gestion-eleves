<?php
session_start();
if(isset($_POST["nom"], $_POST["prenom"], $_POST["sexe"])) {
    require("../../utils.php");
    require("../../db.php");

    $nom = trim($_POST["nom"]);
    $prenom = trim($_POST["prenom"]);
    $sexe = trim($_POST["sexe"]);

    if($nom && $prenom && $sexe) {

        try {
            $db->prepare("INSERT INTO dampierre.eleves VALUES (null, '$nom', '$prenom', $sexe)")->execute();

            $_SESSION["message"] = setMessage(1, "Élève ajouté avec succès.");
        } catch(Exception) {
            $_SESSION["message"] = setMessage(0, "Erreur lors de l'ajout.");
        }

    }

}
header("Location: /");
