-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 17 sep. 2026 à 02:39
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gestform`
--

-- --------------------------------------------------------

--
-- Structure de la table `administrateur`
--

CREATE TABLE `administrateur` (
  `idUtilisateur` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `administrateur`
--

INSERT INTO `administrateur` (`idUtilisateur`) VALUES
(1);

-- --------------------------------------------------------

--
-- Structure de la table `comptable`
--

CREATE TABLE `comptable` (
  `idUtilisateur` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `comptable`
--

INSERT INTO `comptable` (`idUtilisateur`) VALUES
(5);

-- --------------------------------------------------------

--
-- Structure de la table `depot`
--

CREATE TABLE `depot` (
  `idDepot` int(11) NOT NULL,
  `idEvaluation` int(11) NOT NULL,
  `idEtudiant` int(11) NOT NULL,
  `fichier` varchar(255) NOT NULL,
  `dateDepot` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `directeur`
--

CREATE TABLE `directeur` (
  `idUtilisateur` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `directeur`
--

INSERT INTO `directeur` (`idUtilisateur`) VALUES
(2);

-- --------------------------------------------------------

--
-- Structure de la table `dossier_etudiant`
--

CREATE TABLE `dossier_etudiant` (
  `idDossier` int(11) NOT NULL,
  `idEtudiant` int(11) NOT NULL,
  `anneeScolaire` varchar(20) NOT NULL,
  `statut` varchar(50) NOT NULL,
  `dateCreation` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `etudiant`
--

CREATE TABLE `etudiant` (
  `idUtilisateur` int(11) NOT NULL,
  `matricule` varchar(50) NOT NULL,
  `dateNaissance` date DEFAULT NULL,
  `lieuNaissance` varchar(150) DEFAULT NULL,
  `sexe` varchar(20) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `personneUrgence` varchar(150) DEFAULT NULL,
  `telephoneUrgence` varchar(30) DEFAULT NULL,
  `dateInscription` date DEFAULT NULL,
  `situationProfessionnelle` varchar(150) DEFAULT NULL,
  `statutParcours` varchar(80) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `etudiant`
--

INSERT INTO `etudiant` (`idUtilisateur`, `matricule`, `dateNaissance`, `lieuNaissance`, `sexe`, `adresse`, `photo`, `personneUrgence`, `telephoneUrgence`, `dateInscription`, `situationProfessionnelle`, `statutParcours`) VALUES
(6, 'ETU001', '2005-04-10', 'Dakar', 'F', 'Dakar', NULL, NULL, NULL, '2026-09-01', NULL, 'ACTIF');

-- --------------------------------------------------------

--
-- Structure de la table `evaluation`
--

CREATE TABLE `evaluation` (
  `idEvaluation` int(11) NOT NULL,
  `idModule` int(11) NOT NULL,
  `titre` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `dateOuverture` datetime DEFAULT NULL,
  `dateLimite` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `formateur`
--

CREATE TABLE `formateur` (
  `idUtilisateur` int(11) NOT NULL,
  `matricule` varchar(50) NOT NULL,
  `specialite` varchar(150) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `experience` int(11) DEFAULT NULL,
  `disponibilites` varchar(255) DEFAULT NULL,
  `tarifHoraire` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `formateur`
--

INSERT INTO `formateur` (`idUtilisateur`, `matricule`, `specialite`, `adresse`, `experience`, `disponibilites`, `tarifHoraire`) VALUES
(4, 'FOR001', 'Développement Web', 'Dakar', 5, 'Après-midi', 5000.00);

-- --------------------------------------------------------

--
-- Structure de la table `formation`
--

CREATE TABLE `formation` (
  `idFormation` int(11) NOT NULL,
  `libelle` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `duree` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `historique`
--

CREATE TABLE `historique` (
  `idHistorique` int(11) NOT NULL,
  `utilisateurId` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `dateAction` datetime NOT NULL DEFAULT current_timestamp(),
  `details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `inscription`
--

CREATE TABLE `inscription` (
  `idInscription` int(11) NOT NULL,
  `idDossier` int(11) NOT NULL,
  `idNiveau` int(11) NOT NULL,
  `dateInscription` date NOT NULL,
  `statut` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `module`
--

CREATE TABLE `module` (
  `idModule` int(11) NOT NULL,
  `idSemestre` int(11) NOT NULL,
  `idFormateur` int(11) DEFAULT NULL,
  `libelle` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `volumeHoraire` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `niveau`
--

CREATE TABLE `niveau` (
  `idNiveau` int(11) NOT NULL,
  `idFormation` int(11) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `ordre` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `paiement`
--

CREATE TABLE `paiement` (
  `idPaiement` int(11) NOT NULL,
  `idInscription` int(11) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `datePaiement` date NOT NULL,
  `modePaiement` varchar(50) DEFAULT NULL,
  `referencePaiement` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `presence`
--

CREATE TABLE `presence` (
  `idPresence` int(11) NOT NULL,
  `idSeance` int(11) NOT NULL,
  `idEtudiant` int(11) NOT NULL,
  `statutPresence` varchar(30) NOT NULL,
  `heureArrivee` time DEFAULT NULL,
  `justification` text DEFAULT NULL,
  `dateJustification` date DEFAULT NULL,
  `validee` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `remuneration_formateur`
--

CREATE TABLE `remuneration_formateur` (
  `idRemuneration` int(11) NOT NULL,
  `idFormateur` int(11) NOT NULL,
  `mois` varchar(7) NOT NULL,
  `heuresValidees` decimal(8,2) NOT NULL DEFAULT 0.00,
  `montantDu` decimal(10,2) NOT NULL DEFAULT 0.00,
  `montantPaye` decimal(10,2) NOT NULL DEFAULT 0.00,
  `statut` varchar(50) NOT NULL DEFAULT 'NON_PAYE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `responsable_pedagogique`
--

CREATE TABLE `responsable_pedagogique` (
  `idUtilisateur` int(11) NOT NULL,
  `matricule` varchar(50) NOT NULL,
  `specialite` varchar(150) DEFAULT NULL,
  `datePriseFonction` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `responsable_pedagogique`
--

INSERT INTO `responsable_pedagogique` (`idUtilisateur`, `matricule`, `specialite`, `datePriseFonction`) VALUES
(3, 'RP001', 'Gestion pédagogique', '2026-01-01');

-- --------------------------------------------------------

--
-- Structure de la table `resultat`
--

CREATE TABLE `resultat` (
  `idResultat` int(11) NOT NULL,
  `idEvaluation` int(11) NOT NULL,
  `idEtudiant` int(11) NOT NULL,
  `note` decimal(5,2) DEFAULT NULL,
  `appreciation` text DEFAULT NULL,
  `datePublication` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `seance`
--

CREATE TABLE `seance` (
  `idSeance` int(11) NOT NULL,
  `idModule` int(11) NOT NULL,
  `idFormateur` int(11) NOT NULL,
  `dateSeance` date NOT NULL,
  `heureDebut` time NOT NULL,
  `heureFin` time NOT NULL,
  `statut` varchar(50) DEFAULT 'PLANIFIEE'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `semestre`
--

CREATE TABLE `semestre` (
  `idSemestre` int(11) NOT NULL,
  `idNiveau` int(11) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `ordre` int(11) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `support`
--

CREATE TABLE `support` (
  `idSupport` int(11) NOT NULL,
  `idModule` int(11) NOT NULL,
  `titre` varchar(200) NOT NULL,
  `type` varchar(50) NOT NULL,
  `fichier` varchar(255) NOT NULL,
  `dateAjout` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `idUtilisateur` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telephone` varchar(30) DEFAULT NULL,
  `nomUtilisateur` varchar(80) NOT NULL,
  `motDePasseHash` varchar(255) NOT NULL,
  `statutCompte` enum('ACTIF','INACTIF','BLOQUE') NOT NULL DEFAULT 'ACTIF',
  `dateCreation` datetime NOT NULL DEFAULT current_timestamp(),
  `derniereConnexion` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--

INSERT INTO `utilisateur` (`idUtilisateur`, `nom`, `prenom`, `email`, `telephone`, `nomUtilisateur`, `motDePasseHash`, `statutCompte`, `dateCreation`, `derniereConnexion`) VALUES
(1, 'Ba', 'Ibrahima', 'admin@gestform.test', '770000004', 'admin.demo', '$2y$12$StG8nsKv9bljIB5ALa7/F./cZM7ETTgP2GJZ8.dWzZ1/.rEREinde', 'ACTIF', '2026-09-15 15:39:40', '2026-09-15 15:51:11'),
(2, 'Sow', 'Fatou', 'directeur@gestform.test', '770000005', 'directeur.demo', '$2y$12$StG8nsKv9bljIB5ALa7/F./cZM7ETTgP2GJZ8.dWzZ1/.rEREinde', 'ACTIF', '2026-09-15 15:39:40', NULL),
(3, 'Diallo', 'Awa', 'rp@gestform.test', '770000001', 'rp.demo', '$2y$12$StG8nsKv9bljIB5ALa7/F./cZM7ETTgP2GJZ8.dWzZ1/.rEREinde', 'ACTIF', '2026-09-15 15:39:40', '2026-09-15 15:51:29'),
(4, 'Ndiaye', 'Moussa', 'formateur@gestform.test', '770000002', 'formateur.demo', '$2y$12$StG8nsKv9bljIB5ALa7/F./cZM7ETTgP2GJZ8.dWzZ1/.rEREinde', 'ACTIF', '2026-09-15 15:39:40', NULL),
(5, 'Diop', 'Mamadou', 'comptable@gestform.test', '770000006', 'comptable.demo', '$2y$12$StG8nsKv9bljIB5ALa7/F./cZM7ETTgP2GJZ8.dWzZ1/.rEREinde', 'ACTIF', '2026-09-15 15:39:40', NULL),
(6, 'Fall', 'Aminata', 'etudiant@gestform.test', '770000003', 'etudiant.demo', '$2y$12$StG8nsKv9bljIB5ALa7/F./cZM7ETTgP2GJZ8.dWzZ1/.rEREinde', 'ACTIF', '2026-09-15 15:39:40', NULL);
(1, 'Ba', 'Ibrahima', 'admin@centre-formation.sn', '770000004', 'admin.demo', '$2y$10$fX40oZytncyf6Y7E3XvE8eYCkylvoBfoVTZAQl9C8Oze1rtiFFSSW', 'ACTIF', '2026-09-15 15:39:40', '2026-09-15 15:51:11'),
(2, 'Sow', 'Fatou', 'directeur@centre-formation.sn', '770000005', 'directeur.demo', '$2y$10$R2h7JE9Li5Kg9f4HjIu1b.nCXu5Q5H9421SpW6p5u2Vg3BatIbEs.', 'ACTIF', '2026-09-15 15:39:40', NULL),
(3, 'Diallo', 'Awa', 'pedagogie@centre-formation.sn', '770000001', 'pedagogie.demo', '$2y$10$fGmn8lIiRKzn7Iw.NZJ3butYItwnolw7VU4w8KOjXQ8eMbZd51IWO', 'ACTIF', '2026-09-15 15:39:40', '2026-09-15 15:51:29'),
(4, 'Ndiaye', 'Moussa', 'formateur@centre-formation.sn', '770000002', 'formateur.demo', '$2y$10$QGXq9kwMxGLKy1bYz1Cme.Y5cKD2Q0DSgJ6qSgMLeggMYmo8KfzGm', 'ACTIF', '2026-09-15 15:39:40', NULL),
(5, 'Diop', 'Mamadou', 'comptable@centre-formation.sn', '770000006', 'comptable.demo', '$2y$10$MFG9eR1nOsvERlcDcUpi/OK.qYKaLXaOqgcRS.IxVc2W5dg/T26Ai', 'ACTIF', '2026-09-15 15:39:40', NULL),
(6, 'Fall', 'Aminata', 'etudiant@centre-formation.sn', '770000003', 'etudiant.demo', '$2y$10$FPj4XNbuzOzYvjLqQtDdu.GRcGFT8/y6W3aQK46FqjLn8W50n70qu', 'ACTIF', '2026-09-15 15:39:40', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `administrateur`
--
ALTER TABLE `administrateur`
  ADD PRIMARY KEY (`idUtilisateur`);

--
-- Index pour la table `comptable`
--
ALTER TABLE `comptable`
  ADD PRIMARY KEY (`idUtilisateur`);

--
-- Index pour la table `depot`
--
ALTER TABLE `depot`
  ADD PRIMARY KEY (`idDepot`),
  ADD KEY `idEvaluation` (`idEvaluation`),
  ADD KEY `idEtudiant` (`idEtudiant`);

--
-- Index pour la table `directeur`
--
ALTER TABLE `directeur`
  ADD PRIMARY KEY (`idUtilisateur`);

--
-- Index pour la table `dossier_etudiant`
--
ALTER TABLE `dossier_etudiant`
  ADD PRIMARY KEY (`idDossier`),
  ADD UNIQUE KEY `uq_dossier_annee` (`idEtudiant`,`anneeScolaire`);

--
-- Index pour la table `etudiant`
--
ALTER TABLE `etudiant`
  ADD PRIMARY KEY (`idUtilisateur`),
  ADD UNIQUE KEY `matricule` (`matricule`);

--
-- Index pour la table `evaluation`
--
ALTER TABLE `evaluation`
  ADD PRIMARY KEY (`idEvaluation`),
  ADD KEY `idModule` (`idModule`);

--
-- Index pour la table `formateur`
--
ALTER TABLE `formateur`
  ADD PRIMARY KEY (`idUtilisateur`),
  ADD UNIQUE KEY `matricule` (`matricule`);

--
-- Index pour la table `formation`
--
ALTER TABLE `formation`
  ADD PRIMARY KEY (`idFormation`);

--
-- Index pour la table `historique`
--
ALTER TABLE `historique`
  ADD PRIMARY KEY (`idHistorique`),
  ADD KEY `utilisateurId` (`utilisateurId`);

--
-- Index pour la table `inscription`
--
ALTER TABLE `inscription`
  ADD PRIMARY KEY (`idInscription`),
  ADD KEY `idDossier` (`idDossier`),
  ADD KEY `idNiveau` (`idNiveau`);

--
-- Index pour la table `module`
--
ALTER TABLE `module`
  ADD PRIMARY KEY (`idModule`),
  ADD KEY `idSemestre` (`idSemestre`),
  ADD KEY `idFormateur` (`idFormateur`);

--
-- Index pour la table `niveau`
--
ALTER TABLE `niveau`
  ADD PRIMARY KEY (`idNiveau`),
  ADD KEY `idFormation` (`idFormation`);

--
-- Index pour la table `paiement`
--
ALTER TABLE `paiement`
  ADD PRIMARY KEY (`idPaiement`),
  ADD KEY `idInscription` (`idInscription`);

--
-- Index pour la table `presence`
--
ALTER TABLE `presence`
  ADD PRIMARY KEY (`idPresence`),
  ADD UNIQUE KEY `uq_presence` (`idSeance`,`idEtudiant`),
  ADD KEY `idEtudiant` (`idEtudiant`);

--
-- Index pour la table `remuneration_formateur`
--
ALTER TABLE `remuneration_formateur`
  ADD PRIMARY KEY (`idRemuneration`),
  ADD UNIQUE KEY `uq_remuneration_mois` (`idFormateur`,`mois`);

--
-- Index pour la table `responsable_pedagogique`
--
ALTER TABLE `responsable_pedagogique`
  ADD PRIMARY KEY (`idUtilisateur`),
  ADD UNIQUE KEY `matricule` (`matricule`);

--
-- Index pour la table `resultat`
--
ALTER TABLE `resultat`
  ADD PRIMARY KEY (`idResultat`),
  ADD KEY `idEvaluation` (`idEvaluation`),
  ADD KEY `idEtudiant` (`idEtudiant`);

--
-- Index pour la table `seance`
--
ALTER TABLE `seance`
  ADD PRIMARY KEY (`idSeance`),
  ADD KEY `idModule` (`idModule`),
  ADD KEY `idFormateur` (`idFormateur`);

--
-- Index pour la table `semestre`
--
ALTER TABLE `semestre`
  ADD PRIMARY KEY (`idSemestre`),
  ADD KEY `idNiveau` (`idNiveau`);

--
-- Index pour la table `support`
--
ALTER TABLE `support`
  ADD PRIMARY KEY (`idSupport`),
  ADD KEY `idModule` (`idModule`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`idUtilisateur`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `nomUtilisateur` (`nomUtilisateur`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `depot`
--
ALTER TABLE `depot`
  MODIFY `idDepot` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `dossier_etudiant`
--
ALTER TABLE `dossier_etudiant`
  MODIFY `idDossier` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `evaluation`
--
ALTER TABLE `evaluation`
  MODIFY `idEvaluation` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `formation`
--
ALTER TABLE `formation`
  MODIFY `idFormation` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `historique`
--
ALTER TABLE `historique`
  MODIFY `idHistorique` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `inscription`
--
ALTER TABLE `inscription`
  MODIFY `idInscription` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `module`
--
ALTER TABLE `module`
  MODIFY `idModule` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `niveau`
--
ALTER TABLE `niveau`
  MODIFY `idNiveau` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `paiement`
--
ALTER TABLE `paiement`
  MODIFY `idPaiement` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `presence`
--
ALTER TABLE `presence`
  MODIFY `idPresence` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `remuneration_formateur`
--
ALTER TABLE `remuneration_formateur`
  MODIFY `idRemuneration` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `resultat`
--
ALTER TABLE `resultat`
  MODIFY `idResultat` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `seance`
--
ALTER TABLE `seance`
  MODIFY `idSeance` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `semestre`
--
ALTER TABLE `semestre`
  MODIFY `idSemestre` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `support`
--
ALTER TABLE `support`
  MODIFY `idSupport` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `idUtilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `administrateur`
--
ALTER TABLE `administrateur`
  ADD CONSTRAINT `administrateur_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`idUtilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `comptable`
--
ALTER TABLE `comptable`
  ADD CONSTRAINT `comptable_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`idUtilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `depot`
--
ALTER TABLE `depot`
  ADD CONSTRAINT `depot_ibfk_1` FOREIGN KEY (`idEvaluation`) REFERENCES `evaluation` (`idEvaluation`) ON DELETE CASCADE,
  ADD CONSTRAINT `depot_ibfk_2` FOREIGN KEY (`idEtudiant`) REFERENCES `etudiant` (`idUtilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `directeur`
--
ALTER TABLE `directeur`
  ADD CONSTRAINT `directeur_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`idUtilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `dossier_etudiant`
--
ALTER TABLE `dossier_etudiant`
  ADD CONSTRAINT `dossier_etudiant_ibfk_1` FOREIGN KEY (`idEtudiant`) REFERENCES `etudiant` (`idUtilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `etudiant`
--
ALTER TABLE `etudiant`
  ADD CONSTRAINT `etudiant_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`idUtilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `evaluation`
--
ALTER TABLE `evaluation`
  ADD CONSTRAINT `evaluation_ibfk_1` FOREIGN KEY (`idModule`) REFERENCES `module` (`idModule`) ON DELETE CASCADE;

--
-- Contraintes pour la table `formateur`
--
ALTER TABLE `formateur`
  ADD CONSTRAINT `formateur_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`idUtilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `historique`
--
ALTER TABLE `historique`
  ADD CONSTRAINT `historique_ibfk_1` FOREIGN KEY (`utilisateurId`) REFERENCES `utilisateur` (`idUtilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `inscription`
--
ALTER TABLE `inscription`
  ADD CONSTRAINT `inscription_ibfk_1` FOREIGN KEY (`idDossier`) REFERENCES `dossier_etudiant` (`idDossier`) ON DELETE CASCADE,
  ADD CONSTRAINT `inscription_ibfk_2` FOREIGN KEY (`idNiveau`) REFERENCES `niveau` (`idNiveau`);

--
-- Contraintes pour la table `module`
--
ALTER TABLE `module`
  ADD CONSTRAINT `module_ibfk_1` FOREIGN KEY (`idSemestre`) REFERENCES `semestre` (`idSemestre`) ON DELETE CASCADE,
  ADD CONSTRAINT `module_ibfk_2` FOREIGN KEY (`idFormateur`) REFERENCES `formateur` (`idUtilisateur`) ON DELETE SET NULL;

--
-- Contraintes pour la table `niveau`
--
ALTER TABLE `niveau`
  ADD CONSTRAINT `niveau_ibfk_1` FOREIGN KEY (`idFormation`) REFERENCES `formation` (`idFormation`) ON DELETE CASCADE;

--
-- Contraintes pour la table `paiement`
--
ALTER TABLE `paiement`
  ADD CONSTRAINT `paiement_ibfk_1` FOREIGN KEY (`idInscription`) REFERENCES `inscription` (`idInscription`) ON DELETE CASCADE;

--
-- Contraintes pour la table `presence`
--
ALTER TABLE `presence`
  ADD CONSTRAINT `presence_ibfk_1` FOREIGN KEY (`idSeance`) REFERENCES `seance` (`idSeance`) ON DELETE CASCADE,
  ADD CONSTRAINT `presence_ibfk_2` FOREIGN KEY (`idEtudiant`) REFERENCES `etudiant` (`idUtilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `remuneration_formateur`
--
ALTER TABLE `remuneration_formateur`
  ADD CONSTRAINT `remuneration_formateur_ibfk_1` FOREIGN KEY (`idFormateur`) REFERENCES `formateur` (`idUtilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `responsable_pedagogique`
--
ALTER TABLE `responsable_pedagogique`
  ADD CONSTRAINT `responsable_pedagogique_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`idUtilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `resultat`
--
ALTER TABLE `resultat`
  ADD CONSTRAINT `resultat_ibfk_1` FOREIGN KEY (`idEvaluation`) REFERENCES `evaluation` (`idEvaluation`) ON DELETE CASCADE,
  ADD CONSTRAINT `resultat_ibfk_2` FOREIGN KEY (`idEtudiant`) REFERENCES `etudiant` (`idUtilisateur`) ON DELETE CASCADE;

--
-- Contraintes pour la table `seance`
--
ALTER TABLE `seance`
  ADD CONSTRAINT `seance_ibfk_1` FOREIGN KEY (`idModule`) REFERENCES `module` (`idModule`) ON DELETE CASCADE,
  ADD CONSTRAINT `seance_ibfk_2` FOREIGN KEY (`idFormateur`) REFERENCES `formateur` (`idUtilisateur`);

--
-- Contraintes pour la table `semestre`
--
ALTER TABLE `semestre`
  ADD CONSTRAINT `semestre_ibfk_1` FOREIGN KEY (`idNiveau`) REFERENCES `niveau` (`idNiveau`) ON DELETE CASCADE;

--
-- Contraintes pour la table `support`
--
ALTER TABLE `support`
  ADD CONSTRAINT `support_ibfk_1` FOREIGN KEY (`idModule`) REFERENCES `module` (`idModule`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
