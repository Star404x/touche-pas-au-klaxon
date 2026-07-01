-- ============================================================================
-- TOUCHE PAS AU KLAXON - Script de Création de Base de Données
-- Plateforme de Gestion de Trajets Partagés
-- ============================================================================
-- Date: 2024
-- Version: 1.0
-- ============================================================================

-- Suppression de la base si elle existe (à utiliser avec prudence)
-- DROP DATABASE IF EXISTS touche_pas_au_klaxon;

-- Création de la base de données
CREATE DATABASE IF NOT EXISTS touche_pas_au_klaxon 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE touche_pas_au_klaxon;

-- ============================================================================
-- TABLE 1: AGENCES
-- Description: Agences gestionnaires des trajets
-- ============================================================================
CREATE TABLE AGENCES (
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
-- TABLE 2: UTILISATEURS
-- Description: Utilisateurs (admin, chauffeurs, passagers)
-- ============================================================================
CREATE TABLE UTILISATEURS (
    id_user INT PRIMARY KEY AUTO_INCREMENT COMMENT 'Identifiant unique utilisateur',
    nom VARCHAR(50) NOT NULL COMMENT 'Nom de famille',
    prenom VARCHAR(50) NOT NULL COMMENT 'Prénom',
    email VARCHAR(100) NOT NULL UNIQUE COMMENT 'Email unique',
    telephone VARCHAR(15) NOT NULL COMMENT 'Téléphone mobile/fixe',
    type_user ENUM('admin', 'chauffeur', 'passager') NOT NULL COMMENT 'Profil utilisateur',
    statut ENUM('actif', 'suspendu', 'inactif') NOT NULL DEFAULT 'actif' COMMENT 'Statut du compte',
    id_agence_fk INT NOT NULL COMMENT 'Agence d''affiliation',
    note_moyenne DECIMAL(3,2) NOT NULL DEFAULT 0.00 COMMENT 'Moyenne des notes reçues',
    date_inscription DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date d''inscription',
    
    -- Clés étrangères
    CONSTRAINT fk_user_agence FOREIGN KEY (id_agence_fk) 
        REFERENCES AGENCES(id_agence) ON DELETE RESTRICT ON UPDATE CASCADE,
    
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
-- ============================================================================
CREATE TABLE TRAJETS (
    id_trajet INT PRIMARY KEY AUTO_INCREMENT COMMENT 'Identifiant unique du trajet',
    id_chauffeur_fk INT NOT NULL COMMENT 'Chauffeur responsable',
    id_agence_fk INT NOT NULL COMMENT 'Agence gestionnaire',
    lieu_depart VARCHAR(100) NOT NULL COMMENT 'Lieu de départ',
    lieu_arrivee VARCHAR(100) NOT NULL COMMENT 'Lieu d''arrivée',
    lat_depart DECIMAL(10,8) NULL COMMENT 'Latitude point de départ',
    long_depart DECIMAL(11,8) NULL COMMENT 'Longitude point de départ',
    lat_arrivee DECIMAL(10,8) NULL COMMENT 'Latitude point d''arrivée',
    long_arrivee DECIMAL(11,8) NULL COMMENT 'Longitude point d''arrivée',
    date_heure_depart DATETIME NOT NULL COMMENT 'Date et heure de départ',
    date_heure_arrivee DATETIME NOT NULL COMMENT 'Date et heure d''arrivée prévue',
    places_total INT NOT NULL COMMENT 'Nombre total de places',
    places_reservees INT NOT NULL DEFAULT 0 COMMENT 'Places déjà réservées',
    tarif_unitaire DECIMAL(8,2) NOT NULL COMMENT 'Prix par place en euros',
    statut ENUM('programmé', 'en_cours', 'complété', 'annulé') NOT NULL DEFAULT 'programmé' COMMENT 'État du trajet',
    notes_speciales TEXT NULL COMMENT 'Notes : bagages, animaux, fumeurs, etc.',
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de création',
    
    -- Clés étrangères
    CONSTRAINT fk_trajet_chauffeur FOREIGN KEY (id_chauffeur_fk) 
        REFERENCES UTILISATEURS(id_user) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_trajet_agence FOREIGN KEY (id_agence_fk) 
        REFERENCES AGENCES(id_agence) ON DELETE RESTRICT ON UPDATE CASCADE,
    
    -- Contraintes de vérification
    CONSTRAINT chk_places_total CHECK (places_total > 0),
    CONSTRAINT chk_places_reservees CHECK (places_reservees >= 0),
    CONSTRAINT chk_places_coherence CHECK (places_reservees <= places_total),
    CONSTRAINT chk_dates_trajet CHECK (date_heure_depart < date_heure_arrivee),
    CONSTRAINT chk_trajet_futur CHECK (date_heure_depart > NOW()),
    CONSTRAINT chk_tarif CHECK (tarif_unitaire > 0),
    
    -- Indices
    INDEX idx_trajet_statut (statut),
    INDEX idx_trajet_chauffeur (id_chauffeur_fk),
    INDEX idx_trajet_agence (id_agence_fk),
    INDEX idx_trajet_date_depart (date_heure_depart),
    INDEX idx_trajet_lieux (lieu_depart, lieu_arrivee)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Table des trajets disponibles';

-- ============================================================================
-- TABLE 4: RESERVATIONS
-- Description: Réservations des passagers pour les trajets
-- ============================================================================
CREATE TABLE RESERVATIONS (
    id_reservation INT PRIMARY KEY AUTO_INCREMENT COMMENT 'Identifiant unique réservation',
    id_passager_fk INT NOT NULL COMMENT 'Passager réservant',
    id_trajet_fk INT NOT NULL COMMENT 'Trajet réservé',
    nb_places INT NOT NULL COMMENT 'Nombre de places réservées',
    date_reservation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de réservation',
    statut ENUM('confirmée', 'annulée', 'complétée') NOT NULL DEFAULT 'confirmée' COMMENT 'Statut réservation',
    prix_total DECIMAL(10,2) NOT NULL COMMENT 'Prix total (nb_places × tarif)',
    notes TEXT NULL COMMENT 'Notes du passager',
    
    -- Clés étrangères
    CONSTRAINT fk_reservation_passager FOREIGN KEY (id_passager_fk) 
        REFERENCES UTILISATEURS(id_user) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_reservation_trajet FOREIGN KEY (id_trajet_fk) 
        REFERENCES TRAJETS(id_trajet) ON DELETE RESTRICT ON UPDATE CASCADE,
    
    -- Contraintes
    CONSTRAINT chk_nb_places_res CHECK (nb_places > 0),
    CONSTRAINT chk_prix_total CHECK (prix_total > 0),
    CONSTRAINT unique_passager_trajet UNIQUE (id_passager_fk, id_trajet_fk),
    
    -- Indices
    INDEX idx_reservation_passager (id_passager_fk),
    INDEX idx_reservation_trajet (id_trajet_fk),
    INDEX idx_reservation_statut (statut),
    INDEX idx_reservation_date (date_reservation)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Table des réservations de trajets';

-- ============================================================================
-- TABLE 5: EVALUATIONS
-- Description: Avis et évaluations entre utilisateurs après trajet
-- ============================================================================
CREATE TABLE EVALUATIONS (
    id_evaluation INT PRIMARY KEY AUTO_INCREMENT COMMENT 'Identifiant unique évaluation',
    id_trajet_fk INT NOT NULL COMMENT 'Trajet concerné',
    id_auteur_fk INT NOT NULL COMMENT 'Utilisateur évaluant',
    id_cible_fk INT NOT NULL COMMENT 'Utilisateur évalué',
    note INT NOT NULL COMMENT 'Note de 1 à 5 étoiles',
    commentaire TEXT NULL COMMENT 'Avis textuel',
    date_evaluation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date d''évaluation',
    
    -- Clés étrangères
    CONSTRAINT fk_evaluation_trajet FOREIGN KEY (id_trajet_fk) 
        REFERENCES TRAJETS(id_trajet) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_evaluation_auteur FOREIGN KEY (id_auteur_fk) 
        REFERENCES UTILISATEURS(id_user) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_evaluation_cible FOREIGN KEY (id_cible_fk) 
        REFERENCES UTILISATEURS(id_user) ON DELETE CASCADE ON UPDATE CASCADE,
    
    -- Contraintes
    CONSTRAINT chk_note CHECK (note >= 1 AND note <= 5),
    CONSTRAINT unique_evaluation UNIQUE (id_trajet_fk, id_auteur_fk, id_cible_fk),
    
    -- Indices
    INDEX idx_evaluation_trajet (id_trajet_fk),
    INDEX idx_evaluation_cible (id_cible_fk),
    INDEX idx_evaluation_note (note),
    INDEX idx_evaluation_date (date_evaluation)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Table des évaluations entre utilisateurs';

-- ============================================================================
-- TABLE 6: PAIEMENTS
-- Description: Suivi des paiements pour les réservations
-- ============================================================================
CREATE TABLE PAIEMENTS (
    id_paiement INT PRIMARY KEY AUTO_INCREMENT COMMENT 'Identifiant unique paiement',
    id_reservation_fk INT NOT NULL COMMENT 'Réservation payée',
    montant DECIMAL(10,2) NOT NULL COMMENT 'Montant en euros',
    methode_paiement ENUM('carte_bancaire', 'virement', 'especes', 'portefeuille') NOT NULL COMMENT 'Mode de paiement',
    statut ENUM('en_attente', 'confirmé', 'remboursé') NOT NULL DEFAULT 'en_attente' COMMENT 'Statut du paiement',
    date_paiement DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Date transaction',
    
    -- Clés étrangères
    CONSTRAINT fk_paiement_reservation FOREIGN KEY (id_reservation_fk) 
        REFERENCES RESERVATIONS(id_reservation) ON DELETE RESTRICT ON UPDATE CASCADE,
    
    -- Contraintes
    CONSTRAINT chk_montant CHECK (montant > 0),
    
    -- Indices
    INDEX idx_paiement_reservation (id_reservation_fk),
    INDEX idx_paiement_statut (statut),
    INDEX idx_paiement_date (date_paiement),
    INDEX idx_paiement_methode (methode_paiement)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Table des paiements et transactions';

-- ============================================================================
-- VUE 1: Vue d'information des trajets disponibles
-- ============================================================================
CREATE VIEW v_trajets_disponibles AS
SELECT 
    t.id_trajet,
    t.lieu_depart,
    t.lieu_arrivee,
    t.date_heure_depart,
    t.date_heure_arrivee,
    t.places_total,
    t.places_reservees,
    (t.places_total - t.places_reservees) AS places_disponibles,
    t.tarif_unitaire,
    CONCAT(u.prenom, ' ', u.nom) AS chauffeur,
    u.note_moyenne,
    a.nom AS agence,
    t.statut
FROM TRAJETS t
JOIN UTILISATEURS u ON t.id_chauffeur_fk = u.id_user
JOIN AGENCES a ON t.id_agence_fk = a.id_agence
WHERE t.statut IN ('programmé', 'en_cours')
    AND (t.places_total - t.places_reservees) > 0
ORDER BY t.date_heure_depart;

-- ============================================================================
-- VUE 2: Vue du profil utilisateur avec statistiques
-- ============================================================================
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
    COUNT(DISTINCT CASE WHEN r.id_reservation IS NOT NULL THEN r.id_reservation END) AS nb_reservations
FROM UTILISATEURS u
LEFT JOIN AGENCES a ON u.id_agence_fk = a.id_agence
LEFT JOIN TRAJETS t ON u.id_user = t.id_chauffeur_fk
LEFT JOIN RESERVATIONS r ON u.id_user = r.id_passager_fk
GROUP BY u.id_user;

-- ============================================================================
-- VUE 3: Vue de suivi financier par agence
-- ============================================================================
CREATE VIEW v_chiffres_agence AS
SELECT 
    a.id_agence,
    a.nom AS agence,
    a.ville,
    COUNT(DISTINCT t.id_trajet) AS nb_trajets,
    COUNT(DISTINCT r.id_reservation) AS nb_reservations,
    SUM(r.prix_total) AS chiffre_affaires,
    COUNT(DISTINCT u.id_user) AS nb_utilisateurs,
    AVG(u.note_moyenne) AS note_moyenne_agence
FROM AGENCES a
LEFT JOIN TRAJETS t ON a.id_agence = t.id_agence_fk
LEFT JOIN RESERVATIONS r ON t.id_trajet = r.id_trajet_fk
LEFT JOIN UTILISATEURS u ON a.id_agence = u.id_agence_fk
GROUP BY a.id_agence
ORDER BY chiffre_affaires DESC;

-- ============================================================================
-- FIN DU SCRIPT DE CRÉATION
-- ============================================================================
-- Base de données prête pour l'alimentation en données

COMMIT;
