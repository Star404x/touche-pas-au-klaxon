<?php
/**
 * Template Accueil - Liste des trajets publics
 * TOUCHE PAS AU KLAXON
 * 
 * @category Home Page
 * @package KlaxonApp\Views
 */

declare(strict_types=1);

$trips = $trips ?? [];
$currentUser = $_SESSION['user'] ?? null;
?>

<section class="py-5">
    <div class="container">
        <!-- En-tête -->
        <div class="row mb-5 align-items-center">
            <div class="col-md-8">
                <h1>Bienvenue sur TOUCHE PAS AU KLAXON</h1>
                <p class="lead text-muted">
                    Découvrez les trajets disponibles et covoiturez de manière responsable.
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <?php if ($currentUser): ?>
                    <a href="/trip/create" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus"></i> Créer un trajet
                    </a>
                <?php else: ?>
                    <a href="/login" class="btn btn-primary btn-lg">
                        <i class="fas fa-sign-in-alt"></i> Se connecter
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Filtres et recherche (simplifié) -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="/" class="row g-3" id="search-form">
                    <div class="col-md-4">
                        <label for="agency_depart" class="form-label">Agence de départ</label>
                        <select class="form-control" id="agency_depart" name="agency_depart">
                            <option value="">-- Toutes --</option>
                            <option value="1">Paris Centre</option>
                            <option value="2">Lyon Est</option>
                            <option value="3">Marseille Sud</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="agency_arrive" class="form-label">Agence d'arrivée</label>
                        <select class="form-control" id="agency_arrive" name="agency_arrive">
                            <option value="">-- Toutes --</option>
                            <option value="1">Paris Centre</option>
                            <option value="2">Lyon Est</option>
                            <option value="3">Marseille Sud</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> Rechercher
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Trajets disponibles -->
        <h2 class="mb-4">Trajets disponibles</h2>

        <?php if (empty($trips)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>Aucun trajet trouvé</strong> - Soyez le premier à en créer un !
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($trips as $trip): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <!-- En-tête trajet -->
                                <div class="mb-3">
                                    <span class="badge badge-status-confirmed mb-2">
                                        <i class="fas fa-check"></i> Confirmé
                                    </span>
                                    <h5 class="card-title">
                                        <?= htmlspecialchars($trip['agence_depart_nom'] ?? 'Départ') ?> 
                                        <i class="fas fa-arrow-right text-primary"></i> 
                                        <?= htmlspecialchars($trip['agence_arrivee_nom'] ?? 'Arrivée') ?>
                                    </h5>
                                </div>

                                <!-- Détails -->
                                <ul class="list-unstyled small text-muted">
                                    <li class="mb-2">
                                        <i class="fas fa-calendar text-primary"></i>
                                        <strong><?= htmlspecialchars(date('d/m/Y H:i', strtotime($trip['date_heure_depart'] ?? ''))) ?></strong>
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-user text-primary"></i>
                                        <?= htmlspecialchars($trip['user_nom'] ?? 'Conducteur') ?>
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-chair text-primary"></i>
                                        <strong><?= (int)($trip['places_disponibles'] ?? 0) ?></strong> places disponibles 
                                        <span class="badge badge-info"><?= (int)($trip['places_totales'] ?? 0) ?> total</span>
                                    </li>
                                </ul>

                                <!-- Boutons d'action -->
                                <div class="mt-4 btn-group w-100" role="group">
                                    <button type="button" 
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#detailsModal"
                                            data-trip-id="<?= (int)($trip['id'] ?? 0) ?>">
                                        <i class="fas fa-info-circle"></i> Détails
                                    </button>
                                    <?php if ($currentUser && $currentUser['id'] !== ($trip['user_id'] ?? null)): ?>
                                        <button type="button" class="btn btn-sm btn-primary">
                                            <i class="fas fa-check"></i> S'inscrire
                                        </button>
                                    <?php elseif ($currentUser && $currentUser['id'] === ($trip['user_id'] ?? null)): ?>
                                        <a href="/trip/<?= (int)($trip['id'] ?? 0) ?>/edit" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-edit"></i> Modifier
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Modale Détails Trajet -->
<div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailsModalLabel">Détails du trajet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body" id="tripDetailsContent">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" id="enrollButton">S'inscrire</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion de l'ouverture de la modale
    const detailsModal = document.getElementById('detailsModal');
    if (detailsModal) {
        detailsModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const tripId = button.getAttribute('data-trip-id');
            
            // Charger les détails du trajet via AJAX (optionnel)
            // Pour l'instant, remplir avec les infos disponibles
            const content = `
                <div>
                    <h6>Détails du trajet #${tripId}</h6>
                    <p class="text-muted">Chargement des informations...</p>
                </div>
            `;
            document.getElementById('tripDetailsContent').innerHTML = content;
        });
    }
});
</script>
