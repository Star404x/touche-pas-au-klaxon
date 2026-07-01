# MCD MERISE - TOUCHE PAS AU KLAXON

## Modèle Conceptuel de Données (MCD)

### Vue d'ensemble
Base de données relationnelle pour une plateforme de gestion de trajets partagés en France.
- **Entités principales**: 3 (après simplification)
- **Associations**: 2 (implicites par clés étrangères)
- **Attributs**: ~40 au total

---

## 1. ENTITÉS

### 1.1 Entité: AGENCES
**Description**: Agences gestionnaires des trajets partagés

| Attribut | Type | Contrainte | Signification |
|----------|------|-----------|----------------|
| **id_agence** | INT | PK, AUTO_INCREMENT | Identifiant unique |
| nom | VARCHAR(100) | NOT NULL, UNIQUE | Nom commercial |
| adresse | VARCHAR(150) | NOT NULL | Adresse physique |
| ville | VARCHAR(50) | NOT NULL | Ville d'implantation |
| code_postal | CHAR(5) | NOT NULL | Code postal français |
| telephone | VARCHAR(15) | NOT NULL | Téléphone de contact |
| email | VARCHAR(100) | NOT NULL, UNIQUE | Email professionnel |
| capacite_max | INT | DEFAULT 100 | Places/jour maximum |
| statut | ENUM('actif', 'inactif', 'suspendu') | DEFAULT 'actif' | État opérationnel |
| date_creation | DATETIME | DEFAULT NOW() | Date de création |

**Indices**: `statut`, `ville`, `code_postal`

---

### 1.2 Entité: USERS (ancien UTILISATEURS)
**Description**: Utilisateurs de la plateforme (administrateurs, chauffeurs, passagers)

| Attribut | Type | Contrainte | Signification |
|----------|------|-----------|----------------|
| **id_user** | INT | PK, AUTO_INCREMENT | Identifiant unique |
| nom | VARCHAR(50) | NOT NULL | Nom de famille |
| prenom | VARCHAR(50) | NOT NULL | Prénom |
| email | VARCHAR(100) | NOT NULL, UNIQUE | Email unique |
| **password** | VARCHAR(255) | NOT NULL | Mot de passe hashé (bcrypt) |
| telephone | VARCHAR(15) | NOT NULL | Téléphone |
| type_user | ENUM('admin', 'chauffeur', 'passager') | NOT NULL | Rôle utilisateur |
| statut | ENUM('actif', 'suspendu', 'inactif') | DEFAULT 'actif' | État du compte |
| id_agence_fk | INT | NOT NULL, FK → agences | Agence d'affiliation |
| note_moyenne | DECIMAL(3,2) | DEFAULT 0.00 | Note moyenne (0-5) |
| date_inscription | DATETIME | DEFAULT NOW() | Date inscription |

**Clé étrangère**: `id_agence_fk` → AGENCES(id_agence)

**Indices**: `type_user`, `statut`, `id_agence_fk`, `email`

---

### 1.3 Entité: TRAJETS
**Description**: Trajets partagés créés par les chauffeurs

| Attribut | Type | Contrainte | Signification |
|----------|------|-----------|----------------|
| **id_trajet** | INT | PK, AUTO_INCREMENT | Identifiant unique |
| **utilisateur_id** | INT | NOT NULL, FK → users | Chauffeur responsable |
| id_agence_fk | INT | NOT NULL, FK → agences | Agence gestionnaire |
| lieu_depart | VARCHAR(100) | NOT NULL | Adresse/lieu de départ |
| lieu_arrivee | VARCHAR(100) | NOT NULL | Adresse/lieu d'arrivée |
| lat_depart | DECIMAL(10,8) | NULL | Latitude départ (optionnel) |
| long_depart | DECIMAL(11,8) | NULL | Longitude départ (optionnel) |
| lat_arrivee | DECIMAL(10,8) | NULL | Latitude arrivée (optionnel) |
| long_arrivee | DECIMAL(11,8) | NULL | Longitude arrivée (optionnel) |
| **date_depart** | DATETIME | NOT NULL | Date/heure départ (dates 2026) |
| **date_arrivee** | DATETIME | NOT NULL | Date/heure arrivée (dates 2026) |
| places_total | INT | NOT NULL, >0 | Capacité totale |
| places_reservees | INT | DEFAULT 0, ≥0 | Places occupées |
| tarif_unitaire | DECIMAL(8,2) | NOT NULL, >0 | Prix par place (€) |
| statut | ENUM('programmé', 'en_cours', 'complété', 'annulé') | DEFAULT 'programmé' | État |
| notes_speciales | TEXT | NULL | Infos spéciales (bagages, animaux, etc.) |
| date_creation | DATETIME | DEFAULT NOW() | Date création |

**Clés étrangères**:
- `utilisateur_id` → USERS(id_user)
- `id_agence_fk` → AGENCES(id_agence)

**Contraintes d'intégrité**:
- `date_depart < date_arrivee`
- `places_reservees ≤ places_total`
- `places_total > 0`
- `tarif_unitaire > 0`

**Indices**: `statut`, `utilisateur_id`, `id_agence_fk`, `date_depart`, `(lieu_depart, lieu_arrivee)`

---

## 2. ASSOCIATIONS (Implicites dans MLD)

### 2.1 Association: CRÉE
- **Entités**: USERS → TRAJETS
- **Cardinalité**: UN à PLUSIEURS (1,N)
- **Signification**: Un chauffeur (USERS) crée 0 ou plusieurs trajets (TRAJETS)
- **Type**: Implicite via FK `utilisateur_id`

### 2.2 Association: GÈRE
- **Entités**: AGENCES → TRAJETS
- **Cardinalité**: UN à PLUSIEURS (1,N)
- **Signification**: Une agence (AGENCES) gère 0 ou plusieurs trajets (TRAJETS)
- **Type**: Implicite via FK `id_agence_fk`

### 2.3 Association: AFFILIE
- **Entités**: AGENCES → USERS
- **Cardinalité**: UN à PLUSIEURS (1,N)
- **Signification**: Une agence (AGENCES) affilie 1 ou plusieurs utilisateurs (USERS)
- **Type**: Implicite via FK `id_agence_fk` dans USERS

---

## 3. MODÈLE LOGIQUE (MLD)

### Schéma Normalisé (3NF)

```
AGENCES(id_agence, nom, adresse, ville, code_postal, telephone, email, capacite_max, statut, date_creation)

USERS(id_user, nom, prenom, email, password, telephone, type_user, statut, id_agence_fk#, note_moyenne, date_inscription)

TRAJETS(id_trajet, utilisateur_id#, id_agence_fk#, lieu_depart, lieu_arrivee, lat_depart, long_depart, lat_arrivee, long_arrivee, date_depart, date_arrivee, places_total, places_reservees, tarif_unitaire, statut, notes_speciales, date_creation)
```

**Légende**:
- `#` = Clé étrangère (Foreign Key)
- PK = Clé primaire
- FK = Clé étrangère

---

## 4. RÈGLES MÉTIER

### 4.1 Contraintes d'intégrité

| Règle | Entité | Type | Description |
|-------|--------|------|-------------|
| R1 | USERS.password | CHECK | VARCHAR(255) pour bcrypt |
| R2 | USERS.note_moyenne | CHECK | 0.00 ≤ note_moyenne ≤ 5.00 |
| R3 | TRAJETS.places_total | CHECK | places_total > 0 |
| R4 | TRAJETS.places_reservees | CHECK | 0 ≤ places_reservees ≤ places_total |
| R5 | TRAJETS.tarif_unitaire | CHECK | tarif_unitaire > 0 |
| R6 | TRAJETS.dates | CHECK | date_depart < date_arrivee |
| R7 | AGENCES.email | UNIQUE | Emails uniques par agence |
| R8 | USERS.email | UNIQUE | Emails uniques par utilisateur |

### 4.2 Énumérations

| Entité | Colonne | Valeurs |
|--------|---------|---------|
| USERS | type_user | 'admin', 'chauffeur', 'passager' |
| USERS | statut | 'actif', 'suspendu', 'inactif' |
| TRAJETS | statut | 'programmé', 'en_cours', 'complété', 'annulé' |
| AGENCES | statut | 'actif', 'inactif', 'suspendu' |

### 4.3 Règles fonctionnelles

- **R9**: Chaque utilisateur DOIT être affilié à une agence (FK NOT NULL)
- **R10**: Chaque trajet DOIT avoir un chauffeur (FK NOT NULL, utilisateur_id)
- **R11**: Chaque trajet DOIT être géré par une agence (FK NOT NULL, id_agence_fk)
- **R12**: Les réservations ne peuvent pas dépasser la capacité du trajet
- **R13**: Les dates de trajets doivent être en 2026 (futur)
- **R14**: Les mots de passe sont hashés en bcrypt (cost=12)

---

## 5. DIAGRAMME ENTITÉ-ASSOCIATION (ER)

```
┌─────────────────┐
│    AGENCES      │
├─────────────────┤
│ id_agence (PK)  │
│ nom             │
│ adresse         │
│ ville           │
│ code_postal     │
│ telephone       │
│ email (UNIQUE)  │
│ capacite_max    │
│ statut          │
│ date_creation   │
└────────┬────────┘
         │
         │ (1,N) AFFILIE
         │
         ▼
┌─────────────────────────┐          ┌──────────────────┐
│        USERS            │          │      TRAJETS     │
├─────────────────────────┤          ├──────────────────┤
│ id_user (PK)            │          │ id_trajet (PK)   │
│ nom                     │◄─────────┤ utilisateur_id(FK)
│ prenom                  │ (1,N)    │ id_agence_fk(FK) │
│ email (UNIQUE)          │  CRÉE    │ lieu_depart      │
│ password (VARCHAR 255)  │          │ lieu_arrivee     │
│ telephone               │          │ lat_depart       │
│ type_user (ENUM)        │          │ long_depart      │
│ statut                  │          │ lat_arrivee      │
│ id_agence_fk (FK)───────┼─────────┤ long_arrivee     │
│ note_moyenne            │          │ date_depart      │
│ date_inscription        │          │ date_arrivee     │
└─────────────────────────┘ (1,N)    │ places_total     │
                          GÈRE       │ places_reservees │
                            │        │ tarif_unitaire   │
                            │        │ statut           │
                            │        │ notes_speciales  │
                            │        │ date_creation    │
                            │        └──────────────────┘
                            │
                            ▼
                   (FK) id_agence_fk
```

---

## 6. VUES MATÉRIALISÉES

### 6.1 v_trajets_disponibles
Trajets actuellement disponibles avec places libres

```sql
SELECT 
    t.id_trajet, t.lieu_depart, t.lieu_arrivee, t.date_depart, t.date_arrivee,
    t.places_total, t.places_reservees,
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
```

### 6.2 v_profil_utilisateur
Profils utilisateurs avec statistiques

### 6.3 v_chiffres_agence
Statistiques financières par agence

---

## 7. DÉPENDANCES FONCTIONNELLES

```
AGENCES:
  id_agence → {nom, adresse, ville, code_postal, telephone, email, capacite_max, statut, date_creation}

USERS:
  id_user → {nom, prenom, email, password, telephone, type_user, statut, id_agence_fk, note_moyenne, date_inscription}

TRAJETS:
  id_trajet → {utilisateur_id, id_agence_fk, lieu_depart, lieu_arrivee, lat_depart, long_depart, lat_arrivee, long_arrivee, date_depart, date_arrivee, places_total, places_reservees, tarif_unitaire, statut, notes_speciales, date_creation}
```

---

## 8. NOTES IMPORTANTES

### Changements majeurs (v1 → v2)

1. **Simplification du schéma**:
   - ❌ SUPPRIMÉES: RESERVATIONS, EVALUATIONS, PAIEMENTS, autres entités complexes
   - ✅ CONSERVÉES: agences, users (UTILISATEURS renommé), trajets

2. **Harmonisation des noms**:
   - `UTILISATEURS` → `users` (EN)
   - `AGENCES` → `agences` (maintenu)
   - `TRAJETS` → `trajets` (maintenu)
   - Colonnes: `id_auteur` → `utilisateur_id`, `mot_de_passe` → `password`

3. **Sécurité des mots de passe**:
   - `password VARCHAR(255)` pour accommoder les hashes bcrypt (~60 caractères)
   - Algorithme: bcrypt (cost=12)

4. **Dates futures**:
   - Toutes les dates en 2026 (testé comme données futures)
   - Contrainte: `date_depart < date_arrivee`

5. **Normalisation 3NF**:
   - Dépendances fonctionnelles correctes
   - Pas de colonnes dérivées
   - Clés candidates uniques

---

## 9. INDICES (INDEX)

Pour optimiser les requêtes:

| Table | Colonne(s) | Type | Raison |
|-------|-----------|------|--------|
| agences | statut | SIMPLE | Filtrage par état |
| agences | ville | SIMPLE | Recherche par localité |
| users | type_user | SIMPLE | Filtrage par rôle |
| users | id_agence_fk | SIMPLE | Jointure avec agences |
| users | email | SIMPLE | Authentification |
| trajets | statut | SIMPLE | Filtrage état trajet |
| trajets | utilisateur_id | SIMPLE | Trajets d'un chauffeur |
| trajets | id_agence_fk | SIMPLE | Trajets d'une agence |
| trajets | date_depart | SIMPLE | Recherche temporelle |
| trajets | (lieu_depart, lieu_arrivee) | COMPOSITE | Recherche géographique |

---

## 10. CARACTÉRISTIQUES TECHNIQUES

- **SGBD**: MySQL 8.0+ ou MariaDB 10.3+
- **Encoding**: UTF8MB4 (support emojis, accents)
- **Moteur**: InnoDB (transactions ACID)
- **Charsets**: `utf8mb4_unicode_ci` (collation insensible à la casse, à la casse)
- **Foreign Keys**: ON DELETE RESTRICT, ON UPDATE CASCADE (sécurité)

---

## 11. HISTORIQUE DES VERSIONS

| Version | Date | Changements |
|---------|------|-------------|
| 1.0 | 2024 | Design initial (complexe, 6 entités) |
| 2.0 | 2025-07 | Simplification 3 entités, corrections noms, dates 2026 |

---

## Auteur
TOUCHE PAS AU KLAXON Team  
Version: 2.0 (Mérise complète)

