<?php
/**
 * Configuration du routeur izniburak/router
 * 
 * Gère toutes les routes de l'application.
 * 
 * @package KlaxonApp
 * @author TOUCHE PAS AU KLAXON Team
 */

namespace KlaxonApp;

use izniburak\Router\Router as IzniburakRouter;
use KlaxonApp\Controllers\HomeController;
use KlaxonApp\Controllers\AuthController;
use KlaxonApp\Controllers\TripController;
use KlaxonApp\Controllers\AdminController;

class Router
{
    /**
     * Instance du routeur
     * @var IzniburakRouter
     */
    private static IzniburakRouter $router;

    /**
     * Initialise le routeur
     * 
     * @return void
     */
    public static function init(): void
    {
        self::$router = new IzniburakRouter();
        self::registerRoutes();
    }

    /**
     * Enregistre toutes les routes
     * 
     * @return void
     */
    private static function registerRoutes(): void
    {
        // ============== Pages publiques ==============
        self::$router->get('/', [HomeController::class, 'index']);
        self::$router->get('/about', [HomeController::class, 'about']);
        self::$router->get('/contact', [HomeController::class, 'contact']);
        self::$router->post('/contact', [HomeController::class, 'contactSubmit']);
        self::$router->get('/trajets', [HomeController::class, 'trajets']);
        self::$router->get('/trajet/:id', [HomeController::class, 'trajet']);
        self::$router->get('/agences', [HomeController::class, 'agences']);
        self::$router->get('/agence/:slug', [HomeController::class, 'agence']);

        // ============== Authentification ==============
        self::$router->get('/login', [AuthController::class, 'loginForm']);
        self::$router->post('/login', [AuthController::class, 'login']);
        self::$router->get('/register', [AuthController::class, 'registerForm']);
        self::$router->post('/register', [AuthController::class, 'register']);
        self::$router->post('/logout', [AuthController::class, 'logout']);
        self::$router->get('/profile', [AuthController::class, 'profile']);
        self::$router->post('/profile', [AuthController::class, 'updateProfile']);
        self::$router->post('/change-password', [AuthController::class, 'changePassword']);

        // ============== Trajets (API) ==============
        self::$router->get('/api/trajets', [TripController::class, 'index']);
        self::$router->get('/api/trajet/:id', [TripController::class, 'show']);
        self::$router->post('/api/trajet', [TripController::class, 'create']);
        self::$router->put('/api/trajet/:id', [TripController::class, 'update']);
        self::$router->delete('/api/trajet/:id', [TripController::class, 'delete']);
        self::$router->post('/api/trajet/:id/join', [TripController::class, 'join']);
        self::$router->post('/api/trajet/:id/leave', [TripController::class, 'leave']);
        self::$router->post('/api/trajet/:id/horn', [TripController::class, 'useHorn']);
        self::$router->get('/api/my-trips', [TripController::class, 'myTrips']);
        self::$router->get('/api/trajet/:id/stats', [TripController::class, 'stats']);

        // ============== Administration ==============
        self::$router->get('/admin', [AdminController::class, 'dashboard']);
        self::$router->get('/admin/users', [AdminController::class, 'users']);
        self::$router->get('/admin/user/:id', [AdminController::class, 'userDetail']);
        self::$router->put('/admin/user/:id/toggle', [AdminController::class, 'toggleUser']);
        self::$router->put('/admin/user/:id/role', [AdminController::class, 'changeUserRole']);
        self::$router->delete('/admin/user/:id', [AdminController::class, 'deleteUser']);
        self::$router->get('/admin/agences', [AdminController::class, 'agences']);
        self::$router->get('/admin/agence/:id', [AdminController::class, 'agenceDetail']);
        self::$router->put('/admin/agence/:id/toggle', [AdminController::class, 'toggleAgence']);
        self::$router->get('/admin/trajets', [AdminController::class, 'trajets']);
        self::$router->get('/admin/trajet/:id', [AdminController::class, 'trajetDetail']);
        self::$router->put('/admin/trajet/:id/status', [AdminController::class, 'changeTrajetStatus']);
        self::$router->get('/admin/stats', [AdminController::class, 'globalStats']);

        // ============== Erreurs ==============
        self::$router->get('/403', [HomeController::class, 'accessDenied']);
        self::$router->get('/404', [HomeController::class, 'notFound']);
        self::$router->get('/500', [HomeController::class, 'error']);
    }

    /**
     * Exécute le routeur
     * 
     * @return void
     */
    public static function run(): void
    {
        self::init();
        self::$router->run();
    }

    /**
     * Obtient le routeur
     * 
     * @return IzniburakRouter Le routeur
     */
    public static function getRouter(): IzniburakRouter
    {
        return self::$router;
    }
}
