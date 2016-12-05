/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Author:  lsantoca
 * Created: 4 déc. 2016
 */

INSERT INTO `Administrateur` (`ID_administrateur`, `admin_login`, `coded_admin_passe`, `Nom`, `Prenom`, `courriel`, `nonce`) 
    VALUES ('1', 'admin', MD5('admin'), 'Santocanale', 'Luigi', 'luigi.santocanale@lif.univ-mrs.fr', NULL);

INSERT INTO `Regate` (`ID_regate`, `nonce`, `org_login`, `coded_org_passe`, `istest`, `titre`, `description`, `informations`, `cv_organisateur`, `courriel`, `lieu`, `date_debut`, `date_fin`, `date_limite_preinscriptions`, `series`, `polo`, `droits`, `paiement_en_ligne`, `resultats`, `destruction`, `ID_administrateur`) 
    VALUES (1, '', 'coucoucou', MD5('coucoucou'), 0, 'Test', 'cooucou', 'Test', 'LaPelle', 'luigi.santocanale@lif.univ-mrs.fr', 'Marseille', '2016-12-04', '2016-12-04', '2016-12-04', 'LA4,LAR,LAS,OPT', 0, 0, '', '', '2017-12-04', 1);
