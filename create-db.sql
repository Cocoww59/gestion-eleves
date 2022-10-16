CREATE DATABASE dampierre;

USE `dampierre`;

CREATE TABLE eleves (
    id_eleve int(11) PRIMARY KEY NOT NULL AUTO_INCREMENT,
    nom_eleve varchar(50) NOT NULL,
    prenom_eleve varchar(50) NOT NULL,
    sexe_eleve int(1) DEFAULT NULL
);

INSERT INTO eleves
VALUES  (
            null,
            "Karel",
            "Doyon",
            1
        ),
        (
            null,
            "Voleta",
            "Masson",
            2
        ),
        (
            null,
            "Namo",
            "Therriault",
            2
        ),
        (
            null,
            "Avenall",
            "Abril",
            2
        );
