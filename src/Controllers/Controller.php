<?php
/**
 * Classe de base pour tous les contrôleurs
 * 
 * Fournit des méthodes communes pour la gestion
 * des requêtes et des réponses.
 * 
 * @package KlaxonApp\Controllers
 * @author TOUCHE PAS AU KLAXON Team
 */

namespace KlaxonApp\Controllers;

use KlaxonApp\Middleware\AuthMiddleware;
use KlaxonApp\Middleware\DeviceDetectionMiddleware;

abstract class Controller
{
    /**
     * Middleware d'authentification
     * @var AuthMiddleware
     */
    protected AuthMiddleware $auth;

    /**
     * Middleware de détection de périphérique
     * @var DeviceDetectionMiddleware
     */
    protected DeviceDetectionMiddleware $device;

    /**
     * Données à passer aux vues
     * @var array
     */
    protected array $viewData = [];

    /**
     * Constructeur
     */
    public function __construct()
    {
        $this->auth = new AuthMiddleware();
        $this->device = new DeviceDetectionMiddleware();
    }

    /**
     * Définit une donnée de vue
     * 
     * @param string $key Clé
     * @param mixed $value Valeur
     * @return void
     */
    protected function setViewData(string $key, mixed $value): void
    {
        $this->viewData[$key] = $value;
    }

    /**
     * Retourne une réponse JSON
     * 
     * @param mixed $data Les données
     * @param int $statusCode Code HTTP
     * @return void
     */
    protected function json(mixed $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Retourne une réponse avec erreur
     * 
     * @param string $message Message d'erreur
     * @param int $statusCode Code HTTP
     * @param array $data Données supplémentaires
     * @return void
     */
    protected function error(string $message, int $statusCode = 400, array $data = []): void
    {
        $response = array_merge(['error' => $message], $data);
        $this->json($response, $statusCode);
    }

    /**
     * Retourne une réponse avec succès
     * 
     * @param mixed $data Les données
     * @param string $message Message de succès
     * @param int $statusCode Code HTTP
     * @return void
     */
    protected function success(mixed $data = null, string $message = 'Succès', int $statusCode = 200): void
    {
        $response = ['message' => $message];

        if ($data !== null) {
            $response['data'] = $data;
        }

        $this->json($response, $statusCode);
    }

    /**
     * Valide les données de la requête
     * 
     * @param array $rules Les règles de validation
     * @return array Les erreurs (vide si valide)
     */
    protected function validate(array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $rule) {
            $value = $this->getInput($field);

            if ($rule === 'required' && empty($value)) {
                $errors[$field] = "$field est requis";
            }

            if ($rule === 'email' && !empty($value) && !preg_match(EMAIL_REGEX, $value)) {
                $errors[$field] = "$field doit être un email valide";
            }

            if ($rule === 'phone' && !empty($value) && !preg_match(PHONE_REGEX, $value)) {
                $errors[$field] = "$field doit être un numéro de téléphone valide";
            }

            if (strpos($rule, 'min:') === 0) {
                $min = (int) explode(':', $rule)[1];
                if (!empty($value) && strlen($value) < $min) {
                    $errors[$field] = "$field doit contenir au moins $min caractères";
                }
            }

            if (strpos($rule, 'max:') === 0) {
                $max = (int) explode(':', $rule)[1];
                if (!empty($value) && strlen($value) > $max) {
                    $errors[$field] = "$field ne doit pas dépasser $max caractères";
                }
            }
        }

        return $errors;
    }

    /**
     * Récupère une valeur d'entrée
     * 
     * @param string $key Clé
     * @param mixed $default Valeur par défaut
     * @return mixed
     */
    protected function getInput(string $key, mixed $default = null): mixed
    {
        return $_REQUEST[$key] ?? $default;
    }

    /**
     * Récupère toutes les entrées
     * 
     * @return array Les entrées
     */
    protected function getAllInput(): array
    {
        return $_REQUEST;
    }

    /**
     * Redirige vers une URL
     * 
     * @param string $url L'URL
     * @param int $statusCode Code HTTP
     * @return void
     */
    protected function redirect(string $url, int $statusCode = 302): void
    {
        http_response_code($statusCode);
        header("Location: $url");
        exit;
    }

    /**
     * Retourne le statut HTTP actuel
     * 
     * @return int Le code HTTP
     */
    protected function getStatusCode(): int
    {
        return http_response_code();
    }

    /**
     * Définit un en-tête HTTP
     * 
     * @param string $header L'en-tête
     * @param mixed $value La valeur
     * @return void
     */
    protected function setHeader(string $header, mixed $value): void
    {
        header("$header: $value");
    }

    /**
     * Obtient l'utilisateur actuel
     * 
     * @return \KlaxonApp\Models\User|null L'utilisateur
     */
    protected function getAuthUser()
    {
        return $this->auth->getUser();
    }

    /**
     * Vérifie que l'utilisateur est authentifié
     * 
     * @return bool
     */
    protected function isAuthenticated(): bool
    {
        return $this->auth->isAuthenticated();
    }
}
