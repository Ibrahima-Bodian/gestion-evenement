<?php
// Démarrer la session
session_start();

// Inclure la connexion à la base de données
require_once 'config/db_connect.php';

// Inclure la configuration pour BASE_URL
require_once 'config/config.php';

// Récupérer la liste des catégories
$stmt = $pdo->query('SELECT * FROM CategorieEvenement');
$categories = $stmt->fetchAll();

// Traitement du formulaire de recherche et du filtrage
$condition = '';
$params = [];

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Récupérer le terme de recherche s'il existe
    if (isset($_GET['q']) && !empty($_GET['q'])) {
        $q = htmlspecialchars($_GET['q']);
        $condition .= ' AND (e.nom LIKE ? OR e.description LIKE ?)';
        $params[] = '%' . $q . '%';
        $params[] = '%' . $q . '%';
    }

    // Récupérer la catégorie sélectionnée s'il y en a une
    if (isset($_GET['categorie']) && !empty($_GET['categorie'])) {
        $categorie_filtre = $_GET['categorie'];
        $condition .= ' AND e.id_categorie_evenement = ?';
        $params[] = $categorie_filtre;
    }
}

// Récupérer les événements à venir avec les filtres appliqués
try {
    // Préparer la requête SQL pour sélectionner les événements à venir
    $sql = '
        SELECT e.*, c.nom AS categorie
        FROM Evenement e
        JOIN CategorieEvenement c ON e.id_categorie_evenement = c.id_categorie_evenement
        WHERE e.date_evenement >= CURDATE()' . $condition . '
        ORDER BY e.date_evenement ASC
    ';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $evenements = $stmt->fetchAll();
} catch (Exception $e) {
    echo 'Erreur lors de la récupération des événements : ' . $e->getMessage();
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Résultats de la recherche - Événements Aurillac</title>
    <!-- Inclure Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/bootstrap.min.css">
    <!-- Votre fichier CSS personnalisé -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>
<body>
    <!-- En-tête du site -->
    <?php include 'includes/header.php'; ?>

    <div class="container mt-5">
        <h2>Résultats de la recherche</h2>

        <!-- Formulaire de recherche et de filtrage -->
        <form method="GET" action="recherche.php" class="d-flex mb-4">
            <input class="form-control me-2" type="search" name="q" placeholder="Rechercher un événement" aria-label="Rechercher" value="<?php echo isset($q) ? $q : ''; ?>">
            <select class="form-select me-2" name="categorie">
                <option value="">Toutes les catégories</option>
                <?php foreach ($categories as $categorie): ?>
                    <option value="<?php echo $categorie['id_categorie_evenement']; ?>" <?php if (isset($categorie_filtre) && $categorie_filtre == $categorie['id_categorie_evenement']) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($categorie['nom']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button class="btn btn-outline-success" type="submit">Rechercher</button>
        </form>

        <!-- Affichage des événements -->
        <div class="row">
            <?php if (count($evenements) > 0): ?>
                <?php foreach ($evenements as $evenement): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <!-- Image de l'événement (optionnelle) -->
                            <!-- <img src="<?php echo BASE_URL; ?>assets/images/<?php echo htmlspecialchars($evenement['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($evenement['nom']); ?>"> -->
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($evenement['nom']); ?></h5>
                                <p class="card-text">
                                    <?php echo htmlspecialchars(substr($evenement['description'], 0, 100)) . '...'; ?>
                                </p>
                                <p class="card-text">
                                    <small class="text-muted">
                                        Date : <?php echo date('d/m/Y', strtotime($evenement['date_evenement'])); ?><br>
                                        Lieu : <?php echo htmlspecialchars($evenement['lieu']); ?><br>
                                        Catégorie : <?php echo htmlspecialchars($evenement['categorie']); ?>
                                    </small>
                                </p>
                                <a href="evenement.php?id=<?php echo $evenement['id_evenement']; ?>" class="btn btn-primary">Voir plus</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucun événement trouvé.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pied de page du site -->
    <?php include 'includes/footer.php'; ?>

    <!-- Inclure Bootstrap JS -->
    <script src="<?php echo BASE_URL; ?>assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
