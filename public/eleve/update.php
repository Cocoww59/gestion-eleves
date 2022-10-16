<?php
session_start();
if(isset($_POST["id"], $_POST["nom"], $_POST["prenom"])) {
    require("../../utils.php");
    require("../../db.php");

    $id = $_POST["id"];
    $nom = $_POST["nom"];
    $prenom = $_POST["prenom"];

    try {
        $db->prepare("UPDATE dampierre.eleves SET nom_eleve = '$nom', prenom_eleve = '$prenom' WHERE id_eleve = $id")->execute();

        exit($_SESSION["message"] = setMessage(1, "Élève modifié avec succès."));
    } catch(Exception) {
        exit($_SESSION["message"] = setMessage(0, "Erreur lors de la modification."));
    }

}
