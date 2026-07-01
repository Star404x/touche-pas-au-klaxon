# TOUCHE PAS AU KLAXON

Application MVC de partage de trajets avec système de communication responsable.

## Architecture

```
touche-pas-au-klaxon/
├── public/
│   └── index.php                 # Point d'entrée
├── src/
│   ├── Controllers/              # Contrôleurs MVC
│   │   ├── HomeController.php
│   │   ├── AuthController.php
│   │   ├── TripController.php
│   │   └── AdminController.php
│   ├── Models/                   # Modèles de données
│   │   ├── Model.php
│   │   ├── User.php
│   │   ├── Agence.php
│   │   └── Trajet.php
│   ├── Middleware/               # Middleware
│   │   ├── AuthMiddleware.php
│   │   ├── AdminMiddleware.php
│   │   └── DeviceDetectionMiddleware.php
│   ├── Views/                    # Templates
│   │   ├── home/
│   │   ├── auth/
│   │   └── errors/
│   ├── Services/                 # Services métier
│   └── Router.php                # Configuration des routes
├── config/
│   ├── bootstrap.php             # Initialisation
│   ├── constants.php             # Constantes globales
│   └── Database.php              # Connexion PDO
├── tests/
│   ├── Models/
│   ├── Controllers/
│   ├── Middleware/
│   └── Services/
├── logs/                         # Fichiers de log
├── cache/                        # Cache d'application
├── composer.json                 # Dépendances PHP
├── phpunit.xml                   # Configuration PHPUnit
├── .env.example                  # Exemple d'variables d'environnement
├── .gitignore                    # Fichiers ignorés Git
└── README.md                     # Cette documentation
```

## Installation

### Prérequis

- PHP 8.1+
- Composer
- Base de données (PostgreSQL, MySQL ou SQLite)

### Étapes

1. **Cloner le projet**
   ```bash
   git clone <repository>
   cd touche-pas-au-klaxon
   ```

2. **Installer les dépendances**
   ```bash
   composer install
   ```

3. **Configurer l'environnement**
   ```bash
   cp .env.example .env
   # Éditer .env avec vos paramètres
   ```

4. **Créer la base de données**
   ```sql
   -- PostgreSQL exemple
   CREATE DATABASE klaxon_db;
   CREATE USER klaxon_user WITH PASSWORD 'password';
   GRANT ALL PRIVILEGES ON DATABASE klaxon_db TO klaxon_user;
   ```

5. **Déployer les tables** (script à créer)
   ```bash
   php bin/migrate.php
   ```

6. **Démarrer le serveur**
   ```bash
   php -S localhost:8000 -t public/
   ```

## Utilisation

### Authentification

```bash
# Inscription
POST /register
{
  "firstname": "John",
  "lastname": "Doe",
  "email": "john@example.com",
  "password": "SecurePass123",
  "password_confirm": "SecurePass123"
}

# Connexion
POST /login
{
  "email": "john@example.com",
  "password": "SecurePass123"
}

# Déconnexion
POST /logout
```

### Gestion des trajets

```bash
# Créer un trajet
POST /api/trajet
{
  "origin": "Paris",
  "destination": "Lyon",
  "departure_time": "2024-07-01 09:00:00",
  "estimated_duration": 480,
  "agence_id": 1
}

# Rejoindre un trajet
POST /api/trajet/1/join

# Quitter un trajet
POST /api/trajet/1/leave

# Utiliser le klaxon
POST /api/trajet/1/horn
{
  "severity": 2,
  "message": "Attention!"
}
```

### Administration

```bash
# Tableau de bord
GET /admin

# Gérer les utilisateurs
GET /admin/users
PUT /admin/user/1/toggle
PUT /admin/user/1/role
DELETE /admin/user/1

# Statistiques globales
GET /admin/stats
```

## Tests

### Exécuter les tests

```bash
# Tous les tests
composer test

# Avec couverture
composer test:coverage

# Test spécifique
phpunit tests/Models/UserTest.php
```

### Couverture requise

- **Objectif:** >80% de couverture de code
- **Format:** Rapport HTML dans `coverage/`

## Modèles

### User
- **Propriétés:** id, firstname, lastname, email, password, role, is_active, avatar_url, bio, rating, created_at, updated_at
- **Rôles:** admin, user, driver, agence
- **Méthodes CRUD:** find, create, update, delete, findByEmail, authenticate

### Agence
- **Propriétés:** id, name, slug, siret, email, phone, address, city, postal_code, country, website, logo_url, description, is_active, rating, created_at, updated_at
- **Méthodes:** findBySlug, getTrajects, countActiveTrajects, activate, deactivate, getStats

### Trajet
- **Propriétés:** id, origin, destination, departure_time, driver_id, agence_id, passengers_count, horns_count, status, estimated_duration, started_at, ended_at, cancellation_reason, created_at, updated_at
- **Statuts:** pending, confirmed, in_progress, completed, cancelled
- **Méthodes:** addPassenger, removePassenger, useHorn, confirm, start, complete, cancel, getAvailableSeats, getRemainingHorns

## Middleware

### AuthMiddleware
- Vérifie l'authentification utilisateur
- Gère connexion/déconnexion
- Stocke l'utilisateur en session

### AdminMiddleware
- Étend AuthMiddleware
- Vérifie les droits administrateur
- Valide les rôles

### DeviceDetectionMiddleware
- Détecte le type de périphérique (mobile, tablet, desktop)
- Stocke en session pour adaptation du contenu

## Constantes

Voir `config/constants.php` pour:
- Rôles: `ROLE_ADMIN`, `ROLE_USER`, `ROLE_DRIVER`, `ROLE_AGENCE`
- Statuts trajet: `TRAJET_STATUS_PENDING`, `TRAJET_STATUS_CONFIRMED`, etc.
- Klaxons: `MAX_HORNS_PER_TRIP`, `HORN_SEVERITY_*`
- Validation: `EMAIL_REGEX`, `PHONE_REGEX`, etc.

## Structure des tests

```
tests/
├── Models/
│   ├── UserTest.php              # Tests du modèle User
│   ├── TrajetTest.php            # Tests du modèle Trajet
│   └── AgenceTest.php            # Tests du modèle Agence
├── Controllers/
│   ├── AuthControllerTest.php    # Tests du contrôleur Auth
│   └── TripControllerTest.php    # Tests du contrôleur Trips
├── Middleware/
│   ├── AuthMiddlewareTest.php    # Tests du middleware Auth
│   └── AdminMiddlewareTest.php   # Tests du middleware Admin
└── Services/
    └── NotificationServiceTest.php
```

## Autoloading PSR-4

L'application utilise l'autoloading PSR-4:

```json
{
  "autoload": {
    "psr-4": {
      "KlaxonApp\\": "src/"
    }
  }
}
```

## Sécurité

- **Mots de passe:** Hachés avec bcrypt (cost 12)
- **Sessions:** Timeout de 1 heure
- **CSRF:** Tokens de 32 caractères
- **Validation:** Email, téléphone avec regex
- **Base de données:** Prepared statements via PDO

## Performance

- Routes cachées en production (voir `ROUTES_CACHE`)
- Middleware optimisé pour réduire les requêtes DB
- Pagination : 20-50 résultats par défaut
- Indexes DB recommandés sur : user_id, agence_id, status

## Logging

Les logs sont écrits dans `logs/YYYY-MM-DD.log`

```php
log('Message d\'information');
log('Erreur grave', 'error');
```

## API REST

L'application expose une API RESTful:

- **Format:** JSON
- **Authentification:** Session
- **Pagination:** ?page=1&limit=20
- **Erreurs:** Code HTTP + message JSON

## Contribution

1. Respecter le style de code PSR-12
2. Ajouter des tests pour chaque nouvelle fonctionnalité
3. Documenter avec des DocBlocks
4. Commiter avec messages clairs

## Licence

MIT

## Support

Pour toute question ou problème, contacter: team@klaxon.local
