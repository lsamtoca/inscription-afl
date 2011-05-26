# script crŽŽ le : Thu May 26 10:19:33 CEST 2011 -   syntaxe MySQL ;

# use  VOTRE_BASE_DE_DONNEE ;

DROP TABLE IF EXISTS Regate ;
CREATE TABLE Regate (ID_regate int AUTO_INCREMENT NOT NULL,
org_login VARCHAR(10),
org_passe VARCHAR(10),
description VARCHAR(500),
destruction DATE,
ID_administrateur INT NOT NULL,
PRIMARY KEY (ID_regate) ) ENGINE=InnoDB;

DROP TABLE IF EXISTS Inscrit ;
CREATE TABLE Inscrit (ID_inscrit int AUTO_INCREMENT NOT NULL,
nom VARCHAR(20),
prenom VARCHAR(20),
naissance DATE,
num_lic VARCHAR(15),
prefix_voile VARCHAR(4),
num_voile INT,
serie INT,
adherant BOOL,
sexe BOOL,
conf BOOL,
mail VARCHAR(20),
statut INT,
ID_regate INT NOT NULL,
PRIMARY KEY (ID_inscrit) ) ENGINE=InnoDB;

DROP TABLE IF EXISTS Administrateur ;
CREATE TABLE Administrateur (ID_administrateur int AUTO_INCREMENT NOT NULL,
admin_login VARCHAR(10),
admin_passe VARCHAR(10),
PRIMARY KEY (ID_administrateur) ) ENGINE=InnoDB;

ALTER TABLE Regate ADD CONSTRAINT FK_Regate_ID_administrateur FOREIGN KEY (ID_administrateur) REFERENCES Administrateur (ID_administrateur);

ALTER TABLE Inscrit ADD CONSTRAINT FK_Inscrit_ID_regate FOREIGN KEY (ID_regate) REFERENCES Regate (ID_regate);
