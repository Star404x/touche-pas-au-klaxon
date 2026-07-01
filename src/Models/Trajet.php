<?php
/**
 * Modèle Trajet
 * 
 * Représente un trajet partagé avec ses passagers,
 * son statut, et la gestion des communications (klaxons).
 * 
 * @package KlaxonApp\Models
 * @author TOUCHE PAS AU KLAXON Team
 */

namespace KlaxonApp\Models;

use PDOException;

class Trajet extends Model
{
    /**
     * Nom de la table
     * @var string
     */
    protected string $table = 'trajets';

    /**
     * Crée un nouveau trajet
     * 
     * @param array $data Données du trajet
     * @return static Le trajet créé
     * @throws PDOException En cas d'erreur
     */
    public static function create(array $data): static
    {
        // Définit le statut par défaut
        if (!isset($data['status'])) {
            $data['status'] = TRAJET_STATUS_PENDING;
        }

        // Initialise les compteurs
        if (!isset($data['passengers_count'])) {
            $data['passengers_count'] = 0;
        }

        if (!isset($data['horns_count'])) {
            $data['horns_count'] = 0;
        }

        return parent::create($data);
    }

    /**
     * Trouve un trajet par son ID
     * 
     * @param mixed $id L'ID du trajet
     * @return static|null Le trajet ou null
     */
    public static function find(mixed $id): ?static
    {
        return parent::find($id);
    }

    /**
     * Obtient les trajets d'une agence
     * 
     * @param int $agenceId L'ID de l'agence
     * @param int $limit Limite
     * @param int $offset Décalage
     * @return array Les trajets
     */
    public static function findByAgence(int $agenceId, int $limit = 50, int $offset = 0): array
    {
        return static::whereAll(
            ['agence_id' => $agenceId],
            $limit,
            $offset
        );
    }

    /**
     * Obtient les trajets d'un chauffeur
     * 
     * @param int $driverId L'ID du chauffeur
     * @param int $limit Limite
     * @param int $offset Décalage
     * @return array Les trajets
     */
    public static function findByDriver(int $driverId, int $limit = 50, int $offset = 0): array
    {
        return static::whereAll(
            ['driver_id' => $driverId],
            $limit,
            $offset
        );
    }

    /**
     * Obtient les trajets disponibles (non pleins, actifs)
     * 
     * @param int $limit Limite
     * @param int $offset Décalage
     * @return array Les trajets disponibles
     */
    public static function getAvailable(int $limit = 50, int $offset = 0): array
    {
        // Récupère tous les trajets actifs
        $trajets = static::whereAll(
            ['status' => TRAJET_STATUS_IN_PROGRESS],
            $limit,
            $offset
        );

        // Filtre ceux qui ne sont pas pleins
        return array_filter($trajets, function (Trajet $trajet) {
            return $trajet->getAvailableSeats() > 0;
        });
    }

    /**
     * Confirme le trajet
     * 
     * @return bool Succès
     */
    public function confirm(): bool
    {
        $this->status = TRAJET_STATUS_CONFIRMED;
        return $this->save();
    }

    /**
     * Lance le trajet
     * 
     * @return bool Succès
     */
    public function start(): bool
    {
        $this->status = TRAJET_STATUS_IN_PROGRESS;
        $this->started_at = date(DATETIME_FORMAT);
        return $this->save();
    }

    /**
     * Termine le trajet
     * 
     * @return bool Succès
     */
    public function complete(): bool
    {
        $this->status = TRAJET_STATUS_COMPLETED;
        $this->ended_at = date(DATETIME_FORMAT);
        return $this->save();
    }

    /**
     * Annule le trajet
     * 
     * @param string $reason Raison de l'annulation
     * @return bool Succès
     */
    public function cancel(string $reason = ''): bool
    {
        $this->status = TRAJET_STATUS_CANCELLED;
        $this->cancellation_reason = $reason;
        $this->cancelled_at = date(DATETIME_FORMAT);
        return $this->save();
    }

    /**
     * Obtient le nombre de sièges disponibles
     * 
     * @return int Nombre de sièges
     */
    public function getAvailableSeats(): int
    {
        return max(0, TRAJET_MAX_PASSENGERS - ($this->passengers_count ?? 0));
    }

    /**
     * Vérifie si le trajet est plein
     * 
     * @return bool
     */
    public function isFull(): bool
    {
        return $this->getAvailableSeats() <= 0;
    }

    /**
     * Ajoute un passager
     * 
     * @param int $passengerId L'ID du passager
     * @return bool Succès
     */
    public function addPassenger(int $passengerId): bool
    {
        if ($this->isFull()) {
            return false;
        }

        $this->passengers_count = ($this->passengers_count ?? 0) + 1;
        return $this->save();
    }

    /**
     * Retire un passager
     * 
     * @param int $passengerId L'ID du passager
     * @return bool Succès
     */
    public function removePassenger(int $passengerId): bool
    {
        $this->passengers_count = max(0, ($this->passengers_count ?? 0) - 1);
        return $this->save();
    }

    /**
     * Utilise un klaxon
     * 
     * @param int $userId L'ID de l'utilisateur
     * @param int $severity La sévérité (1-3)
     * @param string $message Le message
     * @return bool Succès
     */
    public function useHorn(int $userId, int $severity = HORN_SEVERITY_LOW, string $message = ''): bool
    {
        // Vérifie le nombre de klaxons utilisés
        if (($this->horns_count ?? 0) >= MAX_HORNS_PER_TRIP) {
            return false;
        }

        $this->horns_count = ($this->horns_count ?? 0) + 1;
        return $this->save();
    }

    /**
     * Obtient le nombre de klaxons restants
     * 
     * @return int Nombre restant
     */
    public function getRemainingHorns(): int
    {
        return max(0, MAX_HORNS_PER_TRIP - ($this->horns_count ?? 0));
    }

    /**
     * Valide les données du trajet
     * 
     * @param array $data Données à valider
     * @return array Les erreurs (vide si valide)
     */
    public static function validate(array $data): array
    {
        $errors = [];

        if (empty($data['origin'])) {
            $errors[] = "L'origine est requise";
        }

        if (empty($data['destination'])) {
            $errors[] = "La destination est requise";
        }

        if (empty($data['departure_time'])) {
            $errors[] = "L'heure de départ est requise";
        }

        if (empty($data['driver_id'])) {
            $errors[] = "Un chauffeur est requis";
        }

        if (!isset($data['passengers_count']) || $data['passengers_count'] < 0) {
            $errors[] = "Le nombre de passagers invalide";
        }

        return $errors;
    }

    /**
     * Calcule la durée estimée du trajet
     * 
     * @return int Durée en minutes
     */
    public function getEstimatedDuration(): int
    {
        if (!$this->started_at || !$this->ended_at) {
            return $this->estimated_duration ?? 0;
        }

        $start = strtotime($this->started_at);
        $end = strtotime($this->ended_at);

        return (int) (($end - $start) / 60);
    }

    /**
     * Obtient les détails du trajet pour affichage
     * 
     * @return array Les détails
     */
    public function getDetails(): array
    {
        return [
            'id' => $this->id,
            'origin' => $this->origin,
            'destination' => $this->destination,
            'departure_time' => $this->departure_time,
            'estimated_duration' => $this->estimated_duration,
            'status' => $this->status,
            'passengers_count' => $this->passengers_count ?? 0,
            'available_seats' => $this->getAvailableSeats(),
            'horns_count' => $this->horns_count ?? 0,
            'remaining_horns' => $this->getRemainingHorns(),
            'is_full' => $this->isFull(),
            'created_at' => $this->created_at,
        ];
    }

    /**
     * Retourne le trajet au format JSON
     * 
     * @return string JSON
     */
    public function toJson(): string
    {
        return json_encode($this->getDetails());
    }
}
