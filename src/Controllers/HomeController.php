<?php
/**
 * Contrôleur d'accueil
 * 
 * Gère l'affichage des pages publiques.
 * 
 * @package KlaxonApp\Controllers
 * @author TOUCHE PAS AU KLAXON Team
 */

namespace KlaxonApp\Controllers;

use KlaxonApp\Models\Trajet;
use KlaxonApp\Models\Agence;

class HomeController extends Controller
{
    /**
     * Affiche la page d'accueil
     * 
     * @return void
     */
    public function index(): void
    {
        $this->setViewData('title', 'Accueil - ' . APP_NAME);
        $this->setViewData('available_trajets', Trajet::getAvailable(10));
        $this->setViewData('total_trajets', Trajet::count());
        $this->setViewData('total_users', \KlaxonApp\Models\User::count());
        $this->setViewData('total_agences', Agence::count());

        $this->renderView('home/index');
    }

    /**
     * Affiche la page À propos
     * 
     * @return void
     */
    public function about(): void
    {
        $this->setViewData('title', 'À propos - ' . APP_NAME);
        $this->renderView('home/about');
    }

    /**
     * Affiche la page de contact
     * 
     * @return void
     */
    public function contact(): void
    {
        $this->setViewData('title', 'Contact - ' . APP_NAME);
        $this->renderView('home/contact');
    }

    /**
     * Traite l'envoi du formulaire de contact
     * 
     * @return void
     */
    public function contactSubmit(): void
    {
        $errors = $this->validate([
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required',
        ]);

        if (!empty($errors)) {
            $this->error('Erreurs de validation', 400, ['errors' => $errors]);
        }

        $name = $this->getInput('name');
        $email = $this->getInput('email');
        $message = $this->getInput('message');

        // TODO: Envoyer un email
        // sendEmail('contact@klaxon.local', "Nouveau message de $name", $message);

        $this->success(null, 'Votre message a été envoyé avec succès');
    }

    /**
     * Affiche les trajets disponibles
     * 
     * @return void
     */
    public function trajets(): void
    {
        $page = (int) ($this->getInput('page') ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $trajets = Trajet::getAvailable($limit, $offset);
        $total = Trajet::count();

        $this->setViewData('title', 'Trajets disponibles');
        $this->setViewData('trajets', $trajets);
        $this->setViewData('total', $total);
        $this->setViewData('page', $page);
        $this->setViewData('pages', ceil($total / $limit));

        $this->renderView('home/trajets');
    }

    /**
     * Affiche les détails d'un trajet
     * 
     * @param int $id L'ID du trajet
     * @return void
     */
    public function trajet(int $id): void
    {
        $trajet = Trajet::find($id);

        if (!$trajet) {
            $this->error('Trajet non trouvé', 404);
        }

        $this->setViewData('title', 'Trajet: ' . $trajet->destination);
        $this->setViewData('trajet', $trajet);

        $this->renderView('home/trajet-detail');
    }

    /**
     * Affiche les agences
     * 
     * @return void
     */
    public function agences(): void
    {
        $page = (int) ($this->getInput('page') ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;

        $agences = Agence::getActive($limit, $offset);
        $total = Agence::count(['is_active' => true]);

        $this->setViewData('title', 'Agences partenaires');
        $this->setViewData('agences', $agences);
        $this->setViewData('total', $total);
        $this->setViewData('page', $page);
        $this->setViewData('pages', ceil($total / $limit));

        $this->renderView('home/agences');
    }

    /**
     * Affiche les détails d'une agence
     * 
     * @param string $slug Le slug de l'agence
     * @return void
     */
    public function agence(string $slug): void
    {
        $agence = Agence::findBySlug($slug);

        if (!$agence) {
            $this->error('Agence non trouvée', 404);
        }

        $trajets = $agence->getTrajects(10);

        $this->setViewData('title', $agence->name);
        $this->setViewData('agence', $agence);
        $this->setViewData('trajets', $trajets);

        $this->renderView('home/agence-detail');
    }

    /**
     * Affiche la page d'accès refusé
     * 
     * @return void
     */
    public function accessDenied(): void
    {
        $this->setViewData('title', 'Accès refusé');
        $this->renderView('errors/403');
    }

    /**
     * Affiche la page non trouvée
     * 
     * @return void
     */
    public function notFound(): void
    {
        $this->setViewData('title', 'Page non trouvée');
        $this->renderView('errors/404');
    }

    /**
     * Affiche la page erreur serveur
     * 
     * @return void
     */
    public function error(): void
    {
        $this->setViewData('title', 'Erreur serveur');
        $this->renderView('errors/500');
    }

    /**
     * Affiche une vue
     * 
     * @param string $view Le chemin de la vue
     * @return void
     */
    protected function renderView(string $view): void
    {
        $viewPath = ROOT_PATH . '/src/Views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            $this->error("Vue '$view' non trouvée", 500);
        }

        extract($this->viewData);
        include $viewPath;
    }
}
