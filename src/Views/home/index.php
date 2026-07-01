<?php
/**
 * Vue de la page d'accueil
 * 
 * @package KlaxonApp\Views
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header>
        <h1><?= APP_NAME ?></h1>
        <p class="tagline">Partage de trajets respectueux</p>
    </header>

    <nav>
        <ul>
            <li><a href="/">Accueil</a></li>
            <li><a href="/trajets">Trajets</a></li>
            <li><a href="/agences">Agences</a></li>
            <li><a href="/about">À propos</a></li>
            <li><a href="/contact">Contact</a></li>
        </ul>
    </nav>

    <main>
        <section class="hero">
            <h2>Bienvenue sur <?= APP_NAME ?></h2>
            <p>Partagez vos trajets de manière responsable et bienveillante.</p>
        </section>

        <section class="stats">
            <div class="stat">
                <h3><?= $total_trajets ?? 0 ?></h3>
                <p>Trajets disponibles</p>
            </div>
            <div class="stat">
                <h3><?= $total_users ?? 0 ?></h3>
                <p>Utilisateurs actifs</p>
            </div>
            <div class="stat">
                <h3><?= $total_agences ?? 0 ?></h3>
                <p>Agences partenaires</p>
            </div>
        </section>

        <section class="featured-trajets">
            <h3>Trajets populaires</h3>
            <?php if (!empty($available_trajets)): ?>
                <ul>
                    <?php foreach ($available_trajets as $trajet): ?>
                        <li>
                            <a href="/trajet/<?= $trajet->id ?>">
                                <strong><?= htmlspecialchars($trajet->origin) ?></strong>
                                → 
                                <strong><?= htmlspecialchars($trajet->destination) ?></strong>
                            </a>
                            <p>Sièges disponibles: <?= $trajet->getAvailableSeats() ?>/<?= TRAJET_MAX_PASSENGERS ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Aucun trajet disponible pour le moment.</p>
            <?php endif; ?>
        </section>

        <section class="cta">
            <a href="/trajets" class="btn btn-primary">Voir tous les trajets</a>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 <?= APP_NAME ?>. Tous droits réservés.</p>
    </footer>
</body>
</html>
