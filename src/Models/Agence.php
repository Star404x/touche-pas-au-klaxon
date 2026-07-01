<?php
/**
 * Modèle Agence
 * 
 * Représente une agence de transport ou un service de partage.
 * Gère les trajets de l'agence et les statistiques.
 * 
 * @package KlaxonApp\Models
 * @author TOUCHE PAS AU KLAXON Team
 */

namespace KlaxonApp\Models;

use PDOException;

class Agence extends Model
{
    /**
     * Nom de la table
     * @var string
     */
    protected string $table = 'agences';

    /**
     * Crée une nouvelle agence
     * 
     * @param array $data Données de l'agence
     * @return static L'agence créée
     * @throws PDOException En cas d'erreur
     */
    public static function create(array $data): static
    {
        // Assure qu'une agence est active par défaut
        if (!isset($data['is_active'])) {
            $data['is_active'] = true;
        }

        return parent::create($data);
    }

    /**
     * Trouve une agence par SIRET
     * 
     * @param string $siret Le SIRET
     * @return static|null L'agence ou null
     */
    public static function findBySiret(string $siret): ?static
    {
        return static::findWhere(['siret' => $siret]);
    }

    /**
     * Trouve une agence par slug
     * 
     * @param string $slug Le slug
     * @return static|null L'agence ou null
     */
    public static function findBySlug(string $slug): ?static
    {
        return static::findWhere(['slug' => $slug]);
    }

    /**
     * Obtient toutes les agences actives
     * 
     * @param int $limit Limite
     * @param int $offset Décalage
     * @return array Les agences
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
     * Obtient les trajets de l'agence
     * 
     * @param int $limit Limite
     * @param int $offset Décalage
     * @return array Les trajets
     */
    public function getTrajects(int $limit = 50, int $offset = 0): array
    {
        return Trajet::whereAll(
            ['agence_id' => $this->id],
            $limit,
            $offset
        );
    }

    /**
     * Compte les trajets actifs de l'agence
     * 
     * @return int Le nombre de trajets
     */
    public function countActiveTrajects(): int
    {
        return Trajet::count([
            'agence_id' => $this->id,
            'status' => TRAJET_STATUS_IN_PROGRESS,
        ]);
    }

    /**
     * Compte le nombre total de trajets
     * 
     * @return int Le nombre total
     */
    public function countTotalTrajects(): int
    {
        return Trajet::count(['agence_id' => $this->id]);
    }

    /**
     * Génère un slug unique pour l'agence
     * 
     * @param string $name Nom de l'agence
     * @return string Le slug
     */
    private function generateSlug(string $name): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        $originalSlug = $slug;
        $counter = 1;

        while (static::findBySlug($slug) !== null) {
            $slug = $originalSlug . '-' . $counter++;
        }

        return $slug;
    }

    /**
     * Valide les données de l'agence
     * 
     * @param array $data Données à valider
     * @return array Les erreurs (vide si valide)
     */
    public static function validate(array $data): array
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors[] = "Le nom de l'agence est requis";
        } elseif (strlen($data['name']) > 255) {
            $errors[] = "Le nom ne doit pas dépasser 255 caractères";
        }

        if (!empty($data['siret']) && !preg_match('/^\d{14}$/', $data['siret'])) {
            $errors[] = "Le SIRET doit contenir 14 chiffres";
        }

        if (!empty($data['email']) && !preg_match(EMAIL_REGEX, $data['email'])) {
            $errors[] = "L'email est invalide";
        }

        if (!empty($data['phone']) && !preg_match(PHONE_REGEX, $data['phone'])) {
            $errors[] = "Le numéro de téléphone est invalide";
        }

        return $errors;
    }

    /**
     * Active l'agence
     * 
     * @return bool Succès
     */
    public function activate(): bool
    {
        $this->is_active = true;
        return $this->save();
    }

    /**
     * Désactive l'agence
     * 
     * @return bool Succès
     */
    public function deactivate(): bool
    {
        $this->is_active = false;
        return $this->save();
    }

    /**
     * Met à jour les informations de l'agence
     * 
     * @param array $data Données à mettre à jour
     * @return bool Succès
     */
    public function updateInfo(array $data): bool
    {
        $allowedFields = [
            'name', 'description', 'email', 'phone',
            'address', 'city', 'postal_code', 'country',
            'website', 'logo_url'
        ];

        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $this->$field = $data[$field];
            }
        }

        return $this->save();
    }

    /**
     * Récupère les statistiques de l'agence
     * 
     * @return array Les statistiques
     */
    public function getStats(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'total_trajects' => $this->countTotalTrajects(),
            'active_trajects' => $this->countActiveTrajects(),
            'completed_trajects' => Trajet::count([
                'agence_id' => $this->id,
                'status' => TRAJET_STATUS_COMPLETED,
            ]),
            'rating' => $this->rating ?? 5.0,
            'is_active' => $this->is_active,
        ];
    }

    /**
     * Retourne le profil public de l'agence
     * 
     * @return array Profil public
     */
    public function getPublicProfile(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'website' => $this->website,
            'logo_url' => $this->logo_url,
            'rating' => $this->rating ?? 5.0,
        ];
    }
}
