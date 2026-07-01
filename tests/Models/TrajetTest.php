<?php
/**
 * Tests unitaires pour le modèle Trajet
 * 
 * @package KlaxonApp\Tests\Models
 * @author TOUCHE PAS AU KLAXON Team
 */

namespace KlaxonApp\Tests\Models;

use PHPUnit\Framework\TestCase;
use KlaxonApp\Models\Trajet;

class TrajetTest extends TestCase
{
    /**
     * Teste la création d'un trajet
     * 
     * @return void
     */
    public function testCreateTrajet(): void
    {
        $trajet = new Trajet();
        $trajet->origin = 'Paris';
        $trajet->destination = 'Lyon';
        $trajet->departure_time = date(DATETIME_FORMAT);

        $this->assertEquals('Paris', $trajet->origin);
        $this->assertEquals('Lyon', $trajet->destination);
    }

    /**
     * Teste le nombre de sièges disponibles
     * 
     * @return void
     */
    public function testAvailableSeats(): void
    {
        $trajet = new Trajet();
        $trajet->passengers_count = 2;

        $available = $trajet->getAvailableSeats();
        $this->assertEquals(TRAJET_MAX_PASSENGERS - 2, $available);
    }

    /**
     * Teste si le trajet est plein
     * 
     * @return void
     */
    public function testIsFull(): void
    {
        $trajet = new Trajet();
        $trajet->passengers_count = TRAJET_MAX_PASSENGERS;

        $this->assertTrue($trajet->isFull());

        $trajet->passengers_count = TRAJET_MAX_PASSENGERS - 1;
        $this->assertFalse($trajet->isFull());
    }

    /**
     * Teste l'ajout de passagers
     * 
     * @return void
     */
    public function testAddPassenger(): void
    {
        $trajet = new Trajet();
        $trajet->passengers_count = 0;

        $success = $trajet->addPassenger(1);
        $this->assertTrue($success);
        $this->assertEquals(1, $trajet->passengers_count);

        // Teste que le trajet plein refuse les passagers
        $trajet->passengers_count = TRAJET_MAX_PASSENGERS;
        $success = $trajet->addPassenger(2);
        $this->assertFalse($success);
    }

    /**
     * Teste le retrait de passagers
     * 
     * @return void
     */
    public function testRemovePassenger(): void
    {
        $trajet = new Trajet();
        $trajet->passengers_count = 2;

        $success = $trajet->removePassenger(1);
        $this->assertTrue($success);
        $this->assertEquals(1, $trajet->passengers_count);

        // Teste que le compte ne descend pas en dessous de 0
        $trajet->passengers_count = 0;
        $success = $trajet->removePassenger(1);
        $this->assertTrue($success);
        $this->assertEquals(0, $trajet->passengers_count);
    }

    /**
     * Teste l'utilisation du klaxon
     * 
     * @return void
     */
    public function testUseHorn(): void
    {
        $trajet = new Trajet();
        $trajet->horns_count = 0;

        $success = $trajet->useHorn(1, HORN_SEVERITY_MEDIUM, 'Attention!');
        $this->assertTrue($success);
        $this->assertEquals(1, $trajet->horns_count);
    }

    /**
     * Teste le limite de klaxons
     * 
     * @return void
     */
    public function testHornLimit(): void
    {
        $trajet = new Trajet();
        $trajet->horns_count = MAX_HORNS_PER_TRIP;

        $remaining = $trajet->getRemainingHorns();
        $this->assertEquals(0, $remaining);

        $success = $trajet->useHorn(1);
        $this->assertFalse($success);
    }

    /**
     * Teste le changement de statut
     * 
     * @return void
     */
    public function testChangeStatus(): void
    {
        $trajet = new Trajet();
        $trajet->status = TRAJET_STATUS_PENDING;

        $this->assertTrue($trajet->confirm());
        $this->assertEquals(TRAJET_STATUS_CONFIRMED, $trajet->status);

        $this->assertTrue($trajet->start());
        $this->assertEquals(TRAJET_STATUS_IN_PROGRESS, $trajet->status);

        $this->assertTrue($trajet->complete());
        $this->assertEquals(TRAJET_STATUS_COMPLETED, $trajet->status);
    }

    /**
     * Teste l'annulation d'un trajet
     * 
     * @return void
     */
    public function testCancel(): void
    {
        $trajet = new Trajet();
        $trajet->status = TRAJET_STATUS_PENDING;

        $this->assertTrue($trajet->cancel('Raison de l\'annulation'));
        $this->assertEquals(TRAJET_STATUS_CANCELLED, $trajet->status);
        $this->assertEquals('Raison de l\'annulation', $trajet->cancellation_reason);
    }

    /**
     * Teste la validation du trajet
     * 
     * @return void
     */
    public function testValidate(): void
    {
        // Données invalides
        $errors = Trajet::validate([
            'origin' => '',
            'destination' => 'Lyon',
        ]);
        $this->assertNotEmpty($errors);

        // Données valides
        $errors = Trajet::validate([
            'origin' => 'Paris',
            'destination' => 'Lyon',
            'departure_time' => date(DATETIME_FORMAT),
            'driver_id' => 1,
            'passengers_count' => 0,
        ]);
        $this->assertEmpty($errors);
    }

    /**
     * Teste les détails du trajet
     * 
     * @return void
     */
    public function testGetDetails(): void
    {
        $trajet = new Trajet();
        $trajet->id = 1;
        $trajet->origin = 'Paris';
        $trajet->destination = 'Lyon';
        $trajet->departure_time = '2024-07-01 09:00:00';
        $trajet->status = TRAJET_STATUS_IN_PROGRESS;
        $trajet->passengers_count = 2;
        $trajet->horns_count = 1;

        $details = $trajet->getDetails();

        $this->assertEquals(1, $details['id']);
        $this->assertEquals('Paris', $details['origin']);
        $this->assertEquals('Lyon', $details['destination']);
        $this->assertEquals(2, $details['passengers_count']);
        $this->assertTrue(is_numeric($details['available_seats']));
    }
}
