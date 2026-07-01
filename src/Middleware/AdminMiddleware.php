<?php
/**
 * Middleware d'administration
 * 
 * Vérifie que l'utilisateur a les droits d'administration.
 * 
 * @package KlaxonApp\Middleware
 * @author TOUCHE PAS AU KLAXON Team
 */

namespace KlaxonApp\Middleware;

class AdminMiddleware extends AuthMiddleware
{
    /**
     * Vérifie que l'utilisateur est admin
     * 
     * @return bool
     */
    public function handle(): bool
    {
        // D'abord, vérifie l'authentification
        parent::handle();

        $user = $this->getUser();

        if (!$user || !$user->isAdmin()) {
            $this->sendError(403, 'Accès administrateur requis');
        }

        return true;
    }

    /**
     * Vérifie si l'utilisateur est admin
     * 
     * @return bool
     */
    public function isAdmin(): bool
    {
        $user = $this->getUser();
        return $user !== null && $user->isAdmin();
    }

    /**
     * Vérifie si l'utilisateur a le rôle spécifié
     * 
     * @param string $role Le rôle
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        $user = $this->getUser();
        return $user !== null && $user->hasRole($role);
    }
}
