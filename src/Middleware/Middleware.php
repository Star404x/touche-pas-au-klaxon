<?php
/**
 * Classe de base pour les middleware
 * 
 * Fournit une structure commune pour tous les middleware.
 * 
 * @package KlaxonApp\Middleware
 * @author TOUCHE PAS AU KLAXON Team
 */

namespace KlaxonApp\Middleware;

abstract class Middleware
{
    /**
     * Exécute le middleware
     * 
     * @return bool Continuer ou non
     */
    abstract public function handle(): bool;

    /**
     * Retourne une réponse d'erreur
     * 
     * @param int $code Code HTTP
     * @param string $message Message d'erreur
     * @return void
     */
    protected function sendError(int $code, string $message): void
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode(['error' => $message]);
        exit;
    }

    /**
     * Obtient une valeur de la requête
     * 
     * @param string $key Clé
     * @param mixed $default Valeur par défaut
     * @return mixed
     */
    protected function getRequestValue(string $key, mixed $default = null): mixed
    {
        return $_REQUEST[$key] ?? $default;
    }

    /**
     * Obtient une valeur de la session
     * 
     * @param string $key Clé
     * @param mixed $default Valeur par défaut
     * @return mixed
     */
    protected function getSessionValue(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Définit une valeur en session
     * 
     * @param string $key Clé
     * @param mixed $value Valeur
     * @return void
     */
    protected function setSessionValue(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }
}
