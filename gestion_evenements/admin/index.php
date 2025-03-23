<?php
session_start();
if (!isset($_SESSION['id_annonceur']) || $_SESSION['role_annonceur'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

require_once '../config/db_connect.php';
require_once '../config/config.php';

// Récupérer le nombre total d'annonceurs
$stmt = $pdo->query('SELECT COUNT(*) AS total FROM Annonceur WHERE role_annonceur = "annonceur"');
$total_annonceurs = $stmt->fetch()['total'];

// Récupérer le nombre total d'événements
$stmt = $pdo->query('SELECT COUNT(*) AS total FROM Evenement');
$total_evenements = $stmt->fetch()['total'];

// Récupérer le nombre total de catégories
$stmt = $pdo->query('SELECT COUNT(*) AS total FROM CategorieEvenement');
$total_categories = $stmt->fetch()['total'];

// Récupérer le nombre total de consultations (visites)
$stmt = $pdo->query('SELECT COUNT(*) AS total FROM Statistiques');
$total_consultations = $stmt->fetch()['total'];

$titre_page = "Tableau de bord - Administrateur";
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo $titre_page; ?></title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <h2>Tableau de bord administrateur</h2>
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Annonceurs</h5>
                    <p class="card-text"><?php echo $total_annonceurs; ?></p>
                    <a href="gerer_annonceurs.php" class="btn btn-primary">Gérer les annonceurs</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Événements</h5>
                    <p class="card-text"><?php echo $total_evenements; ?></p>
                    <a href="gerer_evenements.php" class="btn btn-primary">Gérer les événements</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Catégories</h5>
                    <p class="card-text"><?php echo $total_categories; ?></p>
                    <a href="gerer_categories.php" class="btn btn-primary">Gérer les catégories</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">Visites</h5>
                    <p class="card-text"><?php echo $total_consultations; ?></p>
                    <!-- Si vous voulez lister les consultations, vous pouvez faire un lien -->
                    <a href="statistiques_visites.php" class="btn btn-primary">Détails Visites</a>
                </div>
            </div>
        </div>
    </div>
    <br\>
    <p></p>
<div class="text-center">           
    <!-- Les fichiers à telecharger au forma zip -->
    <a href="export_data_zip.php" class="btn btn-success">Exporter les données (ZIP)</a>
</div>
</div>


<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
