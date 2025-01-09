<?php
// Démarrer la session
session_start();

// Vérifier si l'utilisateur est connecté et est un annonceur
if (!isset($_SESSION['id_annonceur']) || $_SESSION['role_annonceur'] != 'annonceur') {
    header('Location: ../login.php');
    exit();
}

// Inclure la connexion à la base de données
require_once '../config/db_connect.php';

// Récupérer les événements de l'annonceur
$stmt = $pdo->prepare('SELECT * FROM Evenement WHERE id_annonceur = ?');
$stmt->execute([$_SESSION['id_annonceur']]);
$evenements = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Espace - Événements Aurillac</title>
    <!-- Inclure Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>
    <!-- En-tête du site -->
    <?php include '../includes/header.php'; ?>

    <div class="container mt-5">
        <h2>Bienvenue, <?php echo htmlspecialchars($_SESSION['nom']); ?> !</h2>
        <p>Voici vos événements :</p>

        <a href="creer_evenement.php" class="btn btn-success mb-3">Créer un nouvel événement</a>

        <?php if (count($evenements) > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Date</th>
                        <th>Lieu</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($evenements as $evenement): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($evenement['nom']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($evenement['date_evenement'])); ?></td>
                            <td><?php echo htmlspecialchars($evenement['lieu']); ?></td>
                            <td>
                                <a href="modifier_evenement.php?id=<?php echo $evenement['id_evenement']; ?>" class="btn btn-primary btn-sm">Modifier</a>
                                <a href="supprimer_evenement.php?id=<?php echo $evenement['id_evenement']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?');">Supprimer</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Vous n'avez créé aucun événement pour le moment.</p>
        <?php endif; ?>
    </div>

    <!-- Inclure Bootstrap JS -->
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
