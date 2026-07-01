<?php
/**
 * Template Admin - Gestion Trajets
 * TOUCHE PAS AU KLAXON
 * 
 * @category Admin Trips
 * @package KlaxonApp\Views
 */

declare(strict_types=1);

$trips = $trips ?? [];
?>

<section class="py-4">
    <div class="container">
        <!-- En-tête -->
        <div class="mb-5">
            <h1>
                <i class="fas fa-car"></i> Gestion des trajets
            </h1>
            <p class="text-muted">Supervisez tous les trajets de la plateforme</p>
        </div>

        <!-- Tableau trajets -->
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Trajets</th>
                        <th>Conducteur</th>
                        <th>Départ</th>
                        <th>Arrivée</th>
                        <th>Places</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($trips)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-inbox"></i> Aucun trajet
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($trips as $trip): ?>
                            <tr>
                                <td>
                                    <strong>#<?= (int)$trip['id'] ?></strong>
                                </td>
                                <td>
                                    <?= htmlspecialchars($trip['agence_depart_nom'] ?? 'Départ') ?> 
                                    <i class="fas fa-arrow-right text-muted"></i> 
                                    <?= htmlspecialchars($trip['agence_arrivee_nom'] ?? 'Arrivée') ?>
                                </td>
                                <td>
                                    <small><?= htmlspecialchars($trip['user_nom'] ?? 'Inconnu') ?></small>
                                </td>
                                <td>
                                    <small><?= htmlspecialchars(date('d/m H:i', strtotime($trip['date_heure_depart'] ?? ''))) ?></small>
                                </td>
                                <td>
                                    <small><?= htmlspecialchars(date('d/m H:i', strtotime($trip['date_heure_arrivee'] ?? ''))) ?></small>
                                </td>
                                <td>
                                    <span class="badge badge-info">
                                        <?= (int)($trip['places_disponibles'] ?? 0) ?>/<?= (int)($trip['places_totales'] ?? 0) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php 
                                    $status = $trip['statut'] ?? 'planning';
                                    $statusBadge = match($status) {
                                        'confirmed' => 'badge-success',
                                        'completed' => 'badge-secondary',
                                        'cancelled' => 'badge-danger',
                                        default => 'badge-warning'
                                    };
                                    $statusLabel = match($status) {
                                        'confirmed' => 'Confirmé',
                                        'completed' => 'Complété',
                                        'cancelled' => 'Annulé',
                                        default => 'En attente'
                                    };
                                    ?>
                                    <span class="badge <?= $statusBadge ?>">
                                        <?= $statusLabel ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-outline-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#tripDetailsModal"
                                                data-trip-id="<?= (int)$trip['id'] ?>">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteTripModal"
                                                data-trip-id="<?= (int)$trip['id'] ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Modal Détails Trajet -->
<div class="modal fade" id="tripDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-info-circle"></i> Détails du trajet
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="tripDetailsContent">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Chargement...</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Supprimer Trajet -->
<div class="modal fade" id="deleteTripModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-danger">
            <div class="modal-header border-danger">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-trash"></i> Supprimer trajet
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer ce trajet ?</p>
                <p class="text-muted small">Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form method="POST" id="deleteTripForm" style="display:inline;">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des modales détails
    const tripDetailsModal = document.getElementById('tripDetailsModal');
    const deleteTripModal = document.getElementById('deleteTripModal');

    if (tripDetailsModal) {
        tripDetailsModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const tripId = button.getAttribute('data-trip-id');
            // Charger via AJAX (optionnel)
            document.getElementById('tripDetailsContent').innerHTML = `
                <p><strong>Trajet #${tripId}</strong></p>
                <p class="text-muted">Chargement des détails...</p>
            `;
        });
    }

    if (deleteTripModal) {
        deleteTripModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const tripId = button.getAttribute('data-trip-id');
            const deleteForm = document.getElementById('deleteTripForm');
            deleteForm.action = `/admin/trips/${tripId}`;
        });
    }
});
</script>
