<?php
/**
 * Bootstrap de l'application
 * 
 * Initialisation de tous les services et configurations
 * 
 * @package KlaxonApp\Config
 * @author TOUCHE PAS AU KLAXON Team
 */

namespace KlaxonApp\Config;

// ============== Charge les constantes ==============
require_once __DIR__ . '/constants.php';

// ============== Configure le timezone ==============
date_default_timezone_set(TIMEZONE);

// ============== Gestion des erreurs ==============
if (APP_DEBUG) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(E_ALL & ~E_DEPRECATED);
}

// ============== Configuration de la session ==============
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_start();
}

// ============== Créer les répertoires nécessaires ==============
@mkdir(LOGS_PATH, 0755, true);
@mkdir(CACHE_PATH, 0755, true);

/**
 * Enregistre un fonction d'autoload PSR-4 basique
 * 
 * @return void
 */
function registerAutoloader(): void
{
    spl_autoload_register(function (string $class) {
        $prefix = 'KlaxonApp\\';

        if (strpos($class, $prefix) !== 0) {
            return;
        }

        $relative = substr($class, strlen($prefix));
        $file = SRC_PATH . '/' . str_replace('\\', '/', $relative) . '.php';

        if (file_exists($file)) {
            require_once $file;
        }
    });
}

registerAutoloader();

/**
 * Log une message
 * 
 * @param string $message Le message
 * @param string $level Le niveau de log
 * @return void
 */
function log(string $message, string $level = 'info'): void
{
    $filename = LOGS_PATH . '/' . date('Y-m-d') . '.log';
    $timestamp = date(DATETIME_FORMAT);
    $logEntry = "[$timestamp] [$level] $message" . PHP_EOL;

    @file_put_contents($filename, $logEntry, FILE_APPEND);
}

/**
 * Dump une variable
 * 
 * @param mixed $var La variable
 * @param bool $die Arrêter l'exécution
 * @return void
 */
function dump(mixed $var, bool $die = false): void
{
    echo '<pre>';
    var_dump($var);
    echo '</pre>';

    if ($die) {
        exit;
    }
}

/**
 * Decode JSON de manière sécurisée
 * 
 * @param string $json Le JSON
 * @return array Les données
 */
function safeJsonDecode(string $json): array
{
    $decoded = json_decode($json, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        return [];
    }

    return $decoded ?? [];
}

/**
 * Encode JSON de manière sécurisée
 * 
 * @param mixed $data Les données
 * @return string Le JSON
 */
function safeJsonEncode(mixed $data): string
{
    return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
}

/**
 * Retourne une URL sécurisée
 * 
     * @param string $path Le chemin
 * @return string L'URL
 */
function url(string $path = ''): string
{
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

    return $protocol . '://' . $host . '/' . ltrim($path, '/');
}
