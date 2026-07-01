<?php
/**
 * Contrôleur d'authentification
 * 
 * Gère l'inscription, connexion et déconnexion.
 * 
 * @package KlaxonApp\Controllers
 * @author TOUCHE PAS AU KLAXON Team
 */

namespace KlaxonApp\Controllers;

use KlaxonApp\Models\User;

class AuthController extends Controller
{
    /**
     * Affiche le formulaire de connexion
     * 
     * @return void
     */
    public function loginForm(): void
    {
        if ($this->isAuthenticated()) {
            $this->redirect('/dashboard');
        }

        $this->setViewData('title', 'Connexion');
        $this->renderView('auth/login');
    }

    /**
     * Traite la connexion
     * 
     * @return void
     */
    public function login(): void
    {
        $errors = $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!empty($errors)) {
            $this->error('Erreurs de validation', 400, ['errors' => $errors]);
        }

        $email = $this->getInput('email');
        $password = $this->getInput('password');

        if (!$this->auth->authenticate($email, $password)) {
            $this->error('Email ou mot de passe incorrect', 401);
        }

        $this->success(null, 'Connexion réussie');
    }

    /**
     * Affiche le formulaire d'inscription
     * 
     * @return void
     */
    public function registerForm(): void
    {
        if ($this->isAuthenticated()) {
            $this->redirect('/dashboard');
        }

        $this->setViewData('title', 'Inscription');
        $this->renderView('auth/register');
    }

    /**
     * Traite l'inscription
     * 
     * @return void
     */
    public function register(): void
    {
        $errors = $this->validate([
            'firstname' => 'required|min:2',
            'lastname' => 'required|min:2',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'password_confirm' => 'required',
        ]);

        if (!empty($errors)) {
            $this->error('Erreurs de validation', 400, ['errors' => $errors]);
        }

        $email = $this->getInput('email');
        $password = $this->getInput('password');
        $passwordConfirm = $this->getInput('password_confirm');

        // Vérifie que l'email n'existe pas
        if (User::findByEmail($email) !== null) {
            $this->error('Cet email est déjà utilisé', 400);
        }

        // Vérifie que les mots de passe correspondent
        if ($password !== $passwordConfirm) {
            $this->error('Les mots de passe ne correspondent pas', 400);
        }

        // Valide le mot de passe
        $passwordErrors = User::validatePassword($password);
        if (!empty($passwordErrors)) {
            $this->error('Mot de passe faible', 400, ['errors' => $passwordErrors]);
        }

        try {
            $user = User::create([
                'firstname' => $this->getInput('firstname'),
                'lastname' => $this->getInput('lastname'),
                'email' => $email,
                'password' => $password,
                'role' => ROLE_USER,
                'is_active' => true,
            ]);

            // Authentifie l'utilisateur
            $this->auth->authenticate($email, $password);

            $this->success(
                ['user_id' => $user->id],
                'Inscription réussie',
                201
            );
        } catch (\Exception $e) {
            $this->error('Erreur lors de l\'inscription: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Déconnecte l'utilisateur
     * 
     * @return void
     */
    public function logout(): void
    {
        $this->auth->logout();
        $this->success(null, 'Déconnexion réussie');
    }

    /**
     * Affiche le profil utilisateur
     * 
     * @return void
     */
    public function profile(): void
    {
        if (!$this->isAuthenticated()) {
            $this->error('Authentification requise', 401);
        }

        $user = $this->getAuthUser();

        $this->setViewData('title', 'Mon profil');
        $this->setViewData('user', $user);

        $this->renderView('auth/profile');
    }

    /**
     * Met à jour le profil utilisateur
     * 
     * @return void
     */
    public function updateProfile(): void
    {
        if (!$this->isAuthenticated()) {
            $this->error('Authentification requise', 401);
        }

        $user = $this->getAuthUser();

        $errors = $this->validate([
            'firstname' => 'required|min:2',
            'lastname' => 'required|min:2',
            'phone' => 'phone',
            'bio' => 'max:500',
        ]);

        if (!empty($errors)) {
            $this->error('Erreurs de validation', 400, ['errors' => $errors]);
        }

        $user->updateProfile([
            'firstname' => $this->getInput('firstname'),
            'lastname' => $this->getInput('lastname'),
            'phone' => $this->getInput('phone', ''),
            'bio' => $this->getInput('bio', ''),
        ]);

        $this->success($user->toArray(), 'Profil mis à jour');
    }

    /**
     * Change le mot de passe
     * 
     * @return void
     */
    public function changePassword(): void
    {
        if (!$this->isAuthenticated()) {
            $this->error('Authentification requise', 401);
        }

        $user = $this->getAuthUser();

        $errors = $this->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8',
            'confirm_password' => 'required',
        ]);

        if (!empty($errors)) {
            $this->error('Erreurs de validation', 400, ['errors' => $errors]);
        }

        $currentPassword = $this->getInput('current_password');
        $newPassword = $this->getInput('new_password');
        $confirmPassword = $this->getInput('confirm_password');

        // Vérifie le mot de passe actuel
        if (!User::verifyPassword($currentPassword, $user->password)) {
            $this->error('Mot de passe actuel incorrect', 401);
        }

        // Vérifie que les nouveaux mots de passe correspondent
        if ($newPassword !== $confirmPassword) {
            $this->error('Les nouveaux mots de passe ne correspondent pas', 400);
        }

        // Valide le nouveau mot de passe
        $passwordErrors = User::validatePassword($newPassword);
        if (!empty($passwordErrors)) {
            $this->error('Mot de passe faible', 400, ['errors' => $passwordErrors]);
        }

        $user->changePassword($newPassword);
        $this->success(null, 'Mot de passe changé avec succès');
    }

    /**
     * Affiche une vue
     * 
     * @param string $view Le chemin de la vue
     * @return void
     */
    protected function renderView(string $view): void
    {
        $viewPath = ROOT_PATH . '/src/Views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            $this->error("Vue '$view' non trouvée", 500);
        }

        extract($this->viewData);
        include $viewPath;
    }
}
