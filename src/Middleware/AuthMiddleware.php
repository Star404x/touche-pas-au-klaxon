<?php
/**
 * Middleware d'authentification
 * 
 * Vérifie que l'utilisateur est authentifié.
 * Redirige vers la page de connexion si nécessaire.
 * 
 * @package KlaxonApp\Middleware
 * @author TOUCHE PAS AU KLAXON Team
 */

namespace KlaxonApp\Middleware;

use KlaxonApp\Models\User;

class AuthMiddleware extends Middleware
{
    /**
     * Vérifie que l'utilisateur est authentifié
     * 
     * @return bool L'utilisateur est authentifié
     */
    public function handle(): bool
    {
        // Démarre la session si nécessaire
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $this->getSessionValue('user_id');

        if (!$userId) {
            $this->sendError(401, 'Authentification requise');
        }

        $user = User::find($userId);

        if (!$user) {
            session_destroy();
            $this->sendError(401, 'Utilisateur non trouvé');
        }

        // Stocke l'utilisateur en session
        $_SESSION['user'] = $user->toArray();

        return true;
    }

    /**
     * Vérifie que l'utilisateur est authentifié et actif
     * 
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0;
    }

    /**
     * Récupère l'utilisateur actuel
     * 
     * @return User|null L'utilisateur ou null
     */
    public function getUser(): ?User
    {
        if (!$this->isAuthenticated()) {
            return null;
        }

        return User::find($_SESSION['user_id']);
    }

    /**
     * Authentifie un utilisateur
     * 
     * @param string $email L'email
     * @param string $password Le mot de passe
     * @return bool Succès
     */
    public function authenticate(string $email, string $password): bool
    {
        $user = User::authenticate($email, $password);

        if (!$user) {
            return false;
        }

        if (!$user->is_active) {
            return false;
        }

        // Démarre la session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user_id'] = $user->id;
        $_SESSION['user'] = $user->toArray();

        return true;
    }

    /**
     * Déconnecte l'utilisateur
     * 
     * @return void
     */
    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_destroy();
    }
}
