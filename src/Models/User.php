<?php
/**
 * Modèle Utilisateur
 * 
 * Représente un utilisateur du système (passager, chauffeur, admin).
 * Gère l'authentification et les permissions.
 * 
 * @package KlaxonApp\Models
 * @author TOUCHE PAS AU KLAXON Team
 */

namespace KlaxonApp\Models;

use PDOException;

class User extends Model
{
    /**
     * Nom de la table
     * @var string
     */
    protected string $table = 'users';

    /**
     * Crée un nouvel utilisateur avec mot de passe hashé
     * 
     * @param array $data Données de l'utilisateur
     * @return static L'utilisateur créé
     * @throws PDOException En cas d'erreur
     */
    public static function create(array $data): static
    {
        if (isset($data['password'])) {
            $data['password'] = self::hashPassword($data['password']);
        }

        return parent::create($data);
    }

    /**
     * Hash un mot de passe
     * 
     * @param string $password Mot de passe en clair
     * @return string Le mot de passe hashé
     */
    public static function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => HASH_COST]);
    }

    /**
     * Vérifie un mot de passe
     * 
     * @param string $password Mot de passe en clair
     * @param string $hash Hash du mot de passe
     * @return bool Valide ou non
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Trouve un utilisateur par email
     * 
     * @param string $email L'email
     * @return static|null L'utilisateur ou null
     */
    public static function findByEmail(string $email): ?static
    {
        return static::findWhere(['email' => $email]);
    }

    /**
     * Authentifie un utilisateur par email et mot de passe
     * 
     * @param string $email L'email
     * @param string $password Le mot de passe
     * @return static|null L'utilisateur authentifié ou null
     */
    public static function authenticate(string $email, string $password): ?static
    {
        $user = self::findByEmail($email);

        if (!$user || !self::verifyPassword($password, $user->password)) {
            return null;
        }

        return $user;
    }

    /**
     * Obtient le rôle de l'utilisateur
     * 
     * @return string Le rôle
     */
    public function getRole(): string
    {
        return $this->role ?? ROLE_USER;
    }

    /**
     * Vérifie si l'utilisateur a un rôle spécifique
     * 
     * @param string $role Le rôle à vérifier
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return $this->getRole() === $role;
    }

    /**
     * Vérifie si l'utilisateur est admin
     * 
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(ROLE_ADMIN);
    }

    /**
     * Vérifie si l'utilisateur est une agence
     * 
     * @return bool
     */
    public function isAgency(): bool
    {
        return $this->hasRole(ROLE_AGENCE);
    }

    /**
     * Obtient tous les utilisateurs actifs
     * 
     * @param int $limit Limite
     * @param int $offset Décalage
     * @return array Les utilisateurs
     */
    public static function getActive(int $limit = 100, int $offset = 0): array
    {
        return static::whereAll(
            ['is_active' => true],
            $limit,
            $offset
        );
    }

    /**
     * Obtient tous les chauffeurs
     * 
     * @param int $limit Limite
     * @param int $offset Décalage
     * @return array Les chauffeurs
     */
    public static function getDrivers(int $limit = 100, int $offset = 0): array
    {
        return static::whereAll(
            ['role' => ROLE_DRIVER],
            $limit,
            $offset
        );
    }

    /**
     * Obtient tous les administrateurs
     * 
     * @return array Les administrateurs
     */
    public static function getAdmins(): array
    {
        return static::whereAll(['role' => ROLE_ADMIN]);
    }

    /**
     * Active l'utilisateur
     * 
     * @return bool Succès
     */
    public function activate(): bool
    {
        $this->is_active = true;
        return $this->save();
    }

    /**
     * Désactive l'utilisateur
     * 
     * @return bool Succès
     */
    public function deactivate(): bool
    {
        $this->is_active = false;
        return $this->save();
    }

    /**
     * Change le mot de passe de l'utilisateur
     * 
     * @param string $newPassword Le nouveau mot de passe
     * @return bool Succès
     */
    public function changePassword(string $newPassword): bool
    {
        $this->password = self::hashPassword($newPassword);
        return $this->save();
    }

    /**
     * Met à jour le profil utilisateur
     * 
     * @param array $data Données à mettre à jour
     * @return bool Succès
     */
    public function updateProfile(array $data): bool
    {
        $allowedFields = ['firstname', 'lastname', 'phone', 'bio', 'avatar_url'];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $this->$field = $data[$field];
            }
        }

        return $this->save();
    }

    /**
     * Vérifie si le mot de passe respecte les critères
     * 
     * @param string $password Le mot de passe
     * @return array Les erreurs (vide si valide)
     */
    public static function validatePassword(string $password): array
    {
        $errors = [];

        if (strlen($password) < PASSWORD_MIN_LENGTH) {
            $errors[] = "Le mot de passe doit contenir au moins " . PASSWORD_MIN_LENGTH . " caractères";
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins une majuscule";
        }

        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Le mot de passe doit contenir au moins un chiffre";
        }

        return $errors;
    }

    /**
     * Retourne le profil public de l'utilisateur
     * 
     * @return array Profil public
     */
    public function getPublicProfile(): array
    {
        return [
            'id' => $this->id,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'avatar_url' => $this->avatar_url,
            'bio' => $this->bio,
            'rating' => $this->rating,
        ];
    }
}
