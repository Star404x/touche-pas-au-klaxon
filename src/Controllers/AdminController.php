<?php
/**
 * Contrôleur administrateur
 * 
 * Gère les fonctionnalités administratives:
 * utilisateurs, agences, modération, statistiques.
 * 
 * @package KlaxonApp\Controllers
 * @author TOUCHE PAS AU KLAXON Team
 */

namespace KlaxonApp\Controllers;

use KlaxonApp\Models\User;
use KlaxonApp\Models\Agence;
use KlaxonApp\Models\Trajet;
use KlaxonApp\Middleware\AdminMiddleware;

class AdminController extends Controller
{
    /**
     * Middleware admin
     * @var AdminMiddleware
     */
    private AdminMiddleware $adminAuth;

    /**
     * Constructeur
     */
    public function __construct()
    {
        parent::__construct();
        $this->adminAuth = new AdminMiddleware();
    }

    /**
     * Affiche le tableau de bord administrateur
     * 
     * @return void
     */
    public function dashboard(): void
    {
        $this->adminAuth->handle();

        $stats = [
            'total_users' => User::count(),
            'total_trajets' => Trajet::count(),
            'active_trajets' => Trajet::count(['status' => TRAJET_STATUS_IN_PROGRESS]),
            'total_agences' => Agence::count(),
            'total_admins' => User::count(['role' => ROLE_ADMIN]),
        ];

        $this->setViewData('stats', $stats);
        $this->json($stats);
    }

    /**
     * Liste tous les utilisateurs
     * 
     * @return void
     */
    public function users(): void
    {
        $this->adminAuth->handle();

        $page = (int) ($this->getInput('page') ?? 1);
        $limit = 50;
        $offset = ($page - 1) * $limit;

        $users = User::all($limit, $offset);
        $total = User::count();

        $this->json([
            'users' => $users,
            'total' => $total,
            'page' => $page,
            'pages' => ceil($total / $limit),
        ]);
    }

    /**
     * Affiche les détails d'un utilisateur
     * 
     * @param int $id L'ID de l'utilisateur
     * @return void
     */
    public function userDetail(int $id): void
    {
        $this->adminAuth->handle();

        $user = User::find($id);

        if (!$user) {
            $this->error('Utilisateur non trouvé', 404);
        }

        $this->json($user->toArray());
    }

    /**
     * Active/désactive un utilisateur
     * 
     * @param int $id L'ID de l'utilisateur
     * @return void
     */
    public function toggleUser(int $id): void
    {
        $this->adminAuth->handle();

        $user = User::find($id);

        if (!$user) {
            $this->error('Utilisateur non trouvé', 404);
        }

        $user->is_active = !$user->is_active;

        if ($user->save()) {
            $this->success(
                $user->toArray(),
                'Utilisateur mis à jour'
            );
        } else {
            $this->error('Erreur lors de la mise à jour', 500);
        }
    }

    /**
     * Change le rôle d'un utilisateur
     * 
     * @param int $id L'ID de l'utilisateur
     * @return void
     */
    public function changeUserRole(int $id): void
    {
        $this->adminAuth->handle();

        $user = User::find($id);

        if (!$user) {
            $this->error('Utilisateur non trouvé', 404);
        }

        $role = $this->getInput('role');

        if (!in_array($role, [ROLE_ADMIN, ROLE_USER, ROLE_DRIVER, ROLE_AGENCE])) {
            $this->error('Rôle invalide', 400);
        }

        $user->role = $role;

        if ($user->save()) {
            $this->success(
                $user->toArray(),
                'Rôle mis à jour'
            );
        } else {
            $this->error('Erreur lors de la mise à jour', 500);
        }
    }

    /**
     * Supprime un utilisateur
     * 
     * @param int $id L'ID de l'utilisateur
     * @return void
     */
    public function deleteUser(int $id): void
    {
        $this->adminAuth->handle();

        $user = User::find($id);

        if (!$user) {
            $this->error('Utilisateur non trouvé', 404);
        }

        if ($user->delete()) {
            $this->success(null, 'Utilisateur supprimé');
        } else {
            $this->error('Erreur lors de la suppression', 500);
        }
    }

    /**
     * Liste toutes les agences
     * 
     * @return void
     */
    public function agences(): void
    {
        $this->adminAuth->handle();

        $page = (int) ($this->getInput('page') ?? 1);
        $limit = 50;
        $offset = ($page - 1) * $limit;

        $agences = Agence::all($limit, $offset);
        $total = Agence::count();

        $this->json([
            'agences' => $agences,
            'total' => $total,
            'page' => $page,
            'pages' => ceil($total / $limit),
        ]);
    }

    /**
     * Affiche les détails d'une agence
     * 
     * @param int $id L'ID de l'agence
     * @return void
     */
    public function agenceDetail(int $id): void
    {
        $this->adminAuth->handle();

        $agence = Agence::find($id);

        if (!$agence) {
            $this->error('Agence non trouvée', 404);
        }

        $stats = $agence->getStats();

        $this->json($stats);
    }

    /**
     * Active/désactive une agence
     * 
     * @param int $id L'ID de l'agence
     * @return void
     */
    public function toggleAgence(int $id): void
    {
        $this->adminAuth->handle();

        $agence = Agence::find($id);

        if (!$agence) {
            $this->error('Agence non trouvée', 404);
        }

        $agence->is_active = !$agence->is_active;

        if ($agence->save()) {
            $this->success(
                $agence->getPublicProfile(),
                'Agence mise à jour'
            );
        } else {
            $this->error('Erreur lors de la mise à jour', 500);
        }
    }

    /**
     * Liste tous les trajets
     * 
     * @return void
     */
    public function trajets(): void
    {
        $this->adminAuth->handle();

        $page = (int) ($this->getInput('page') ?? 1);
        $status = $this->getInput('status');
        $limit = 50;
        $offset = ($page - 1) * $limit;

        if ($status) {
            $trajets = Trajet::whereAll(['status' => $status], $limit, $offset);
            $total = Trajet::count(['status' => $status]);
        } else {
            $trajets = Trajet::all($limit, $offset);
            $total = Trajet::count();
        }

        $this->json([
            'trajets' => $trajets,
            'total' => $total,
            'page' => $page,
            'pages' => ceil($total / $limit),
        ]);
    }

    /**
     * Affiche les détails d'un trajet
     * 
     * @param int $id L'ID du trajet
     * @return void
     */
    public function trajetDetail(int $id): void
    {
        $this->adminAuth->handle();

        $trajet = Trajet::find($id);

        if (!$trajet) {
            $this->error('Trajet non trouvé', 404);
        }

        $this->json($trajet->getDetails());
    }

    /**
     * Change le statut d'un trajet
     * 
     * @param int $id L'ID du trajet
     * @return void
     */
    public function changeTrajetStatus(int $id): void
    {
        $this->adminAuth->handle();

        $trajet = Trajet::find($id);

        if (!$trajet) {
            $this->error('Trajet non trouvé', 404);
        }

        $status = $this->getInput('status');
        $validStatuses = [
            TRAJET_STATUS_PENDING,
            TRAJET_STATUS_CONFIRMED,
            TRAJET_STATUS_IN_PROGRESS,
            TRAJET_STATUS_COMPLETED,
            TRAJET_STATUS_CANCELLED,
        ];

        if (!in_array($status, $validStatuses)) {
            $this->error('Statut invalide', 400);
        }

        $trajet->status = $status;

        if ($trajet->save()) {
            $this->success(
                $trajet->getDetails(),
                'Statut du trajet mis à jour'
            );
        } else {
            $this->error('Erreur lors de la mise à jour', 500);
        }
    }

    /**
     * Obtient les statistiques globales
     * 
     * @return void
     */
    public function globalStats(): void
    {
        $this->adminAuth->handle();

        $stats = [
            'users' => [
                'total' => User::count(),
                'active' => User::count(['is_active' => true]),
                'admins' => User::count(['role' => ROLE_ADMIN]),
                'drivers' => User::count(['role' => ROLE_DRIVER]),
                'agencies' => User::count(['role' => ROLE_AGENCE]),
            ],
            'trajets' => [
                'total' => Trajet::count(),
                'pending' => Trajet::count(['status' => TRAJET_STATUS_PENDING]),
                'confirmed' => Trajet::count(['status' => TRAJET_STATUS_CONFIRMED]),
                'in_progress' => Trajet::count(['status' => TRAJET_STATUS_IN_PROGRESS]),
                'completed' => Trajet::count(['status' => TRAJET_STATUS_COMPLETED]),
                'cancelled' => Trajet::count(['status' => TRAJET_STATUS_CANCELLED]),
            ],
            'agences' => [
                'total' => Agence::count(),
                'active' => Agence::count(['is_active' => true]),
            ],
        ];

        $this->json($stats);
    }
}
