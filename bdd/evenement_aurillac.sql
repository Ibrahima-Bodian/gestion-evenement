-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 09 jan. 2025 à 21:12
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
-- Base de données : `evenement_aurillac`
--

-- --------------------------------------------------------

--
-- Structure de la table `annonceur`
--

CREATE TABLE `annonceur` (
  `id_annonceur` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `id_categorie_annonceur` int(11) NOT NULL,
  `id_sous_categorie_annonceur` int(11) DEFAULT NULL,
  `role_annonceur` enum('annonceur','admin') NOT NULL DEFAULT 'annonceur',
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp(),
  `statut` varchar(20) NOT NULL DEFAULT 'actif',
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `annonceur`
--

INSERT INTO `annonceur` (`id_annonceur`, `nom`, `email`, `mot_de_passe`, `telephone`, `id_categorie_annonceur`, `id_sous_categorie_annonceur`, `role_annonceur`, `date_creation`, `statut`, `reset_token`, `reset_expiry`) VALUES
(9, 'IBRA Admin', 'ibrahima97bodian@gmail.com', '$2y$10$bpagIp6NXv.uP/OQ.lQK7.hCdXoFlVtueNaKlStV5SzNrtE.OZHUC', '0766604616', 1, NULL, 'admin', '2025-01-01 11:56:38', 'actif', '1888a098637c55f8c427577ce58a28423bc0cefaba1942ece05b592487073ea4', '2025-01-08 04:47:44'),
(11, 'Ibrahhhh bbb', 'ibrahima97fbodian@gmail.com', '$2y$10$xMDeBgrNhnOcDmzM3oIV2..639Ehknxrj/c/3Xx2DBD8MlbytAYJW', '45678765', 2, NULL, 'annonceur', '2025-01-07 23:17:02', 'actif', NULL, NULL),
(12, 'A B', 'ab@gmail.com', '$2y$10$D9eN1v5T.P3oACmsztKDWOgETAVxg8rQ/dgVkej9.yPhqA3avbRAW', '1234567', 2, NULL, 'annonceur', '2025-01-08 02:05:39', 'actif', NULL, NULL),
(13, 'IB BO', 'ibbo@gmail.com', '$2y$10$GDzRyLHZpHGSuNWoT4t2kOHnWqhIPRAfXOTbUfYqNj0DpAm2KEOmC', '765432111111', 2, NULL, 'annonceur', '2025-01-08 09:44:14', 'actif', NULL, NULL),
(14, 'Ibra', 'i.bodian879@gmail.com', '$2y$10$opNYy1qjcSU.Y3TWIahW5eZ8GTsH.oVpZ.GYcDx46wuRl4bEGMFDm', '567898765', 2, NULL, 'annonceur', '2025-01-08 16:40:52', 'actif', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `categorieannonceur`
--

CREATE TABLE `categorieannonceur` (
  `id_categorie_annonceur` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categorieannonceur`
--

INSERT INTO `categorieannonceur` (`id_categorie_annonceur`, `nom`) VALUES
(1, 'Administrateur'),
(3, 'Association'),
(2, 'Entreprise'),
(4, 'Particulier');

-- --------------------------------------------------------

--
-- Structure de la table `categorieevenement`
--

CREATE TABLE `categorieevenement` (
  `id_categorie_evenement` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categorieevenement`
--

INSERT INTO `categorieevenement` (`id_categorie_evenement`, `nom`) VALUES
(3, 'Compétition'),
(1, 'Foire'),
(2, 'Soirée');

-- --------------------------------------------------------

--
-- Structure de la table `evenement`
--

CREATE TABLE `evenement` (
  `id_evenement` int(11) NOT NULL,
  `id_annonceur` int(11) NOT NULL,
  `id_categorie_evenement` int(11) NOT NULL,
  `id_sous_categorie_evenement` int(11) DEFAULT NULL,
  `nom` varchar(150) NOT NULL,
  `description` text NOT NULL,
  `date_evenement` date NOT NULL,
  `heure` time DEFAULT NULL,
  `lieu` varchar(200) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `statut` varchar(20) DEFAULT 'actif',
  `compteur_consultations` int(11) DEFAULT 0,
  `date_creation` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `evenement`
--

INSERT INTO `evenement` (`id_evenement`, `id_annonceur`, `id_categorie_evenement`, `id_sous_categorie_evenement`, `nom`, `description`, `date_evenement`, `heure`, `lieu`, `image`, `statut`, `compteur_consultations`, `date_creation`) VALUES
(23, 13, 2, 1, 'DDDD', 'DD', '2025-01-18', '12:12:00', 'D', 'event_677e498537028.jpg', 'actif', 1, '2025-01-08 09:46:45'),
(24, 12, 1, 1, 'Foire du nouvelle année', 'Venez on va biiiien bouffer du thiéb', '2025-01-10', '10:00:00', 'Square', 'event_677e63e913b52.jpg', 'actif', 1, '2025-01-08 11:39:21'),
(28, 13, 3, 6, 'DDF', 'DD', '2025-01-16', '22:39:00', 'AURILAC', 'event_677ea96389fe1.jpg', 'actif', 2, '2025-01-08 16:35:47'),
(32, 14, 2, 4, 'soiré', 'ddddd', '2025-01-16', '12:00:00', 'france', 'event_677eba80b9c5a.jpg', 'actif', 2, '2025-01-08 17:48:48');

-- --------------------------------------------------------

--
-- Structure de la table `imagesevenement`
--

CREATE TABLE `imagesevenement` (
  `id_image` int(11) NOT NULL,
  `id_evenement` int(11) NOT NULL,
  `nom_fichier` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `souscategorieannonceur`
--

CREATE TABLE `souscategorieannonceur` (
  `id_sous_categorie_annonceur` int(11) NOT NULL,
  `id_categorie_annonceur` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `souscategorieannonceur`
--

INSERT INTO `souscategorieannonceur` (`id_sous_categorie_annonceur`, `id_categorie_annonceur`, `nom`) VALUES
(1, 1, 'Mairie'),
(2, 1, 'CABA'),
(3, 2, 'Informatique'),
(4, 2, 'Production'),
(5, 3, 'Sportive'),
(6, 3, 'Culturelle'),
(7, 4, 'Étudiant');

-- --------------------------------------------------------

--
-- Structure de la table `souscategorieevenement`
--

CREATE TABLE `souscategorieevenement` (
  `id_sous_categorie_evenement` int(11) NOT NULL,
  `id_categorie_evenement` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `souscategorieevenement`
--

INSERT INTO `souscategorieevenement` (`id_sous_categorie_evenement`, `id_categorie_evenement`, `nom`) VALUES
(1, 1, 'Animation'),
(2, 1, 'Artisanat'),
(3, 2, 'Danse'),
(4, 2, 'Jeu'),
(5, 3, 'Tournoi Foot'),
(6, 3, 'Course');

-- --------------------------------------------------------

--
-- Structure de la table `statistiques`
--

CREATE TABLE `statistiques` (
  `id_statistique` int(11) NOT NULL,
  `id_evenement` int(11) NOT NULL,
  `date_consultation` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_annonceur` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `statistiques`
--

INSERT INTO `statistiques` (`id_statistique`, `id_evenement`, `date_consultation`, `id_annonceur`) VALUES
(23, 28, '2025-01-08 16:36:12', 13),
(24, 24, '2025-01-08 16:46:33', 14),
(26, 28, '2025-01-08 16:46:41', 14),
(28, 23, '2025-01-08 16:46:51', 14),
(30, 32, '2025-01-08 17:49:14', 14),
(31, 32, '2025-01-08 17:55:09', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `annonceur`
--
ALTER TABLE `annonceur`
  ADD PRIMARY KEY (`id_annonceur`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `id_categorie_annonceur` (`id_categorie_annonceur`),
  ADD KEY `id_sous_categorie_annonceur` (`id_sous_categorie_annonceur`),
  ADD KEY `idx_annonceur_email` (`email`);

--
-- Index pour la table `categorieannonceur`
--
ALTER TABLE `categorieannonceur`
  ADD PRIMARY KEY (`id_categorie_annonceur`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Index pour la table `categorieevenement`
--
ALTER TABLE `categorieevenement`
  ADD PRIMARY KEY (`id_categorie_evenement`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Index pour la table `evenement`
--
ALTER TABLE `evenement`
  ADD PRIMARY KEY (`id_evenement`),
  ADD KEY `id_annonceur` (`id_annonceur`),
  ADD KEY `id_sous_categorie_evenement` (`id_sous_categorie_evenement`),
  ADD KEY `idx_evenement_date` (`date_evenement`),
  ADD KEY `idx_evenement_categorie` (`id_categorie_evenement`);

--
-- Index pour la table `imagesevenement`
--
ALTER TABLE `imagesevenement`
  ADD PRIMARY KEY (`id_image`),
  ADD KEY `id_evenement` (`id_evenement`);

--
-- Index pour la table `souscategorieannonceur`
--
ALTER TABLE `souscategorieannonceur`
  ADD PRIMARY KEY (`id_sous_categorie_annonceur`),
  ADD KEY `id_categorie_annonceur` (`id_categorie_annonceur`);

--
-- Index pour la table `souscategorieevenement`
--
ALTER TABLE `souscategorieevenement`
  ADD PRIMARY KEY (`id_sous_categorie_evenement`),
  ADD KEY `id_categorie_evenement` (`id_categorie_evenement`);

--
-- Index pour la table `statistiques`
--
ALTER TABLE `statistiques`
  ADD PRIMARY KEY (`id_statistique`),
  ADD KEY `id_evenement` (`id_evenement`),
  ADD KEY `id_annonceur` (`id_annonceur`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `annonceur`
--
ALTER TABLE `annonceur`
  MODIFY `id_annonceur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT pour la table `categorieannonceur`
--
ALTER TABLE `categorieannonceur`
  MODIFY `id_categorie_annonceur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `categorieevenement`
--
ALTER TABLE `categorieevenement`
  MODIFY `id_categorie_evenement` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `evenement`
--
ALTER TABLE `evenement`
  MODIFY `id_evenement` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT pour la table `imagesevenement`
--
ALTER TABLE `imagesevenement`
  MODIFY `id_image` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `souscategorieannonceur`
--
ALTER TABLE `souscategorieannonceur`
  MODIFY `id_sous_categorie_annonceur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `souscategorieevenement`
--
ALTER TABLE `souscategorieevenement`
  MODIFY `id_sous_categorie_evenement` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `statistiques`
--
ALTER TABLE `statistiques`
  MODIFY `id_statistique` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `annonceur`
--
ALTER TABLE `annonceur`
  ADD CONSTRAINT `annonceur_ibfk_1` FOREIGN KEY (`id_categorie_annonceur`) REFERENCES `categorieannonceur` (`id_categorie_annonceur`) ON UPDATE CASCADE,
  ADD CONSTRAINT `annonceur_ibfk_2` FOREIGN KEY (`id_sous_categorie_annonceur`) REFERENCES `sous_categorie_evenement` (`id_sous_categorie_annonceur`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `evenement`
--
ALTER TABLE `evenement`
  ADD CONSTRAINT `evenement_ibfk_1` FOREIGN KEY (`id_annonceur`) REFERENCES `annonceur` (`id_annonceur`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `evenement_ibfk_2` FOREIGN KEY (`id_categorie_evenement`) REFERENCES `categorieevenement` (`id_categorie_evenement`) ON UPDATE CASCADE,
  ADD CONSTRAINT `evenement_ibfk_3` FOREIGN KEY (`id_sous_categorie_evenement`) REFERENCES `souscategorieevenement` (`id_sous_categorie_evenement`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `imagesevenement`
--
ALTER TABLE `imagesevenement`
  ADD CONSTRAINT `imagesevenement_ibfk_1` FOREIGN KEY (`id_evenement`) REFERENCES `evenement` (`id_evenement`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `souscategorieannonceur`
--
ALTER TABLE `souscategorieannonceur`
  ADD CONSTRAINT `souscategorieannonceur_ibfk_1` FOREIGN KEY (`id_categorie_annonceur`) REFERENCES `categorieannonceur` (`id_categorie_annonceur`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `souscategorieevenement`
--
ALTER TABLE `souscategorieevenement`
  ADD CONSTRAINT `souscategorieevenement_ibfk_1` FOREIGN KEY (`id_categorie_evenement`) REFERENCES `categorieevenement` (`id_categorie_evenement`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `statistiques`
--
ALTER TABLE `statistiques`
  ADD CONSTRAINT `statistiques_ibfk_1` FOREIGN KEY (`id_evenement`) REFERENCES `evenement` (`id_evenement`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `statistiques_ibfk_2` FOREIGN KEY (`id_annonceur`) REFERENCES `annonceur` (`id_annonceur`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
