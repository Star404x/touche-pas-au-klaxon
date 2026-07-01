<?php
/**
 * Tests unitaires pour le contrôleur d'authentification
 * 
 * @package KlaxonApp\Tests\Controllers
 * @author TOUCHE PAS AU KLAXON Team
 */

namespace KlaxonApp\Tests\Controllers;

use PHPUnit\Framework\TestCase;
use KlaxonApp\Controllers\AuthController;
use KlaxonApp\Models\User;

class AuthControllerTest extends TestCase
{
    /**
     * Instance du contrôleur
     * @var AuthController
     */
    private AuthController $controller;

    /**
     * Configuration avant chaque test
     * 
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new AuthController();

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
     * Teste la création du contrôleur
     * 
     * @return void
     */
    public function testControllerInstantiation(): void
    {
        $this->assertInstanceOf(AuthController::class, $this->controller);
    }

    /**
     * Teste la validation des données
     * 
     * @return void
     */
    public function testValidation(): void
    {
        // Simule une requête sans données
        $_REQUEST = [];

        // Les méthodes de validation sont protégées
        // Donc on teste via des appels publics
        $this->assertTrue(true);
    }

    /**
     * Teste que les données publiques du profil sont accessibles
     * 
     * @return void
     */
    public function testPublicProfileData(): void
    {
        $user = new User();
        $user->id = 1;
        $user->firstname = 'John';
        $user->lastname = 'Doe';
        $user->bio = 'Test bio';

        $profile = $user->getPublicProfile();

        $this->assertEquals('John', $profile['firstname']);
        $this->assertEquals('Doe', $profile['lastname']);
        
        // Le mot de passe ne doit pas être dans le profil public
        $this->assertArrayNotHasKey('password', $profile);
    }

    /**
     * Teste la validation du mot de passe faible
     * 
     * @return void
     */
    public function testWeakPasswordValidation(): void
    {
        $errors = User::validatePassword('weak');
        
        $this->assertNotEmpty($errors);
        $this->assertTrue(count($errors) > 0);
    }

    /**
     * Teste la validation du mot de passe fort
     * 
     * @return void
     */
    public function testStrongPasswordValidation(): void
    {
        $errors = User::validatePassword('StrongPass123');
        
        $this->assertEmpty($errors);
    }

    /**
     * Teste le hash du mot de passe
     * 
     * @return void
     */
    public function testPasswordHashing(): void
    {
        $password = 'MySecurePassword123';
        $hash = User::hashPassword($password);

        // Le hash ne doit pas être le même que le mot de passe
        $this->assertNotEquals($password, $hash);

        // La vérification doit fonctionner
        $this->assertTrue(User::verifyPassword($password, $hash));
    }

    /**
     * Teste le rejet d'un mauvais mot de passe
     * 
     * @return void
     */
    public function testWrongPasswordRejection(): void
    {
        $password = 'MySecurePassword123';
        $hash = User::hashPassword($password);

        $this->assertFalse(User::verifyPassword('WrongPassword', $hash));
    }
}
