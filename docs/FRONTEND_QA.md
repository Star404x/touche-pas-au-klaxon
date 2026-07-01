# 📱 Frontend & QA Documentation - TOUCHE PAS AU KLAXON

## Vue d'ensemble

Cette documentation couvre le frontend responsive Bootstrap 5, les tests QA et l'analyse de code pour **TOUCHE PAS AU KLAXON**.

---

## 📁 Structure des fichiers Frontend

```
public/
├── index.php                    # Point d'entrée routing
├── css/
│   └── app.css                 # CSS compilé (minifié)
├── scss/
│   ├── app.scss                # Principal SCSS
│   ├── _variables.scss         # Variables Bootstrap personnalisées
│   └── _mixins.scss            # Mixins réutilisables
└── js/
    ├── app.js                  # Logique applicative
    ├── validation.js           # Validation formulaires HTML5 + JS
    └── modal.js                # Gestion modales Bootstrap

src/Views/
├── layout.php                  # Template layout partagé
├── home.php                    # Accueil - liste publique
├── login.php                   # Connexion utilisateur
├── trip_create.php             # Création trajet
├── trip_edit.php               # Édition trajet
├── admin_dashboard.php         # Admin tableau de bord
├── admin_users.php             # Admin gestion utilisateurs
├── admin_agencies.php          # Admin gestion agences
└── admin_trips.php             # Admin gestion trajets
```

---

## 🎨 Bootstrap 5 & SCSS Personnalisé

### Variables principales

```scss
// Couleurs
$primary: #2563eb           // Bleu confiance
$secondary: #64748b         // Gris neutre
$success: #10b981           // Vert validation
$danger: #ef4444            // Rouge alerte
$warning: #f59e0b           // Orange attention
$info: #0ea5e9              // Cyan information

// Typographie
$font-family-base: système stack (performance)
$font-size-base: 1rem
$font-weight-bold: 700
$line-height-base: 1.5

// Espacements
$spacer: 1rem               // unité de base
$spacers: (0, 0.25rem, 0.5rem, 1rem, 1.5rem, 3rem, 4rem)

// Border radius
$border-radius: 0.5rem      // 8px
$border-radius-lg: 1rem     // 16px
```

### Mixins disponibles

```scss
// Responsive
@mixin media-up($breakpoint)    // min-width queries
@mixin media-down($breakpoint)  // max-width queries

// Layout
@mixin flex-center              // Flexbox centré
@mixin space-between            // Space-between
@mixin container                // Container responsive

// Composants
@mixin btn-base                 // Bouton de base
@mixin input-base               // Input standard
@mixin card                     // Card style
@mixin badge                    // Badge style

// Accessibilité
@mixin sr-only                  // Screen reader only
@mixin skip-link                // Skip to content
```

---

## 📋 Templates et Composants

### Layout Principal (`layout.php`)

Le template layout est le wrapper pour toutes les pages. Il inclut:

- ✅ Navigation sticky avec menu utilisateur
- ✅ Flash messages (success, danger, warning, info)
- ✅ Footer avec liens utiles
- ✅ Bootstrap 5 et assets CSS/JS

**Variables attendues:**
```php
$title          // Titre de la page
$currentUser    // Objet utilisateur actuel (session)
$isAdmin        // Boolean si admin
$content        // Contenu page (rendu par contrôleur)
```

### Pages publiques

#### `home.php` - Accueil
- Liste des trajets disponibles
- Filtre par agence
- Modale détails trajet
- Boutons S'inscrire / Modifier

#### `login.php` - Authentification
- Formulaire email/password
- Validation HTML5 + JS
- Gestion erreurs avec feedback
- "Se souvenir de moi" (checkbox)
- Identifiants test (démo)

### Pages utilisateur

#### `trip_create.php` - Créer un trajet
- Sélection agences (différentes)
- Date/heure départ & arrivée
- Nombre de places
- Validation client & serveur
- Annuler / Créer

#### `trip_edit.php` - Éditer un trajet
- Pré-remplir champs existants
- Validation identique à créer
- Bouton suppression (avec modale confirmation)
- Retour / Enregistrer

### Pages Admin

#### `admin_dashboard.php` - Tableau de bord
- Statistiques: utilisateurs, agences, trajets, actifs
- Actions rapides
- Timeline activité récente

#### `admin_users.php` - Gestion utilisateurs
- Tableau avec tri/filtre
- Modales: Ajouter, Éditer, Supprimer
- Affichage rôle (Admin/User)
- Gestion permission CRUD

#### `admin_agencies.php` - Gestion agences
- Cards grid responsive
- Modales: Ajouter, Éditer, Supprimer
- Info création date

#### `admin_trips.php` - Gestion trajets
- Tableau complet trajets
- Détails: trajet, conducteur, dates, places, statut
- Modales détails & suppression

---

## ✅ Validation Formulaires

### HTML5 Natif

Tous les formulaires utilisent validation HTML5:

```html
<input type="email" required>
<input type="datetime-local" required>
<input type="number" min="1" max="8" required>
<textarea required></textarea>
<select required></select>
```

### JavaScript Personnalisé (`validation.js`)

Classe `FormValidator` pour validation avancée:

```javascript
// Initialisation automatique
const form = new FormValidator(formElement);

// Méthodes
form.validate()          // Valider tout le formulaire
form.validateField(field) // Valider un champ
form.clearErrors()       // Réinitialiser erreurs
form.displayErrors()     // Afficher messages d'erreur
```

### Règles de validation

| Champ | Règles |
|-------|--------|
| Email | Format valide + existe check (serveur) |
| Password | Min 8 caractères |
| Dates | Futures + départ < arrivée |
| Agences | Différentes (départ ≠ arrivée) |
| Places | 1-8 inclus |
| Téléphone | Format français (0612345678) |

**Validation côté client:**
- HTML5 + JavaScript personnalisé
- Messages d'erreur dynamiques
- Feedback visuel (is-invalid)

**Validation côté serveur:**
- Double vérification PHP
- Sanitization (htmlspecialchars)
- Type checking stricts

---

## 🔒 Sécurité & Accessibilité

### Sécurité Frontend

✅ **XSS Protection**
```javascript
// Tous les outputs avec htmlspecialchars
<?= htmlspecialchars($userInput, ENT_QUOTES, 'UTF-8') ?>
```

✅ **CSRF Protection**
- Tokens inclus dans formulaires (optionnel, serveur)

✅ **Input Validation**
- HTML5 + JS client
- Serveur PHP strict

✅ **Password Hashing**
- Bcrypt (serveur PHP)
- Jamais en plain text

### Accessibilité

✅ **Semantic HTML**
```html
<nav>, <main>, <section>, <article>, <footer>
<button type="submit">  <!-- pas <a class="btn"> -->
<label for="input">    <!-- label → input relation -->
```

✅ **ARIA Labels**
```html
<button aria-label="Fermer modale">×</button>
<span class="sr-only">Champ requis</span>
```

✅ **Keyboard Navigation**
- Tab order logique
- Focus visible (2px outline)
- Modales fermables avec Échap

✅ **Alt Text**
```html
<img src="icon.svg" alt="Icône utilisateur">
<i class="fas fa-user" aria-hidden="true"></i>
```

---

## 📱 Responsive Design

### Breakpoints

```scss
xs: 0        // Mobile
sm: 576px    // Small tablets
md: 768px    // Tablets
lg: 992px    // Small desktops
xl: 1200px   // Desktops
xxl: 1400px  // Large desktops
```

### Grid responsive

```html
<div class="row">
  <div class="col"><!-- Auto 1 col mobile, 2 md, 3 lg --></div>
</div>
```

### Média queries SCSS

```scss
@include media-up(md) {
  // Min 768px
}

@include media-down(md) {
  // Max 767px
}
```

---

## 🎭 Modales Bootstrap

### Modale détails trajet

```html
<button data-bs-toggle="modal" data-bs-target="#detailsModal">
  Détails
</button>

<div class="modal" id="detailsModal">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <!-- Contenu -->
    </div>
  </div>
</div>
```

### Modale confirmation suppression

```html
<button data-bs-toggle="modal" data-bs-target="#deleteConfirmModal">
  Supprimer
</button>

<div class="modal" id="deleteConfirmModal">
  <div class="modal-dialog">
    <div class="modal-content border-danger">
      <div class="modal-header border-danger">
        <h5 class="text-danger">Confirmer suppression</h5>
      </div>
      <form method="POST">
        <!-- Formulaire suppression -->
      </form>
    </div>
  </div>
</div>
```

---

## 🧪 Tests QA

### PHPUnit Tests

Emplacement: `tests/Unit/FormValidationTest.php`

```bash
# Lancer tous les tests
composer test

# Coverage HTML
composer test:coverage

# Tests spécifiques
vendor/bin/phpunit tests/Unit/FormValidationTest.php
```

### Test Cases

| Test | Objectif |
|------|----------|
| Email valide/invalide | Validation format |
| Password fort/faible | Sécurité |
| Dates future/passée | Logique métier |
| Plage dates | Cohérence |
| Agences différentes | Logique métier |
| Places valides | Range (1-8) |
| XSS Protection | Sécurité |
| SQL Injection | Sécurité |

### Coverage Objectif

- **Minimum 80%** line coverage
- **75%** branch coverage
- **80%** method coverage
- **85%** class coverage

---

## 🔍 PHPStan Analyse (Level 8)

### Configuration

Fichier: `phpstan.neon`

```bash
# Lancer PHPStan
phpstan analyse src/

# Avec rapport
phpstan analyse src/ --level=8 --errorFormat=table
```

### Régles strictes (Level 8)

✅ Types explicites sur tous les paramètres/retours  
✅ Aucun `mixed` implicite  
✅ Zéro erreurs de type  
✅ Zéro warnings  
✅ Analyse variance types  

### Exemple code conforme

```php
<?php
declare(strict_types=1);

namespace KlaxonApp\Controllers;

final class TripController {
    /**
     * @param int $tripId
     * @return array<string, mixed>
     */
    public function edit(int $tripId): array {
        // Code type-safe
    }
}
```

---

## 📊 Flash Messages

### Types disponibles

```php
// Success
$_SESSION['flash'] = [
    'type' => 'success',
    'message' => 'Trajet créé avec succès'
];

// Danger
$_SESSION['flash'] = [
    'type' => 'danger',
    'message' => 'Erreur: Email déjà utilisé'
];

// Warning
$_SESSION['flash'] = [
    'type' => 'warning',
    'message' => 'Attention: places limitées'
];

// Info
$_SESSION['flash'] = [
    'type' => 'info',
    'message' => 'Votre trajet est en attente'
];
```

### Styling

Chaque type a couleur/icône spécifique:
- ✅ Success (vert)
- ❌ Danger (rouge)
- ⚠️ Warning (orange)
- ℹ️ Info (bleu)

---

## 🔧 Build & Compilation

### SCSS vers CSS

```bash
# Compiler SCSS (à faire avec node-sass/dart-sass)
npm install -g sass
sass public/scss/app.scss public/css/app.css

# Minifier
sass public/scss/app.scss public/css/app.min.css --style=compressed
```

### JavaScript

Tous les JS sont pré-optimisés:
- ✅ ES6 syntax
- ✅ Strict mode
- ✅ Modulaire & lisible

---

## 🚀 Performance

### Frontend

✅ CSS minifié (~9KB)  
✅ JS modulaire (chargement sélectif)  
✅ Bootstrap CDN (cache navigateur)  
✅ Lazy loading images (optionnel)  
✅ System font stack (pas Google Fonts)  

### Métriques cibles

- Lighthouse: 90+
- Core Web Vitals: Green
- LCP < 2.5s
- CLS < 0.1
- FID < 100ms

---

## 📖 API Documentation

### Flash Message JavaScript

```javascript
// Afficher success
FlashMessage.success('Trajet créé!');

// Afficher error
FlashMessage.error('Erreur de validation');

// Custom message
new FlashMessage('Message personnalisé', 'warning').show();
```

### API Client

```javascript
// GET
const data = await api.get('/api/trips');

// POST
await api.post('/api/trips', {
    agence_depart_id: 1,
    agence_arrivee_id: 2
});
```

---

## ✨ Checklist QA

- [ ] Tous les formulaires valident côté client
- [ ] Flash messages s'affichent après CRUD
- [ ] Admin dashboard affiche stats correctes
- [ ] Modales ferment avec Échap
- [ ] Responsive OK (xs, md, lg)
- [ ] Pas de console errors
- [ ] PHPStan level 8 = 0 errors
- [ ] Tests PHPUnit passent tous (>80% coverage)
- [ ] WCAG 2.1 AA compliant
- [ ] XSS/SQL injection protégés

---

## 🆘 Troubleshooting

### Problème: Flash messages ne s'affichent pas
**Solution:** Vérifier `session_start()` en haut de public/index.php

### Problème: Modales ne s'ouvrent pas
**Solution:** Vérifier Bootstrap 5 JS chargé dans layout.php

### Problème: Validation JS ne fonctionne pas
**Solution:** Vérifier attribut `novalidate` sur `<form>`

### Problème: PHPStan erreurs de type
**Solution:** Ajouter type hints explicites + DocBlocks

---

## 📚 Ressources

- [Bootstrap 5 Docs](https://getbootstrap.com/docs/5.0/)
- [PHPUnit Docs](https://phpunit.de/)
- [PHPStan Docs](https://phpstan.org/)
- [WCAG 2.1 Guidelines](https://www.w3.org/WAI/WCAG21/quickref/)

---

**Dernière mise à jour:** Juillet 2025  
**Version:** 1.0.0  
**Statut:** ✅ Production Ready
