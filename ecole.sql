-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3307
-- Généré le : mer. 01 oct. 2025 à 13:57
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
-- Base de données : `ecole`
--

-- --------------------------------------------------------

--
-- Structure de la table `absences`
--

CREATE TABLE `absences` (
  `id` int(11) NOT NULL,
  `eleve_id` int(11) DEFAULT NULL,
  `date_absence` date DEFAULT NULL,
  `justifiee` tinyint(1) DEFAULT 0,
  `matiere_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `attribution_cours`
--

CREATE TABLE `attribution_cours` (
  `id` int(11) NOT NULL,
  `enseignant_id` int(11) NOT NULL,
  `matiere_id` int(11) NOT NULL,
  `date_attribution` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `annee` int(11) NOT NULL DEFAULT 1,
  `niveau` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `classes`
--

INSERT INTO `classes` (`id`, `nom`, `annee`, `niveau`) VALUES
(2, '2ere Annee', 2, '2'),
(3, '3ere Annee', 3, '3'),
(4, '4ere Annee', 4, '4'),
(5, '5ere Annee', 5, '5'),
(6, '6ere Annee', 6, '6'),
(7, '7ere Annee', 7, '7'),
(8, '8ere Annee', 8, '8'),
(9, '9ere Annee', 9, '9'),
(11, '1 annnée', 1, '1');

-- --------------------------------------------------------

--
-- Structure de la table `eleves`
--

CREATE TABLE `eleves` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `photo` varchar(100) NOT NULL,
  `created_at` datetime NOT NULL,
  `date_naissance` date DEFAULT NULL,
  `classe` varchar(100) DEFAULT NULL,
  `genre` enum('M','F') DEFAULT NULL,
  `classe_id` int(11) DEFAULT NULL,
  `matricule` varchar(50) DEFAULT NULL,
  `date_inscription` date DEFAULT NULL,
  `statut` enum('actif','inactif') DEFAULT 'actif',
  `lieu_naissance` varchar(100) DEFAULT NULL,
  `nationalite` varchar(50) DEFAULT NULL,
  `nom_pere` varchar(50) DEFAULT NULL,
  `nom_mere` varchar(50) DEFAULT NULL,
  `tel_parent` varchar(20) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL COMMENT 'Adresse email de l''élève',
  `remarques` text DEFAULT NULL COMMENT 'Remarques et observations sur l''élève'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `eleves`
--

INSERT INTO `eleves` (`id`, `nom`, `prenom`, `photo`, `created_at`, `date_naissance`, `classe`, `genre`, `classe_id`, `matricule`, `date_inscription`, `statut`, `lieu_naissance`, `nationalite`, `nom_pere`, `nom_mere`, `tel_parent`, `adresse`, `telephone`, `email`, `remarques`) VALUES
(1, 'BERBB', 'RRRR', 'uploads/eleves/68907acb9b08b_DHLO8960.JPG', '2025-07-28 14:02:02', '2017-06-12', NULL, NULL, 7, 'vvvtjftf-', NULL, 'inactif', 'bko', 'ivoirien', 'kkkk', 'hhh', '54566766', 'lafiabougou', '66666666', 'kadidiacoulibaly977@gmail.com', 'impoli'),
(4, 'berthe', 'fousseyni', 'uploads/eleves/68907ab3cb201_IMG_1233 - Copie.JPG', '2025-07-29 12:18:17', '2018-02-13', NULL, NULL, 4, 'ml4E56565', NULL, 'actif', 'bko', 'ivoirien', 'kkkk', 'hhh', '54566766', 'Kalaban-coura lafiabougou', '66666666', 'kadidiacoulibaly977@gmail.com', 'gytgytgy'),
(5, 'coulibaly', 'fata', 'uploads/eleves/68907a7c6997d_AJWN7622.JPG', '2025-07-29 13:07:55', '2025-07-11', NULL, NULL, 2, 'ml5456', NULL, 'actif', 'france', 'francaise', 'dsdf', 'lj', '76778888', 'aci', '56667778', 'ami@gmail.com', 'tyikyf'),
(6, 'diarra', 'kuku', 'uploads/eleves/6890792974dba_ADPS0812.JPG', '2025-07-29 15:24:25', '2018-06-11', NULL, NULL, 8, 'mjjj33', NULL, 'actif', 'burkina', 'burkinabé', 'moussa', 'mah', '56667778', 'aci', '56667778', 'ami@gmail.com', 'TGFG'),
(8, 'cisse', 'aicha', 'uploads/eleves/689099d1f420b_ABII6153 - Copie.JPG', '2025-08-04 12:29:18', '2018-06-11', NULL, NULL, 6, 'mlsses3T3', NULL, 'actif', 'bko', 'malienne', 'oumar', 'gogo', '457676878', 'aci', '56667778', 'ami@gmail.com', 'dcft,f'),
(9, 'traore', 'oumou', 'uploads/eleves/6890bdadcc0e8_BTER6521.JPG', '2025-08-04 14:56:35', '2025-08-04', NULL, NULL, 11, 'mlddg334', NULL, 'actif', 'burkina', 'burkinabé', 'pipi', 'dudu', '78784686', 'lafiabougou', '56667778', 'ami@gmail.com', 'rydyjfj'),
(10, 'cisse', 'issa', 'uploads/eleves/6890bca1f1295_ABII6153.JPG', '2025-08-04 14:57:24', '2023-02-04', NULL, NULL, 11, 'mldh44556', NULL, 'actif', 'bko', 'malienne', 'papa', 'didi', '57898889', 'kalaban', '54566766', 'kkkk@gmail.com', 'dtfrtfhd'),
(11, 'keita', 'biba', 'uploads/eleves/6890be54dfdc1_EFGP4797.JPG', '2025-08-04 15:04:45', '2021-06-14', NULL, NULL, 2, 'mmldfx44556', NULL, 'actif', 'togo', 'togolaise', 'bouba', 'bibi', '66666666', 'kalaban', '54566766', 'kkkk@gmail.com', 'eses'),
(12, 'Traore', 'fakoro', 'uploads/eleves/6890bf0f1deb1_DZMO6175.JPG', '2025-08-04 15:07:29', '2021-06-07', NULL, NULL, 4, 'mlZFS3444', NULL, 'actif', 'senegal', 'senegalais', 'boubou', 'tou', '99999999', 'titibougou', '467777878', 'ami@gmail.com', 'tftyf'),
(13, 'Sall', 'kounandy', 'uploads/eleves/6890bf94e95ad_ABOW2123.JPG', '2025-08-04 15:10:17', '2014-06-02', NULL, NULL, 8, 'mlessd34345', NULL, 'actif', 'senegal', 'senegalaise', 'tata', 'moba', '57898889', '300 logement', '45576778', 'sall@gmail.com', 'vcjjcg'),
(14, 'sidibe', 'rita', 'uploads/eleves/6890c04933073_CDDM6098.JPG', '2025-08-04 15:12:45', '2015-01-26', NULL, NULL, 7, 'mldsd4555', NULL, 'actif', 'bko', 'malienne', 'alou', 'kadi', '66577677', 'kati', '565667676', 'sidibe@gmail.com', 'dxdxd'),
(15, 'Tembely', 'assassita', 'uploads/eleves/68bfea0b9abf3_ABOW2123.JPG', '2025-09-09 09:45:44', '2018-06-13', NULL, NULL, 3, 'ml 757978', NULL, 'actif', 'bko', 'malienne', 'issa', 'bintou', '78784686', 'Kalaban-coura', '78784686', 'assa@gmail.com', 'yu');

-- --------------------------------------------------------

--
-- Structure de la table `emplois_temps`
--

CREATE TABLE `emplois_temps` (
  `id` int(11) NOT NULL,
  `classe_id` int(11) DEFAULT NULL,
  `jour` varchar(20) DEFAULT NULL,
  `heure_debut` time DEFAULT NULL,
  `heure_fin` time DEFAULT NULL,
  `matiere_id` int(11) DEFAULT NULL,
  `enseignant_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `enseignants`
--

CREATE TABLE `enseignants` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `statut` varchar(50) DEFAULT NULL,
  `profession` varchar(100) DEFAULT NULL,
  `dernier_diplome` varchar(100) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `enseignants`
--

INSERT INTO `enseignants` (`id`, `nom`, `prenom`, `email`, `ville`, `statut`, `profession`, `dernier_diplome`, `telephone`) VALUES
(6, 'coulibaly', 'kadi', 'kadidiacoulibaly977@gmail.com', 'Bamako', 'regulier', 'professeur', 'master', '66666666'),
(7, 'Traore', 'issou', 'issou@gmail.com', 'Bamako', 'regulier', 'professeur', 'doctorat', '77876564'),
(8, 'toure', 'aizidini', 'toure@gmail.com', 'bamako', 'Actif', 'professeur', 'master', '655756778'),
(9, 'Timbo', 'Ali', 'ali@gmail.com', 'Bamako', 'Actif', 'professeur', 'doctorat', '66666666'),
(10, 'Traore', 'Salim', 'sall@gmail.com', 'bamako', 'Actif', 'professeur', 'doctorat', '6667777'),
(11, 'Kone', 'Ibrahima', 'ibrahima@gmail.com', 'bamako', 'Actif', 'professeur', 'master', '6667777'),
(12, 'cisse', 'Oumou', 'Oumy@gmail.com', 'bamako', 'Actif', 'professeur', 'master', '78784686'),
(13, 'Sogoba', 'moussa', 'moussa@gmail.com', 'bamako', 'Actif', 'professeur', 'doctorat', '56667778');

-- --------------------------------------------------------

--
-- Structure de la table `matieres`
--

CREATE TABLE `matieres` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `coefficient` int(11) DEFAULT NULL,
  `niveau` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `matieres`
--

INSERT INTO `matieres` (`id`, `nom`, `coefficient`, `niveau`) VALUES
(2, 'anglais', 2, '9'),
(3, 'francais', 3, '10'),
(4, 'mathematique', 3, '7'),
(5, 'ecm', 2, '8'),
(6, 'Histoire', 2, '8'),
(7, 'physique', 2, '8'),
(8, 'informatique', 2, '7'),
(9, 'biologie', 3, '9'),
(10, 'economie familial', 1, '6');

-- --------------------------------------------------------

--
-- Structure de la table `notes`
--

CREATE TABLE `notes` (
  `id` int(11) NOT NULL,
  `eleve_id` int(11) DEFAULT NULL,
  `matiere_id` int(11) DEFAULT NULL,
  `periode` varchar(50) DEFAULT NULL,
  `note` decimal(5,2) DEFAULT NULL,
  `note_composition` decimal(4,2) DEFAULT NULL,
  `commentaire` varchar(100) NOT NULL,
  `note_classe` decimal(4,2) DEFAULT NULL COMMENT 'Note de classe sur 20',
  `annee_scolaire` varchar(20) NOT NULL DEFAULT '2023-2024'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `notes`
--

INSERT INTO `notes` (`id`, `eleve_id`, `matiere_id`, `periode`, `note`, `note_composition`, `commentaire`, `note_classe`, `annee_scolaire`) VALUES
(1, 4, 2, 'trimestre1', 18.00, 10.00, '', 11.00, '2024-2025'),
(2, 1, 3, 'trimestre1', 15.00, 14.00, '', 15.00, '2024-2025'),
(3, 5, 2, 'trimestre1', 10.00, 18.00, 'passable', 11.00, '2024-2025'),
(4, 6, 4, 'trimestre1', 15.00, 17.00, 'bien', 13.00, '2024-2025'),
(8, 4, 5, 'trimestre2', 19.00, 12.00, '', 18.00, '2024-2025'),
(10, 5, 5, 'trimestre2', 17.00, 13.00, '', 15.50, '2024-2025'),
(11, 6, 5, 'trimestre2', 18.00, 17.50, 'bien', 16.00, '2024-2025'),
(13, 1, 5, 'trimestre2', 15.00, 19.00, 'FG', 17.75, '2024-2025'),
(14, 4, 4, 'trimestre1', 9.00, 12.00, 'Assez-bien', 15.00, '2024-2025'),
(15, 1, 2, 'trimestre1', 9.00, 12.00, 'passable', 15.00, '2024-2025'),
(16, 10, 2, 'composition', 13.00, 6.00, 'Assez-bien', 6.00, '2024-2025'),
(17, 4, 2, 'composition', 6.00, 6.00, 'aaa', 6.00, '2024-2025'),
(18, 10, 5, 'trimestre1', 16.00, 13.00, 'BVC', 13.00, '2024-2025'),
(19, 6, 2, '1er Trimestre', 0.00, 17.00, '', 12.00, '2024-2025'),
(20, 6, 2, '1er Trimestre', 13.00, 17.00, 'FYYY', 12.00, '2024-2025'),
(21, 10, 5, '1er Trimestre', 13.50, 13.00, 'ASSEZ BIEN', 14.00, '2024-2025'),
(22, 6, 2, '2ème Trimestre', 16.00, 16.00, 'BIEN', 16.00, '2024-2025'),
(23, 10, 2, '1er Trimestre', 15.00, 15.00, 'BIEN', 15.00, '2025-2026'),
(24, 6, 2, '1er Trimestre', 20.00, 12.00, '', 12.00, '2025-2026');

-- --------------------------------------------------------

--
-- Structure de la table `paiements`
--

CREATE TABLE `paiements` (
  `id` int(11) NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `date_paiement` date NOT NULL,
  `mois_couvert` varchar(20) NOT NULL,
  `statut` enum('payé','en attente','annulé') DEFAULT 'payé',
  `id_eleve` int(11) DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `paiements`
--

INSERT INTO `paiements` (`id`, `montant`, `date_paiement`, `mois_couvert`, `statut`, `id_eleve`, `created_by`, `created_at`, `updated_at`) VALUES
(2, 100000.00, '2025-08-04', '12/09.2025', 'payé', 1, 5, '2025-08-21 09:03:02', '2025-08-21 19:59:47');

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nom_role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id`, `nom_role`) VALUES
(1, 'administrateur'),
(3, 'enseignant'),
(2, 'secrétaire');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `mot_de_passe` varchar(255) DEFAULT NULL,
  `rôle` enum('admin','enseignant','eleve') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `email`, `mot_de_passe`, `rôle`) VALUES
(2, 'Kadi', 'kadicoulibaly977@gmail.com', '12345', 'admin'),
(3, 'coulibaly kadi', 'tembelyc@gmail.com', '$2y$10$e2zzdpLRErxwD.8CIJa0c.EMy.qsOLNu5mxMs77qfpxwA.baC4l1K', 'admin'),
(4, 'coulibaly', 'kadidia@gmail.com', '$2y$10$Yi1yo1.JK9mjizrmUwn/iO1SBPnyoCycv.TmfgaaH5JbODhS6zQOu', 'admin'),
(5, 'mama coulibaly', 'mama@gmail.com', '$2y$10$EaItGCs1yYpWvWYSvwOrQuF6iLB3aXKNwjIQARZ1M8G1Vh1wL7JcS', 'admin');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `absences`
--
ALTER TABLE `absences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `eleve_id` (`eleve_id`),
  ADD KEY `matiere_id` (`matiere_id`);

--
-- Index pour la table `attribution_cours`
--
ALTER TABLE `attribution_cours`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `enseignant_id` (`enseignant_id`,`matiere_id`),
  ADD KEY `matiere_id` (`matiere_id`);

--
-- Index pour la table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `eleves`
--
ALTER TABLE `eleves`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `matricule` (`matricule`),
  ADD KEY `classe_id` (`classe_id`);

--
-- Index pour la table `emplois_temps`
--
ALTER TABLE `emplois_temps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `classe_id` (`classe_id`),
  ADD KEY `matiere_id` (`matiere_id`),
  ADD KEY `enseignant_id` (`enseignant_id`);

--
-- Index pour la table `enseignants`
--
ALTER TABLE `enseignants`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `matieres`
--
ALTER TABLE `matieres`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `eleve_id` (`eleve_id`),
  ADD KEY `matiere_id` (`matiere_id`);

--
-- Index pour la table `paiements`
--
ALTER TABLE `paiements`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom_role` (`nom_role`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `absences`
--
ALTER TABLE `absences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `attribution_cours`
--
ALTER TABLE `attribution_cours`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `eleves`
--
ALTER TABLE `eleves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `emplois_temps`
--
ALTER TABLE `emplois_temps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `enseignants`
--
ALTER TABLE `enseignants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `matieres`
--
ALTER TABLE `matieres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT pour la table `paiements`
--
ALTER TABLE `paiements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `absences`
--
ALTER TABLE `absences`
  ADD CONSTRAINT `absences_ibfk_1` FOREIGN KEY (`eleve_id`) REFERENCES `eleves` (`id`),
  ADD CONSTRAINT `absences_ibfk_2` FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`);

--
-- Contraintes pour la table `attribution_cours`
--
ALTER TABLE `attribution_cours`
  ADD CONSTRAINT `attribution_cours_ibfk_1` FOREIGN KEY (`enseignant_id`) REFERENCES `enseignants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attribution_cours_ibfk_2` FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `eleves`
--
ALTER TABLE `eleves`
  ADD CONSTRAINT `eleves_ibfk_1` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`);

--
-- Contraintes pour la table `emplois_temps`
--
ALTER TABLE `emplois_temps`
  ADD CONSTRAINT `emplois_temps_ibfk_1` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `emplois_temps_ibfk_2` FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`),
  ADD CONSTRAINT `emplois_temps_ibfk_3` FOREIGN KEY (`enseignant_id`) REFERENCES `enseignants` (`id`);

--
-- Contraintes pour la table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`eleve_id`) REFERENCES `eleves` (`id`),
  ADD CONSTRAINT `notes_ibfk_2` FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
