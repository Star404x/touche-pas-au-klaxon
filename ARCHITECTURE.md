# Architecture MVC - TOUCHE PAS AU KLAXON

Document détaillé de l'architecture et des patterns utilisés.

## Vue d'ensemble

Architecture **MVC** (Model-View-Controller) avec **API REST** et **Middleware**.

```
                           ┌─────────────────┐
                           │    Router       │
                           └────────┬────────┘
                                    │
                ┌───────────────────┼───────────────────┐
                │                   │                   │
            ┌───▼────┐         ┌────▼───┐         ┌────▼────┐
            │Middleware    │    │Controller      │    │  Model  │
            │   Stack   │    │                │         │ Layer  │
            └──────────┘    └────────────────┘        └────────┘
                                    │
                           ┌────────▼────────┐
                           │    Database     │
                           │     (PDO)       │
                           └─────────────────┘
```

## Couches de l'application

### 1. **Couche Routage (Router)**

Fichier: `src/Router.php`

- Utilise la bibliothèque `izniburak/router`
- Enregistre toutes les routes (GET, POST, PUT, DELETE)
- Mappe les URLs vers les contrôleurs/méthodes

```php
self::$router->post('/api/trajet', [TripController::class, 'create']);
```

### 2. **Couche Middleware**

Répertoire: `src/Middleware/`

**Middleware de base:** `Middleware.php`
- Classe abstraite pour tous les middleware
- Méthodes communes : `getRequestValue()`, `setSessionValue()`, etc.

**Middleware spécialisés:**

#### AuthMiddleware
- Vérifie l'authentification utilisateur
- Récupère l'utilisateur de la session
- Authentifie/déconnecte l'utilisateur

```php
$auth = new AuthMiddleware();
if (!$auth->isAuthenticated()) {
    $auth->sendError(401, 'Non authentifié');
}
```

#### AdminMiddleware
- Étend `AuthMiddleware`
- Vérifie les droits administrateur
- Valide les rôles utilisateur

```php
$admin = new AdminMiddleware();
$admin->handle(); // Vérifie admin
```

#### DeviceDetectionMiddleware
- Détecte le type de périphérique (mobile, tablet, desktop)
- Stocke en session pour adaptation du contenu
- Fournit résolution recommandée

```php
$device = new DeviceDetectionMiddleware();
if ($device->isMobile()) {
    // Afficher layout mobile
}
```

### 3. **Couche Contrôleur (Controller)**

Répertoire: `src/Controllers/`

**Classe de base:** `Controller.php`
- Méthodes HTTP : `json()`, `error()`, `success()`
- Validation : `validate()`, `getInput()`
- Redirection : `redirect()`, `setHeader()`

#### HomeController
Routes publiques :
- `/` : Page d'accueil
- `/about` : À propos
- `/contact` : Formulaire de contact
- `/trajets` : Liste des trajets
- `/trajet/:id` : Détails trajet
- `/agences` : Liste agences

#### AuthController
Authentification :
- `/register` : Inscription
- `/login` : Connexion
- `/logout` : Déconnexion
- `/profile` : Mon profil
- `/change-password` : Changement mot de passe

#### TripController
API Trajets :
- `GET /api/trajets` : Lister
- `POST /api/trajet` : Créer
- `PUT /api/trajet/:id` : Modifier
- `DELETE /api/trajet/:id` : Supprimer
- `POST /api/trajet/:id/join` : Rejoindre
- `POST /api/trajet/:id/horn` : Utiliser klaxon

#### AdminController
Administration :
- `GET /admin` : Tableau de bord
- `GET /admin/users` : Gérer utilisateurs
- `GET /admin/agences` : Gérer agences
- `GET /admin/trajets` : Gérer trajets
- `GET /admin/stats` : Statistiques

### 4. **Couche Modèle (Model)**

Répertoire: `src/Models/`

**Pattern ORM simplifié** avec PDO.

#### Model.php (Classe de base)
Fonctionnalités CRUD :

```php
// Create
$user = User::create($data);

// Read
$user = User::find($id);
$users = User::all(10, 0);
$user = User::findWhere(['email' => $email]);

// Update
$user->name = 'Nouveau nom';
$user->save();

// Delete
$user->delete();

// Count
$total = User::count();
$active = User::count(['is_active' => true]);
```

Hydration et sérialisation :
```php
$user->hydrate($data);
$array = $user->toArray();
$json = $user->toJson();
```

#### User.php
Modèle utilisateur avec :
- Hash/vérification mot de passe (bcrypt)
- Validation mot de passe
- Gestion des rôles
- Activation/désactivation
- Profil public (sans mot de passe)

```php
$user = User::authenticate('email@example.com', 'password');
$user->changePassword('newpass');
$user->hasRole(ROLE_ADMIN);
```

#### Agence.php
Modèle agence avec :
- SIRET unique
- Slug auto-généré
- Trajets associés
- Statistiques
- Profil public

```php
$agence = Agence::findBySlug('ma-societe');
$trajets = $agence->getTrajects();
$stats = $agence->getStats();
```

#### Trajet.php
Modèle trajet avec :
- Gestion des passagers (sièges disponibles)
- Klaxons (limite par trajet)
- Statuts (pending, confirmed, in_progress, completed, cancelled)
- Durée estimée
- Validation des données

```php
$trajet = Trajet::create($data);
$trajet->addPassenger($userId);
$trajet->useHorn($userId, HORN_SEVERITY_MEDIUM);
$available = $trajet->getAvailableSeats();
$remaining = $trajet->getRemainingHorns();
```

### 5. **Couche Données (Database)**

Fichier: `config/Database.php`

**Singleton PDO** avec support multi-drivers :

```php
$pdo = Database::getInstance();

// Requêtes préparées
$stmt = Database::query('SELECT * FROM users WHERE id = ?', [$id]);

// Helper CRUD
Database::insert('users', $data);
Database::update('users', $data, 'id = ?', [$id]);
Database::delete('users', 'id = ?', [$id]);
```

Drivers supportés :
- PostgreSQL
- MySQL / MariaDB
- SQLite

### 6. **Couche Services**

Répertoire: `src/Services/`

#### NotificationService
Gère les notifications utilisateur :

```php
NotificationService::send($userId, 'type', 'message', $data);
NotificationService::notifyTripCreated($userId, $trajetId);
NotificationService::notifyHornUsed($userId, $trajetId, $severity);
```

## Patterns et Conventions

### Pattern ORM Simplifié
```php
class Model {
    public static function find($id) { }
    public static function create($data) { }
    public function save() { }
    public function delete() { }
}
```

### Pattern Middleware
```php
abstract class Middleware {
    abstract public function handle(): bool;
    protected function sendError($code, $message) { }
}
```

### Pattern Contrôleur
```php
abstract class Controller {
    protected function json($data, $code = 200) { }
    protected function validate($rules) { }
    protected function getInput($key) { }
}
```

### Pattern Singleton (Database)
```php
Database::getInstance(); // Toujours la même instance
```

## Flux de requête HTTP

```
┌─────────────────┐
│  HTTP Request   │
└────────┬────────┘
         │
         ▼
┌─────────────────────┐
│  config/bootstrap   │  Autoload, sessions, constantes
└────────┬────────────┘
         │
         ▼
┌─────────────────────┐
│  public/index.php   │  Point d'entrée
└────────┬────────────┘
         │
         ▼
┌─────────────────────┐
│  Router             │  Route matching
└────────┬────────────┘
         │
         ▼
┌─────────────────────┐
│  Middleware Chain   │  Auth, Device, etc.
└────────┬────────────┘
         │
         ▼
┌─────────────────────┐
│  Controller         │  Métier de la requête
└────────┬────────────┘
         │
         ▼
┌─────────────────────┐
│  Model              │  Accès aux données
└────────┬────────────┘
         │
         ▼
┌─────────────────────┐
│  Database (PDO)     │  Requête SQL
└────────┬────────────┘
         │
         ▼
┌─────────────────────┐
│  HTTP Response      │  JSON, HTML, etc.
└─────────────────────┘
```

## Configuration

### Constantes (`config/constants.php`)

```php
// Environnement
APP_ENV, APP_DEBUG, APP_NAME, APP_VERSION

// Chemins
ROOT_PATH, SRC_PATH, CONFIG_PATH, LOGS_PATH

// Base de données
DB_HOST, DB_PORT, DB_NAME, DB_USER, DB_PASS, DB_DRIVER

// Rôles
ROLE_ADMIN, ROLE_USER, ROLE_DRIVER, ROLE_AGENCE

// Statuts trajet
TRAJET_STATUS_PENDING, TRAJET_STATUS_IN_PROGRESS, etc.

// Klaxon
MAX_HORNS_PER_TRIP, HORN_SEVERITY_LOW, HORN_SEVERITY_MEDIUM, HORN_SEVERITY_HIGH

// Validation
EMAIL_REGEX, PHONE_REGEX, PASSWORD_MIN_LENGTH
```

### Variables d'environnement (`.env`)

```env
APP_ENV=development
DB_DRIVER=pgsql
DB_HOST=localhost
DB_NAME=klaxon_db
DB_USER=klaxon_user
DB_PASS=password
TIMEZONE=Europe/Paris
```

## Tests

### Coverage requis : >80%

#### Tests Unitaires
```
tests/Models/
  ├── UserTest.php
  ├── TrajetTest.php
  └── AgenceTest.php

tests/Controllers/
  ├── AuthControllerTest.php
  └── TripControllerTest.php

tests/Middleware/
  └── AuthMiddlewareTest.php

tests/Services/
  └── NotificationServiceTest.php
```

#### Configuration PHPUnit
- Fichier: `phpunit.xml`
- Coverage HTML: `coverage/`
- Bootstrap: `config/constants.php`
- Driver test: SQLite `:memory:`

## Sécurité

### Mots de passe
- **Algorithme:** BCrypt
- **Cost:** 12
- **Validation:** Min 8 caractères, 1 majuscule, 1 chiffre

### Base de données
- **Prepared statements** (PDO)
- **Pas de SQL injection**

### Sessions
- **Timeout:** 1 heure
- **Stockage serveur**
- **Sécurisation côté middleware**

### Authentification
- Vérification du rôle et de l'activité
- Protection des routes admin

## Performance

### Optimisations
- Middleware court-circuité (fail fast)
- Cache possible des routes
- Pagination par défaut
- Indexes DB recommandés

### Benchmarks recommandés
- Page d'accueil: < 500ms
- API trajet: < 300ms
- Admin: < 1s

## Logs

Fichier: `logs/YYYY-MM-DD.log`

```php
log('Message d\'information');
log('Erreur', 'error');
```

Format: `[YYYY-MM-DD HH:MM:SS] [LEVEL] Message`

## Déploiement

### Production checklist
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] Variables d'environnement configurées
- [ ] Base de données migrée
- [ ] HTTPS activé
- [ ] Permissions fichiers correctes
- [ ] Logs writeable
- [ ] Cache writeable

---

**Version:** 1.0.0  
**Auteur:** TOUCHE PAS AU KLAXON Team  
**Date:** 2024-07-01
