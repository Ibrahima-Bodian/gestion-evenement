<?php
session_start();
if (!isset($_SESSION['id_annonceur']) || $_SESSION['role_annonceur'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

require_once '../config/db_connect.php';
require_once '../config/config.php';

// Récupérer les catégories d’événement pour un menu déroulant
$stmt = $pdo->query('SELECT * FROM CategorieEvenement');
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Si le formulaire d'ajout est soumis
if (isset($_POST['ajouter_sous_cat'])) {
    $nom = htmlspecialchars($_POST['nom_sous_cat']);
    $id_categorie = (int) $_POST['id_categorie_evenement'];

    $insert = $pdo->prepare('INSERT INTO Sous_categorie_evenement (id_categorie_evenement, nom) VALUES (?, ?)');
    $insert->execute([$id_categorie, $nom]);
    header('Location: gerer_sous_categories_evenement.php');
    exit();
}

// Si on veut supprimer une sous-catégorie
if (isset($_GET['supprimer']) && is_numeric($_GET['supprimer'])) {
    $id = (int) $_GET['supprimer'];
    $delete = $pdo->prepare('DELETE FROM Sous_categorie_evenement WHERE id_sous_categorie_evenement = ?');
    $delete->execute([$id]);
    header('Location: gerer_sous_categories_evenement.php');
    exit();
}

// Lister toutes les sous-catégories
$sql = 'SELECT sce.*, ce.nom AS nom_cat
        FROM Sous_categorie_evenement sce
        JOIN CategorieEvenement ce ON sce.id_categorie_evenement = ce.id_categorie_evenement
        ORDER BY ce.nom, sce.nom';
$list = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$titre_page = "Gestion Sous-Catégories Événement";
include '../includes/head.php';
include '../includes/header.php';
?>

<div class="container mt-5">
    <h2>Gestion des Sous-Catégories d'Événement</h2>

    <form method="POST" class="mb-4">
        <div class="mb-3">
            <label for="id_categorie_evenement" class="form-label">Catégorie Événement</label>
            <select class="form-select" name="id_categorie_evenement" required>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id_categorie_evenement']; ?>">
                        <?php echo htmlspecialchars($cat['nom']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="nom_sous_cat" class="form-label">Nom de la Sous-Catégorie</label>
            <input type="text" class="form-control" name="nom_sous_cat" required>
        </div>
        <button type="submit" class="btn btn-primary" name="ajouter_sous_cat">Ajouter</button>
    </form>

    <h3>Liste des Sous-Catégories</h3>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Sous-Catégorie</th>
                <th>Catégorie Principale</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($list as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['nom']); ?></td>
                <td><?php echo htmlspecialchars($item['nom_cat']); ?></td>
                <td>
                    <a href="?supprimer=<?php echo $item['id_sous_categorie_evenement']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer cette sous-catégorie ?');">Supprimer</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
<script src="<?php echo BASE_URL; ?>assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
