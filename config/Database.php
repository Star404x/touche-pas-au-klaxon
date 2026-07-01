<?php
/**
 * Gestionnaire de connexion à la base de données PDO
 * 
 * Classe singleton pour gérer les connexions PDO avec support
 * de plusieurs drivers (PostgreSQL, MySQL, SQLite).
 * 
 * @package KlaxonApp\Config
 * @author TOUCHE PAS AU KLAXON Team
 */

namespace KlaxonApp\Config;

use PDO;
use PDOException;

class Database
{
    /**
     * Instance singleton de la connexion PDO
     * @var PDO|null
     */
    private static ?PDO $instance = null;

    /**
     * Configuration de la base de données
     * @var array
     */
    private static array $config = [];

    /**
     * Constructeur privé (pattern singleton)
     */
    private function __construct() {}

    /**
     * Obtient l'instance singleton de PDO
     * 
     * @return PDO La connexion à la base de données
     * @throws PDOException Si la connexion échoue
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            self::$instance = self::createConnection();
        }

        return self::$instance;
    }

    /**
     * Crée une nouvelle connexion PDO
     * 
     * @return PDO La connexion établie
     * @throws PDOException Si la connexion échoue
     */
    private static function createConnection(): PDO
    {
        $driver = DB_DRIVER;
        $dsn = '';

        try {
            match ($driver) {
                'pgsql' => $dsn = self::buildPostgreSqlDSN(),
                'mysql' => $dsn = self::buildMysqlDSN(),
                'sqlite' => $dsn = self::buildSqliteDSN(),
                default => throw new PDOException("Driver '$driver' non supporté"),
            };

            $pdo = new PDO(
                $dsn,
                DB_USER,
                DB_PASS,
                self::getAttributes()
            );

            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

            return $pdo;
        } catch (PDOException $e) {
            throw new PDOException(
                "Erreur de connexion à la base de données: " . $e->getMessage(),
                0,
                $e
            );
        }
    }

    /**
     * Construit le DSN PostgreSQL
     * 
     * @return string Le DSN PostgreSQL
     */
    private static function buildPostgreSqlDSN(): string
    {
        return sprintf(
            'pgsql:host=%s;port=%d;dbname=%s;sslmode=prefer',
            DB_HOST,
            DB_PORT,
            DB_NAME
        );
    }

    /**
     * Construit le DSN MySQL
     * 
     * @return string Le DSN MySQL
     */
    private static function buildMysqlDSN(): string
    {
        return sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            DB_HOST,
            DB_PORT,
            DB_NAME
        );
    }

    /**
     * Construit le DSN SQLite
     * 
     * @return string Le DSN SQLite
     */
    private static function buildSqliteDSN(): string
    {
        return sprintf(
            'sqlite:%s/database.sqlite',
            ROOT_PATH
        );
    }

    /**
     * Retourne les attributs PDO par défaut
     * 
     * @return array Les attributs PDO
     */
    private static function getAttributes(): array
    {
        return [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT => 5,
        ];
    }

    /**
     * Exécute une requête préparée
     * 
     * @param string $query Requête SQL
     * @param array $params Paramètres de la requête
     * @return \PDOStatement Le statement exécuté
     * @throws PDOException En cas d'erreur
     */
    public static function query(string $query, array $params = []): \PDOStatement
    {
        $pdo = self::getInstance();
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);

        return $stmt;
    }

    /**
     * Insère un enregistrement
     * 
     * @param string $table Nom de la table
     * @param array $data Données à insérer
     * @return string L'ID de l'enregistrement inséré
     * @throws PDOException En cas d'erreur
     */
    public static function insert(string $table, array $data): string
    {
        $columns = array_keys($data);
        $placeholders = array_fill(0, count($columns), '?');

        $query = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $table,
            implode(', ', $columns),
            implode(', ', $placeholders)
        );

        self::query($query, array_values($data));

        return self::getInstance()->lastInsertId();
    }

    /**
     * Met à jour des enregistrements
     * 
     * @param string $table Nom de la table
     * @param array $data Données à mettre à jour
     * @param string $where Condition WHERE
     * @param array $whereParams Paramètres WHERE
     * @return int Nombre de lignes affectées
     * @throws PDOException En cas d'erreur
     */
    public static function update(
        string $table,
        array $data,
        string $where,
        array $whereParams = []
    ): int {
        $sets = [];
        foreach (array_keys($data) as $column) {
            $sets[] = "$column = ?";
        }

        $query = sprintf(
            'UPDATE %s SET %s WHERE %s',
            $table,
            implode(', ', $sets),
            $where
        );

        $params = array_merge(array_values($data), $whereParams);
        $stmt = self::query($query, $params);

        return $stmt->rowCount();
    }

    /**
     * Supprime des enregistrements
     * 
     * @param string $table Nom de la table
     * @param string $where Condition WHERE
     * @param array $params Paramètres WHERE
     * @return int Nombre de lignes supprimées
     * @throws PDOException En cas d'erreur
     */
    public static function delete(string $table, string $where, array $params = []): int
    {
        $query = sprintf('DELETE FROM %s WHERE %s', $table, $where);
        $stmt = self::query($query, $params);

        return $stmt->rowCount();
    }

    /**
     * Ferme la connexion
     * 
     * @return void
     */
    public static function close(): void
    {
        self::$instance = null;
    }

    /**
     * Interdiction du clonage
     */
    private function __clone() {}

    /**
     * Interdiction de la désérialisation
     */
    public function __wakeup(): void
    {
        throw new \Exception("Désérialisation non autorisée");
    }
}
