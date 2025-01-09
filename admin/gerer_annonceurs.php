<?php
session_start();

// Vérifier si l'utilisateur est un admin
if (!isset($_SESSION['id_annonceur']) || $_SESSION['role_annonceur'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

require_once '../config/db_connect.php';

// BLOQUER / DEBLOQUER
if (isset($_GET['bloquer']) && is_numeric($_GET['bloquer'])) {
    $id = (int)$_GET['bloquer'];
    // Mettre statut = 'bloque'
    $upd = $pdo->prepare('UPDATE annonceur SET statut = "bloque" WHERE id_annonceur = ?');
    $upd->execute([$id]);
    header('Location: gerer_annonceurs.php');
    exit();
}
if (isset($_GET['debloquer']) && is_numeric($_GET['debloquer'])) {
    $id = (int)$_GET['debloquer'];
    // Mettre statut = 'actif'
    $upd = $pdo->prepare('UPDATE annonceur SET statut = "actif" WHERE id_annonceur = ?');
    $upd->execute([$id]);
    header('Location: gerer_annonceurs.php');
    exit();
}

// Récupérer la liste des annonceurs
$sql = '
    SELECT a.*, sc.nom AS sous_cat_nom
    FROM annonceur a
    LEFT JOIN souscategorieannonceur sc
      ON a.id_sous_categorie_annonceur = sc.id_sous_categorie_annonceur
    WHERE a.role_annonceur = "annonceur"
    ORDER BY a.date_creation DESC
';
$stmt = $pdo->query($sql);
$annonceurs = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gérer les Annonceurs - Administrateur</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container mt-5">
    <h2>Gérer les annonceurs</h2>
    <?php if (count($annonceurs) > 0): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Sous-Catégorie</th>
                    <th>Date d'inscription</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($annonceurs as $annonceur): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($annonceur['nom']); ?></td>
                        <td><?php echo htmlspecialchars($annonceur['email']); ?></td>
                        <td><?php echo htmlspecialchars($annonceur['telephone']); ?></td>
                        <td><?php echo $annonceur['sous_cat_nom'] ?: '—'; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($annonceur['date_creation'])); ?></td>
                        <td><?php echo htmlspecialchars($annonceur['statut'] ?? 'actif'); ?></td>
                        <td>
                            <!-- Suppression -->
                            <a href="supprimer_annonceur.php?id=<?php echo $annonceur['id_annonceur']; ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet annonceur ?');">
                                Supprimer
                            </a>
                            <?php if ($annonceur['statut'] === 'actif'): ?>
                                <a href="?bloquer=<?php echo $annonceur['id_annonceur']; ?>"
                                   class="btn btn-warning btn-sm"
                                   onclick="return confirm('Voulez-vous bloquer cet annonceur ?');">
                                   Bloquer
                                </a>
                            <?php else: ?>
                                <a href="?debloquer=<?php echo $annonceur['id_annonceur']; ?>"
                                   class="btn btn-success btn-sm"
                                   onclick="return confirm('Voulez-vous débloquer cet annonceur ?');">
                                   Débloquer
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucun annonceur trouvé.</p>
    <?php endif; ?>
</div>

<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
