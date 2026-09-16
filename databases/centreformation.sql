SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `centreformation`
--

-- --------------------------------------------------------
-- Suppression des tables non conformes au modèle UML
-- --------------------------------------------------------

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS affectations_formateurs;
DROP TABLE IF EXISTS appels;
DROP TABLE IF EXISTS echeances;
DROP TABLE IF EXISTS groupes;
DROP TABLE IF EXISTS modules_semestres;
DROP TABLE IF EXISTS paiements_etudiants;
DROP TABLE IF EXISTS paiements_formateurs;
DROP TABLE IF EXISTS journal_activites;
DROP TABLE IF EXISTS roles;
DROP TABLE IF EXISTS salles;
DROP TABLE IF EXISTS supports_pedagogiques;
DROP TABLE IF EXISTS travaux_etudiants;
DROP TABLE IF EXISTS presences;
DROP TABLE IF EXISTS seances;
DROP TABLE IF EXISTS evaluations;
DROP TABLE IF EXISTS formateurs;
DROP TABLE IF EXISTS formations;
DROP TABLE IF EXISTS inscriptions;
DROP TABLE IF EXISTS modules;
DROP TABLE IF EXISTS niveaux;
DROP TABLE IF EXISTS semestres;
DROP TABLE IF EXISTS etudiants;
DROP TABLE IF EXISTS utilisateurs;
DROP TABLE IF EXISTS REMUNERATION_FORMATEUR;
DROP TABLE IF EXISTS PAIEMENT;
DROP TABLE IF EXISTS INSCRIPTION;
DROP TABLE IF EXISTS DOSSIER_ETUDIANT;
DROP TABLE IF EXISTS RESULTAT;
DROP TABLE IF EXISTS DEPOT;
DROP TABLE IF EXISTS EVALUATION;
DROP TABLE IF EXISTS SUPPORT;
DROP TABLE IF EXISTS PRESENCE;
DROP TABLE IF EXISTS SEANCE;
DROP TABLE IF EXISTS MODULE;
DROP TABLE IF EXISTS SEMESTRE;
DROP TABLE IF EXISTS NIVEAU;
DROP TABLE IF EXISTS FORMATION;
DROP TABLE IF EXISTS HISTORIQUE;
DROP TABLE IF EXISTS ETUDIANT;
DROP TABLE IF EXISTS COMPTABLE;
DROP TABLE IF EXISTS FORMATEUR;
DROP TABLE IF EXISTS RESPONSABLE_PEDAGOGIQUE;
DROP TABLE IF EXISTS DIRECTEUR;
DROP TABLE IF EXISTS ADMINISTRATEUR;
DROP TABLE IF EXISTS UTILISATEUR;

SET FOREIGN_KEY_CHECKS = 1;

-- --------------------------------------------------------
-- Domaine D1 — Utilisateurs et accès (7 tables)
-- --------------------------------------------------------

--
-- Structure de la table `UTILISATEUR`
--

CREATE TABLE `UTILISATEUR` (
  `idUtilisateur` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telephone` varchar(30) DEFAULT NULL,
  `nomUtilisateur` varchar(100) NOT NULL,
  `motDePasseHash` varchar(255) NOT NULL,
  `statutCompte` varchar(30) NOT NULL DEFAULT 'ACTIF',
  `dateCreation` datetime NOT NULL DEFAULT current_timestamp(),
  `derniereConnexion` datetime DEFAULT NULL,
  PRIMARY KEY (`idUtilisateur`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `nomUtilisateur` (`nomUtilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Structure de la table `ADMINISTRATEUR`
--

CREATE TABLE `ADMINISTRATEUR` (
  `idUtilisateur` int(11) NOT NULL,
  PRIMARY KEY (`idUtilisateur`),
  CONSTRAINT `fk_admin_utilisateur`
    FOREIGN KEY (`idUtilisateur`)
    REFERENCES `UTILISATEUR` (`idUtilisateur`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Structure de la table `DIRECTEUR`
--

CREATE TABLE `DIRECTEUR` (
  `idUtilisateur` int(11) NOT NULL,
  PRIMARY KEY (`idUtilisateur`),
  CONSTRAINT `fk_directeur_utilisateur`
    FOREIGN KEY (`idUtilisateur`)
    REFERENCES `UTILISATEUR` (`idUtilisateur`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Structure de la table `RESPONSABLE_PEDAGOGIQUE`
--

CREATE TABLE `RESPONSABLE_PEDAGOGIQUE` (
  `idUtilisateur` int(11) NOT NULL,
  `matricule` varchar(50) NOT NULL,
  `specialite` varchar(150) DEFAULT NULL,
  `datePriseFonction` date DEFAULT NULL,
  PRIMARY KEY (`idUtilisateur`),
  UNIQUE KEY `matricule` (`matricule`),
  CONSTRAINT `fk_responsable_utilisateur`
    FOREIGN KEY (`idUtilisateur`)
    REFERENCES `UTILISATEUR` (`idUtilisateur`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Structure de la table `FORMATEUR`
--

CREATE TABLE `FORMATEUR` (
  `idUtilisateur` int(11) NOT NULL,
  `matricule` varchar(50) NOT NULL,
  `specialite` varchar(150) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `experience` int(11) DEFAULT NULL,
  `disponibilites` text DEFAULT NULL,
  `tarifHoraire` decimal(12,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`idUtilisateur`),
  UNIQUE KEY `matricule` (`matricule`),
  CONSTRAINT `fk_formateur_utilisateur`
    FOREIGN KEY (`idUtilisateur`)
    REFERENCES `UTILISATEUR` (`idUtilisateur`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Structure de la table `COMPTABLE`
--

CREATE TABLE `COMPTABLE` (
  `idUtilisateur` int(11) NOT NULL,
  PRIMARY KEY (`idUtilisateur`),
  CONSTRAINT `fk_comptable_utilisateur`
    FOREIGN KEY (`idUtilisateur`)
    REFERENCES `UTILISATEUR` (`idUtilisateur`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Structure de la table `ETUDIANT`
--

CREATE TABLE `ETUDIANT` (
  `idUtilisateur` int(11) NOT NULL,
  `matricule` varchar(50) NOT NULL,
  `dateNaissance` date DEFAULT NULL,
  `lieuNaissance` varchar(150) DEFAULT NULL,
  `sexe` varchar(30) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `personneUrgence` varchar(150) DEFAULT NULL,
  `telephoneUrgence` varchar(30) DEFAULT NULL,
  `dateInscription` date DEFAULT NULL,
  `situationProfessionnelle` varchar(150) DEFAULT NULL,
  `statutParcours` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`idUtilisateur`),
  UNIQUE KEY `matricule` (`matricule`),
  CONSTRAINT `fk_etudiant_utilisateur`
    FOREIGN KEY (`idUtilisateur`)
    REFERENCES `UTILISATEUR` (`idUtilisateur`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Structure de la table `HISTORIQUE`
--

CREATE TABLE `HISTORIQUE` (
  `idHistorique` int(11) NOT NULL AUTO_INCREMENT,
  `action` varchar(255) NOT NULL,
  `dateAction` datetime NOT NULL DEFAULT current_timestamp(),
  `utilisateurId` int(11) NOT NULL,
  PRIMARY KEY (`idHistorique`),
  KEY `fk_historique_utilisateur` (`utilisateurId`),
  CONSTRAINT `fk_historique_utilisateur`
    FOREIGN KEY (`utilisateurId`)
    REFERENCES `UTILISATEUR` (`idUtilisateur`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Domaine D2 — Formation et pédagogie (5 tables)
-- --------------------------------------------------------

--
-- Structure de la table `FORMATION`
--

CREATE TABLE `FORMATION` (
  `idFormation` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `duree` int(11) DEFAULT NULL,
  PRIMARY KEY (`idFormation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Structure de la table `NIVEAU`
--

CREATE TABLE `NIVEAU` (
  `idNiveau` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(100) NOT NULL,
  `ordre` int(11) NOT NULL,
  `idFormation` int(11) NOT NULL,
  PRIMARY KEY (`idNiveau`),
  KEY `fk_niveau_formation` (`idFormation`),
  CONSTRAINT `fk_niveau_formation`
    FOREIGN KEY (`idFormation`)
    REFERENCES `FORMATION` (`idFormation`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Structure de la table `SEMESTRE`
--

CREATE TABLE `SEMESTRE` (
  `idSemestre` int(11) NOT NULL AUTO_INCREMENT,
  `libelle` varchar(100) NOT NULL,
  `ordre` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `idNiveau` int(11) NOT NULL,
  PRIMARY KEY (`idSemestre`),
  KEY `fk_semestre_niveau` (`idNiveau`),
  CONSTRAINT `fk_semestre_niveau`
    FOREIGN KEY (`idNiveau`)
    REFERENCES `NIVEAU` (`idNiveau`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Structure de la table `MODULE`
--

CREATE TABLE `MODULE` (
  `idModule` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `libelle` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `volumeHoraire` int(11) DEFAULT NULL,
  `idSemestre` int(11) NOT NULL,
  `idFormateur` int(11) DEFAULT NULL,
  PRIMARY KEY (`idModule`),
  UNIQUE KEY `code` (`code`),
  KEY `fk_module_semestre` (`idSemestre`),
  KEY `fk_module_formateur` (`idFormateur`),
  CONSTRAINT `fk_module_semestre`
    FOREIGN KEY (`idSemestre`)
    REFERENCES `SEMESTRE` (`idSemestre`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_module_formateur`
    FOREIGN KEY (`idFormateur`)
    REFERENCES `FORMATEUR` (`idUtilisateur`)
    ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Structure de la table `SEANCE`
--

CREATE TABLE `SEANCE` (
  `idSeance` int(11) NOT NULL AUTO_INCREMENT,
  `dateSeance` date NOT NULL,
  `heureDebut` time NOT NULL,
  `heureFin` time NOT NULL,
  `statut` varchar(50) NOT NULL DEFAULT 'PROGRAMMEE',
  `idModule` int(11) NOT NULL,
  `idFormateur` int(11) NOT NULL,
  PRIMARY KEY (`idSeance`),
  KEY `fk_seance_module` (`idModule`),
  KEY `fk_seance_formateur` (`idFormateur`),
  CONSTRAINT `fk_seance_module`
    FOREIGN KEY (`idModule`)
    REFERENCES `MODULE` (`idModule`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_seance_formateur`
    FOREIGN KEY (`idFormateur`)
    REFERENCES `FORMATEUR` (`idUtilisateur`)
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Domaine D3 — Planification et présence (1 table)
-- --------------------------------------------------------

--
-- Structure de la table `PRESENCE`
--

CREATE TABLE `PRESENCE` (
  `idPresence` int(11) NOT NULL AUTO_INCREMENT,
  `statutPresence` varchar(30) NOT NULL,
  `heureArrivee` time DEFAULT NULL,
  `justification` text DEFAULT NULL,
  `dateJustification` date DEFAULT NULL,
  `validee` tinyint(1) NOT NULL DEFAULT 0,
  `idSeance` int(11) NOT NULL,
  `idEtudiant` int(11) NOT NULL,
  PRIMARY KEY (`idPresence`),
  UNIQUE KEY `uq_presence_seance_etudiant` (`idSeance`, `idEtudiant`),
  KEY `fk_presence_etudiant` (`idEtudiant`),
  CONSTRAINT `fk_presence_seance`
    FOREIGN KEY (`idSeance`)
    REFERENCES `SEANCE` (`idSeance`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_presence_etudiant`
    FOREIGN KEY (`idEtudiant`)
    REFERENCES `ETUDIANT` (`idUtilisateur`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Domaine D4 — Évaluations et ressources (4 tables)
-- --------------------------------------------------------

--
-- Structure de la table `SUPPORT`
--

CREATE TABLE `SUPPORT` (
  `idSupport` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(200) NOT NULL,
  `type` varchar(50) NOT NULL,
  `fichier` varchar(500) NOT NULL,
  `dateAjout` datetime NOT NULL DEFAULT current_timestamp(),
  `idModule` int(11) NOT NULL,
  PRIMARY KEY (`idSupport`),
  KEY `fk_support_module` (`idModule`),
  CONSTRAINT `fk_support_module`
    FOREIGN KEY (`idModule`)
    REFERENCES `MODULE` (`idModule`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Structure de la table `EVALUATION`
--

CREATE TABLE `EVALUATION` (
  `idEvaluation` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `dateEvaluation` date DEFAULT NULL,
  `dateLimite` datetime NOT NULL,
  `idModule` int(11) NOT NULL,
  PRIMARY KEY (`idEvaluation`),
  KEY `fk_evaluation_module` (`idModule`),
  CONSTRAINT `fk_evaluation_module`
    FOREIGN KEY (`idModule`)
    REFERENCES `MODULE` (`idModule`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Structure de la table `DEPOT`
--

CREATE TABLE `DEPOT` (
  `idDepot` int(11) NOT NULL AUTO_INCREMENT,
  `fichier` varchar(500) NOT NULL,
  `dateDepot` datetime NOT NULL DEFAULT current_timestamp(),
  `statut` varchar(50) DEFAULT NULL,
  `idEvaluation` int(11) NOT NULL,
  `idEtudiant` int(11) NOT NULL,
  PRIMARY KEY (`idDepot`),
  UNIQUE KEY `uq_depot_evaluation_etudiant` (`idEvaluation`, `idEtudiant`),
  KEY `fk_depot_etudiant` (`idEtudiant`),
  CONSTRAINT `fk_depot_evaluation`
    FOREIGN KEY (`idEvaluation`)
    REFERENCES `EVALUATION` (`idEvaluation`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_depot_etudiant`
    FOREIGN KEY (`idEtudiant`)
    REFERENCES `ETUDIANT` (`idUtilisateur`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Structure de la table `RESULTAT`
--

CREATE TABLE `RESULTAT` (
  `idResultat` int(11) NOT NULL AUTO_INCREMENT,
  `note` decimal(6,2) NOT NULL,
  `appreciation` text DEFAULT NULL,
  `datePublication` datetime DEFAULT NULL,
  `idEvaluation` int(11) NOT NULL,
  `idEtudiant` int(11) NOT NULL,
  PRIMARY KEY (`idResultat`),
  UNIQUE KEY `uq_resultat_evaluation_etudiant` (`idEvaluation`, `idEtudiant`),
  KEY `fk_resultat_etudiant` (`idEtudiant`),
  CONSTRAINT `fk_resultat_evaluation`
    FOREIGN KEY (`idEvaluation`)
    REFERENCES `EVALUATION` (`idEvaluation`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_resultat_etudiant`
    FOREIGN KEY (`idEtudiant`)
    REFERENCES `ETUDIANT` (`idUtilisateur`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Domaine D5 — Inscriptions et finances (5 tables)
-- --------------------------------------------------------

--
-- Structure de la table `DOSSIER_ETUDIANT`
--

CREATE TABLE `DOSSIER_ETUDIANT` (
  `idDossier` int(11) NOT NULL AUTO_INCREMENT,
  `anneeScolaire` varchar(20) NOT NULL,
  `statut` varchar(50) NOT NULL,
  `dateCreation` datetime NOT NULL DEFAULT current_timestamp(),
  `idEtudiant` int(11) NOT NULL,
  PRIMARY KEY (`idDossier`),
  UNIQUE KEY `uq_dossier_etudiant` (`idEtudiant`, `anneeScolaire`),
  KEY `fk_dossier_etudiant` (`idEtudiant`),
  CONSTRAINT `fk_dossier_etudiant`
    FOREIGN KEY (`idEtudiant`)
    REFERENCES `ETUDIANT` (`idUtilisateur`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Structure de la table `INSCRIPTION`
--

CREATE TABLE `INSCRIPTION` (
  `idInscription` int(11) NOT NULL AUTO_INCREMENT,
  `dateInscription` date NOT NULL,
  `statut` varchar(50) NOT NULL DEFAULT 'EN_ATTENTE',
  `idDossier` int(11) NOT NULL,
  `idNiveau` int(11) NOT NULL,
  PRIMARY KEY (`idInscription`),
  KEY `fk_inscription_dossier` (`idDossier`),
  KEY `fk_inscription_niveau` (`idNiveau`),
  CONSTRAINT `fk_inscription_dossier`
    FOREIGN KEY (`idDossier`)
    REFERENCES `DOSSIER_ETUDIANT` (`idDossier`)
    ON DELETE CASCADE,
  CONSTRAINT `fk_inscription_niveau`
    FOREIGN KEY (`idNiveau`)
    REFERENCES `NIVEAU` (`idNiveau`)
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Structure de la table `PAIEMENT`
--

CREATE TABLE `PAIEMENT` (
  `idPaiement` int(11) NOT NULL AUTO_INCREMENT,
  `montant` decimal(12,2) NOT NULL,
  `datePaiement` datetime NOT NULL DEFAULT current_timestamp(),
  `modePaiement` varchar(50) NOT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `idInscription` int(11) NOT NULL,
  PRIMARY KEY (`idPaiement`),
  UNIQUE KEY `uq_paiement_reference` (`reference`),
  KEY `fk_paiement_inscription` (`idInscription`),
  CONSTRAINT `fk_paiement_inscription`
    FOREIGN KEY (`idInscription`)
    REFERENCES `INSCRIPTION` (`idInscription`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Structure de la table `REMUNERATION_FORMATEUR`
--

CREATE TABLE `REMUNERATION_FORMATEUR` (
  `idRemuneration` int(11) NOT NULL AUTO_INCREMENT,
  `mois` varchar(20) NOT NULL,
  `heuresValidees` decimal(8,2) NOT NULL DEFAULT 0.00,
  `montantDu` decimal(12,2) NOT NULL DEFAULT 0.00,
  `montantPaye` decimal(12,2) NOT NULL DEFAULT 0.00,
  `statut` varchar(50) NOT NULL DEFAULT 'A_CALCULER',
  `idFormateur` int(11) NOT NULL,
  PRIMARY KEY (`idRemuneration`),
  UNIQUE KEY `uq_remuneration_formateur_mois` (`idFormateur`, `mois`),
  KEY `fk_remuneration_formateur` (`idFormateur`),
  CONSTRAINT `fk_remuneration_formateur`
    FOREIGN KEY (`idFormateur`)
    REFERENCES `FORMATEUR` (`idUtilisateur`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Déchargement des données
-- --------------------------------------------------------

--
-- Données pour la table `UTILISATEUR`
--

INSERT INTO `UTILISATEUR` (`idUtilisateur`, `nom`, `prenom`, `email`, `telephone`, `nomUtilisateur`, `motDePasseHash`, `statutCompte`, `dateCreation`, `derniereConnexion`) VALUES
(1, 'Ndiaye', 'Moussa', 'admin@test.com', '770000001', 'admin', 'motdepasse_hash_admin', 'ACTIF', '2026-09-14 15:39:08', NULL),
(2, 'Fall', 'Awa', 'pedagogie@test.com', '770000002', 'responsable_pedago', 'motdepasse_hash_pedagogie', 'ACTIF', '2026-09-14 15:39:08', NULL),
(3, 'Ba', 'Ousmane', 'comptable@test.com', '770000003', 'comptable', 'motdepasse_hash_comptable', 'ACTIF', '2026-09-14 15:39:08', NULL),
(4, 'Diop', 'Fatou', 'formateur@test.com', '770000004', 'formateur', 'motdepasse_hash_formateur', 'ACTIF', '2026-09-14 15:39:08', NULL),
(5, 'Sarr', 'Ibrahima', 'etudiant@test.com', '770000005', 'etudiant', 'motdepasse_hash_etudiant', 'ACTIF', '2026-09-14 15:39:08', NULL);

--
-- Données pour la table `ADMINISTRATEUR`
--

INSERT INTO `ADMINISTRATEUR` (`idUtilisateur`) VALUES
(1);

--
-- Données pour la table `DIRECTEUR`
--

INSERT INTO `DIRECTEUR` (`idUtilisateur`) VALUES
(2);

--
-- Données pour la table `RESPONSABLE_PEDAGOGIQUE`
--

INSERT INTO `RESPONSABLE_PEDAGOGIQUE` (`idUtilisateur`, `matricule`, `specialite`, `datePriseFonction`) VALUES
(2, 'RP-2026-001', 'Pédagogie', '2026-09-01');

--
-- Données pour la table `FORMATEUR`
--

INSERT INTO `FORMATEUR` (`idUtilisateur`, `matricule`, `specialite`, `adresse`, `experience`, `disponibilites`, `tarifHoraire`) VALUES
(4, 'FORM-2026-001', 'Développement web', 'Dakar', 5, 'Lundi-Vendredi', 7500.00);

--
-- Données pour la table `COMPTABLE`
--

INSERT INTO `COMPTABLE` (`idUtilisateur`) VALUES
(3);

--
-- Données pour la table `ETUDIANT`
--

INSERT INTO `ETUDIANT` (`idUtilisateur`, `matricule`, `dateNaissance`, `lieuNaissance`, `sexe`, `adresse`, `photo`, `personneUrgence`, `telephoneUrgence`, `dateInscription`, `situationProfessionnelle`, `statutParcours`) VALUES
(5, 'ETU-2026-001', '2002-05-12', 'Dakar', 'M', 'Pikine, Dakar', NULL, 'Parent', '770000006', '2026-09-14', 'Étudiant', 'EN_COURS');

--
-- Données pour la table `FORMATION`
--

INSERT INTO `FORMATION` (`idFormation`, `nom`, `description`, `duree`) VALUES
(1, 'Développement web', 'Formation en développement web et bases de données', 12);

--
-- Données pour la table `NIVEAU`
--

INSERT INTO `NIVEAU` (`idNiveau`, `libelle`, `ordre`, `idFormation`) VALUES
(1, 'L1', 1, 1);

--
-- Données pour la table `SEMESTRE`
--

INSERT INTO `SEMESTRE` (`idSemestre`, `libelle`, `ordre`, `description`, `idNiveau`) VALUES
(1, 'S1', 1, 'Premier semestre', 1);

--
-- Données pour la table `MODULE`
--

INSERT INTO `MODULE` (`idModule`, `code`, `libelle`, `description`, `volumeHoraire`, `idSemestre`, `idFormateur`) VALUES
(1, 'SQL101', 'Bases de données', 'Introduction à MySQL et à la conception relationnelle', 40, 1, 4);

--
-- Données pour la table `SEANCE`
--

INSERT INTO `SEANCE` (`idSeance`, `dateSeance`, `heureDebut`, `heureFin`, `statut`, `idModule`, `idFormateur`) VALUES
(1, '2026-10-05', '08:00:00', '10:00:00', 'VALIDEE', 1, 4);

--
-- Données pour la table `PRESENCE`
--

INSERT INTO `PRESENCE` (`idPresence`, `statutPresence`, `heureArrivee`, `justification`, `dateJustification`, `validee`, `idSeance`, `idEtudiant`) VALUES
(1, 'PRESENT', NULL, NULL, NULL, 1, 1, 5);

--
-- Données pour la table `DOSSIER_ETUDIANT`
--

INSERT INTO `DOSSIER_ETUDIANT` (`idDossier`, `anneeScolaire`, `statut`, `dateCreation`, `idEtudiant`) VALUES
(1, '2026-2027', 'ACTIF', '2026-09-14 15:39:08', 5);

--
-- Données pour la table `INSCRIPTION`
--

INSERT INTO `INSCRIPTION` (`idInscription`, `dateInscription`, `statut`, `idDossier`, `idNiveau`) VALUES
(1, '2026-09-14', 'VALIDEE', 1, 1);

--
-- Données pour la table `PAIEMENT`
--

INSERT INTO `PAIEMENT` (`idPaiement`, `montant`, `datePaiement`, `modePaiement`, `reference`, `idInscription`) VALUES
(1, 100000.00, '2026-09-14 15:39:10', 'ESPECES', 'PAY-2026-0001', 1);

--
-- Données pour la table `REMUNERATION_FORMATEUR`
--

INSERT INTO `REMUNERATION_FORMATEUR` (`idRemuneration`, `mois`, `heuresValidees`, `montantDu`, `montantPaye`, `statut`, `idFormateur`) VALUES
(1, '2026-09', 2.00, 15000.00, 0.00, 'A_PAYER', 4);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;