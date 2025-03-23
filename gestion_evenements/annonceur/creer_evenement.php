<?php
session_start();
if (!isset($_SESSION['id_annonceur']) || $_SESSION['role_annonceur'] != 'annonceur') {
    header('Location: ../login.php');
    exit();
}

require_once '../config/db_connect.php';

// Vérifier si l'annonceur est bloqué
$stmtBlock = $pdo->prepare('SELECT statut FROM annonceur WHERE id_annonceur = ?');
$stmtBlock->execute([$_SESSION['id_annonceur']]);
$statut = $stmtBlock->fetchColumn();

if ($statut === 'bloque') {
    die("<div class='alert alert-danger text-center mt-5'>Votre compte est bloqué. Vous ne pouvez pas créer d'événements.</div>");
}

// Récupérer la liste des catégories d’événement
$stmt = $pdo->query('SELECT * FROM CategorieEvenement ORDER BY nom');
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupérer la liste des sous-catégories d’événement
$stmt2 = $pdo->query('SELECT * FROM souscategorieevenement ORDER BY nom');
$sousCategories = $stmt2->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $description = htmlspecialchars($_POST['description']);
    $date_evenement = $_POST['date_evenement'];
    $heure = $_POST['heure'];
    $lieu = htmlspecialchars($_POST['lieu']);
    $id_categorie_evenement = (int)$_POST['id_categorie_evenement'];

    // Récupérer la sous-catégorie si l'utilisateur en a sélectionné une
    $id_sous_categorie_evenement = !empty($_POST['id_sous_categorie_evenement'])
        ? (int)$_POST['id_sous_categorie_evenement']
        : null;

    $id_annonceur = $_SESSION['id_annonceur'];

    // Gestion de l'image (facultatif)
    $image = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['image']['type'], $allowed_types)) {
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $image_name = uniqid('event_') . '.' . $extension;
            move_uploaded_file($_FILES['image']['tmp_name'], '../assets/images/' . $image_name);
            $image = $image_name;
        }
    }

    // Insertion en BDD
    $stmtInsert = $pdo->prepare('
        INSERT INTO Evenement (
            id_annonceur, 
            id_categorie_evenement, 
            id_sous_categorie_evenement,
            nom, 
            description, 
            date_evenement, 
            heure, 
            lieu, 
            image
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ');
    $stmtInsert->execute([
        $id_annonceur,
        $id_categorie_evenement,
        $id_sous_categorie_evenement,
        $nom,
        $description,
        $date_evenement,
        $heure,
        $lieu,
        $image
    ]);

    // Redirection
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un Événement - Événements Aurillac</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <!-- Fichier CSS personnalisé -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-5 creer-evenement-container">
        <h2 class="text-center mb-4 creer-evenement-title">Créer un nouvel événement</h2>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <form method="POST" action="" enctype="multipart/form-data" class="p-4 form-evenement">

                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom de l'événement :</label>
                        <input type="text" class="form-control form-control-sm" id="nom" name="nom" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description :</label>
                        <textarea class="form-control form-control-sm" id="description" name="description" rows="5" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="date_evenement" class="form-label">Date :</label>
                        <input type="date" class="form-control form-control-sm" id="date_evenement" name="date_evenement" required>
                    </div>

                    <div class="mb-3">
                        <label for="heure" class="form-label">Heure :</label>
                        <input type="time" class="form-control form-control-sm" id="heure" name="heure">
                    </div>

                    <div class="mb-3">
                        <label for="lieu" class="form-label">Lieu :</label>
                        <input type="text" class="form-control form-control-sm" id="lieu" name="lieu" required>
                    </div>

                    <div class="mb-3">
                        <label for="id_categorie_evenement" class="form-label">Catégorie :</label>
                        <select class="form-select form-select-sm" id="id_categorie_evenement" name="id_categorie_evenement" required>
                            <option value="">Choisir une catégorie</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id_categorie_evenement']; ?>">
                                    <?php echo htmlspecialchars($cat['nom']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Sous-catégorie (optionnelle) -->
                    <div class="mb-3">
                        <label for="id_sous_categorie_evenement" class="form-label">
                            Sous-catégorie (optionnel) :
                        </label>
                        <select class="form-select form-select-sm"
                                id="id_sous_categorie_evenement"
                                name="id_sous_categorie_evenement">
                            <option value="">(Aucune)</option>
                            <?php foreach ($sousCategories as $sc): ?>
                                <option value="<?php echo $sc['id_sous_categorie_evenement']; ?>">
                                    <?php echo htmlspecialchars($sc['nom']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Images possible (JPEG, PNG, GIF) :</label>
                        <input type="file" class="form-control form-control-sm" id="image" name="image" accept="image/*">
                    </div>

                    <button type="submit" class="btn btn-primary d-block mx-auto btn-creer">
                        Créer l'événement
                    </button>
                </form>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
