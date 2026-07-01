<?php
/**
 * Contrôleur des trajets
 * 
 * Gère les opérations sur les trajets:
 * créer, rejoindre, utiliser le klaxon, etc.
 * 
 * @package KlaxonApp\Controllers
 * @author TOUCHE PAS AU KLAXON Team
 */

namespace KlaxonApp\Controllers;

use KlaxonApp\Models\Trajet;
use KlaxonApp\Models\User;

class TripController extends Controller
{
    /**
     * Liste les trajets disponibles
     * 
     * @return void
     */
    public function index(): void
    {
        $page = (int) ($this->getInput('page') ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $trajets = Trajet::getAvailable($limit, $offset);
        $total = Trajet::count();

        $this->setViewData('trajets', $trajets);
        $this->setViewData('total', $total);
        $this->setViewData('page', $page);
        $this->setViewData('pages', ceil($total / $limit));

        $this->json(['trajets' => $trajets, 'total' => $total]);
    }

    /**
     * Récupère les détails d'un trajet
     * 
     * @param int $id L'ID du trajet
     * @return void
     */
    public function show(int $id): void
    {
        $trajet = Trajet::find($id);

        if (!$trajet) {
            $this->error('Trajet non trouvé', 404);
        }

        $this->json($trajet->getDetails());
    }

    /**
     * Crée un nouveau trajet
     * 
     * @return void
     */
    public function create(): void
    {
        if (!$this->isAuthenticated()) {
            $this->error('Authentification requise', 401);
        }

        $errors = $this->validate([
            'origin' => 'required',
            'destination' => 'required',
            'departure_time' => 'required',
            'agence_id' => 'required',
        ]);

        if (!empty($errors)) {
            $this->error('Erreurs de validation', 400, ['errors' => $errors]);
        }

        $user = $this->getAuthUser();
        $origin = $this->getInput('origin');
        $destination = $this->getInput('destination');
        $departureTime = $this->getInput('departure_time');
        $agenceId = (int) $this->getInput('agence_id');
        $estimatedDuration = (int) ($this->getInput('estimated_duration') ?? 0);
        $maxPassengers = (int) ($this->getInput('max_passengers') ?? TRAJET_MAX_PASSENGERS);

        try {
            $trajet = Trajet::create([
                'origin' => $origin,
                'destination' => $destination,
                'departure_time' => $departureTime,
                'estimated_duration' => $estimatedDuration,
                'driver_id' => $user->id,
                'agence_id' => $agenceId,
                'passengers_count' => 0,
                'horns_count' => 0,
                'status' => TRAJET_STATUS_PENDING,
            ]);

            $this->success(
                $trajet->getDetails(),
                'Trajet créé avec succès',
                201
            );
        } catch (\Exception $e) {
            $this->error('Erreur lors de la création: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Met à jour un trajet
     * 
     * @param int $id L'ID du trajet
     * @return void
     */
    public function update(int $id): void
    {
        if (!$this->isAuthenticated()) {
            $this->error('Authentification requise', 401);
        }

        $trajet = Trajet::find($id);

        if (!$trajet) {
            $this->error('Trajet non trouvé', 404);
        }

        $user = $this->getAuthUser();

        if ($trajet->driver_id !== $user->id && !$user->isAdmin()) {
            $this->error('Vous n\'avez pas le droit de modifier ce trajet', 403);
        }

        $updates = [];
        
        if ($this->getInput('status')) {
            $updates['status'] = $this->getInput('status');
        }
        if ($this->getInput('destination')) {
            $updates['destination'] = $this->getInput('destination');
        }
        if ($this->getInput('departure_time')) {
            $updates['departure_time'] = $this->getInput('departure_time');
        }

        foreach ($updates as $key => $value) {
            $trajet->$key = $value;
        }

        if ($trajet->save()) {
            $this->success($trajet->getDetails(), 'Trajet mis à jour');
        } else {
            $this->error('Erreur lors de la mise à jour', 500);
        }
    }

    /**
     * Supprime un trajet
     * 
     * @param int $id L'ID du trajet
     * @return void
     */
    public function delete(int $id): void
    {
        if (!$this->isAuthenticated()) {
            $this->error('Authentification requise', 401);
        }

        $trajet = Trajet::find($id);

        if (!$trajet) {
            $this->error('Trajet non trouvé', 404);
        }

        $user = $this->getAuthUser();

        if ($trajet->driver_id !== $user->id && !$user->isAdmin()) {
            $this->error('Vous n\'avez pas le droit de supprimer ce trajet', 403);
        }

        if ($trajet->delete()) {
            $this->success(null, 'Trajet supprimé');
        } else {
            $this->error('Erreur lors de la suppression', 500);
        }
    }

    /**
     * Rejoint un trajet
     * 
     * @param int $id L'ID du trajet
     * @return void
     */
    public function join(int $id): void
    {
        if (!$this->isAuthenticated()) {
            $this->error('Authentification requise', 401);
        }

        $trajet = Trajet::find($id);

        if (!$trajet) {
            $this->error('Trajet non trouvé', 404);
        }

        if ($trajet->isFull()) {
            $this->error('Le trajet est plein', 400);
        }

        $user = $this->getAuthUser();

        if ($trajet->addPassenger($user->id)) {
            $this->success(
                ['available_seats' => $trajet->getAvailableSeats()],
                'Vous avez rejoint le trajet'
            );
        } else {
            $this->error('Erreur lors de la connexion au trajet', 500);
        }
    }

    /**
     * Quitte un trajet
     * 
     * @param int $id L'ID du trajet
     * @return void
     */
    public function leave(int $id): void
    {
        if (!$this->isAuthenticated()) {
            $this->error('Authentification requise', 401);
        }

        $trajet = Trajet::find($id);

        if (!$trajet) {
            $this->error('Trajet non trouvé', 404);
        }

        $user = $this->getAuthUser();

        if ($trajet->removePassenger($user->id)) {
            $this->success(
                ['available_seats' => $trajet->getAvailableSeats()],
                'Vous avez quitté le trajet'
            );
        } else {
            $this->error('Erreur lors de la déconnexion du trajet', 500);
        }
    }

    /**
     * Utilise le klaxon sur un trajet
     * 
     * @param int $id L'ID du trajet
     * @return void
     */
    public function useHorn(int $id): void
    {
        if (!$this->isAuthenticated()) {
            $this->error('Authentification requise', 401);
        }

        $trajet = Trajet::find($id);

        if (!$trajet) {
            $this->error('Trajet non trouvé', 404);
        }

        if ($trajet->getRemainingHorns() <= 0) {
            $this->error('Limite de klaxons atteinte pour ce trajet', 400);
        }

        $user = $this->getAuthUser();
        $severity = (int) ($this->getInput('severity') ?? HORN_SEVERITY_MEDIUM);
        $message = $this->getInput('message', '');

        if ($trajet->useHorn($user->id, $severity, $message)) {
            $this->success(
                [
                    'remaining_horns' => $trajet->getRemainingHorns(),
                    'message' => 'Klaxon utilisé'
                ],
                'Klaxon utilisé avec succès'
            );
        } else {
            $this->error('Erreur lors de l\'utilisation du klaxon', 500);
        }
    }

    /**
     * Obtient les trajets de l'utilisateur
     * 
     * @return void
     */
    public function myTrips(): void
    {
        if (!$this->isAuthenticated()) {
            $this->error('Authentification requise', 401);
        }

        $user = $this->getAuthUser();
        $page = (int) ($this->getInput('page') ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $trajets = Trajet::findByDriver($user->id, $limit, $offset);

        $this->json(['trajets' => $trajets]);
    }

    /**
     * Obtient les statistiques d'un trajet
     * 
     * @param int $id L'ID du trajet
     * @return void
     */
    public function stats(int $id): void
    {
        $trajet = Trajet::find($id);

        if (!$trajet) {
            $this->error('Trajet non trouvé', 404);
        }

        $stats = [
            'id' => $trajet->id,
            'origin' => $trajet->origin,
            'destination' => $trajet->destination,
            'passengers' => $trajet->passengers_count ?? 0,
            'max_passengers' => TRAJET_MAX_PASSENGERS,
            'horns_used' => $trajet->horns_count ?? 0,
            'max_horns' => MAX_HORNS_PER_TRIP,
            'status' => $trajet->status,
        ];

        $this->json($stats);
    }
}
