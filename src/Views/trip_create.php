<?php
/**
 * Template Création d'un Trajet
 * TOUCHE PAS AU KLAXON
 * 
 * @category Trip Management
 * @package KlaxonApp\Views
 */

declare(strict_types=1);

$errors = $errors ?? [];
$agencies = $agencies ?? [];
?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Titre -->
                <div class="mb-5">
                    <h1>Créer un nouveau trajet</h1>
                    <p class="text-muted lead">Remplissez les informations ci-dessous pour proposer un trajet.</p>
                </div>

                <!-- Formulaire -->
                <div class="card">
                    <div class="card-body p-4">
                        <form method="POST" action="/trip" id="tripCreateForm" novalidate>
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
                                                <?= ($_POST['agence_depart_id'] ?? '') == $agency['id'] ? 'selected' : '' ?>>
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
                                                <?= ($_POST['agence_arrivee_id'] ?? '') == $agency['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($agency['nom']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (!empty($errors['agence_arrivee_id'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($errors['agence_arrivee_id']) ?>
                                    </div>
                                <?php endif; ?>
                                <small class="form-text text-muted mt-1">
                                    Doit être différente de l'agence de départ
                                </small>
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
                                       value="<?= htmlspecialchars($_POST['date_heure_depart'] ?? '') ?>"
                                       required>
                                <?php if (!empty($errors['date_heure_depart'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($errors['date_heure_depart']) ?>
                                    </div>
                                <?php endif; ?>
                                <small class="form-text text-muted mt-1">
                                    Doit être dans le futur
                                </small>
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
                                       value="<?= htmlspecialchars($_POST['date_heure_arrivee'] ?? '') ?>"
                                       required>
                                <?php if (!empty($errors['date_heure_arrivee'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($errors['date_heure_arrivee']) ?>
                                    </div>
                                <?php endif; ?>
                                <small class="form-text text-muted mt-1">
                                    Doit être après l'heure de départ
                                </small>
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
                                       value="<?= htmlspecialchars($_POST['places_totales'] ?? '4') ?>"
                                       required>
                                <?php if (!empty($errors['places_totales'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($errors['places_totales']) ?>
                                    </div>
                                <?php endif; ?>
                                <small class="form-text text-muted mt-1">
                                    Entre 1 et 8 places
                                </small>
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
                                    <i class="fas fa-arrow-left"></i> Annuler
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-check"></i> Créer le trajet
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Info -->
                <div class="alert alert-info mt-4">
                    <i class="fas fa-info-circle"></i>
                    <strong>💡 Conseil :</strong> Assurez-vous de laisser suffisamment de temps pour le trajet.
                    Profitez-en pour communiquer de manière responsable sur la route !
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('tripCreateForm');
    const departAgency = document.getElementById('agence_depart_id');
    const arrivalAgency = document.getElementById('agence_arrivee_id');
    
    // Validation que les agences sont différentes
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
