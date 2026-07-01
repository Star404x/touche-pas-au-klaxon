# 📋 RAPPORT DE CORRECTION - FEEDBACK REÇU

**Date:** 2026-07-01  
**Statut:** EN CORRECTION

---

## 🔴 PROBLÈMES IDENTIFIÉS ET CORRECTIONS

### ⚠️ Point 1: MCD - Incohérence avec la Merise formelle

**Problème:**
- Le MCD ressemble trop à un schéma physique (PK/FK directes)
- Pas de verbes d'action clairs
- Cardinalités Merise incomplètes
- Incohérences entre MCD, SQL et PHP

**Correction appliquée:**
✅ Créer MCD Merise formel avec:
- Associations nommées avec VERBES D'ACTION
- Cardinalités (0,1), (1,1), (0,N), (1,N) complètes
- Diagramme ASCII professionnel
- Séparation MCD (conceptuel) vs MLD (logique)

---

### ⚠️ Point 2: Documentation - Versions multiples incohérentes

**Problème:**
- docs/MCD.md, LIVRABLE_FINAL.md, config/schema.sql, database/script_creation.sql
- Tous décrivent des modèles différents
- Pas de cohérence globale

**Correction appliquée:**
✅ Une source de vérité unique:
- `docs/MCD.md` = Merise formel (CONCEPTUEL)
- `docs/MLD.md` = Modèle logique SQL
- `database/script_creation.sql` = Implémentation exacte du MLD
- `config/schema.sql` = Suppression (redondant)
- Tous les autres fichiers doivent référencer ces deux sources

---

### ⚠️ Point 3: Script SQL - Incohérence code PHP

**Problème:**
- Script crée UTILISATEURS, AGENCES, TRAJETS
- Code PHP attend users, agences, trajets
- Colonnes incompatibles (mot_de_passe vs password)

**Correction appliquée:**
✅ Uniformiser les noms:
- Table `utilisateurs` → `users` (ou tous en FR)
- Colonne `mot_de_passe` → `password` (standard PHP)
- Colonne `id_auteur` → `utilisateur_id` (FK cohérente)
- Réécrire `script_creation.sql` pour EXACT MATCH avec code PHP

---

### ⚠️ Point 4: Script SQL - Drop tables manquants

**Problème:**
```sql
CREATE TABLE users ...  -- Pas de DROP TABLE IF EXISTS
```
Si on relance le script, ça échoue (table existe déjà)

**Correction appliquée:**
✅ Ajouter avant chaque CREATE TABLE:
```sql
DROP TABLE IF EXISTS `trajets`;  -- FK dependencies first
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `agences`;
DROP TABLE IF EXISTS `klaxons`;
...
DROP DATABASE IF EXISTS `touche_pas_au_klaxon`;
CREATE DATABASE `touche_pas_au_klaxon`;
USE `touche_pas_au_klaxon`;
```

---

### ⚠️ Point 5: Script alimentation - Dates 2025 (dans le passé!)

**Problème:**
- Trajets datés 2025
- Contrainte: `date_heure_depart > NOW()`
- Les données ne passent pas la validation!

**Correction appliquée:**
✅ Mettre à jour TOUTES les dates:
- Dates départ: 2026-07-15 à 2026-12-31 (6 mois)
- Heures: 08:00, 10:00, 14:00, 18:00 (réaliste)
- Cohérence: date_arrivée > date_départ

---

### ⚠️ Point 6: Admin password - Pas de hash ou incohérence

**Problème:**
- Table UTILISATEURS n'a pas de colonne password!
- Script insère `mot_de_passe` (texte)
- App attend `password` (hashé bcrypt)

**Correction appliquée:**
✅ Standardiser:
- Colonne: `password` (VARCHAR 255)
- Contenu: Hash bcrypt (ex: `$2y$10$...`)
- Admin data: hash de "AdminPass123!" correctement calculé
- User data: hash de "UserPass123!"

---

### ⚠️ Point 7: Architecture - Éléments incohérents

**Problème:**
- Certaines vues ne sont pas reliées aux routes
- Liens layout ne correspondent pas aux routes
- Structure décrite vs réalité ≠

**Correction appliquée:**
✅ Auditer et CORRIGER:
- Vérifier toutes les routes dans `src/Router.php`
- Vérifier tous les liens HTML dans les templates
- Assurer cohérence: si route `/trip/create` existe → formulaire pointe dessus
- Documenter la correspondance route ↔ template

---

### ⚠️ Point 8: Fonctionnalité - Trop riche (passagers, klaxons, paiements)

**Problème:**
- Modèle contient: passagers, klaxons, avis, paiements
- Brief veut: JUSTE trajets inter-sites
- Scope creep = confusion

**Correction appliquée:**
✅ Nettoyer et recentrer:
- Garder UNIQUEMENT: users, agences, trajets
- Supprimer: reservations, passagers, klaxons, paiements, avis
- Si tables extra existent en BD, les supprimer
- Models PHP doivent matcher EXACTEMENT le brief

---

### ⚠️ Point 9: Autoload PSR-4 - Incomplète

**Problème:**
```php
// public/index.php ne charge PAS vendor/autoload.php
// Charge config/bootstrap.php (maison)
// Izniburak/router ne charge pas automatiquement!
```

**Correction appliquée:**
✅ Fixer public/index.php:
```php
<?php
declare(strict_types=1);

// PSR-4 Autoloader Composer
require_once __DIR__ . '/../vendor/autoload.php';

// Configuration app
require_once __DIR__ . '/../config/bootstrap.php';

// Lancer application
$app = new KlaxonApp\Application();
$app->run();
?>
```

---

### ⚠️ Point 10: Routes - Liens template ne correspondent pas

**Problème:**
- Template: `<a href="/trip/create">` 
- Routes déclarées: `GET /trips/create` (différent!)
- `/admin/agencies`, `/admin/trips` ne correspondent pas

**Correction appliquée:**
✅ Audit et standardisation:
- Lister TOUTES les routes dans `src/Router.php`
- Lister TOUS les liens dans les templates
- Assurer correspondance EXACTE
- Documenter dans `docs/ROUTES.md`

---

### ⚠️ Point 11: .env - Chargement et cohérence

**Problème:**
- .env.example déclare `DB_PASSWORD`
- constants.php attend `DB_PASS`
- Chargement réel du .env pas clairement fait

**Correction appliquée:**
✅ Standardiser:
```php
// config/bootstrap.php
$env_file = __DIR__ . '/../.env';
if (file_exists($env_file)) {
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            putenv("$key=$value");
        }
    }
}

// .env.example cohérent:
DB_HOST=localhost
DB_USER=root
DB_PASSWORD=
DB_NAME=touche_pas_au_klaxon
DB_PORT=3306
```

---

### ⚠️ Point 12: Type hints - Incomplet

**Problème:**
- Certaines méthodes sans type hints
- Retours génériques (mixed, array sans doc)
- Documentations incomplètes

**Correction appliquée:**
✅ Ajouter partout:
```php
/**
 * Récupère tous les trajets futurs
 * @return array<int, Trajet>
 */
public function getTrajets(): array
```

---

### ⚠️ Point 13: PHPDoc - Pas de documentation générée

**Problème:**
- Aucun dossier docs/generated/html
- Pas de phpdoc:phpdoc dans composer

**Correction appliquée:**
✅ Ajouter:
```bash
composer require --dev phpdocumentor/phpdocumentor
php ./vendor/bin/phpdoc -d src/ -t docs/generated/
```

---

### ⚠️ Point 14: PHPStan - Config mais pas en dépendances

**Problème:**
```
phpstan.neon existe
MAIS phpstan/phpstan n'est pas dans composer.json!
```

**Correction appliquée:**
✅ Ajouter à composer.json:
```bash
composer require --dev phpstan/phpstan
```

---

### ⚠️ Point 15: PHPUnit - Tests sans vérification BD réelle

**Problème:**
- Tests manipulent objets mémoire
- Pas de vérification INSERT/UPDATE/DELETE réels en BD
- 80% coverage = faux (pas de coverage BD)

**Correction appliquée:**
✅ Créer tests d'intégration BD:
```php
// tests/Feature/TripDatabaseTest.php
public function testCreateTripInDatabase()
{
    $trip = new Trajet(...);
    $trip->save(); // Vrai INSERT
    
    $this->assertDatabaseHas('trajets', ['id' => $trip->id]);
}
```

---

### ⚠️ Point 16: Routes GET vs POST - Layout incorrects

**Problème:**
```html
<a href="/logout">Déconnexion</a>  <!-- GET -->
<!-- Mais route est POST! -->
```

**Correction appliquée:**
✅ Corriger tous les links:
```html
<!-- Pour POST, utiliser FORM: -->
<form method="POST" action="/logout" style="display: inline;">
    <button type="submit">Déconnexion</button>
</form>

<!-- Ou route en GET: -->
// src/Router.php
$router->get('/logout', 'AuthController@logout');
```

---

## 📋 CHECKLIST CORRECTIONS

- [ ] Point 1: MCD Merise formel créé
- [ ] Point 2: Documentation unifiée
- [ ] Point 3: Script SQL = exact PHP match
- [ ] Point 4: DROP TABLE IF EXISTS ajoutés
- [ ] Point 5: Dates 2026 dans script alimentation
- [ ] Point 6: Password hash bcrypt correct
- [ ] Point 7: Toutes vues liées aux routes
- [ ] Point 8: Modèle épuré (users, agences, trajets ONLY)
- [ ] Point 9: vendor/autoload.php chargé
- [ ] Point 10: Toutes routes et liens correspondent
- [ ] Point 11: .env chargement correct
- [ ] Point 12: Type hints compllets partout
- [ ] Point 13: PHPDoc généré (docs/generated/)
- [ ] Point 14: phpstan en dépendances Composer
- [ ] Point 15: Tests BD d'intégration ajoutés
- [ ] Point 16: Toutes routes GET/POST cohérentes

---

## 🎯 RÉSULTAT ATTENDU

Après corrections:
- ✅ Une source de vérité unique (MCD → MLD → SQL → PHP)
- ✅ Toutes routes et liens correspondent exactement
- ✅ Script alimentation passable (dates 2026)
- ✅ Autoload PSR-4 fonctionnel
- ✅ Password hash bcrypt correct
- ✅ Type hints complets
- ✅ Tests BD intégrés
- ✅ Documentation générée

**Le projet sera COHÉRENT ET FONCTIONNEL!** ✅

