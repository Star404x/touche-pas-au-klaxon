<?php
/**
 * Classe de base pour tous les modèles
 * 
 * Fournit les méthodes CRUD communes et la gestion
 * des timestamps (created_at, updated_at).
 * 
 * @package KlaxonApp\Models
 * @author TOUCHE PAS AU KLAXON Team
 */

namespace KlaxonApp\Models;

use KlaxonApp\Config\Database;
use PDOException;

abstract class Model
{
    /**
     * Nom de la table en base de données
     * @var string
     */
    protected string $table = '';

    /**
     * Clé primaire
     * @var string
     */
    protected string $primaryKey = 'id';

    /**
     * Attributs du modèle
     * @var array
     */
    protected array $attributes = [];

    /**
     * Changements non sauvegardés
     * @var array
     */
    protected array $dirty = [];

    /**
     * Définit un attribut
     * 
     * @param string $key Clé
     * @param mixed $value Valeur
     * @return void
     */
    public function __set(string $key, mixed $value): void
    {
        $this->attributes[$key] = $value;
        $this->dirty[$key] = true;
    }

    /**
     * Récupère un attribut
     * 
     * @param string $key Clé
     * @return mixed La valeur
     */
    public function __get(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Vérifie si un attribut est défini
     * 
     * @param string $key Clé
     * @return bool
     */
    public function __isset(string $key): bool
    {
        return isset($this->attributes[$key]);
    }

    /**
     * Crée un nouvel enregistrement
     * 
     * @param array $data Données du modèle
     * @return static L'instance créée
     * @throws PDOException En cas d'erreur
     */
    public static function create(array $data): static
    {
        $instance = new static();
        
        foreach ($data as $key => $value) {
            $instance->$key = $value;
        }

        $instance->save();

        return $instance;
    }

    /**
     * Trouve un enregistrement par ID
     * 
     * @param mixed $id L'identifiant
     * @return static|null Le modèle ou null
     */
    public static function find(mixed $id): ?static
    {
        $instance = new static();
        $query = sprintf(
            'SELECT * FROM %s WHERE %s = ?',
            $instance->table,
            $instance->primaryKey
        );

        $stmt = Database::query($query, [$id]);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return $instance->hydrate($data);
    }

    /**
     * Récupère tous les enregistrements
     * 
     * @param int $limit Nombre maximum de résultats
     * @param int $offset Décalage
     * @return array Les modèles
     */
    public static function all(int $limit = 100, int $offset = 0): array
    {
        $instance = new static();
        $query = sprintf(
            'SELECT * FROM %s LIMIT ? OFFSET ?',
            $instance->table
        );

        $stmt = Database::query($query, [$limit, $offset]);
        $results = $stmt->fetchAll();

        return array_map(function ($data) {
            return (new static())->hydrate($data);
        }, $results);
    }

    /**
     * Trouve le premier enregistrement correspondant aux conditions
     * 
     * @param array $where Conditions WHERE
     * @return static|null Le modèle ou null
     */
    public static function findWhere(array $where): ?static
    {
        $instance = new static();
        $conditions = [];
        $values = [];

        foreach ($where as $key => $value) {
            $conditions[] = "$key = ?";
            $values[] = $value;
        }

        $query = sprintf(
            'SELECT * FROM %s WHERE %s LIMIT 1',
            $instance->table,
            implode(' AND ', $conditions)
        );

        $stmt = Database::query($query, $values);
        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        return $instance->hydrate($data);
    }

    /**
     * Trouve tous les enregistrements correspondant aux conditions
     * 
     * @param array $where Conditions WHERE
     * @param int $limit Limite
     * @param int $offset Décalage
     * @return array Les modèles
     */
    public static function whereAll(array $where, int $limit = 100, int $offset = 0): array
    {
        $instance = new static();
        $conditions = [];
        $values = [];

        foreach ($where as $key => $value) {
            $conditions[] = "$key = ?";
            $values[] = $value;
        }

        $query = sprintf(
            'SELECT * FROM %s WHERE %s LIMIT ? OFFSET ?',
            $instance->table,
            implode(' AND ', $conditions)
        );

        $values[] = $limit;
        $values[] = $offset;

        $stmt = Database::query($query, $values);
        $results = $stmt->fetchAll();

        return array_map(function ($data) {
            return (new static())->hydrate($data);
        }, $results);
    }

    /**
     * Compte les enregistrements
     * 
     * @param array $where Conditions WHERE optionnelles
     * @return int Le nombre d'enregistrements
     */
    public static function count(array $where = []): int
    {
        $instance = new static();
        $values = [];

        if (empty($where)) {
            $query = sprintf('SELECT COUNT(*) as count FROM %s', $instance->table);
        } else {
            $conditions = [];
            foreach ($where as $key => $value) {
                $conditions[] = "$key = ?";
                $values[] = $value;
            }
            $query = sprintf(
                'SELECT COUNT(*) as count FROM %s WHERE %s',
                $instance->table,
                implode(' AND ', $conditions)
            );
        }

        $stmt = Database::query($query, $values);
        $result = $stmt->fetch();

        return $result['count'] ?? 0;
    }

    /**
     * Sauvegarde le modèle (insert ou update)
     * 
     * @return bool Succès de l'opération
     * @throws PDOException En cas d'erreur
     */
    public function save(): bool
    {
        if (isset($this->attributes[$this->primaryKey])) {
            return $this->update();
        } else {
            return $this->insert();
        }
    }

    /**
     * Insère le modèle
     * 
     * @return bool Succès de l'opération
     */
    protected function insert(): bool
    {
        $this->attributes['created_at'] = date(DATETIME_FORMAT);
        $this->attributes['updated_at'] = date(DATETIME_FORMAT);

        try {
            $id = Database::insert($this->table, $this->attributes);
            $this->attributes[$this->primaryKey] = $id;
            $this->dirty = [];

            return true;
        } catch (PDOException $e) {
            throw new PDOException("Erreur lors de l'insertion: " . $e->getMessage());
        }
    }

    /**
     * Met à jour le modèle
     * 
     * @return bool Succès de l'opération
     */
    protected function update(): bool
    {
        if (empty($this->dirty)) {
            return false;
        }

        $this->attributes['updated_at'] = date(DATETIME_FORMAT);

        try {
            Database::update(
                $this->table,
                $this->attributes,
                "{$this->primaryKey} = ?",
                [$this->attributes[$this->primaryKey]]
            );
            $this->dirty = [];

            return true;
        } catch (PDOException $e) {
            throw new PDOException("Erreur lors de la mise à jour: " . $e->getMessage());
        }
    }

    /**
     * Supprime le modèle
     * 
     * @return bool Succès de l'opération
     */
    public function delete(): bool
    {
        if (!isset($this->attributes[$this->primaryKey])) {
            return false;
        }

        try {
            Database::delete(
                $this->table,
                "{$this->primaryKey} = ?",
                [$this->attributes[$this->primaryKey]]
            );

            return true;
        } catch (PDOException $e) {
            throw new PDOException("Erreur lors de la suppression: " . $e->getMessage());
        }
    }

    /**
     * Hydrate le modèle avec des données
     * 
     * @param array $data Données
     * @return static Le modèle hydraté
     */
    protected function hydrate(array $data): static
    {
        $this->attributes = $data;
        $this->dirty = [];

        return $this;
    }

    /**
     * Retourne les attributs du modèle
     * 
     * @return array Les attributs
     */
    public function toArray(): array
    {
        return $this->attributes;
    }

    /**
     * Retourne le JSON du modèle
     * 
     * @return string JSON
     */
    public function toJson(): string
    {
        return json_encode($this->attributes);
    }

    /**
     * Retourne l'ID du modèle
     * 
     * @return mixed L'ID
     */
    public function getId(): mixed
    {
        return $this->attributes[$this->primaryKey] ?? null;
    }
}
