<?php
session_start();
if (!isset($_SESSION['id_annonceur']) || $_SESSION['role_annonceur'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

require_once '../config/db_connect.php';
require_once '../config/config.php';

// Récupérer les catégories d'annonceur
$stmtCat = $pdo->query('SELECT * FROM CategorieAnnonceur ORDER BY nom');
$catAnnonceurs = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

// Ajouter une sous-catégorie
if (isset($_POST['ajouter_sous_cat'])) {
    $nom = htmlspecialchars($_POST['nom_sous_cat']);
    $id_cat = (int) $_POST['id_categorie_annonceur'];

    $insert = $pdo->prepare('INSERT INTO Sous_categorie_annonceur (id_categorie_annonceur, nom) VALUES (?, ?)');
    $insert->execute([$id_cat, $nom]);
    header('Location: gerer_sous_categories_annonceur.php');
    exit();
}

// Supprimer
if (isset($_GET['supprimer']) && is_numeric($_GET['supprimer'])) {
    $id = (int) $_GET['supprimer'];
    $delete = $pdo->prepare('DELETE FROM Sous_categorie_annonceur WHERE id_sous_categorie_annonceur = ?');
    $delete->execute([$id]);
    header('Location: gerer_sous_categories_annonceur.php');
    exit();
}

// Lister
$sql = '
  SELECT sca.*, ca.nom AS cat_nom
  FROM Sous_categorie_annonceur sca
  JOIN CategorieAnnonceur ca ON sca.id_categorie_annonceur = ca.id_categorie_annonceur
  ORDER BY ca.nom, sca.nom
';
$list = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

$titre_page = "Gestion des Sous-Catégories Annonceur";
include '../includes/head.php';
include '../includes/header.php';
?>

<div class="container mt-5">
    <h2>Gestion des Sous-Catégories d'Annonceur</h2>

    <form method="POST" class="mb-4">
        <div class="mb-3">
            <label for="id_categorie_annonceur" class="form-label">Catégorie d'Annonceur</label>
            <select class="form-select" name="id_categorie_annonceur" required>
                <?php foreach ($catAnnonceurs as $catA): ?>
                    <option value="<?php echo $catA['id_categorie_annonceur']; ?>">
                        <?php echo htmlspecialchars($catA['nom']); ?>
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
                <td><?php echo htmlspecialchars($item['cat_nom']); ?></td>
                <td>
                    <a href="?supprimer=<?php echo $item['id_sous_categorie_annonceur']; ?>" 
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Supprimer cette sous-catégorie ?');">
                       Supprimer
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include '../includes/footer.php'; ?>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
