<?php
/**
 * Tests unitaires pour le modèle User
 * 
 * @package KlaxonApp\Tests\Models
 * @author TOUCHE PAS AU KLAXON Team
 */

namespace KlaxonApp\Tests\Models;

use PHPUnit\Framework\TestCase;
use KlaxonApp\Models\User;

class UserTest extends TestCase
{
    /**
     * Teste le hash du mot de passe
     * 
     * @return void
     */
    public function testHashPassword(): void
    {
        $password = 'SecurePassword123';
        $hash = User::hashPassword($password);

        $this->assertNotEquals($password, $hash);
        $this->assertTrue(User::verifyPassword($password, $hash));
    }

    /**
     * Teste la vérification du mot de passe
     * 
     * @return void
     */
    public function testVerifyPassword(): void
    {
        $password = 'SecurePassword123';
        $hash = User::hashPassword($password);

        $this->assertTrue(User::verifyPassword($password, $hash));
        $this->assertFalse(User::verifyPassword('WrongPassword', $hash));
    }

    /**
     * Teste la validation du mot de passe
     * 
     * @return void
     */
    public function testValidatePassword(): void
    {
        // Mot de passe trop court
        $errors = User::validatePassword('Short1');
        $this->assertNotEmpty($errors);

        // Mot de passe sans majuscule
        $errors = User::validatePassword('nouppercasepassword123');
        $this->assertNotEmpty($errors);

        // Mot de passe sans chiffre
        $errors = User::validatePassword('NoNumberPassword');
        $this->assertNotEmpty($errors);

        // Mot de passe valide
        $errors = User::validatePassword('SecurePassword123');
        $this->assertEmpty($errors);
    }

    /**
     * Teste les rôles utilisateur
     * 
     * @return void
     */
    public function testUserRoles(): void
    {
        $user = new User();
        $user->role = ROLE_ADMIN;

        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isAgency());
        $this->assertTrue($user->hasRole(ROLE_ADMIN));
    }

    /**
     * Teste l'activation/désactivation
     * 
     * @return void
     */
    public function testActivateDeactivate(): void
    {
        $user = new User();
        $user->is_active = true;

        $this->assertTrue($user->is_active);

        $user->is_active = false;
        $this->assertFalse($user->is_active);
    }

    /**
     * Teste la mise à jour du profil
     * 
     * @return void
     */
    public function testUpdateProfile(): void
    {
        $user = new User();
        $user->firstname = 'John';
        $user->lastname = 'Doe';

        $data = [
            'firstname' => 'Jane',
            'lastname' => 'Smith',
            'phone' => '+33612345678',
            'bio' => 'Je suis un utilisateur',
        ];

        $user->updateProfile($data);

        $this->assertEquals('Jane', $user->firstname);
        $this->assertEquals('Smith', $user->lastname);
        $this->assertEquals('+33612345678', $user->phone);
        $this->assertEquals('Je suis un utilisateur', $user->bio);
    }

    /**
     * Teste le profil public
     * 
     * @return void
     */
    public function testGetPublicProfile(): void
    {
        $user = new User();
        $user->id = 1;
        $user->firstname = 'John';
        $user->lastname = 'Doe';
        $user->avatar_url = 'https://example.com/avatar.jpg';
        $user->bio = 'Mon bio';
        $user->rating = 4.5;

        $profile = $user->getPublicProfile();

        $this->assertEquals(1, $profile['id']);
        $this->assertEquals('John', $profile['firstname']);
        $this->assertEquals('Doe', $profile['lastname']);
        $this->assertEquals('https://example.com/avatar.jpg', $profile['avatar_url']);
        $this->assertEquals('Mon bio', $profile['bio']);
        $this->assertEquals(4.5, $profile['rating']);

        // Vérifie que le mot de passe n'est pas inclus
        $this->assertArrayNotHasKey('password', $profile);
    }
}
