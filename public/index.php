<!--
###############################################
#                  BTS SIO1                   #
#---------------------------------------------#
#                   Cocoww_                   #
#---------------------------------------------#
#       SYSTÈME DE GESTION D'UNE CLASSE       #
#               PHP & MYSQL & JS              #
###############################################
-->
<?php
// Démarrage de la session pour les messages flash
session_start();

// importation des fichers php requis.
require("../utils.php");
require("../db.php");

// Création de la base de données.
if(isset($_POST["create-db"])) {

    try {
        $db->prepare(file_get_contents("../create-db.sql"))->execute();
        $_SESSION["message"] = setMessage(1, "Base de données créée avec succès.");
    } catch(Exception) {
        $_SESSION["message"] = setMessage(0, "La base de données a déjà été créée");
    }

}

// Suppression de la base de données.
if(isset($_POST["remove-db"])) {

    try {
        $db->prepare("DROP DATABASE dampierre")->execute();
        $_SESSION["message"] = setMessage(1, "Base de données supprimée avec succès.");
    } catch(Exception) {}

}

// Récupération de tous les élèves.
try {
    $req = $db->prepare("SELECT id_eleve, nom_eleve, prenom_eleve, sexe_eleve FROM dampierre.eleves");
    $req->execute();

    $eleves = ($req->rowCount() ? $req->fetchAll() : setMessage(0, "Aucun élève enregistré."));
} catch(Exception) {
    $_SESSION["message"] = setMessage(0, "Base de données 'Dampierre' inexistante.<br><br>
                            Le bouton 'créer' a pour but :<br>
                            - de créer une base de données appelée 'dampierre' sur le serveur local<br><br>
                            - de créer une table 'eleves'<br><br>
                            - d'ajouter 4 enregistrements dans la table 'eleves'");
}

// message
$message = "";
if(isset($_SESSION["message"])) {
    $message = <<<HTML
        <div>
            <p>{$_SESSION["message"]}<p>
        </div>
    HTML;
    unset($_SESSION["message"]);
}

// boxR && js
$boxR = "";
$js = "";
if(isset($eleves)) {

    // boxR
    $boxR = <<<HTML
        <form action="/eleve/add.php" method="POST" class="form-add">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom">
            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom">
            <label for="sexe">Sexe</label>
            <select id="sexe" name="sexe">
                <option value="1">Masculin</option>
                <option value="2">Féminin</option>
            </select>
            <button class="btn-add" name="btn-add">Valider</button>
        </form>
    HTML;

    if(is_array($eleves)) {
        
        // boxR
        $boxR .= '<div class="flex">';
        foreach($eleves as $eleve) {
            $sexe = " " . ($eleve->sexe_eleve === 2 ? "women" : "");

            $boxR .= <<<HTML
                <div class="box-eleve" title="$eleve->nom_eleve $eleve->prenom_eleve" data-id_eleve="$eleve->id_eleve">
                    <div class="image-eleve$sexe"></div>
                    <p class="name-eleve" contenteditable>$eleve->nom_eleve $eleve->prenom_eleve</p>
                    <div class="actions-eleve">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="confirm-eleve hidden"><path d="M5 12l5 5L20 7"></path></svg>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="remove-eleve"><path d="M3 7h18"></path><path d="M8 7V3h8v4"></path><path d="M19 7v14H5V7"></path><path d="M10 12v4"></path><path d="M14 12v4"></path></svg>
                    </div>
                </div>
            HTML;
        }
        $boxR .= "</div>";

        // js
        $elevesJS = array_map(function($eleve) {
            return "{id:" . $eleve->id_eleve . ",nom:'" . $eleve->nom_eleve . "',prenom:'" . $eleve->prenom_eleve . "'},";
        }, $eleves);
        $elevesJS = join("", $elevesJS);
        
        $js .= <<<HTML
            <script>
                (() => {
                    const eleves = [$elevesJS];

                    // name
                    const nameEleves = document.querySelectorAll(".name-eleve");

                    const onInputNameEleve = function() {
                        
                        const id_eleve = parseInt(this.parentElement.getAttribute("data-id_eleve"));
                        const eleve = eleves.find(eleve => eleve.id === id_eleve);
                        const fullName = eleve.nom + " " + eleve.prenom;

                        if(this.innerText !== fullName) {
                            this.parentElement.querySelector(".confirm-eleve").classList.remove("hidden");
                        } else {
                            this.parentElement.querySelector(".confirm-eleve").classList.add("hidden");
                        }

                    }
                    nameEleves.forEach(nameEleve => {
                        nameEleve.addEventListener("input", onInputNameEleve);
                    });

                    // remove and confirm
                    const onActionEleve = (file, action = file) => {

                        return function() {

                            const id_eleve = parseInt(this.parentElement.parentElement.getAttribute("data-id_eleve"));

                            const paramsPost = new FormData;
                            paramsPost.append("id", id_eleve);

                            if(action === "update") {
                                const fullName = this.parentElement.parentElement.querySelector(".name-eleve").innerText.split(" ");

                                paramsPost.append("nom", fullName[0]);
                                paramsPost.append("prenom", fullName[1]);
                            }

                            fetch("/eleve/" + file + ".php",
                                {
                                    method: "POST",
                                    body: paramsPost
                                }
                            )
                            .then(() => document.location = "/");
                        
                        }

                    }
                    const confirmEleves = document.querySelectorAll(".confirm-eleve");
                    const removeEleves = document.querySelectorAll(".remove-eleve");

                    confirmEleves.forEach(confirmEleve => {
                        confirmEleve.addEventListener("click", onActionEleve("update"));
                    });
                    removeEleves.forEach(removeEleve => {
                        removeEleve.addEventListener("click", onActionEleve("remove"));
                    });
                })();
            </script>
        HTML;

    } else {
        $boxR .= $eleves;
    }
}

// DOM
echo(<<<HTML
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cocoww_ | SYSTÈME DE GESTION D'UNE CLASSE</title>
        <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="css/default.css">
        <link rel="stylesheet" href="css/index.css">
    </head>
    <body>
        <div class="g">
            <div id="app" class="app">
                <h4 class="author">Cocoww_ &#10084;&#65039;</h4>
                <h1>BTS SIO1 Ex. PHP SQL JS</h1>
                <div class="flex">
                    <div class="box-l">
                        <form method="POST">
                            <p>Créer la base de données</p>
                            <button name="create-db">Créer</button>
                            <p>Supprimer la base de données</p>
                            <button name="remove-db">Supprimer</button>
                        </form>
                        $message
                    </div>
                    <div class="box-r">
                        $boxR
                    </div>
                </div>
            </div>
        </div>
        $js
    </body>
</html>
HTML);
