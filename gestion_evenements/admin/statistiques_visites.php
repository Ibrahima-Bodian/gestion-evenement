<?php
session_start();
if (!isset($_SESSION['id_annonceur']) || $_SESSION['role_annonceur'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

require_once '../config/db_connect.php';
require_once '../config/config.php';

// 1. Visites par Catégorie d'Événement
$sqlCat = "
    SELECT c.nom AS categorie, COUNT(s.id_statistique) AS nb_visites
    FROM statistiques s
    JOIN evenement e ON s.id_evenement = e.id_evenement
    JOIN categorieevenement c ON e.id_categorie_evenement = c.id_categorie_evenement
    GROUP BY c.id_categorie_evenement
    ORDER BY nb_visites DESC
";
$stmtCat = $pdo->query($sqlCat);
$stats_categorie = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

// 2. Visites par Sous-Catégorie
$sqlSousCat = "
    SELECT IFNULL(sc.nom, 'Aucune') AS sous_categorie, COUNT(s.id_statistique) AS nb_visites
    FROM statistiques s
    JOIN evenement e ON s.id_evenement = e.id_evenement
    LEFT JOIN souscategorieevenement sc 
      ON e.id_sous_categorie_evenement = sc.id_sous_categorie_evenement
    GROUP BY e.id_sous_categorie_evenement
    ORDER BY nb_visites DESC
";
$stmtSous = $pdo->query($sqlSousCat);
$stats_sous_categorie = $stmtSous->fetchAll(PDO::FETCH_ASSOC);

$titre_page = "Détail Visites";
include '../includes/head.php';

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Statistiques des Visites - Administrateur</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php // vous pourriez inclure header ici si besoin, mais c'est déjà fait plus haut, attention au duplicat
include '../includes/header.php';
?>

<div class="container mt-5 admin-page-container">
    <h2 class="text-center mb-4 admin-page-title">Statistiques des Visites</h2>

    <div class="row justify-content-center">
        <div class="col-md-8 form-evenement p-4">
            <h4>Visites par Catégorie d'Événement</h4>
            <table class="table table-striped table-sm mb-4">
                <thead>
                    <tr>
                        <th>Catégorie</th>
                        <th>Nombre de Visites</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats_categorie as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['categorie']); ?></td>
                            <td><?php echo $row['nb_visites']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <br><br>

            <h4>Visites par Sous-Catégorie d'Événement</h4>
            <table class="table table-striped table-sm">
                <thead>
                    <tr>
                        <th>Sous-Catégorie</th>
                        <th>Nombre de Visites</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($stats_sous_categorie as $row): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['sous_categorie']); ?></td>
                            <td><?php echo $row['nb_visites']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div><!-- .col-md-8 -->
    </div><!-- .row -->
</div><!-- .container -->

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
