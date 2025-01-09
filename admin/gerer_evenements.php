<?php
session_start();
if (!isset($_SESSION['id_annonceur']) || $_SESSION['role_annonceur'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

require_once '../config/db_connect.php';
require_once '../config/config.php';

// Si l'admin veut supprimer un événement
if (isset($_GET['supprimer']) && is_numeric($_GET['supprimer'])) {
    $id = (int) $_GET['supprimer'];
    $delete = $pdo->prepare('DELETE FROM Evenement WHERE id_evenement = ?');
    $delete->execute([$id]);
    header('Location: gerer_evenements.php');
    exit();
}

// Récupérer la liste des événements
$sql = '
    SELECT e.*, a.nom AS annonceur_nom, a.statut AS annonceur_statut,
           c.nom AS cat_nom, sc.nom AS sous_cat_nom
    FROM Evenement e
    JOIN Annonceur a ON e.id_annonceur = a.id_annonceur
    JOIN CategorieEvenement c ON e.id_categorie_evenement = c.id_categorie_evenement
    LEFT JOIN souscategorieevenement sc 
      ON e.id_sous_categorie_evenement = sc.id_sous_categorie_evenement
    ORDER BY e.date_evenement DESC
';
$stmt = $pdo->query($sql);
$evenements = $stmt->fetchAll(PDO::FETCH_ASSOC);

$titre_page = "Gérer les événements";
include '../includes/head.php';
include '../includes/header.php';
?>

<div class="container mt-5">
    <h2>Gérer les événements</h2>
    <!-- Bouton pour éventuellement créer un événement côté admin -->
    <a href="creer_evenement.php" class="btn btn-success mb-3">Créer un événement</a>

    <?php if (count($evenements) > 0): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nom (Événement)</th>
                    <th>Annonceur</th>
                    <th>Date</th>
                    <th>Lieu</th>
                    <th>Catégorie</th>
                    <th>Sous-Catégorie</th>
                    <th>Consultations</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($evenements as $evt): ?>
                    <tr>
                        <td>
                            <?php 
                            echo htmlspecialchars($evt['nom']); 
                            // Optionnel : marquer si l'annonceur est bloqué
                            if ($evt['annonceur_statut'] === 'bloque') {
                                echo ' <span class="badge bg-warning text-dark">[Annonceur bloqué]</span>';
                            }
                            ?>
                        </td>
                        <td><?php echo htmlspecialchars($evt['annonceur_nom']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($evt['date_evenement'])); ?></td>
                        <td><?php echo htmlspecialchars($evt['lieu']); ?></td>
                        <td><?php echo htmlspecialchars($evt['cat_nom']); ?></td>
                        <td><?php echo htmlspecialchars($evt['sous_cat_nom'] ?: '—'); ?></td>
                        <td><?php echo $evt['compteur_consultations']; ?></td>
                        <td>
                            <a href="?supprimer=<?php echo $evt['id_evenement']; ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Supprimer cet événement ?');">
                               Supprimer
                            </a>
                            <!-- Vous pouvez ajouter un bouton "Modifier" si vous voulez -->
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucun événement trouvé.</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
