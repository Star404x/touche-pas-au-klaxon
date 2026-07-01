<?php
/**
 * Template de Layout Principal
 * TOUCHE PAS AU KLAXON
 * 
 * @category Layout
 * @package KlaxonApp\Views
 */

declare(strict_types=1);

$title = $title ?? 'TOUCHE PAS AU KLAXON';
$currentUser = $_SESSION['user'] ?? null;
$isAdmin = $currentUser && $currentUser['role'] === 'admin';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Application de covoiturage responsable - TOUCHE PAS AU KLAXON">
    <meta name="theme-color" content="#2563eb">
    <title><?= htmlspecialchars($title) ?> | TOUCHE PAS AU KLAXON</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- App CSS -->
    <link href="/css/app.css" rel="stylesheet">
    
    <!-- Font Awesome (optionnel mais recommandé) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Skip to main content (accessibilité) -->
    <a href="#main-content" class="skip-to-content">Sauter au contenu principal</a>

    <!-- NAVIGATION -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="navbar-container">
            <a class="navbar-brand" href="/">
                <i class="fas fa-car"></i> TOUCHE PAS AU KLAXON
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">
                            <i class="fas fa-home"></i> Accueil
                        </a>
                    </li>

                    <?php if ($currentUser): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/trip/create">
                                <i class="fas fa-plus"></i> Créer un trajet
                            </a>
                        </li>

                        <?php if ($isAdmin): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-cog"></i> Admin
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="/admin">Tableau de bord</a></li>
                                    <li><a class="dropdown-item" href="/admin/users">Utilisateurs</a></li>
                                    <li><a class="dropdown-item" href="/admin/agencies">Agences</a></li>
                                    <li><a class="dropdown-item" href="/admin/trips">Trajets</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                </ul>
                            </li>
                        <?php endif; ?>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user-circle"></i> <?= htmlspecialchars($currentUser['prenom']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <span class="dropdown-item-text">
                                        <strong><?= htmlspecialchars($currentUser['email']) ?></strong>
                                    </span>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/logout">
                                    <i class="fas fa-sign-out-alt"></i> Déconnexion
                                </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link btn btn-primary text-white" href="/login">
                                <i class="fas fa-sign-in-alt"></i> Connexion
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- CONTENU PRINCIPAL -->
    <main id="main-content">
        <div class="container mt-4 mb-5">
            <!-- Flash Messages -->
            <?php if (!empty($_SESSION['flash'])): ?>
                <div class="alert alert-<?= htmlspecialchars($_SESSION['flash']['type'] ?? 'info') ?> alert-dismissible fade show" role="alert">
                    <div class="alert-icon">
                        <?php switch($_SESSION['flash']['type'] ?? 'info'):
                            case 'success': ?>
                                <i class="fas fa-check-circle"></i>
                            <?php break;
                            case 'danger': ?>
                                <i class="fas fa-exclamation-circle"></i>
                            <?php break;
                            case 'warning': ?>
                                <i class="fas fa-info-circle"></i>
                            <?php break;
                            default: ?>
                                <i class="fas fa-info-circle"></i>
                        <?php endswitch; ?>
                    </div>
                    <div class="alert-content">
                        <div><?= htmlspecialchars($_SESSION['flash']['message'] ?? '') ?></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                </div>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>

            <!-- Contenu de la page (inséré ici par le contrôleur) -->
            <?= $content ?? '' ?>
        </div>
    </main>

    <!-- FOOTER -->
    <footer class="footer mt-5">
        <div class="footer-content">
            <div class="footer-section">
                <div>
                    <h5>TOUCHE PAS AU KLAXON</h5>
                    <p>Application de covoiturage responsable pour une communication bienveillante sur la route.</p>
                </div>
                <div>
                    <h5>Navigation</h5>
                    <ul>
                        <li><a href="/">Accueil</a></li>
                        <?php if ($currentUser): ?>
                            <li><a href="/trip/create">Créer un trajet</a></li>
                        <?php else: ?>
                            <li><a href="/login">Connexion</a></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <div>
                    <h5>Support</h5>
                    <ul>
                        <li><a href="mailto:support@klaxon.local">Contact</a></li>
                        <li><a href="#">Conditions d'utilisation</a></li>
                        <li><a href="#">Politique de confidentialité</a></li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; 2025 TOUCHE PAS AU KLAXON. Tous droits réservés. | 
                    <a href="#">Conditions d'utilisation</a> | 
                    <a href="#">Confidentialité</a>
                </p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- App JS -->
    <script src="/js/validation.js"></script>
    <script src="/js/modal.js"></script>
    <script src="/js/app.js"></script>
</body>
</html>
