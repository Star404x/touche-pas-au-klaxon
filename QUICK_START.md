# 🚀 Quick Start - TOUCHE PAS AU KLAXON

Démarrer rapidement le projet en 5 minutes.

## Installation rapide

### 1. Cloner et entrer dans le répertoire
```bash
cd /home/node/.openclaw/workspace/touche-pas-au-klaxon
```

### 2. Installer les dépendances
```bash
composer install
```

### 3. Configurer l'environnement
```bash
cp .env.example .env
```

### 4. Créer la base de données
**Pour SQLite (plus simple pour dev):**
```bash
# La base est automatiquement créée
# Migrer le schéma:
sqlite3 database.sqlite < config/schema.sql
```

**Pour PostgreSQL:**
```bash
psql -U postgres -c "CREATE DATABASE klaxon_db"
psql -U postgres -d klaxon_db < config/schema.sql
```

**Pour MySQL:**
```bash
mysql -u root -p < config/schema.sql
```

### 5. Démarrer le serveur
```bash
php -S localhost:8000 -t public/
```

### 6. Accéder à l'application
```
http://localhost:8000
```

## Routes principales

### Publiques
- `GET /` - Accueil
- `GET /trajets` - Liste des trajets
- `GET /agences` - Liste des agences
- `GET /about` - À propos
- `GET /contact` - Contact

### Authentification
- `GET /login` - Formulaire connexion
- `POST /login` - Connexion
- `GET /register` - Formulaire inscription
- `POST /register` - Inscription
- `POST /logout` - Déconnexion
- `GET /profile` - Mon profil

### API Trajets
- `GET /api/trajets` - Lister
- `POST /api/trajet` - Créer
- `GET /api/trajet/:id` - Détails
- `PUT /api/trajet/:id` - Modifier
- `DELETE /api/trajet/:id` - Supprimer
- `POST /api/trajet/:id/join` - Rejoindre
- `POST /api/trajet/:id/leave` - Quitter
- `POST /api/trajet/:id/horn` - Klaxon

### Administration (authentification admin requise)
- `GET /admin` - Tableau de bord
- `GET /admin/users` - Utilisateurs
- `GET /admin/agences` - Agences
- `GET /admin/trajets` - Trajets
- `GET /admin/stats` - Statistiques

## Tests unitaires

### Lancer tous les tests
```bash
composer test
```

### Générer le rapport de couverture
```bash
composer test:coverage
```

### Lancer un test spécifique
```bash
phpunit tests/Models/UserTest.php
phpunit tests/Controllers/AuthControllerTest.php
```

## Données de test

### Créer un utilisateur admin en base

```bash
php -r "
require 'config/bootstrap.php';
\$user = \KlaxonApp\Models\User::create([
    'firstname' => 'Admin',
    'lastname' => 'Administrateur',
    'email' => 'admin@klaxon.local',
    'password' => 'AdminPass123',
    'role' => ROLE_ADMIN,
    'is_active' => true,
]);
echo 'Admin créé avec ID: ' . \$user->id;
"
```

### Se connecter
- Email: `admin@klaxon.local`
- Password: `AdminPass123`

## Structure du projet

```
touche-pas-au-klaxon/
├── public/               → Point d'entrée web
├── src/
│   ├── Controllers/      → Logique métier
│   ├── Models/          → Accès données
│   ├── Middleware/      → Middleware HTTP
│   ├── Services/        → Services métier
│   └── Views/           → Templates (optionnel)
├── config/
│   ├── bootstrap.php    → Initialisation
│   ├── constants.php    → Constantes globales
│   └── Database.php     → Connexion PDO
├── tests/               → Tests unitaires
├── logs/                → Logs application
└── composer.json        → Dépendances PHP
```

## Troubleshooting

### Erreur: "Class not found"
```bash
# Vérifier que l'autoloading est correct:
composer dump-autoload
```

### Erreur de connexion BD
```bash
# Vérifier les variables d'environnement dans .env
# Vérifier que la base de données existe
# Vérifier les permissions utilisateur DB
```

### Permission denied sur logs/
```bash
chmod -R 755 logs/
chmod -R 755 cache/
```

### Port 8000 déjà utilisé
```bash
php -S localhost:8001 -t public/
```

## Fichiers de configuration

### .env
```env
APP_ENV=development
DB_DRIVER=sqlite
DB_NAME=database.sqlite
```

### config/constants.php
- Rôles: `ROLE_ADMIN`, `ROLE_USER`, etc.
- Statuts: `TRAJET_STATUS_PENDING`, etc.
- Klaxon: `MAX_HORNS_PER_TRIP`, `HORN_SEVERITY_*`

## Architecture

```
HTTP Request
    ↓
Router (izniburak/router)
    ↓
Middleware Stack (Auth, Device, etc.)
    ↓
Controller (validation, logique)
    ↓
Model (CRUD via PDO)
    ↓
Database (SQLite/PostgreSQL/MySQL)
    ↓
HTTP Response (JSON/HTML)
```

## Exemples d'API

### Créer un trajet
```bash
curl -X POST http://localhost:8000/api/trajet \
  -H "Content-Type: application/json" \
  -d '{
    "origin": "Paris",
    "destination": "Lyon",
    "departure_time": "2024-07-01 09:00:00",
    "estimated_duration": 480,
    "agence_id": 1
  }'
```

### Rejoindre un trajet
```bash
curl -X POST http://localhost:8000/api/trajet/1/join
```

### Utiliser le klaxon
```bash
curl -X POST http://localhost:8000/api/trajet/1/horn \
  -H "Content-Type: application/json" \
  -d '{
    "severity": 2,
    "message": "Attention!"
  }'
```

### Lister les trajets
```bash
curl http://localhost:8000/api/trajets?page=1
```

## Mode développement

```php
// Dans .env
APP_ENV=development

// Cela active:
// - Affichage des erreurs
// - Debug mode
// - Logs détaillés
```

## Mode production

```php
// Dans .env
APP_ENV=production

// Cela désactive:
// - Affichage des erreurs
// - Debug info en logs
// - Cache activé
```

## Modules clés

### User Model
```php
use KlaxonApp\Models\User;

// Authentification
$user = User::authenticate('email@example.com', 'password');

// Hasher mot de passe
$hash = User::hashPassword('mypassword');
User::verifyPassword('mypassword', $hash); // true

// Gestion rôles
$user->hasRole(ROLE_ADMIN);
$user->isAdmin();
```

### Trajet Model
```php
use KlaxonApp\Models\Trajet;

// Créer
$trajet = Trajet::create([
    'origin' => 'Paris',
    'destination' => 'Lyon',
    'departure_time' => '2024-07-01 09:00:00',
    'driver_id' => 1,
    'agence_id' => 1,
]);

// Gestion passagers
$trajet->addPassenger(2);
$available = $trajet->getAvailableSeats();

// Klaxon
$trajet->useHorn(1, HORN_SEVERITY_MEDIUM, 'Attention!');
$remaining = $trajet->getRemainingHorns();
```

### Middleware Auth
```php
use KlaxonApp\Middleware\AuthMiddleware;

$auth = new AuthMiddleware();

// Vérifier authentification
if (!$auth->isAuthenticated()) {
    // Non authentifié
}

// Obtenir l'utilisateur
$user = $auth->getUser();

// Authentifier
$auth->authenticate('email@example.com', 'password');

// Déconnecter
$auth->logout();
```

## Documentation complète

Voir les fichiers:
- `README.md` - Documentation générale
- `ARCHITECTURE.md` - Design patterns et flux
- `DELIVERABLES.md` - Checklist complète

---

**Prêt?** Allez à http://localhost:8000 ! 🎉

Besoin d'aide? Consultez la documentation ou posez une question.
