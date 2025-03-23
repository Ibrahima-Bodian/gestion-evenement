<?php
// Démarrer la session
session_start();

// Afficher les erreurs (à retirer en production)
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Inclure la connexion à la base de données
require_once 'config/db_connect.php';

// Inclure la configuration pour BASE_URL
require_once 'config/config.php';

// Récupérer la liste des catégories
$stmt = $pdo->query('SELECT * FROM CategorieEvenement');
$categories = $stmt->fetchAll();

/* -------------------------------------------------------
   PARTIE 3 : Gestion de Cookie pour la dernière catégorie
   ------------------------------------------------------- */
$condition = '';
$params = [];

// 1. Si on n'a pas de catégorie en GET, mais qu'un cookie existe, on relit le cookie
if (!isset($_GET['categorie']) && isset($_COOKIE['derniere_categorie'])) {
    $categorie_filtre = $_COOKIE['derniere_categorie'];
    $condition .= ' AND e.id_categorie_evenement = ?';
    $params[] = $categorie_filtre;
}

// 2. Si on a une catégorie en GET, on la stocke en cookie
if (isset($_GET['categorie']) && !empty($_GET['categorie'])) {
    $categorie_filtre = $_GET['categorie'];
    $condition .= ' AND e.id_categorie_evenement = ?';
    $params[] = $categorie_filtre;

    // On enregistre dans un cookie valable 7 jours
    setcookie('derniere_categorie', $categorie_filtre, time() + 7 * 24 * 3600, '/');
}

// Récupérer le terme de recherche s'il existe
if (isset($_GET['q']) && !empty($_GET['q'])) {
    $q = htmlspecialchars($_GET['q']);
    $condition .= ' AND (e.nom LIKE ? OR e.description LIKE ?)';
    $params[] = '%' . $q . '%';
    $params[] = '%' . $q . '%';
}

// Requête pour récupérer les événements
try {
    $sql = '
    SELECT e.*, c.nom AS categorie
    FROM Evenement e
    JOIN CategorieEvenement c ON e.id_categorie_evenement = c.id_categorie_evenement
    WHERE e.date_evenement >= CURRENT_DATE
    ORDER BY e.date_evenement ASC
';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $evenements = $stmt->fetchAll();
} catch (Exception $e) {
    echo 'Erreur lors de la récupération des événements : ' . $e->getMessage();
    exit();
}

// Définir le titre de page comme "Accueil"
$titre_page = "Accueil";

// Inclure le head et le header
include 'includes/head.php';
?>
<!-- IMPORTANT : body avec id="homepage" -->
<body id="homepage">
    <?php include 'includes/header.php'; ?>

    <!-- HERO SECTION : image d'arrière-plan ici -->
    <div class="hero-section d-flex flex-column justify-content-center align-items-center text-center position-relative">
        <div class="overlay"></div>
        <div class="content position-relative text-white">
            <h1 class="fw-bold mb-3">Découvrez les Meilleurs Événements à Aurillac</h1>
        </div>
    </div>

    <!-- Formulaire de recherche et filtrage -->
    <div class="search-container">
        <form method="GET" action="index.php" class="d-flex align-items-center mb-4 flex-wrap gap-2">
            <input class="form-control search-input" 
                   type="search" 
                   name="q" 
                   placeholder="Rechercher un événement" 
                   aria-label="Rechercher" 
                   value="<?php echo isset($q) ? $q : ''; ?>">

            <select class="form-select category-select" name="categorie">
                <option value="">Toutes les catégories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id_categorie_evenement']; ?>"
                        <?php
                        if (isset($categorie_filtre) && $categorie_filtre == $cat['id_categorie_evenement']) {
                            echo 'selected';
                        }
                        ?>>
                        <?php echo htmlspecialchars($cat['nom']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button class="btn btn-primary" type="submit">Rechercher</button>
        </form>
    </div>

    <!-- Affichage des événements en liste -->
    <div class="container py-5">
        <div class="row g-4">
            <?php if (count($evenements) > 0): ?>
                <?php foreach ($evenements as $evenement): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card event-card h-100">
                            <?php if (!empty($evenement['image'])): ?>
                                <img src="<?php echo BASE_URL; ?>assets/images/<?php echo htmlspecialchars($evenement['image']); ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo htmlspecialchars($evenement['nom']); ?>">
                            <?php else: ?>
                                <img src="<?php echo BASE_URL; ?>assets/images/default.jpg" 
                                     class="card-img-top" 
                                     alt="Image par défaut">
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title mb-1"><?php echo htmlspecialchars($evenement['nom']); ?></h5>
                                <p class="card-text text-muted mb-2" style="font-size:0.9rem;">
                                    Date : <?php echo date('d/m/Y', strtotime($evenement['date_evenement'])); ?>
                                </p>
                                <p class="card-text flex-grow-1" style="font-size:0.9rem;">
                                    <?php echo htmlspecialchars(substr($evenement['description'], 0, 80)) . '...'; ?>
                                </p>
                                <a href="evenement.php?id=<?php echo $evenement['id_evenement']; ?>" 
                                   class="btn btn-primary mt-auto">En savoir plus</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucun événement pour le moment.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="<?php echo BASE_URL; ?>assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
