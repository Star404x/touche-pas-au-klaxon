<?php
/**
 * Template Admin - Gestion Utilisateurs
 * TOUCHE PAS AU KLAXON
 * 
 * @category Admin Users
 * @package KlaxonApp\Views
 */

declare(strict_types=1);

$users = $users ?? [];
?>

<section class="py-4">
    <div class="container">
        <!-- En-tête -->
        <div class="row align-items-center mb-5">
            <div class="col-md-8">
                <h1>
                    <i class="fas fa-users"></i> Gestion des utilisateurs
                </h1>
                <p class="text-muted">Gérez tous les utilisateurs de la plateforme</p>
            </div>
            <div class="col-md-4 text-md-end">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="fas fa-user-plus"></i> Ajouter un utilisateur
                </button>
            </div>
        </div>

        <!-- Tableau utilisateurs -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>Rôle</th>
                        <th>Inscrit le</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox"></i> Aucun utilisateur
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td>
                                    <strong>#<?= (int)$user['id'] ?></strong>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($user['prenom'] ?? '') ?> <?= htmlspecialchars($user['nom'] ?? '') ?></strong>
                                </td>
                                <td>
                                    <code><?= htmlspecialchars($user['email'] ?? '') ?></code>
                                </td>
                                <td>
                                    <?= htmlspecialchars($user['phone'] ?? '-') ?>
                                </td>
                                <td>
                                    <?php if ($user['role'] === 'admin'): ?>
                                        <span class="badge badge-danger">
                                            <i class="fas fa-crown"></i> Admin
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-info">
                                            <i class="fas fa-user"></i> Utilisateur
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <small><?= htmlspecialchars(date('d/m/Y', strtotime($user['created_at'] ?? ''))) ?></small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editUserModal" 
                                                data-user-id="<?= (int)$user['id'] ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal" 
                                                data-user-id="<?= (int)$user['id'] ?>">
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

<!-- Modal Ajouter utilisateur -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus"></i> Ajouter un utilisateur
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/admin/users/add" id="addUserForm">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="phone" class="form-label">Téléphone</label>
                        <input type="tel" class="form-control" id="phone" name="phone">
                    </div>
                    <div class="form-group mb-3">
                        <label for="role" class="form-label">Rôle</label>
                        <select class="form-control" id="role" name="role" required>
                            <option value="user">Utilisateur</option>
                            <option value="admin">Administrateur</option>
                        </select>
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

<!-- Modal Éditer utilisateur -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit"></i> Éditer utilisateur
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editUserForm">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label for="edit_nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="edit_nom" name="nom" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="edit_prenom" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="edit_prenom" name="prenom" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="edit_phone" class="form-label">Téléphone</label>
                        <input type="tel" class="form-control" id="edit_phone" name="phone">
                    </div>
                    <div class="form-group mb-3">
                        <label for="edit_role" class="form-label">Rôle</label>
                        <select class="form-control" id="edit_role" name="role" required>
                            <option value="user">Utilisateur</option>
                            <option value="admin">Administrateur</option>
                        </select>
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

<!-- Modal Supprimer utilisateur -->
<div class="modal fade" id="deleteUserModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-danger">
            <div class="modal-header border-danger">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-trash"></i> Supprimer utilisateur
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Êtes-vous sûr de vouloir supprimer cet utilisateur ?</p>
                <p class="text-muted small">Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form method="POST" id="deleteUserForm" style="display:inline;">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
