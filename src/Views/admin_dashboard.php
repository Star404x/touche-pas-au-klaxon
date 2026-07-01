<?php
/**
 * Template Admin Dashboard
 * TOUCHE PAS AU KLAXON
 * 
 * @category Admin Panel
 * @package KlaxonApp\Views
 */

declare(strict_types=1);

$stats = $stats ?? [];
?>

<section class="py-4">
    <div class="container">
        <!-- Titre -->
        <div class="mb-5">
            <h1>
                <i class="fas fa-chart-line"></i> Tableau de bord administrateur
            </h1>
            <p class="text-muted">Vue d'ensemble de votre application</p>
        </div>

        <!-- Statistiques -->
        <div class="row mb-5">
            <!-- Total utilisateurs -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-users fa-3x text-primary mb-3"></i>
                        <h6 class="card-subtitle mb-2 text-muted">Utilisateurs</h6>
                        <h2 class="card-title mb-0"><?= (int)($stats['total_users'] ?? 0) ?></h2>
                    </div>
                    <div class="card-footer bg-light">
                        <a href="/admin/users" class="btn btn-sm btn-primary">
                            Voir la liste
                        </a>
                    </div>
                </div>
            </div>

            <!-- Total agences -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-building fa-3x text-success mb-3"></i>
                        <h6 class="card-subtitle mb-2 text-muted">Agences</h6>
                        <h2 class="card-title mb-0"><?= (int)($stats['total_agencies'] ?? 0) ?></h2>
                    </div>
                    <div class="card-footer bg-light">
                        <a href="/admin/agencies" class="btn btn-sm btn-success">
                            Voir la liste
                        </a>
                    </div>
                </div>
            </div>

            <!-- Total trajets -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-car fa-3x text-info mb-3"></i>
                        <h6 class="card-subtitle mb-2 text-muted">Trajets</h6>
                        <h2 class="card-title mb-0"><?= (int)($stats['total_trips'] ?? 0) ?></h2>
                    </div>
                    <div class="card-footer bg-light">
                        <a href="/admin/trips" class="btn btn-sm btn-info">
                            Voir la liste
                        </a>
                    </div>
                </div>
            </div>

            <!-- Trajets actifs -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-check-circle fa-3x text-warning mb-3"></i>
                        <h6 class="card-subtitle mb-2 text-muted">Trajets actifs</h6>
                        <h2 class="card-title mb-0"><?= (int)($stats['active_trips'] ?? 0) ?></h2>
                    </div>
                    <div class="card-footer bg-light">
                        <small class="text-muted">En cours ou à venir</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="card mb-5">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt"></i> Actions rapides
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 col-lg-3 mb-3">
                        <a href="/admin/users" class="btn btn-outline-primary w-100">
                            <i class="fas fa-user-plus"></i> Gérer utilisateurs
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <a href="/admin/agencies" class="btn btn-outline-success w-100">
                            <i class="fas fa-building"></i> Gérer agences
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <a href="/admin/trips" class="btn btn-outline-info w-100">
                            <i class="fas fa-car"></i> Gérer trajets
                        </a>
                    </div>
                    <div class="col-md-6 col-lg-3 mb-3">
                        <button class="btn btn-outline-secondary w-100" disabled>
                            <i class="fas fa-download"></i> Exporter données
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activité récente -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-clock"></i> Activité récente
                </h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-badge bg-primary">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="timeline-content">
                            <h6>Nouvel utilisateur inscrit</h6>
                            <p class="text-muted small">Il y a 2 heures</p>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-badge bg-success">
                            <i class="fas fa-car"></i>
                        </div>
                        <div class="timeline-content">
                            <h6>Nouveau trajet créé</h6>
                            <p class="text-muted small">Il y a 4 heures</p>
                        </div>
                    </div>

                    <div class="timeline-item">
                        <div class="timeline-badge bg-info">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="timeline-content">
                            <h6>Nouvelle agence ajoutée</h6>
                            <p class="text-muted small">Hier à 14:30</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.timeline {
    position: relative;
    padding: 0;
}

.timeline-item {
    display: flex;
    gap: 1.5rem;
    margin-bottom: 2rem;
    position: relative;
}

.timeline-item:last-child {
    margin-bottom: 0;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: 1.5rem;
    top: 3rem;
    bottom: -2rem;
    width: 2px;
    background-color: #e2e8f0;
}

.timeline-badge {
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.timeline-content {
    padding-top: 0.25rem;
}

.timeline-content h6 {
    margin-bottom: 0.25rem;
    font-weight: 600;
}
</style>
