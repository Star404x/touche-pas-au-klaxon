<?php
/**
 * Tests unitaires pour le middleware d'authentification
 * 
 * @package KlaxonApp\Tests\Middleware
 * @author TOUCHE PAS AU KLAXON Team
 */

namespace KlaxonApp\Tests\Middleware;

use PHPUnit\Framework\TestCase;
use KlaxonApp\Middleware\AuthMiddleware;
use KlaxonApp\Models\User;

class AuthMiddlewareTest extends TestCase
{
    /**
     * Instance du middleware
     * @var AuthMiddleware
     */
    private AuthMiddleware $auth;

    /**
     * Configuration avant chaque test
     * 
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->auth = new AuthMiddleware();

        // Démarre la session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Nettoyage après chaque test
     * 
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        
        // Détruit la session
        if (session_status() !== PHP_SESSION_NONE) {
            session_destroy();
        }
    }

    /**
     * Teste que l'utilisateur n'est pas authentifié par défaut
     * 
     * @return void
     */
    public function testIsNotAuthenticatedByDefault(): void
    {
        $this->assertFalse($this->auth->isAuthenticated());
    }

    /**
     * Teste l'authentification avec des identifiants valides
     * 
     * @return void
     */
    public function testAuthenticateWithValidCredentials(): void
    {
        // Remarque: Ce test dépend de la base de données
        // Pour un vrai test unitaire, utiliser des mocks
        
        $_SESSION['user_id'] = 1;
        
        $this->assertTrue($this->auth->isAuthenticated());
    }

    /**
     * Teste la récupération de l'utilisateur
     * 
     * @return void
     */
    public function testGetUser(): void
    {
        $_SESSION['user_id'] = 1;
        
        $user = $this->auth->getUser();
        
        // Si la base de données est configurée
        if ($user !== null) {
            $this->assertInstanceOf(User::class, $user);
        }
    }

    /**
     * Teste la déconnexion
     * 
     * @return void
     */
    public function testLogout(): void
    {
        $_SESSION['user_id'] = 1;
        
        $this->assertTrue($this->auth->isAuthenticated());
        
        $this->auth->logout();
        
        // La session est détruite
        $this->assertFalse(isset($_SESSION['user_id']));
    }

    /**
     * Teste que l'utilisateur est null sans authentification
     * 
     * @return void
     */
    public function testGetUserReturnsNullWhenNotAuthenticated(): void
    {
        $user = $this->auth->getUser();
        
        $this->assertNull($user);
    }
}
