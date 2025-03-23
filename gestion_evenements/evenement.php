<?php
// Démarrer la session
session_start();

// Vérifier si l'ID de l'événement est passé en paramètre
if (!isset($_GET['id'])) {
    echo 'Événement non spécifié.';
    exit();
}

$id_evenement = $_GET['id'];

// Inclure le fichier de connexion à la base de données
require_once 'config/db_connect.php';
require_once 'config/config.php';

try {
    // Récupérer les détails de l'événement
    $stmt = $pdo->prepare('
        SELECT e.*, c.nom AS categorie, a.nom AS annonceur
        FROM Evenement e
        JOIN CategorieEvenement c ON e.id_categorie_evenement = c.id_categorie_evenement
        JOIN Annonceur a ON e.id_annonceur = a.id_annonceur
        WHERE e.id_evenement = ?
    ');
    $stmt->execute([$id_evenement]);
    $evenement = $stmt->fetch();

    if (!$evenement) {
        echo 'Événement non trouvé.';
        exit();
    }

    // 1. Incrémenter le compteur de consultations dans Evenement
    $stmt = $pdo->prepare('UPDATE Evenement SET compteur_consultations = compteur_consultations + 1 WHERE id_evenement = ?');
    $stmt->execute([$id_evenement]);

    // 2. Enregistrer la visite dans la table Statistiques
    // Vérifier si un utilisateur est connecté
    $id_annonceur = isset($_SESSION['id_annonceur']) ? $_SESSION['id_annonceur'] : null;

    $stmtStats = $pdo->prepare('INSERT INTO Statistiques (id_evenement, id_annonceur) VALUES (?, ?)');
    $stmtStats->execute([$id_evenement, $id_annonceur]);

} catch (Exception $e) {
    echo 'Erreur lors de la récupération de l\'événement : ' . $e->getMessage();
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($evenement['nom']); ?> - Événements Aurillac</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container mt-5">
        <h1><?php echo htmlspecialchars($evenement['nom']); ?></h1>
        <p class="text-muted">Par <?php echo htmlspecialchars($evenement['annonceur']); ?> | Catégorie : <?php echo htmlspecialchars($evenement['categorie']); ?></p>
        <p><strong>Date :</strong> <?php echo date('d/m/Y', strtotime($evenement['date_evenement'])); ?></p>
        <p><strong>Heure :</strong> <?php echo date('H:i', strtotime($evenement['heure'])); ?></p>
        <p><strong>Lieu :</strong> <?php echo htmlspecialchars($evenement['lieu']); ?></p>
        <p><?php echo nl2br(htmlspecialchars($evenement['description'])); ?></p>
    </div>

    <?php include 'includes/footer.php'; ?>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
