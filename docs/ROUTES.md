# ROUTES ET MAPPINGS - TOUCHE PAS AU KLAXON

## Vue d'ensemble
Document exhaustif des routes PHP définies dans `src/Router.php` et leur correspondance avec les templates HTML.

**Framework**: izniburak/router
**Namespace**: `KlaxonApp\Router`
**Version**: 2.0

---

## 1. ROUTES PUBLIQUES (Pages)

### 1.1 Accueil et Pages Statiques

| Route | Méthode | Controller | Action | Template | Description |
|-------|---------|-----------|--------|----------|-------------|
| `/` | GET | HomeController | index | home/index.php | Page d'accueil |
| `/about` | GET | HomeController | about | home/about.php | À propos |
| `/contact` | GET | HomeController | contact | home/contact.php | Formulaire contact |
| `/contact` | POST | HomeController | contactSubmit | - | Traitement contact |

**Liens attendus dans templates**:
```html
<a href="/">Accueil</a>
<a href="/about">À propos</a>
<a href="/contact">Contact</a>
```

---

### 1.2 Trajets Publics

| Route | Méthode | Controller | Action | Template | Description |
|-------|---------|-----------|--------|----------|-------------|
| `/trajets` | GET | HomeController | trajets | home/trajets.php | Liste tous les trajets |
| `/trajet/:id` | GET | HomeController | trajet | home/trajet.php | Détail d'un trajet |
| `/api/trajets` | GET | TripController | index | JSON | Liste (API) |
| `/api/trajet/:id` | GET | TripController | show | JSON | Détail (API) |

**Liens attendus dans templates**:
```html
<a href="/trajets">Nos trajets</a>
<a href="/trajet/1">Détails trajet</a>
```

---

### 1.3 Agences Publiques

| Route | Méthode | Controller | Action | Template | Description |
|-------|---------|-----------|--------|----------|-------------|
| `/agences` | GET | HomeController | agences | home/agences.php | Liste toutes les agences |
| `/agence/:slug` | GET | HomeController | agence | home/agence.php | Détail d'une agence |

**Liens attendus dans templates**:
```html
<a href="/agences">Nos agences</a>
<a href="/agence/paris-centre">Agence Paris Centre</a>
```

---

## 2. AUTHENTIFICATION

### 2.1 Formulaires d'Authentification

| Route | Méthode | Controller | Action | Template | Description |
|-------|---------|-----------|--------|----------|-------------|
| `/login` | GET | AuthController | loginForm | auth/login.php | Formulaire connexion |
| `/login` | POST | AuthController | login | - | Traitement connexion |
| `/register` | GET | AuthController | registerForm | auth/register.php | Formulaire inscription |
| `/register` | POST | AuthController | register | - | Traitement inscription |
| `/logout` | POST | AuthController | logout | - | Déconnexion |

**⚠️ CRITIQUE (POINT 8)**:
- `/logout` est en **POST** → Nécessite un formulaire HTML
- Les liens `<a>` directs ne fonctionneront PAS
- À utiliser avec form HTML ou wrapper JavaScript

**Formulaire Logout correct**:
```html
<form method="POST" action="/logout">
    <button type="submit">Déconnexion</button>
</form>
```

**Liens attendus**:
```html
<a href="/login">Connexion</a>
<a href="/register">Inscription</a>
```

---

### 2.2 Profil Utilisateur

| Route | Méthode | Controller | Action | Template | Description |
|-------|---------|-----------|--------|----------|-------------|
| `/profile` | GET | AuthController | profile | auth/profile.php | Voir profil |
| `/profile` | POST | AuthController | updateProfile | - | Mettre à jour profil |
| `/change-password` | POST | AuthController | changePassword | - | Changer mot de passe |

**Liens attendus**:
```html
<a href="/profile">Mon profil</a>
```

---

## 3. TRAJETS (API REST)

### 3.1 Opérations CRUD

| Route | Méthode | Controller | Action | Réponse | Description |
|-------|---------|-----------|--------|---------|-------------|
| `/api/trajet` | POST | TripController | create | JSON | Créer un trajet |
| `/api/trajet/:id` | PUT | TripController | update | JSON | Modifier trajet |
| `/api/trajet/:id` | DELETE | TripController | delete | JSON | Supprimer trajet |

### 3.2 Opérations Spéciales

| Route | Méthode | Controller | Action | Réponse | Description |
|-------|---------|-----------|--------|---------|-------------|
| `/api/my-trips` | GET | TripController | myTrips | JSON | Mes trajets |
| `/api/trajet/:id/join` | POST | TripController | join | JSON | Rejoindre trajet |
| `/api/trajet/:id/leave` | POST | TripController | leave | JSON | Quitter trajet |
| `/api/trajet/:id/horn` | POST | TripController | useHorn | JSON | Utiliser klaxon |
| `/api/trajet/:id/stats` | GET | TripController | stats | JSON | Statistiques trajet |

**Appels attendus (JavaScript/fetch)**:
```javascript
// Créer un trajet
fetch('/api/trajet', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({lieu_depart, lieu_arrivee, date_depart, ...})
});

// Rejoindre
fetch('/api/trajet/1/join', {method: 'POST'});

// Utiliser klaxon
fetch('/api/trajet/1/horn', {method: 'POST'});
```

---

## 4. ADMIN (Tableau de bord)

### 4.1 Dashboard

| Route | Méthode | Controller | Action | Template | Description |
|-------|---------|-----------|--------|----------|-------------|
| `/admin` | GET | AdminController | dashboard | admin/dashboard.php | Accueil admin |
| `/admin/stats` | GET | AdminController | globalStats | JSON | Statistiques globales |

**Lien attendu**:
```html
<a href="/admin">Tableau de bord</a>
```

---

### 4.2 Gestion des Utilisateurs

| Route | Méthode | Controller | Action | Template | Description |
|-------|---------|-----------|--------|----------|-------------|
| `/admin/users` | GET | AdminController | users | admin/users.php | Liste utilisateurs |
| `/admin/user/:id` | GET | AdminController | userDetail | admin/user-detail.php | Détail utilisateur |
| `/admin/user/:id/toggle` | PUT | AdminController | toggleUser | JSON | Activer/désactiver |
| `/admin/user/:id/role` | PUT | AdminController | changeUserRole | JSON | Changer rôle |
| `/admin/user/:id` | DELETE | AdminController | deleteUser | JSON | Supprimer utilisateur |

**Liens attendus**:
```html
<a href="/admin/users">Utilisateurs</a>
<a href="/admin/user/1">Détails utilisateur 1</a>
```

---

### 4.3 Gestion des Agences

| Route | Méthode | Controller | Action | Template | Description |
|-------|---------|-----------|--------|----------|-------------|
| `/admin/agences` | GET | AdminController | agences | admin/agences.php | Liste agences |
| `/admin/agence/:id` | GET | AdminController | agenceDetail | admin/agence-detail.php | Détail agence |
| `/admin/agence/:id/toggle` | PUT | AdminController | toggleAgence | JSON | Activer/désactiver |

**Liens attendus**:
```html
<a href="/admin/agences">Agences</a>
<a href="/admin/agence/1">Détails agence 1</a>
```

---

### 4.4 Gestion des Trajets

| Route | Méthode | Controller | Action | Template | Description |
|-------|---------|-----------|--------|----------|-------------|
| `/admin/trajets` | GET | AdminController | trajets | admin/trajets.php | Liste trajets |
| `/admin/trajet/:id` | GET | AdminController | trajetDetail | admin/trajet-detail.php | Détail trajet |
| `/admin/trajet/:id/status` | PUT | AdminController | changeTrajetStatus | JSON | Changer statut |

**Liens attendus**:
```html
<a href="/admin/trajets">Trajets</a>
<a href="/admin/trajet/1">Détails trajet 1</a>
```

---

## 5. PAGES D'ERREUR

| Route | Méthode | Controller | Action | Template | Description |
|-------|---------|-----------|--------|----------|-------------|
| `/403` | GET | HomeController | accessDenied | home/403.php | Accès interdit |
| `/404` | GET | HomeController | notFound | home/404.php | Non trouvé |
| `/500` | GET | HomeController | error | home/500.php | Erreur serveur |

---

## 6. RÉSUMÉ PAR MÉTHODE HTTP

### GET (Affichage)
- `/` → Accueil
- `/about`, `/contact` → Pages statiques
- `/trajets`, `/trajet/:id` → Trajets publics
- `/agences`, `/agence/:slug` → Agences
- `/login`, `/register`, `/profile` → Auth
- `/api/trajets`, `/api/trajet/:id` → API trajets
- `/api/my-trips`, `/api/trajet/:id/stats` → Mes trajets
- `/admin/*` → Pages admin
- `/403`, `/404`, `/500` → Erreurs

### POST (Créer/Action)
- `/contact` → Soumettre contact
- `/login`, `/register`, `/logout` → Auth
- `/profile`, `/change-password` → Profil
- `/api/trajet` → Créer trajet
- `/api/trajet/:id/join`, `/api/trajet/:id/leave` → Rejoindre/quitter
- `/api/trajet/:id/horn` → Klaxon

### PUT (Modifier)
- `/api/trajet/:id` → Modifier trajet
- `/admin/user/:id/toggle`, `/admin/user/:id/role` → Utilisateurs
- `/admin/agence/:id/toggle` → Agences
- `/admin/trajet/:id/status` → Trajets

### DELETE (Supprimer)
- `/api/trajet/:id` → Supprimer trajet
- `/admin/user/:id` → Supprimer utilisateur

---

## 7. MATRICE DE CORRESPONDANCE

### Routes → Templates

```
/ ........................... src/Views/home/index.php
/about ........................ src/Views/home/about.php
/contact (GET) ................ src/Views/home/contact.php
/contact (POST) ............... (TRAITEMENT)
/trajets ...................... src/Views/home/trajets.php
/trajet/:id ................... src/Views/home/trajet.php
/agences ...................... src/Views/home/agences.php
/agence/:slug ................. src/Views/home/agence.php

/login (GET) .................. src/Views/auth/login.php
/login (POST) ................. (TRAITEMENT)
/register (GET) ............... src/Views/auth/register.php
/register (POST) .............. (TRAITEMENT)
/profile (GET) ................ src/Views/auth/profile.php
/profile (POST) ............... (TRAITEMENT)

/admin ........................ src/Views/admin/dashboard.php
/admin/users .................. src/Views/admin/users.php
/admin/user/:id ............... src/Views/admin/user-detail.php
/admin/agences ................ src/Views/admin/agences.php
/admin/agence/:id ............. src/Views/admin/agence-detail.php
/admin/trajets ................ src/Views/admin/trajets.php
/admin/trajet/:id ............. src/Views/admin/trajet-detail.php

/403 .......................... src/Views/home/403.php
/404 .......................... src/Views/home/404.php
/500 .......................... src/Views/home/500.php

/api/* ........................ JSON (pas de template)
```

---

## 8. POINTS CRITIQUES À VÉRIFIER

### ❌ Point 8 - GET/POST Mismatch

**PROBLÈME DÉTECTÉ**:
```php
self::$router->post('/logout', [AuthController::class, 'logout']);
```

**Liens HTML qui NE FONCTIONNERONT PAS**:
```html
<a href="/logout">Déconnexion</a>  <!-- ❌ GET, pas POST -->
```

**SOLUTION CORRECTE**:
```html
<form method="POST" action="/logout">
    <button type="submit">Déconnexion</button>
</form>
```

**OU** avec JavaScript:
```javascript
// Dans le lien
<a href="#" onclick="document.getElementById('logoutForm').submit(); return false;">
    Déconnexion
</a>

<!-- Le formulaire caché -->
<form id="logoutForm" method="POST" action="/logout" style="display:none;"></form>
```

---

## 9. VÉRIFICATION DES LINKS DANS TEMPLATES

### À chercher dans tous les fichiers `src/Views/*.php`:

```bash
# Liens <a> vers /logout
grep -r "href.*logout" src/Views/

# Formulaires POST
grep -r "<form" src/Views/ | grep -v "method=\"POST\""

# Formulaires manquants (POST routes sans form)
grep -r "/contact\|/login\|/register\|/profile\|/logout" src/Views/
```

### Audit requis:
1. ✅ Tous les liens `/logout` → wrapper form POST
2. ✅ Tous les formulaires contact/login/register/profile → `method="POST"`
3. ✅ Tous les appels API `/api/*` → fetch avec method correct

---

## 10. NOMENCLATURE DES PARAMÈTRES

### Paramètres dynamiques (`:param`)

| Paramètre | Type | Utilisation | Validation |
|-----------|------|-------------|-----------|
| `:id` | INT | ID utilisateur, agence, trajet | Must exist in DB |
| `:slug` | STRING | Slug agence (url-friendly) | Doit matcher nom agence |

### Paramètres de query (optionnels)

```
GET /trajets?page=1&sort=date&direction=asc&limit=20
GET /admin/users?filter=actif&search=dupont&page=1
```

---

## 11. CODES HTTP ATTENDUS

| Code | Signification | Cas d'usage |
|------|---|--|
| 200 | OK | Requête réussie |
| 201 | Created | Ressource créée (POST) |
| 204 | No Content | Pas de contenu (DELETE) |
| 301/302 | Redirect | Redirection après action |
| 400 | Bad Request | Données invalides |
| 401 | Unauthorized | Non authentifié |
| 403 | Forbidden | Pas de permissions |
| 404 | Not Found | Ressource inexistante |
| 500 | Server Error | Erreur serveur |

---

## 12. AUTHENTIFICATION & SÉCURITÉ

### Sessions
- Variable: `$_SESSION['user_id']`
- Timeout: 1 heure (SESSION_TIMEOUT)

### Middleware requis
- AuthMiddleware (vérifier authentification)
- RoleMiddleware (vérifier rôle/permissions)
- CSRF (si formulaires HTML)

### Routes protégées

| Route | Rôle requis |
|-------|---------|
| `/profile`, `/change-password` | user, chauffeur, admin |
| `/admin/*` | admin |
| `/api/trajet` (CREATE) | chauffeur |
| `/api/trajet/:id/join` | passager, chauffeur |

---

## 13. CHANGELOG

| Version | Date | Changements |
|---------|------|-------------|
| 1.0 | 2024 | Version initiale |
| 2.0 | 2025-07 | Audit complet, identification des problèmes GET/POST |

---

## Auteur
TOUCHE PAS AU KLAXON Team  
Version: 2.0  

