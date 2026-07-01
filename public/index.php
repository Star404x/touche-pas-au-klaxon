<?php
/**
 * Point d'entrée principal de l'application
 * 
 * @package KlaxonApp
 * @author TOUCHE PAS AU KLAXON Team
 */

// Charge le bootstrap
require_once dirname(dirname(__FILE__)) . '/config/bootstrap.php';

use KlaxonApp\Router;

// Initialise et exécute le routeur
Router::run();
