-- Script PostgreSQL converti depuis MySQL
-- Adaptez si nécessaire (noms de tables, etc.)

-- ============================
-- 1) CREATION DE LA DATABASE (optionnel)
-- ============================
-- CREATE DATABASE evenement_aurillac;
-- \c evenement_aurillac;  -- Se connecter à la base

-- ============================
-- SUPPRESSION des tables (si besoin)
-- ============================
DROP TABLE IF EXISTS imagesevenement CASCADE;
DROP TABLE IF EXISTS statistiques CASCADE;
DROP TABLE IF EXISTS evenement CASCADE;
DROP TABLE IF EXISTS souscategorieevenement CASCADE;
DROP TABLE IF EXISTS categorieevenement CASCADE;
DROP TABLE IF EXISTS souscategorieannonceur CASCADE;
DROP TABLE IF EXISTS categorieannonceur CASCADE;
DROP TABLE IF EXISTS annonceur CASCADE;

-- ============================
-- TABLE annonceur
-- ============================
CREATE TABLE annonceur (
    id_annonceur SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    telephone VARCHAR(20),
    id_categorie_annonceur INT NOT NULL,
    id_sous_categorie_annonceur INT,
    role_annonceur VARCHAR(20) NOT NULL DEFAULT 'annonceur'
        CHECK (role_annonceur IN ('annonceur', 'admin')),
    date_creation TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    statut VARCHAR(20) NOT NULL DEFAULT 'actif',
    reset_token VARCHAR(64),
    reset_expiry TIMESTAMP
);

-- Index sur email (unique)
CREATE UNIQUE INDEX annonceur_email_idx 
    ON annonceur(email);

-- ============================
-- TABLE categorieannonceur
-- ============================
CREATE TABLE categorieannonceur (
    id_categorie_annonceur SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE
);

-- ============================
-- TABLE categorieevenement
-- ============================
CREATE TABLE categorieevenement (
    id_categorie_evenement SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE
);

-- ============================
-- TABLE souscategorieannonceur
-- ============================
CREATE TABLE souscategorieannonceur (
    id_sous_categorie_annonceur SERIAL PRIMARY KEY,
    id_categorie_annonceur INT NOT NULL,
    nom VARCHAR(100) NOT NULL
);

-- ============================
-- TABLE souscategorieevenement
-- ============================
CREATE TABLE souscategorieevenement (
    id_sous_categorie_evenement SERIAL PRIMARY KEY,
    id_categorie_evenement INT NOT NULL,
    nom VARCHAR(100) NOT NULL
);

-- ============================
-- TABLE evenement
-- ============================
CREATE TABLE evenement (
    id_evenement SERIAL PRIMARY KEY,
    id_annonceur INT NOT NULL,
    id_categorie_evenement INT NOT NULL,
    id_sous_categorie_evenement INT,
    nom VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    date_evenement DATE NOT NULL,
    heure TIME,
    lieu VARCHAR(200) NOT NULL,
    image VARCHAR(255),
    statut VARCHAR(20) DEFAULT 'actif',
    compteur_consultations INT DEFAULT 0,
    date_creation TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- ============================
-- TABLE imagesevenement
-- ============================
CREATE TABLE imagesevenement (
    id_image SERIAL PRIMARY KEY,
    id_evenement INT NOT NULL,
    nom_fichier VARCHAR(255) NOT NULL
);

-- ============================
-- TABLE statistiques
-- ============================
CREATE TABLE statistiques (
    id_statistique SERIAL PRIMARY KEY,
    id_evenement INT NOT NULL,
    date_consultation TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    id_annonceur INT
);

-- ============================
-- Création des FOREIGN KEYS
-- (Pour respecter l'ordre, elles se créent après la table)
-- ============================

-- 1) annonceur -> categorieannonceur
ALTER TABLE annonceur
    ADD CONSTRAINT annonceur_catannonceur_fk
    FOREIGN KEY (id_categorie_annonceur)
    REFERENCES categorieannonceur(id_categorie_annonceur)
    ON UPDATE CASCADE
    ON DELETE RESTRICT;

-- 2) annonceur -> souscategorieannonceur
ALTER TABLE annonceur
    ADD CONSTRAINT annonceur_souscatannonceur_fk
    FOREIGN KEY (id_sous_categorie_annonceur)
    REFERENCES souscategorieannonceur(id_sous_categorie_annonceur)
    ON UPDATE CASCADE
    ON DELETE SET NULL;

-- 3) evenement -> annonceur
ALTER TABLE evenement
    ADD CONSTRAINT evenement_annonceur_fk
    FOREIGN KEY (id_annonceur)
    REFERENCES annonceur(id_annonceur)
    ON UPDATE CASCADE
    ON DELETE CASCADE;

-- 4) evenement -> categorieevenement
ALTER TABLE evenement
    ADD CONSTRAINT evenement_cat_evt_fk
    FOREIGN KEY (id_categorie_evenement)
    REFERENCES categorieevenement(id_categorie_evenement)
    ON UPDATE CASCADE
    ON DELETE RESTRICT;

-- 5) evenement -> souscategorieevenement
ALTER TABLE evenement
    ADD CONSTRAINT evenement_souscat_evt_fk
    FOREIGN KEY (id_sous_categorie_evenement)
    REFERENCES souscategorieevenement(id_sous_categorie_evenement)
    ON UPDATE CASCADE
    ON DELETE SET NULL;

-- 6) imagesevenement -> evenement
ALTER TABLE imagesevenement
    ADD CONSTRAINT imagesevenement_evt_fk
    FOREIGN KEY (id_evenement)
    REFERENCES evenement(id_evenement)
    ON UPDATE CASCADE
    ON DELETE CASCADE;

-- 7) souscategorieannonceur -> categorieannonceur
ALTER TABLE souscategorieannonceur
    ADD CONSTRAINT souscatann_catann_fk
    FOREIGN KEY (id_categorie_annonceur)
    REFERENCES categorieannonceur(id_categorie_annonceur)
    ON UPDATE CASCADE
    ON DELETE CASCADE;

-- 8) souscategorieevenement -> categorieevenement
ALTER TABLE souscategorieevenement
    ADD CONSTRAINT souscatevt_catevt_fk
    FOREIGN KEY (id_categorie_evenement)
    REFERENCES categorieevenement(id_categorie_evenement)
    ON UPDATE CASCADE
    ON DELETE CASCADE;

-- 9) statistiques -> evenement
ALTER TABLE statistiques
    ADD CONSTRAINT stats_evt_fk
    FOREIGN KEY (id_evenement)
    REFERENCES evenement(id_evenement)
    ON UPDATE CASCADE
    ON DELETE CASCADE;

-- 10) statistiques -> annonceur
ALTER TABLE statistiques
    ADD CONSTRAINT stats_annonceur_fk
    FOREIGN KEY (id_annonceur)
    REFERENCES annonceur(id_annonceur)
    ON UPDATE CASCADE
    ON DELETE SET NULL;

-- ============================
-- INSERTION DES DONNÉES
-- (adaptez pour PostgreSQL)
-- ============================

-- TABLE categorieannonceur
INSERT INTO categorieannonceur (id_categorie_annonceur, nom)
VALUES 
(1, 'Administrateur'),
(3, 'Association'),
(2, 'Entreprise'),
(4, 'Particulier');

-- TABLE categorieevenement
INSERT INTO categorieevenement (id_categorie_evenement, nom)
VALUES 
(3, 'Compétition'),
(1, 'Foire'),
(2, 'Soirée');

-- TABLE souscategorieannonceur
INSERT INTO souscategorieannonceur (id_sous_categorie_annonceur, id_categorie_annonceur, nom)
VALUES
(1, 1, 'Mairie'),
(2, 1, 'CABA'),
(3, 2, 'Informatique'),
(4, 2, 'Production'),
(5, 3, 'Sportive'),
(6, 3, 'Culturelle'),
(7, 4, 'Étudiant');

-- TABLE souscategorieevenement
INSERT INTO souscategorieevenement (id_sous_categorie_evenement, id_categorie_evenement, nom)
VALUES
(1, 1, 'Animation'),
(2, 1, 'Artisanat'),
(3, 2, 'Danse'),
(4, 2, 'Jeu'),
(5, 3, 'Tournoi Foot'),
(6, 3, 'Course');

-- TABLE annonceur
INSERT INTO annonceur 
(id_annonceur, nom, email, mot_de_passe, telephone, id_categorie_annonceur, 
 id_sous_categorie_annonceur, role_annonceur, date_creation, statut, reset_token, reset_expiry)
VALUES
(9, 'IBRA Admin', 'ibrahima97bodian@gmail.com', 
  '$2y$10$bpagIp6NXv.uP/OQ.lQK7.hCdXoFlVtueNaKlStV5SzNrtE.OZHUC', 
  '0766604616', 1, NULL, 'admin', '2025-01-01 11:56:38', 'actif',
  '1888a098637c55f8c427577ce58a28423bc0cefaba1942ece05b592487073ea4', '2025-01-08 04:47:44'),

(11, 'Ibrahhhh bbb', 'ibrahima97fbodian@gmail.com',
  '$2y$10$xMDeBgrNhnOcDmzM3oIV2..639Ehknxrj/c/3Xx2DBD8MlbytAYJW',
  '45678765', 2, NULL, 'annonceur', '2025-01-07 23:17:02', 'actif', NULL, NULL),

(12, 'A B', 'ab@gmail.com',
  '$2y$10$D9eN1v5T.P3oACmsztKDWOgETAVxg8rQ/dgVkej9.yPhqA3avbRAW', 
  '1234567', 2, NULL, 'annonceur', '2025-01-08 02:05:39', 'actif', NULL, NULL),

(13, 'IB BO', 'ibbo@gmail.com',
  '$2y$10$GDzRyLHZpHGSuNWoT4t2kOHnWqhIPRAfXOTbUfYqNj0DpAm2KEOmC',
  '765432111111', 2, NULL, 'annonceur', '2025-01-08 09:44:14', 'actif', NULL, NULL),

(14, 'Ibra', 'i.bodian879@gmail.com',
  '$2y$10$opNYy1qjcSU.Y3TWIahW5eZ8GTsH.oVpZ.GYcDx46wuRl4bEGMFDm',
  '567898765', 2, NULL, 'annonceur', '2025-01-08 16:40:52', 'actif', NULL, NULL);

-- TABLE evenement
INSERT INTO evenement 
(id_evenement, id_annonceur, id_categorie_evenement, id_sous_categorie_evenement,
 nom, description, date_evenement, heure, lieu, image, statut, compteur_consultations, date_creation)
VALUES
(23, 13, 2, 1, 'DDDD', 'DD', '2025-01-18', '12:12:00', 'D', 'event_677e498537028.jpg', 'actif', 1, '2025-01-08 09:46:45'),
(24, 12, 1, 1, 'Foire du nouvelle année', 'Venez on va biiiien bouffer du thiéb', '2025-01-10', '10:00:00', 'Square', 'event_677e63e913b52.jpg', 'actif', 1, '2025-01-08 11:39:21'),
(28, 13, 3, 6, 'DDF', 'DD', '2025-01-16', '22:39:00', 'AURILAC', 'event_677ea96389fe1.jpg', 'actif', 2, '2025-01-08 16:35:47'),
(32, 14, 2, 4, 'soiré', 'ddddd', '2025-01-16', '12:00:00', 'france', 'event_677eba80b9c5a.jpg', 'actif', 2, '2025-01-08 17:48:48');

-- TABLE statistiques
INSERT INTO statistiques (id_statistique, id_evenement, date_consultation, id_annonceur)
VALUES
(23, 28, '2025-01-08 16:36:12', 13),
(24, 24, '2025-01-08 16:46:33', 14),
(26, 28, '2025-01-08 16:46:41', 14),
(28, 23, '2025-01-08 16:46:51', 14),
(30, 32, '2025-01-08 17:49:14', 14),
(31, 32, '2025-01-08 17:55:09', NULL);

-- TABLE imagesevenement : aucune donnée ?

-- Fin du script
