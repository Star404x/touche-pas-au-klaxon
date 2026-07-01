<?php
/**
 * Template Formulaire de Connexion
 * TOUCHE PAS AU KLAXON
 * 
 * @category Authentication Page
 * @package KlaxonApp\Views
 */

declare(strict_types=1);

$errors = $errors ?? [];
?>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <!-- Titre -->
                <div class="text-center mb-5">
                    <h1 class="h2 mb-2">Connexion</h1>
                    <p class="text-muted">Accédez à votre compte TOUCHE PAS AU KLAXON</p>
                </div>

                <!-- Formulaire -->
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <form method="POST" action="/login" id="loginForm" novalidate>
                            <!-- Email -->
                            <div class="form-group mb-3">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope"></i> Email
                                </label>
                                <input type="email" 
                                       class="form-control <?= !empty($errors['email']) ? 'is-invalid' : '' ?>"
                                       id="email" 
                                       name="email"
                                       placeholder="votre.email@example.fr"
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                       required
                                       autocomplete="email">
                                <?php if (!empty($errors['email'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($errors['email']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Mot de passe -->
                            <div class="form-group mb-4">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i> Mot de passe
                                </label>
                                <input type="password" 
                                       class="form-control <?= !empty($errors['password']) ? 'is-invalid' : '' ?>"
                                       id="password" 
                                       name="password"
                                       placeholder="••••••••"
                                       required
                                       autocomplete="current-password">
                                <?php if (!empty($errors['password'])): ?>
                                    <div class="invalid-feedback d-block">
                                        <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($errors['password']) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Erreur générale -->
                            <?php if (!empty($errors['general'])): ?>
                                <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <strong>Erreur</strong><br>
                                    <?= htmlspecialchars($errors['general']) ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            <?php endif; ?>

                            <!-- Se souvenir de moi -->
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Se souvenir de moi
                                </label>
                            </div>

                            <!-- Bouton connexion -->
                            <button type="submit" class="btn btn-primary w-100 btn-lg mb-3">
                                <i class="fas fa-sign-in-alt"></i> Se connecter
                            </button>

                            <!-- Lien mot de passe oublié -->
                            <div class="text-center">
                                <a href="#" class="text-muted small">Mot de passe oublié ?</a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Lien inscription -->
                <div class="text-center mt-4">
                    <p class="text-muted">
                        Pas encore de compte ? 
                        <a href="#" class="fw-bold">Contactez votre administrateur</a>
                    </p>
                </div>

                <!-- Identifiants de test -->
                <div class="alert alert-info mt-4 small">
                    <strong>🔑 Identifiants de test :</strong><br>
                    Email: <code>admin@email.fr</code><br>
                    Mot de passe: <code>password123</code>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="/js/validation.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            // Validation client côté
            if (!loginForm.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
                loginForm.classList.add('was-validated');
            }
        });
    }
});
</script>
