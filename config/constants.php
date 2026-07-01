<?php
/**
 * Constantes globales de l'application TOUCHE PAS AU KLAXON
 * 
 * @package KlaxonApp\Config
 */

// ============== Environnement ==============
define('APP_ENV', $_ENV['APP_ENV'] ?? 'development');
define('APP_DEBUG', APP_ENV === 'development');
define('APP_NAME', 'TOUCHE PAS AU KLAXON');
define('APP_VERSION', '1.0.0');

// ============== Chemins ==============
define('ROOT_PATH', dirname(dirname(__FILE__)));
define('SRC_PATH', ROOT_PATH . '/src');
define('CONFIG_PATH', ROOT_PATH . '/config');
define('LOGS_PATH', ROOT_PATH . '/logs');
define('CACHE_PATH', ROOT_PATH . '/cache');

// ============== Base de données ==============
define('DB_HOST', $_ENV['DB_HOST'] ?? 'localhost');
define('DB_PORT', $_ENV['DB_PORT'] ?? 5432);
define('DB_NAME', $_ENV['DB_NAME'] ?? 'klaxon_db');
define('DB_USER', $_ENV['DB_USER'] ?? 'klaxon_user');
define('DB_PASS', $_ENV['DB_PASS'] ?? 'password');
define('DB_DRIVER', $_ENV['DB_DRIVER'] ?? 'pgsql');

// ============== Sessions ==============
define('SESSION_TIMEOUT', 3600); // 1 heure
define('SESSION_NAME', 'KLAXON_SESSION');

// ============== Sécurité ==============
define('HASH_ALGORITHM', 'bcrypt');
define('HASH_COST', 12);
define('CSRF_TOKEN_LENGTH', 32);
define('PASSWORD_MIN_LENGTH', 8);

// ============== API & Routes ==============
define('API_VERSION', 'v1');
define('ROUTES_CACHE', false); // Activer en production

// ============== Rôles & Permissions ==============
define('ROLE_ADMIN', 'admin');
define('ROLE_AGENCE', 'agence');
define('ROLE_USER', 'user');
define('ROLE_DRIVER', 'driver');

// ============== Trajet ==============
define('TRAJET_STATUS_PENDING', 'pending');
define('TRAJET_STATUS_CONFIRMED', 'confirmed');
define('TRAJET_STATUS_IN_PROGRESS', 'in_progress');
define('TRAJET_STATUS_COMPLETED', 'completed');
define('TRAJET_STATUS_CANCELLED', 'cancelled');

define('TRAJET_MAX_PASSENGERS', 4);
define('TRAJET_RADIUS_KM', 50);

// ============== Communication ==============
define('HORN_SEVERITY_LOW', 1);
define('HORN_SEVERITY_MEDIUM', 2);
define('HORN_SEVERITY_HIGH', 3);

define('MAX_HORNS_PER_TRIP', 3);
define('HORN_COOLDOWN_SECONDS', 300); // 5 minutes

// ============== Formatage ==============
define('DATE_FORMAT', 'Y-m-d');
define('TIME_FORMAT', 'H:i:s');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');
define('TIMEZONE', $_ENV['TIMEZONE'] ?? 'Europe/Paris');

// ============== Notifications ==============
define('NOTIFICATION_TYPES', [
    'trip_created' => 'Trajet créé',
    'trip_joined' => 'Passager rejoint',
    'trip_cancelled' => 'Trajet annulé',
    'horn_used' => 'Klaxon utilisé',
    'message_received' => 'Message reçu',
]);

// ============== Validations ==============
define('EMAIL_REGEX', '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/');
define('PHONE_REGEX', '/^\+?[1-9]\d{1,14}$/');
define('NAME_MIN_LENGTH', 2);
define('NAME_MAX_LENGTH', 255);
define('BIO_MAX_LENGTH', 500);
define('PHONE_MAX_LENGTH', 20);
