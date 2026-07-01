<?php
/**
 * Template Édition d'un Trajet
 * TOUCHE PAS AU KLAXON
 * 
 * @category Trip Management
 * @package KlaxonApp\Views
 */

declare(strict_types=1);

$trip = $trip ?? [];
$agencies = $agencies ?? [];
$errors = $errors ?? [];
?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Titre -->
                <div class="mb-5">
                    <h1>Modifier le trajet</h1>
                    <p class="text-muted lead">Trajet #<?= (int)($trip['id'] ?? 0) ?></p>
                </div>

                <!-- Formulaire -->
                <div class="card">
                    <div class="card-body p-4">
                        <form method="POST" action="/trip/<?= (int)($trip['id'] ?? 0) ?>" id="tripEditForm" novalidate>
                            <!-- Agence de départ -->
                            <div class="form-group mb-4">
                                <label for="agence_depart_id" class="form-label">
                                    <i class="fas fa-map-marker-alt text-danger"></i> Agence de départ
                                </label>
                                <select class="form-control <?= !empty($errors['agence_depart_id']) ? 'is-invalid' : '' ?>"
                                        id="agence_depart_id" 
                                        name="agence_depart_id"
                                        required>
                                    <option value="">-- Sélectionner une agence --</option>
                                    <?php foreach ($agencies as $agency): ?>
                                        <option value="<?= (int)$agency['id'] ?>" 
                                                <?= ($trip['agence_depart_id'] ?? '') == $agency['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($agency['nom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (!empty($errors['agence_depart_id'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($errors['agence_depart_id']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Agence d'arrivée -->
                            <div class="form-group mb-4">
                                <label for="agence_arrivee_id" class="form-label">
                                    <i class="fas fa-map-marker-alt text-success"></i> Agence d'arrivée
                                </label>
                                <select class="form-control <?= !empty($errors['agence_arrivee_id']) ? 'is-invalid' : '' ?>"
                                        id="agence_arrivee_id" 
                                        name="agence_arrivee_id"
                                        required>
                                    <option value="">-- Sélectionner une agence --</option>
                                    <?php foreach ($agencies as $agency): ?>
                                        <option value="<?= (int)$agency['id'] ?>"
                                                <?= ($trip['agence_arrivee_id'] ?? '') == $agency['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($agency['nom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (!empty($errors['agence_arrivee_id'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($errors['agence_arrivee_id']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Date et heure départ -->
                            <div class="form-group mb-4">
                                <label for="date_heure_depart" class="form-label">
                                    <i class="fas fa-calendar"></i> Date et heure de départ
                                </label>
                                <input type="datetime-local" 
                                       class="form-control <?= !empty($errors['date_heure_depart']) ? 'is-invalid' : '' ?>"
                                       id="date_heure_depart" 
                                       name="date_heure_depart"
                                       value="<?= htmlspecialchars($trip['date_heure_depart'] ?? '') ?>"
                                       required>
                                <?php if (!empty($errors['date_heure_depart'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($errors['date_heure_depart']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Date et heure arrivée -->
                            <div class="form-group mb-4">
                                <label for="date_heure_arrivee" class="form-label">
                                    <i class="fas fa-calendar-check"></i> Date et heure d'arrivée
                                </label>
                                <input type="datetime-local" 
                                       class="form-control <?= !empty($errors['date_heure_arrivee']) ? 'is-invalid' : '' ?>"
                                       id="date_heure_arrivee" 
                                       name="date_heure_arrivee"
                                       value="<?= htmlspecialchars($trip['date_heure_arrivee'] ?? '') ?>"
                                       required>
                                <?php if (!empty($errors['date_heure_arrivee'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($errors['date_heure_arrivee']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Places totales -->
                            <div class="form-group mb-4">
                                <label for="places_totales" class="form-label">
                                    <i class="fas fa-chair"></i> Nombre total de places
                                </label>
                                <input type="number" 
                                       class="form-control <?= !empty($errors['places_totales']) ? 'is-invalid' : '' ?>"
                                       id="places_totales" 
                                       name="places_totales"
                                       min="1"
                                       max="8"
                                       value="<?= htmlspecialchars($trip['places_totales'] ?? '4') ?>"
                                       required>
                                <?php if (!empty($errors['places_totales'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($errors['places_totales']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Erreur générale -->
                            <?php if (!empty($errors['general'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <strong>Erreur</strong><br>
                                    <?= htmlspecialchars($errors['general']) ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <!-- Boutons -->
                            <div class="btn-group w-100" role="group">
                                <a href="/" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Retour
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Enregistrer les modifications
                                </button>
                            </div>
                        </form>

                        <!-- Bouton supprimer -->
                        <div class="mt-4 pt-4 border-top">
                            <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#deleteConfirmModal">
                                <i class="fas fa-trash"></i> Supprimer ce trajet
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Modale de confirmation suppression -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-danger">
                <h5 class="modal-title text-danger" id="deleteConfirmLabel">
                    <i class="fas fa-trash"></i> Confirmer la suppression
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer ce trajet ?</p>
                <p class="text-muted small">Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form method="POST" action="/trip/<?= (int)($trip['id'] ?? 0) ?>" style="display:inline;">
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
    const form = document.getElementById('tripEditForm');
    const departAgency = document.getElementById('agence_depart_id');
    const arrivalAgency = document.getElementById('agence_arrivee_id');
    
    [departAgency, arrivalAgency].forEach(el => {
        el.addEventListener('change', function() {
            if (departAgency.value && arrivalAgency.value && departAgency.value === arrivalAgency.value) {
                arrivalAgency.classList.add('is-invalid');
            } else {
                arrivalAgency.classList.remove('is-invalid');
            }
        });
    });

    form.addEventListener('submit', function(e) {
        if (!form.checkValidity() || departAgency.value === arrivalAgency.value) {
            e.preventDefault();
            e.stopPropagation();
            form.classList.add('was-validated');
        }
    });
});
</script>
