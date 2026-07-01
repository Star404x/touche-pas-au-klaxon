<?php
/**
 * Template Admin - Gestion Agences
 * TOUCHE PAS AU KLAXON
 * 
 * @category Admin Agencies
 * @package KlaxonApp\Views
 */

declare(strict_types=1);

$agencies = $agencies ?? [];
?>

<section class="py-4">
    <div class="container">
        <!-- En-tête -->
        <div class="row align-items-center mb-5">
            <div class="col-md-8">
                <h1>
                    <i class="fas fa-building"></i> Gestion des agences
                </h1>
                <p class="text-muted">Gérez toutes les agences de la plateforme</p>
            </div>
            <div class="col-md-4 text-md-end">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAgencyModal">
                    <i class="fas fa-plus"></i> Ajouter une agence
                </button>
            </div>
        </div>

        <!-- Tableau agences -->
        <div class="row">
            <?php if (empty($agencies)): ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-inbox"></i> Aucune agence trouvée
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($agencies as $agency): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <i class="fas fa-building text-primary"></i> 
                                    <?= htmlspecialchars($agency['nom'] ?? '') ?>
                                </h5>
                                <p class="text-muted small mb-3">
                                    ID: <strong>#<?= (int)$agency['id'] ?></strong>
                                </p>
                                <p class="card-text text-muted">
                                    <small>
                                        Créée le <?= htmlspecialchars(date('d/m/Y', strtotime($agency['created_at'] ?? ''))) ?>
                                    </small>
                                </p>
                            </div>
                            <div class="card-footer bg-light">
                                <div class="btn-group w-100" role="group">
                                    <button class="btn btn-sm btn-outline-primary w-50" data-bs-toggle="modal" data-bs-target="#editAgencyModal"
                                            data-agency-id="<?= (int)$agency['id'] ?>">
                                        <i class="fas fa-edit"></i> Éditer
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger w-50" data-bs-toggle="modal" data-bs-target="#deleteAgencyModal"
                                            data-agency-id="<?= (int)$agency['id'] ?>">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Modal Ajouter agence -->
<div class="modal fade" id="addAgencyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus"></i> Ajouter une agence
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/admin/agencies" id="addAgencyForm">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="agencyName" class="form-label">Nom de l'agence</label>
                        <input type="text" class="form-control" id="agencyName" name="nom" 
                               placeholder="Ex: Agence Paris Centre" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="agencyCity" class="form-label">Ville</label>
                        <input type="text" class="form-control" id="agencyCity" name="city" 
                               placeholder="Ex: Paris" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="agencyAddress" class="form-label">Adresse (optionnel)</label>
                        <textarea class="form-control" id="agencyAddress" name="address" 
                                  placeholder="123 Rue de l'Exemple, 75000 Paris" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Ajouter
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Éditer agence -->
<div class="modal fade" id="editAgencyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit"></i> Éditer agence
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editAgencyForm">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="edit_agencyName" class="form-label">Nom de l'agence</label>
                        <input type="text" class="form-control" id="edit_agencyName" name="nom" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="edit_agencyCity" class="form-label">Ville</label>
                        <input type="text" class="form-control" id="edit_agencyCity" name="city" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="edit_agencyAddress" class="form-label">Adresse</label>
                        <textarea class="form-control" id="edit_agencyAddress" name="address" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Supprimer agence -->
<div class="modal fade" id="deleteAgencyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-danger">
            <div class="modal-header border-danger">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-trash"></i> Supprimer agence
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cette agence ?</p>
                <p class="text-muted small">Cette action est irréversible. Elle affectera tous les trajets liés.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form method="POST" id="deleteAgencyForm" style="display:inline;">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
