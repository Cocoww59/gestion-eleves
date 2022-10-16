<?php
session_start();
if(isset($_POST["id"])) {
    require("../../utils.php");
    require("../../db.php");

    $id = $_POST["id"];

    try {
        $db->prepare("DELETE FROM dampierre.eleves WHERE id_eleve = $id")->execute();

        exit($_SESSION["message"] = setMessage(1, "Élève supprimé avec succès."));
    } catch(Exception) {
        exit($_SESSION["message"] = setMessage(0, "Erreur lors de la suppresion."));
    }

}
