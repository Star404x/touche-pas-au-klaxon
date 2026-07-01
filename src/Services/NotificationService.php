<?php
/**
 * Service de notifications
 * 
 * Gère les notifications utilisateur et les alertes.
 * 
 * @package KlaxonApp\Services
 * @author TOUCHE PAS AU KLAXON Team
 */

namespace KlaxonApp\Services;

use KlaxonApp\Models\User;

/**
 * Service de gestion des notifications
 */
class NotificationService
{
    /**
     * Envoie une notification à un utilisateur
     * 
     * @param int $userId L'ID de l'utilisateur
     * @param string $type Le type de notification
     * @param string $message Le message
     * @param array $data Les données additionnelles
     * @return bool Succès
     */
    public static function send(
        int $userId,
        string $type,
        string $message,
        array $data = []
    ): bool {
        $user = User::find($userId);

        if (!$user) {
            return false;
        }

        // TODO: Implémentation réelle (email, push, etc.)
        // Pour maintenant, juste logger
        log("Notification à $userId: [$type] $message", 'info');

        return true;
    }

    /**
     * Envoie une notification de trajet créé
     * 
     * @param int $userId L'ID de l'utilisateur
     * @param int $trajetId L'ID du trajet
     * @return bool
     */
    public static function notifyTripCreated(int $userId, int $trajetId): bool
    {
        return self::send(
            $userId,
            'trip_created',
            'Votre trajet a été créé',
            ['trajet_id' => $trajetId]
        );
    }

    /**
     * Envoie une notification de passager rejoint
     * 
     * @param int $userId L'ID du chauffeur
     * @param int $trajetId L'ID du trajet
     * @param string $passengerName Le nom du passager
     * @return bool
     */
    public static function notifyPassengerJoined(
        int $userId,
        int $trajetId,
        string $passengerName
    ): bool {
        return self::send(
            $userId,
            'trip_joined',
            "$passengerName a rejoint votre trajet",
            ['trajet_id' => $trajetId, 'passenger_name' => $passengerName]
        );
    }

    /**
     * Envoie une notification d'utilisation du klaxon
     * 
     * @param int $userId L'ID de l'utilisateur
     * @param int $trajetId L'ID du trajet
     * @param int $severity La sévérité
     * @return bool
     */
    public static function notifyHornUsed(int $userId, int $trajetId, int $severity): bool
    {
        $severityText = match ($severity) {
            HORN_SEVERITY_LOW => 'faible',
            HORN_SEVERITY_MEDIUM => 'moyenne',
            HORN_SEVERITY_HIGH => 'haute',
            default => 'inconnue',
        };

        return self::send(
            $userId,
            'horn_used',
            "Klaxon utilisé (sévérité: $severityText) sur votre trajet",
            ['trajet_id' => $trajetId, 'severity' => $severity]
        );
    }

    /**
     * Envoie une notification de trajet annulé
     * 
     * @param int $userId L'ID de l'utilisateur
     * @param int $trajetId L'ID du trajet
     * @param string $reason La raison
     * @return bool
     */
    public static function notifyTripCancelled(
        int $userId,
        int $trajetId,
        string $reason = ''
    ): bool {
        $message = 'Un trajet auquel vous participez a été annulé';

        if ($reason) {
            $message .= ": $reason";
        }

        return self::send(
            $userId,
            'trip_cancelled',
            $message,
            ['trajet_id' => $trajetId, 'reason' => $reason]
        );
    }
}
