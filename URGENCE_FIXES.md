# 🔴 URGENCE - CORRECTIONS CRITIQUES EN COURS

**Statut:** Agent en travail  
**Priorité:** CRITIQUE  

---

## ✅ FIXES EN PROGRESS

### 🚨 POINT 3 (CRITIQUE): Unification schema BD
**Status:** 🔄 EN COURS
- Renommer tables vers noms simples (users, agences, trajets)
- Harmoniser colonnes (password vs mot_de_passe)
- FK cohérentes (utilisateur_id)
- Script création EXACT MATCH code PHP

### 🚨 POINT 5 (CRITIQUE): Dates alimentation
**Status:** 🔄 EN COURS
- Changer 2025 → 2026 (dates futures)
- Format: 2026-07-15 à 2026-12-31
- date_depart < date_arrivee
- Test data passable

### 🚨 POINT 6 (CRITIQUE): Admin password
**Status:** 🔄 EN COURS
- Bcrypt hash AdminPass123!
- Bcrypt hash UserPass123!
- Colonne password VARCHAR 255
- Mise à jour scripts SQL

### 🔄 POINT 4: DROP TABLE IF EXISTS
**Status:** 🔄 EN COURS
- Prépend tous CREATE TABLE
- Allow script relance

### 🔄 POINT 9: PSR-4 Autoload
**Status:** 🔄 EN COURS
- public/index.php: load vendor/autoload.php FIRST
- Puis config/bootstrap.php
- Izniburak/router auto-load

### 🔄 POINT 10: Routes/Links mapping
**Status:** 🔄 EN COURS
- Audit src/Router.php
- Audit templates
- Correspondance EXACTE
- docs/ROUTES.md

### 🔄 POINT 11: .env loading
**Status:** 🔄 EN COURS
- Standardize DB_PASSWORD
- Chargement réel .env
- config/constants.php fix

### 🔄 POINT 16: GET/POST routes
**Status:** 🔄 EN COURS
- Logout GET ou POST form
- Template links match routes

### 🔄 POINT 8: Cleanup entities
**Status:** 🔄 EN COURS
- Keep ONLY: users, agences, trajets
- Remove extra tables
- Update MCD/MLD

### 🔄 POINT 1: Merise MCD formel
**Status:** 🔄 EN COURS
- Verb-based associations
- Complete Merise cardinalities
- Conceptual + Logical separation

---

## 📁 FICHIERS À GÉNÉRER

- [ ] `database/script_creation_fixed.sql` (BD corrigée)
- [ ] `database/script_alimentation_fixed.sql` (Données 2026)
- [ ] `docs/MCD_MERISE.md` (Merise formel)
- [ ] `docs/ROUTES.md` (All routes mapped)
- [ ] `docs/CORRECTIONS_APPLIED.md` (Summary)

---

**Agent travaille... Attente des résultats...** ⏳

