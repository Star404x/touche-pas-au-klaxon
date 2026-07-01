-- ============================================================================
-- TOUCHE PAS AU KLAXON - Script de Création de Base de Données (CORRIGÉ)
-- Plateforme de Gestion de Trajets Partagés
-- ============================================================================
-- Version: 2.0 (Corrections appliquées)
-- - Noms de tables EN ANGLAIS (users, agences, trajets)
-- - Noms de colonnes EN ANGLAIS (password, utilisateur_id)
-- - DROP TABLE IF EXISTS pour chaque table
-- - Schéma simplifié: SEULEMENT users, agences, trajets
-- - Toutes les dates en 2026 (futur)
-- - password VARCHAR(255) pour bcrypt
-- ============================================================================

-- Suppression de la base si elle existe (sûr à utiliser avec DROP TABLE IF EXISTS)
DROP DATABASE IF EXISTS touche_pas_au_klaxon;

-- Création de la base de données
CREATE DATABASE IF NOT EXISTS touche_pas_au_klaxon 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE touche_pas_au_klaxon;

-- ============================================================================
-- TABLE 1: AGENCES
-- Description: Agences gestionnaires des trajets
-- ============================================================================
DROP TABLE IF EXISTS agences;
CREATE TABLE agences (
    id_agence INT PRIMARY KEY AUTO_INCREMENT COMMENT 'Identifiant unique de l''agence',
    nom VARCHAR(100) NOT NULL COMMENT 'Nom de l''agence',
    adresse VARCHAR(150) NOT NULL COMMENT 'Adresse physique',
    ville VARCHAR(50) NOT NULL COMMENT 'Ville d''implantation',
    code_postal CHAR(5) NOT NULL COMMENT 'Code postal (format France)',
    telephone VARCHAR(15) NOT NULL COMMENT 'Numéro de téléphone',
    email VARCHAR(100) NOT NULL UNIQUE COMMENT 'Email professionnel (unique)',
    capacite_max INT NOT NULL DEFAULT 100 COMMENT 'Capacité maximale trajets/jour',
    statut ENUM('actif', 'inactif', 'suspendu') NOT NULL DEFAULT 'actif' COMMENT 'Statut opérationnel',
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de création',
    
    -- Indices
    INDEX idx_statut (statut),
    INDEX idx_ville (ville),
    INDEX idx_code_postal (code_postal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Table des agences gestionnaires';

-- ============================================================================
-- TABLE 2: USERS (ancien UTILISATEURS)
-- Description: Utilisateurs (admin, chauffeurs, passagers)
-- Colonne password: VARCHAR(255) pour bcrypt
-- Colonne utilisateur_id: supprimée (utiliser id_user comme PK)
-- ============================================================================
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id_user INT PRIMARY KEY AUTO_INCREMENT COMMENT 'Identifiant unique utilisateur',
    nom VARCHAR(50) NOT NULL COMMENT 'Nom de famille',
    prenom VARCHAR(50) NOT NULL COMMENT 'Prénom',
    email VARCHAR(100) NOT NULL UNIQUE COMMENT 'Email unique',
    password VARCHAR(255) NOT NULL COMMENT 'Mot de passe hashé (bcrypt)',
    telephone VARCHAR(15) NOT NULL COMMENT 'Téléphone mobile/fixe',
    type_user ENUM('admin', 'chauffeur', 'passager') NOT NULL COMMENT 'Profil utilisateur',
    statut ENUM('actif', 'suspendu', 'inactif') NOT NULL DEFAULT 'actif' COMMENT 'Statut du compte',
    id_agence_fk INT NOT NULL COMMENT 'Agence d''affiliation',
    note_moyenne DECIMAL(3,2) NOT NULL DEFAULT 0.00 COMMENT 'Moyenne des notes reçues',
    date_inscription DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date d''inscription',
    
    -- Clés étrangères
    CONSTRAINT fk_user_agence FOREIGN KEY (id_agence_fk) 
        REFERENCES agences(id_agence) ON DELETE RESTRICT ON UPDATE CASCADE,
    
    -- Contraintes de vérification
    CONSTRAINT chk_note_moyenne CHECK (note_moyenne >= 0.00 AND note_moyenne <= 5.00),
    
    -- Indices
    INDEX idx_type_user (type_user),
    INDEX idx_statut_user (statut),
    INDEX idx_agence_user (id_agence_fk),
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Table des utilisateurs de la plateforme';

-- ============================================================================
-- TABLE 3: TRAJETS
-- Description: Trajets partagés créés par les chauffeurs
-- Colonne id_auteur: renommée en utilisateur_id
-- Toutes les dates: 2026 (futur)
-- ============================================================================
DROP TABLE IF EXISTS trajets;
CREATE TABLE trajets (
    id_trajet INT PRIMARY KEY AUTO_INCREMENT COMMENT 'Identifiant unique du trajet',
    utilisateur_id INT NOT NULL COMMENT 'Chauffeur responsable (ancien id_auteur)',
    id_agence_fk INT NOT NULL COMMENT 'Agence gestionnaire',
    lieu_depart VARCHAR(100) NOT NULL COMMENT 'Lieu de départ',
    lieu_arrivee VARCHAR(100) NOT NULL COMMENT 'Lieu d''arrivée',
    lat_depart DECIMAL(10,8) NULL COMMENT 'Latitude point de départ',
    long_depart DECIMAL(11,8) NULL COMMENT 'Longitude point de départ',
    lat_arrivee DECIMAL(10,8) NULL COMMENT 'Latitude point d''arrivée',
    long_arrivee DECIMAL(11,8) NULL COMMENT 'Longitude point d''arrivée',
    date_depart DATETIME NOT NULL COMMENT 'Date et heure de départ',
    date_arrivee DATETIME NOT NULL COMMENT 'Date et heure d''arrivée prévue',
    places_total INT NOT NULL COMMENT 'Nombre total de places',
    places_reservees INT NOT NULL DEFAULT 0 COMMENT 'Places déjà réservées',
    tarif_unitaire DECIMAL(8,2) NOT NULL COMMENT 'Prix par place en euros',
    statut ENUM('programmé', 'en_cours', 'complété', 'annulé') NOT NULL DEFAULT 'programmé' COMMENT 'État du trajet',
    notes_speciales TEXT NULL COMMENT 'Notes : bagages, animaux, fumeurs, etc.',
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de création',
    
    -- Clés étrangères
    CONSTRAINT fk_trajet_chauffeur FOREIGN KEY (utilisateur_id) 
        REFERENCES users(id_user) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_trajet_agence FOREIGN KEY (id_agence_fk) 
        REFERENCES agences(id_agence) ON DELETE RESTRICT ON UPDATE CASCADE,
    
    -- Contraintes de vérification
    CONSTRAINT chk_places_total CHECK (places_total > 0),
    CONSTRAINT chk_places_reservees CHECK (places_reservees >= 0),
    CONSTRAINT chk_places_coherence CHECK (places_reservees <= places_total),
    CONSTRAINT chk_dates_trajet CHECK (date_depart < date_arrivee),
    CONSTRAINT chk_tarif CHECK (tarif_unitaire > 0),
    
    -- Indices
    INDEX idx_trajet_statut (statut),
    INDEX idx_trajet_chauffeur (utilisateur_id),
    INDEX idx_trajet_agence (id_agence_fk),
    INDEX idx_trajet_date_depart (date_depart),
    INDEX idx_trajet_lieux (lieu_depart, lieu_arrivee)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Table des trajets disponibles';

-- ============================================================================
-- VUE 1: Vue d'information des trajets disponibles
-- ============================================================================
DROP VIEW IF EXISTS v_trajets_disponibles;
CREATE VIEW v_trajets_disponibles AS
SELECT 
    t.id_trajet,
    t.lieu_depart,
    t.lieu_arrivee,
    t.date_depart,
    t.date_arrivee,
    t.places_total,
    t.places_reservees,
    (t.places_total - t.places_reservees) AS places_disponibles,
    t.tarif_unitaire,
    CONCAT(u.prenom, ' ', u.nom) AS chauffeur,
    u.note_moyenne,
    a.nom AS agence,
    t.statut
FROM trajets t
JOIN users u ON t.utilisateur_id = u.id_user
JOIN agences a ON t.id_agence_fk = a.id_agence
WHERE t.statut IN ('programmé', 'en_cours')
    AND (t.places_total - t.places_reservees) > 0
ORDER BY t.date_depart;

-- ============================================================================
-- VUE 2: Vue du profil utilisateur avec statistiques
-- ============================================================================
DROP VIEW IF EXISTS v_profil_utilisateur;
CREATE VIEW v_profil_utilisateur AS
SELECT 
    u.id_user,
    CONCAT(u.prenom, ' ', u.nom) AS nom_complet,
    u.email,
    u.telephone,
    u.type_user,
    u.statut,
    u.note_moyenne,
    u.date_inscription,
    a.nom AS agence,
    COUNT(DISTINCT CASE WHEN t.id_trajet IS NOT NULL THEN t.id_trajet END) AS nb_trajets_cree,
    COUNT(DISTINCT u.id_user) AS nb_reservations
FROM users u
LEFT JOIN agences a ON u.id_agence_fk = a.id_agence
LEFT JOIN trajets t ON u.id_user = t.utilisateur_id
GROUP BY u.id_user;

-- ============================================================================
-- VUE 3: Vue de suivi financier par agence
-- ============================================================================
DROP VIEW IF EXISTS v_chiffres_agence;
CREATE VIEW v_chiffres_agence AS
SELECT 
    a.id_agence,
    a.nom AS agence,
    a.ville,
    COUNT(DISTINCT t.id_trajet) AS nb_trajets,
    SUM(t.places_reservees) AS nb_reservations,
    SUM(t.places_reservees * t.tarif_unitaire) AS chiffre_affaires,
    COUNT(DISTINCT u.id_user) AS nb_utilisateurs,
    AVG(u.note_moyenne) AS note_moyenne_agence
FROM agences a
LEFT JOIN trajets t ON a.id_agence = t.id_agence_fk
LEFT JOIN users u ON a.id_agence = u.id_agence_fk
GROUP BY a.id_agence
ORDER BY chiffre_affaires DESC;

-- ============================================================================
-- COMMIT ET VÉRIFICATION
-- ============================================================================
COMMIT;

-- Vérifications
SELECT '=== SCHÉMA CRÉÉ AVEC SUCCÈS ===' AS '';
SELECT 'Tables: agences, users, trajets' AS '';
SELECT 'Colonnes password: VARCHAR(255) pour bcrypt' AS '';
SELECT 'Colonnes renommées: utilisateur_id (ancien id_auteur)' AS '';
SELECT 'Dates: toutes les trajets en 2026 (voir script_alimentation)' AS '';

-- ============================================================================
-- FIN DU SCRIPT DE CRÉATION
-- ============================================================================
-- Base de données prête pour l'alimentation en données

