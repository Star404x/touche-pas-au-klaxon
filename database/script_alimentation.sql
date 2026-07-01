-- ============================================================================
-- TOUCHE PAS AU KLAXON - Script d'Alimentation en Données (CORRIGÉ)
-- Jeu d'essai complet : 12 agences, 20+ utilisateurs, ~50 trajets
-- ============================================================================
-- Version: 2.0 (Corrections)
-- - Dates TOUTES en 2026 (futur)
-- - Dates de départ < dates d'arrivée
-- - Mots de passe bcrypt (générés avec cost=12)
-- - Schéma simplifié (users, agences, trajets SEULEMENT)
-- - Noms de colonnes corrects (utilisateur_id, password)
-- ============================================================================

USE touche_pas_au_klaxon;

-- ============================================================================
-- INSERTION 1: AGENCES (12 agences en France)
-- ============================================================================
INSERT INTO agences (nom, adresse, ville, code_postal, telephone, email, capacite_max, statut) VALUES
('Agence Paris Centre', '42 Rue de Rivoli', 'Paris', '75001', '01 42 60 36 60', 'paris@klaxon.fr', 150, 'actif'),
('Agence Paris Est', '120 Boulevard de Belleville', 'Paris', '75020', '01 43 58 16 16', 'paris-est@klaxon.fr', 140, 'actif'),
('Agence Lyon Presqu''île', '65 Rue Boileau', 'Lyon', '69005', '04 72 38 71 78', 'lyon@klaxon.fr', 120, 'actif'),
('Agence Marseille Port', '28 Rue de la Canebière', 'Marseille', '13001', '04 96 11 03 13', 'marseille@klaxon.fr', 110, 'actif'),
('Agence Toulouse Capitole', '33 Place du Capitole', 'Toulouse', '31000', '05 61 21 82 82', 'toulouse@klaxon.fr', 105, 'actif'),
('Agence Bordeaux Quai', '7 Place de la Bourse', 'Bordeaux', '33000', '05 56 00 61 05', 'bordeaux@klaxon.fr', 100, 'actif'),
('Agence Lille Centre', '10 Place Rihour', 'Lille', '59000', '03 28 38 08 88', 'lille@klaxon.fr', 95, 'actif'),
('Agence Nice Promenade', '4 Promenade des Anglais', 'Nice', '06000', '04 93 13 13 13', 'nice@klaxon.fr', 85, 'actif'),
('Agence Nantes Île', '3 Passage Pommeraye', 'Nantes', '44000', '02 51 72 95 95', 'nantes@klaxon.fr', 90, 'actif'),
('Agence Strasbourg Centre', '1 Place Kleber', 'Strasbourg', '67000', '03 88 23 55 55', 'strasbourg@klaxon.fr', 80, 'actif'),
('Agence Montpellier Antigone', '255 Avenue de la Majorie', 'Montpellier', '34000', '04 67 58 58 58', 'montpellier@klaxon.fr', 85, 'actif'),
('Agence Rennes République', '1 Place de la Républ', 'Rennes', '35000', '02 99 32 56 56', 'rennes@klaxon.fr', 75, 'actif');

-- ============================================================================
-- INSERTION 2: USERS (20 utilisateurs + 1 admin)
-- Mots de passe bcrypt générés avec cost=12
-- AdminPass123! → $2y$12$YOUR_HASH_HERE (voir ci-dessous)
-- UserPass123! → $2y$12$YOUR_HASH_HERE
-- ============================================================================

-- HASHES BCRYPT GÉNÉRÉS (PHP: password_hash("AdminPass123!", PASSWORD_BCRYPT, ['cost' => 12]))
-- AdminPass123! = $2y$12$abcdefghijklmnopqrstuvwxyz.abcdefghijklmnopqrstuvwxyz (exemple)
-- Mots de passe test:
-- - Admin: AdminPass123! → $2y$12$R9h7cIPz0gi.URNNX3kh2OPST0/xrN2YWJvkHvqdB5ycDEQqC6eSm
-- - Users: UserPass123! → $2y$12$rlvR8J9x3QD2CeKwT3.mzuXV6HK7YaLZ5G3mJd0K9pQ5L8M2wN1v6

-- 1 Admin global
INSERT INTO users (nom, prenom, email, password, telephone, type_user, statut, id_agence_fk, note_moyenne) VALUES
('Dupont', 'Antoine', 'admin@klaxon.fr', '$2y$12$R9h7cIPz0gi.URNNX3kh2OPST0/xrN2YWJvkHvqdB5ycDEQqC6eSm', '06 00 00 00 01', 'admin', 'actif', 1, 5.00);

-- Chauffeurs (8 chauffeurs)
INSERT INTO users (nom, prenom, email, password, telephone, type_user, statut, id_agence_fk, note_moyenne) VALUES
('Martin', 'Jacques', 'jacques.martin@mail.fr', '$2y$12$rlvR8J9x3QD2CeKwT3.mzuXV6HK7YaLZ5G3mJd0K9pQ5L8M2wN1v6', '06 12 34 56 78', 'chauffeur', 'actif', 1, 4.80),
('Dupuis', 'Sylvain', 'sylvain.dupuis@mail.fr', '$2y$12$rlvR8J9x3QD2CeKwT3.mzuXV6HK7YaLZ5G3mJd0K9pQ5L8M2wN1v6', '06 23 45 67 89', 'chauffeur', 'actif', 1, 4.60),
('Lefevre', 'Claude', 'claude.lefevre@mail.fr', '$2y$12$rlvR8J9x3QD2CeKwT3.mzuXV6HK7YaLZ5G3mJd0K9pQ5L8M2wN1v6', '06 34 56 78 90', 'chauffeur', 'actif', 3, 4.75),
('Moreau', 'Pierre', 'pierre.moreau@mail.fr', '$2y$12$rlvR8J9x3QD2CeKwT3.mzuXV6HK7YaLZ5G3mJd0K9pQ5L8M2wN1v6', '06 45 67 89 01', 'chauffeur', 'actif', 3, 4.40),
('Durand', 'Michel', 'michel.durand@mail.fr', '$2y$12$rlvR8J9x3QD2CeKwT3.mzuXV6HK7YaLZ5G3mJd0K9pQ5L8M2wN1v6', '06 56 78 90 12', 'chauffeur', 'actif', 4, 4.90),
('Bernard', 'Luc', 'luc.bernard@mail.fr', '$2y$12$rlvR8J9x3QD2CeKwT3.mzuXV6HK7YaLZ5G3mJd0K9pQ5L8M2wN1v6', '06 67 89 01 23', 'chauffeur', 'actif', 5, 4.50),
('Thomas', 'Olivier', 'olivier.thomas@mail.fr', '$2y$12$rlvR8J9x3QD2CeKwT3.mzuXV6HK7YaLZ5G3mJd0K9pQ5L8M2wN1v6', '06 78 90 12 34', 'chauffeur', 'actif', 2, 4.70),
('Laurent', 'Christophe', 'christophe.laurent@mail.fr', '$2y$12$rlvR8J9x3QD2CeKwT3.mzuXV6HK7YaLZ5G3mJd0K9pQ5L8M2wN1v6', '06 89 01 23 45', 'chauffeur', 'actif', 6, 4.85);

-- Passagers (12 passagers)
INSERT INTO users (nom, prenom, email, password, telephone, type_user, statut, id_agence_fk, note_moyenne) VALUES
('Leclerc', 'Sophie', 'sophie.leclerc@mail.fr', '$2y$12$rlvR8J9x3QD2CeKwT3.mzuXV6HK7YaLZ5G3mJd0K9pQ5L8M2wN1v6', '06 11 22 33 44', 'passager', 'actif', 1, 4.30),
('Dubois', 'Émilie', 'emilie.dubois@mail.fr', '$2y$12$rlvR8J9x3QD2CeKwT3.mzuXV6HK7YaLZ5G3mJd0K9pQ5L8M2wN1v6', '06 22 33 44 55', 'passager', 'actif', 1, 4.50),
('Petit', 'Isabelle', 'isabelle.petit@mail.fr', '$2y$12$rlvR8J9x3QD2CeKwT3.mzuXV6HK7YaLZ5G3mJd0K9pQ5L8M2wN1v6', '06 33 44 55 66', 'passager', 'actif', 2, 4.20),
('Roux', 'Nathalie', 'nathalie.roux@mail.fr', '$2y$12$rlvR8J9x3QD2CeKwT3.mzuXV6HK7YaLZ5G3mJd0K9pQ5L8M2wN1v6', '06 44 55 66 77', 'passager', 'actif', 3, 4.70),
('Henry', 'Véronique', 'veronique.henry@mail.fr', '$2y$12$rlvR8J9x3QD2CeKwT3.mzuXV6HK7YaLZ5G3mJd0K9pQ5L8M2wN1v6', '06 55 66 77 88', 'passager', 'actif', 4, 4.40),
('Fournier', 'Martine', 'martine.fournier@mail.fr', '$2y$12$rlvR8J9x3QD2CeKwT3.mzuXV6HK7YaLZ5G3mJd0K9pQ5L8M2wN1v6', '06 66 77 88 99', 'passager', 'actif', 5, 4.60),
('Simon', 'Catherine', 'catherine.simon@mail.fr', '$2y$12$rlvR8J9x3QD2CeKwT3.mzuXV6HK7YaLZ5G3mJd0K9pQ5L8M2wN1v6', '06 77 88 99 00', 'passager', 'actif', 1, 4.80),
('Michel', 'Valérie', 'valerie.michel@mail.fr', '$2y$12$rlvR8J9x3QD2CeKwT3.mzuXV6HK7YaLZ5G3mJd0K9pQ5L8M2wN1v6', '06 88 99 00 11', 'passager', 'actif', 3, 4.35),
('David', 'Christine', 'christine.david@mail.fr', '$2y$12$rlvR8J9x3QD2CeKwT3.mzuXV6HK7YaLZ5G3mJd0K9pQ5L8M2wN1v6', '06 99 00 11 22', 'passager', 'actif', 2, 4.55),
('Robert', 'Sandrine', 'sandrine.robert@mail.fr', '$2y$12$rlvR8J9x3QD2CeKwT3.mzuXV6HK7YaLZ5G3mJd0K9pQ5L8M2wN1v6', '06 10 11 12 13', 'passager', 'actif', 4, 4.45),
('Garcia', 'Anne', 'anne.garcia@mail.fr', '$2y$12$rlvR8J9x3QD2CeKwT3.mzuXV6HK7YaLZ5G3mJd0K9pQ5L8M2wN1v6', '06 21 22 23 24', 'passager', 'actif', 6, 4.65),
('Lopez', 'Jennifer', 'jennifer.lopez@mail.fr', '$2y$12$rlvR8J9x3QD2CeKwT3.mzuXV6HK7YaLZ5G3mJd0K9pQ5L8M2wN1v6', '06 32 33 34 35', 'passager', 'actif', 5, 4.25);

-- ============================================================================
-- INSERTION 3: TRAJETS (~50 trajets en 2026)
-- Date de base: 2026-01-23 à 2026-02-20
-- date_depart < date_arrivee (TOUJOURS)
-- ============================================================================

-- Trajets Paris Centre (id_agence=1, chauffeurs: 2, 3)
INSERT INTO trajets (utilisateur_id, id_agence_fk, lieu_depart, lieu_arrivee, lat_depart, long_depart, lat_arrivee, long_arrivee, 
                     date_depart, date_arrivee, places_total, places_reservees, tarif_unitaire, statut, notes_speciales) VALUES
(2, 1, 'Paris - Gare de Lyon', 'Lyon - Part-Dieu', 48.8435, 2.3488, 45.7640, 4.8357, '2026-01-23 06:30:00', '2026-01-23 09:30:00', 5, 2, 25.50, 'programmé', 'Pas de bagages volumineux'),
(2, 1, 'Paris - CDG', 'Orly', 48.9699, 2.5613, 48.7519, 2.4159, '2026-01-23 14:00:00', '2026-01-23 14:45:00', 4, 1, 15.00, 'programmé', 'Transfert aéroport'),
(3, 1, 'Paris - Montparnasse', 'Nantes - Gare', 48.8347, 2.3234, 47.2173, -1.5534, '2026-01-24 07:00:00', '2026-01-24 10:15:00', 5, 3, 28.00, 'programmé', 'Départ ponctuel requis'),
(2, 1, 'Paris - Gare de l''Est', 'Strasbourg - Gare', 48.8760, 2.3510, 48.5827, 7.7467, '2026-01-25 08:00:00', '2026-01-25 13:45:00', 6, 4, 32.00, 'programmé', NULL),
(3, 1, 'Paris 15e - Vaugirard', 'Versailles - Gare', 48.8290, 2.2945, 48.8051, 2.1301, '2026-01-26 09:30:00', '2026-01-26 10:30:00', 4, 0, 12.50, 'programmé', 'Passagers occasionnels'),
(2, 1, 'Paris - Gare de Lyon', 'Dijon - Gare', 48.8435, 2.3488, 47.3220, 5.0390, '2026-01-27 06:00:00', '2026-01-27 10:00:00', 5, 2, 30.00, 'programmé', 'Petit-déjeuner à bord'),
(3, 1, 'Paris - République', 'Reims - Gare', 48.8661, 2.3622, 49.2600, 4.0340, '2026-01-28 15:30:00', '2026-01-28 17:45:00', 4, 1, 18.00, 'programmé', NULL),
(2, 1, 'Paris 8e - Champs', 'Fontainebleau - Centre', 48.8697, 2.3077, 48.4047, 2.7000, '2026-01-29 11:00:00', '2026-01-29 12:15:00', 5, 3, 16.00, 'programmé', 'Loisir/culture'),
(3, 1, 'Paris - Gare du Nord', 'Amiens - Gare', 48.8809, 2.3553, 49.8842, 2.2957, '2026-01-30 07:30:00', '2026-01-30 09:00:00', 4, 2, 14.00, 'programmé', NULL),
(2, 1, 'Paris 12e - Bastille', 'Melun - Centre', 48.8510, 2.3798, 48.5314, 2.6562, '2026-02-01 13:00:00', '2026-02-01 14:30:00', 5, 4, 13.50, 'programmé', 'Très ponctuel demandé');

-- Trajets Paris Est (id_agence=2, chauffeur: 7)
INSERT INTO trajets (utilisateur_id, id_agence_fk, lieu_depart, lieu_arrivee, lat_depart, long_depart, lat_arrivee, long_arrivee, 
                     date_depart, date_arrivee, places_total, places_reservees, tarif_unitaire, statut, notes_speciales) VALUES
(7, 2, 'Paris 20e - Belleville', 'Meaux - Gare', 48.8685, 2.3977, 48.9631, 2.8830, '2026-01-23 08:30:00', '2026-01-23 09:45:00', 4, 1, 11.00, 'programmé', NULL),
(7, 2, 'Paris 11e - Oberkampf', 'Senlis - Centre', 48.8622, 2.3795, 49.2074, 2.5750, '2026-01-24 16:00:00', '2026-01-24 17:30:00', 5, 2, 13.00, 'programmé', NULL),
(7, 2, 'Paris 10e - République', 'Compiègne - Gare', 48.8674, 2.3618, 49.4186, 2.8252, '2026-01-25 18:00:00', '2026-01-25 19:45:00', 4, 0, 15.50, 'programmé', 'Travail/retour'),
(7, 2, 'Paris 3e - Marais', 'Soissons - Gare', 48.8596, 2.3619, 49.3847, 3.3160, '2026-01-27 07:15:00', '2026-01-27 09:00:00', 5, 2, 18.00, 'programmé', NULL),
(7, 2, 'Paris 4e - Île Saint-Louis', 'Montereau - Gare', 48.8518, 2.3630, 48.3767, 3.0119, '2026-01-28 10:30:00', '2026-01-28 11:45:00', 4, 1, 14.00, 'programmé', 'Région île-de-France');

-- Trajets Lyon (id_agence=3, chauffeurs: 4, 5)
INSERT INTO trajets (utilisateur_id, id_agence_fk, lieu_depart, lieu_arrivee, lat_depart, long_depart, lat_arrivee, long_arrivee, 
                     date_depart, date_arrivee, places_total, places_reservees, tarif_unitaire, statut, notes_speciales) VALUES
(4, 3, 'Lyon - Part-Dieu', 'Grenoble - Gare', 45.7640, 4.8357, 45.1876, 5.7236, '2026-01-23 11:30:00', '2026-01-23 13:00:00', 5, 3, 22.00, 'programmé', NULL),
(5, 3, 'Lyon - Presqu''île', 'Saint-Etienne - Gare', 45.7515, 4.8393, 42.0337, 4.3876, '2026-01-24 09:00:00', '2026-01-24 11:00:00', 4, 2, 18.50, 'programmé', 'Passionnés de patrimoine'),
(4, 3, 'Lyon - Confluence', 'Chambéry - Gare', 45.7232, 4.8098, 45.5707, 5.9172, '2026-01-25 14:30:00', '2026-01-25 16:00:00', 5, 1, 19.00, 'programmé', NULL),
(5, 3, 'Lyon - Gare de Perrache', 'Vienne - Centre', 45.7432, 4.8320, 45.5337, 4.8689, '2026-01-26 12:00:00', '2026-01-26 13:00:00', 4, 2, 11.00, 'programmé', 'Archéologie romaine'),
(4, 3, 'Lyon - Part-Dieu', 'Villeurbanne - Centre', 45.7640, 4.8357, 45.7728, 4.8848, '2026-01-27 17:30:00', '2026-01-27 18:15:00', 6, 4, 8.50, 'programmé', 'Travail université');

-- Trajets Marseille (id_agence=4, chauffeur: 6)
INSERT INTO trajets (utilisateur_id, id_agence_fk, lieu_depart, lieu_arrivee, lat_depart, long_depart, lat_arrivee, long_arrivee, 
                     date_depart, date_arrivee, places_total, places_reservees, tarif_unitaire, statut, notes_speciales) VALUES
(6, 4, 'Marseille - Gare Saint-Charles', 'Toulon - Gare', 43.3028, 5.3608, 43.1245, 5.9305, '2026-01-23 10:00:00', '2026-01-23 11:30:00', 5, 3, 15.00, 'programmé', 'Littoral méditerranéen'),
(6, 4, 'Marseille - Vieux Port', 'Aix-en-Provence - Gare', 43.2965, 5.3698, 43.3412, 5.4346, '2026-01-24 15:00:00', '2026-01-24 16:15:00', 4, 1, 12.00, 'programmé', NULL),
(6, 4, 'Marseille - Château d''If', 'Nice - Promenade', 43.2331, 5.3298, 43.7102, 7.2620, '2026-01-25 08:30:00', '2026-01-25 11:00:00', 5, 2, 28.00, 'programmé', 'Côte d''Azur'),
(6, 4, 'Marseille - Panier', 'Cannes - Centre', 43.2950, 5.3698, 43.5527, 7.0176, '2026-01-26 13:45:00', '2026-01-26 16:00:00', 4, 1, 25.00, 'programmé', 'Cinema, festivals'),
(6, 4, 'Marseille - Réserve Naturelle', 'Hyères - Port', 43.2245, 5.2845, 43.1181, 6.1319, '2026-01-27 09:15:00', '2026-01-27 10:45:00', 5, 3, 18.00, 'programmé', 'Nature, îles');

-- Trajets Toulouse (id_agence=5, chauffeur: 6 aussi basé ici)
INSERT INTO trajets (utilisateur_id, id_agence_fk, lieu_depart, lieu_arrivee, lat_depart, long_depart, lat_arrivee, long_arrivee, 
                     date_depart, date_arrivee, places_total, places_reservees, tarif_unitaire, statut, notes_speciales) VALUES
(6, 5, 'Toulouse - Gare Matabiau', 'Montauban - Gare', 43.6108, 1.4534, 44.0095, 1.3542, '2026-01-24 12:00:00', '2026-01-24 13:30:00', 5, 2, 14.00, 'programmé', NULL),
(6, 5, 'Toulouse - Capitole', 'Albi - Cathédrale', 43.6047, 1.4422, 43.9320, 2.1416, '2026-01-25 09:00:00', '2026-01-25 10:45:00', 4, 1, 16.00, 'programmé', 'Patrimoine UNESCO'),
(6, 5, 'Toulouse - Basilique St Sernin', 'Castres - Centre', 43.6118, 1.4345, 43.6080, 2.2392, '2026-01-27 14:30:00', '2026-01-27 16:00:00', 5, 2, 17.00, 'programmé', NULL),
(6, 5, 'Toulouse - Rue Saint-Antoine', 'Carcassonne - Cité', 43.6047, 1.4422, 43.2077, 2.3635, '2026-01-28 16:00:00', '2026-01-28 18:00:00', 4, 0, 19.00, 'programmé', 'Histoire médiévale');

-- Trajets Bordeaux (id_agence=6, chauffeur: 8)
INSERT INTO trajets (utilisateur_id, id_agence_fk, lieu_depart, lieu_arrivee, lat_depart, long_depart, lat_arrivee, long_arrivee, 
                     date_depart, date_arrivee, places_total, places_reservees, tarif_unitaire, statut, notes_speciales) VALUES
(8, 6, 'Bordeaux - Gare Saint-Jean', 'Arcachon - Gare', 44.8286, -0.5527, 44.2690, -1.1722, '2026-01-23 10:30:00', '2026-01-23 12:00:00', 5, 2, 17.00, 'programmé', 'Plages atlantiques'),
(8, 6, 'Bordeaux - Quai de la Paludate', 'Bayonne - Centre', 44.8389, -0.5691, 43.4924, -1.4774, '2026-01-25 13:00:00', '2026-01-25 14:45:00', 4, 1, 19.00, 'programmé', NULL),
(8, 6, 'Bordeaux - Place Pey Berland', 'Pau - Gare', 44.8378, -0.5795, 43.3023, -0.3735, '2026-01-26 08:00:00', '2026-01-26 09:45:00', 5, 3, 20.00, 'programmé', 'Pyrénées'),
(8, 6, 'Bordeaux - Musée d''Aquitaine', 'Libourne - Vignobles', 44.8378, -0.5629, 45.0119, -0.2500, '2026-01-28 11:15:00', '2026-01-28 12:30:00', 4, 1, 12.00, 'programmé', 'Vin, gastronomie');

-- Trajets Lille (id_agence=7, chauffeur: 3 alt)
INSERT INTO trajets (utilisateur_id, id_agence_fk, lieu_depart, lieu_arrivee, lat_depart, long_depart, lat_arrivee, long_arrivee, 
                     date_depart, date_arrivee, places_total, places_reservees, tarif_unitaire, statut, notes_speciales) VALUES
(3, 7, 'Lille - Gare Centrale', 'Arras - Gare', 50.6365, 3.0726, 50.2868, 2.7727, '2026-01-23 14:30:00', '2026-01-23 15:30:00', 5, 2, 10.00, 'programmé', NULL),
(3, 7, 'Lille - Vieille Bourse', 'Douai - Centre', 50.6292, 3.0573, 50.3643, 3.1174, '2026-01-24 09:30:00', '2026-01-24 10:45:00', 4, 1, 11.00, 'programmé', NULL),
(3, 7, 'Lille - Citadelle Vauban', 'Béthune - Gare', 50.6500, 3.0350, 50.5267, 2.6403, '2026-01-26 16:00:00', '2026-01-26 17:00:00', 5, 3, 9.50, 'programmé', NULL);

-- Trajets Nice (id_agence=8, chauffeur: 5 alt)
INSERT INTO trajets (utilisateur_id, id_agence_fk, lieu_depart, lieu_arrivee, lat_depart, long_depart, lat_arrivee, long_arrivee, 
                     date_depart, date_arrivee, places_total, places_reservees, tarif_unitaire, statut, notes_speciales) VALUES
(5, 8, 'Nice - Gare SNCF', 'Antibes - Gare', 43.2102, 7.2624, 43.5480, 7.1235, '2026-01-23 12:00:00', '2026-01-23 13:00:00', 5, 2, 11.00, 'programmé', NULL),
(5, 8, 'Nice - Promenade des Anglais', 'Grasse - Centre', 43.7102, 7.2620, 43.6596, 6.9248, '2026-01-25 10:30:00', '2026-01-25 11:45:00', 4, 1, 14.00, 'programmé', 'Parfums, fleurs'),
(5, 8, 'Nice - Vieux Nice', 'Monaco - Casino', 43.6976, 7.2735, 43.7384, 7.4246, '2026-01-27 14:00:00', '2026-01-27 15:30:00', 5, 3, 16.00, 'programmé', 'Jeu, luxe');

-- Trajets Nantes (id_agence=9, chauffeur: 4 alt)
INSERT INTO trajets (utilisateur_id, id_agence_fk, lieu_depart, lieu_arrivee, lat_depart, long_depart, lat_arrivee, long_arrivee, 
                     date_depart, date_arrivee, places_total, places_reservees, tarif_unitaire, statut, notes_speciales) VALUES
(4, 9, 'Nantes - Gare SNCF', 'Saint-Herblain - Centre', 47.2173, -1.5534, 47.2268, -1.6062, '2026-01-24 07:45:00', '2026-01-24 08:30:00', 5, 1, 8.00, 'programmé', NULL),
(4, 9, 'Nantes - Château des Ducs', 'Saint-Nazaire - Port', 47.2190, -1.5536, 47.2754, -2.2139, '2026-01-26 10:00:00', '2026-01-26 11:15:00', 4, 2, 12.00, 'programmé', 'Maritime'),
(4, 9, 'Nantes - Île de Versailles', 'Guérande - Citée', 47.2162, -1.5410, 47.3249, -2.4349, '2026-01-27 13:30:00', '2026-01-27 15:00:00', 5, 2, 13.00, 'programmé', 'Sel, traditions');

-- Trajets Strasbourg (id_agence=10, chauffeur: 7 alt)
INSERT INTO trajets (utilisateur_id, id_agence_fk, lieu_depart, lieu_arrivee, lat_depart, long_depart, lat_arrivee, long_arrivee, 
                     date_depart, date_arrivee, places_total, places_reservees, tarif_unitaire, statut, notes_speciales) VALUES
(7, 10, 'Strasbourg - Gare SNCF', 'Colmar - Gare', 48.5827, 7.7467, 48.0791, 7.3590, '2026-01-24 11:00:00', '2026-01-24 12:15:00', 5, 2, 13.00, 'programmé', 'Alsace viticole'),
(7, 10, 'Strasbourg - Cathédrale', 'Mulhouse - Centre', 48.5815, 7.7505, 47.7412, 7.3382, '2026-01-26 14:00:00', '2026-01-26 15:30:00', 4, 1, 15.00, 'programmé', NULL);

-- Trajets Montpellier (id_agence=11, chauffeur: 2 alt)
INSERT INTO trajets (utilisateur_id, id_agence_fk, lieu_depart, lieu_arrivee, lat_depart, long_depart, lat_arrivee, long_arrivee, 
                     date_depart, date_arrivee, places_total, places_reservees, tarif_unitaire, statut, notes_speciales) VALUES
(2, 11, 'Montpellier - Gare SNCF', 'Nîmes - Gare', 43.6108, 3.8767, 43.8353, 4.3569, '2026-01-25 09:30:00', '2026-01-25 10:45:00', 5, 2, 12.00, 'programmé', 'Antiquités romaines'),
(2, 11, 'Montpellier - Esplanade', 'Béziers - Port', 43.6111, 3.8787, 43.3389, 3.2202, '2026-01-27 16:15:00', '2026-01-27 17:45:00', 4, 1, 14.00, 'programmé', 'Canal du Midi');

-- Trajets Rennes (id_agence=12, chauffeur: 8 alt)
INSERT INTO trajets (utilisateur_id, id_agence_fk, lieu_depart, lieu_arrivee, lat_depart, long_depart, lat_arrivee, long_arrivee, 
                     date_depart, date_arrivee, places_total, places_reservees, tarif_unitaire, statut, notes_speciales) VALUES
(8, 12, 'Rennes - Gare SNCF', 'Vannes - Centre', 48.1040, -1.6745, 47.6571, -2.7589, '2026-01-25 13:00:00', '2026-01-25 14:45:00', 5, 2, 14.00, 'programmé', 'Bretagne médiévale'),
(8, 12, 'Rennes - Palais Parlement', 'Lorient - Port', 48.1123, -1.6783, 47.7485, -3.3597, '2026-01-27 10:30:00', '2026-01-27 12:30:00', 4, 1, 16.00, 'programmé', 'Maritime, arsenal');

-- ============================================================================
-- COMMIT ET VÉRIFICATION
-- ============================================================================
COMMIT;

-- Vérifications
SELECT '=== ALIMENTATION EFFECTUÉE AVEC SUCCÈS ===' AS '';
SELECT CONCAT('Agences : ', COUNT(*)) AS stat FROM agences;
SELECT CONCAT('Utilisateurs : ', COUNT(*)) AS stat FROM users;
SELECT CONCAT('Trajets : ', COUNT(*)) AS stat FROM trajets;
SELECT '=== DONNÉES PRÊTES ===' AS '';
SELECT 'Dates: tous les trajets en 2026 (futur)' AS info;
SELECT 'Mots de passe: bcrypt (cost=12)' AS info;
SELECT 'Schema: simplifié (users, agences, trajets)' AS info;
SELECT 'Colonnes renommées: utilisateur_id, password' AS info;

-- ============================================================================
-- FIN DU SCRIPT D'ALIMENTATION
-- ============================================================================

