<?php
/**
 * Tests unitaires pour le service de notifications
 * 
 * @package KlaxonApp\Tests\Services
 * @author TOUCHE PAS AU KLAXON Team
 */

namespace KlaxonApp\Tests\Services;

use PHPUnit\Framework\TestCase;
use KlaxonApp\Services\NotificationService;

class NotificationServiceTest extends TestCase
{
    /**
     * Teste l'envoi de notification basique
     * 
     * @return void
     */
    public function testSendNotification(): void
    {
        // Note: Ce test dépend de la base de données
        // En production, utiliser des mocks
        
        $result = NotificationService::send(
            1,
            'test_notification',
            'Message de test',
            ['key' => 'value']
        );

        // La méthode devrait retourner un booléen
        $this->assertIsBool($result);
    }

    /**
     * Teste la notification de trajet créé
     * 
     * @return void
     */
    public function testNotifyTripCreated(): void
    {
        $result = NotificationService::notifyTripCreated(1, 1);

        $this->assertIsBool($result);
    }

    /**
     * Teste la notification de passager rejoint
     * 
     * @return void
     */
    public function testNotifyPassengerJoined(): void
    {
        $result = NotificationService::notifyPassengerJoined(1, 1, 'John Doe');

        $this->assertIsBool($result);
    }

    /**
     * Teste la notification d'utilisation du klaxon
     * 
     * @return void
     */
    public function testNotifyHornUsed(): void
    {
        $result = NotificationService::notifyHornUsed(1, 1, HORN_SEVERITY_MEDIUM);

        $this->assertIsBool($result);
    }

    /**
     * Teste la notification d'annulation de trajet
     * 
     * @return void
     */
    public function testNotifyTripCancelled(): void
    {
        $result = NotificationService::notifyTripCancelled(
            1,
            1,
            'Raison de l\'annulation'
        );

        $this->assertIsBool($result);
    }

    /**
     * Teste la notification avec données vides
     * 
     * @return void
     */
    public function testNotifyWithoutReason(): void
    {
        $result = NotificationService::notifyTripCancelled(1, 1);

        $this->assertIsBool($result);
    }
}
