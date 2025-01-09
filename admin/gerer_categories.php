<?php
session_start();
if (!isset($_SESSION['id_annonceur']) || $_SESSION['role_annonceur'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

require_once '../config/db_connect.php';

// Ajouter une catégorie
if (isset($_POST['ajouter_categorie'])) {
    $nom_categorie = htmlspecialchars($_POST['nom_categorie']);
    $stmt = $pdo->prepare('INSERT INTO categorieevenement (nom) VALUES (?)');
    $stmt->execute([$nom_categorie]);
}

// Supprimer une catégorie
if (isset($_GET['supprimer_categorie'])) {
    $id_categorie = $_GET['supprimer_categorie'];
    $stmt = $pdo->prepare('DELETE FROM categorieevenement WHERE id_categorie_evenement = ?');
    $stmt->execute([$id_categorie]);
}

// Récupérer la liste des catégories
$stmt = $pdo->query('SELECT * FROM categorieevenement');
$categories = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gérer les Catégories - Administrateur</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php include '../includes/header.php'; ?>

<div class="container mt-5 admin-page-container">
    <h2 class="text-center mb-4 admin-page-title">Gérer les catégories</h2>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <form method="POST" action="" class="p-4 form-evenement">
                <div class="mb-3">
                    <label for="nom_categorie" class="form-label">Ajouter une nouvelle catégorie :</label>
                    <input type="text" class="form-control form-control-sm" id="nom_categorie" name="nom_categorie" required>
                </div>
                <button type="submit" name="ajouter_categorie" class="btn btn-primary d-block mx-auto btn-creer">
                    Ajouter
                </button>
            </form>

            <h3 class="mt-5">Liste des catégories existantes :</h3>
            <?php if (count($categories) > 0): ?>
                <ul class="list-group">
                    <?php foreach ($categories as $categorie): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?php echo htmlspecialchars($categorie['nom']); ?>
                            <a href="?supprimer_categorie=<?php echo $categorie['id_categorie_evenement']; ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?');">
                               Supprimer
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>Aucune catégorie trouvée.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
