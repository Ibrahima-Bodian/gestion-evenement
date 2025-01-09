<?php
session_start();
// Vérifier si l'utilisateur est connecté et est un annonceur
if (!isset($_SESSION['id_annonceur']) || $_SESSION['role_annonceur'] != 'annonceur') {
    header('Location: ../login.php');
    exit();
}

// Vérifier si l'ID de l'événement est passé en paramètre
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$id_evenement = $_GET['id'];

// Inclure la connexion à la base de données
require_once '../config/db_connect.php';

// Vérifier si l'annonceur est bloqué (optionnel)
$stmtBlock = $pdo->prepare('SELECT statut FROM annonceur WHERE id_annonceur = ?');
$stmtBlock->execute([$_SESSION['id_annonceur']]);
$statut = $stmtBlock->fetchColumn();
if ($statut === 'bloque') {
    die("<div class='alert alert-danger text-center mt-5'>Votre compte est bloqué. Vous ne pouvez pas modifier d'événements.</div>");
}

// Récupérer la liste des catégories d'événements
$stmtCat = $pdo->query('SELECT * FROM CategorieEvenement ORDER BY nom');
$categories = $stmtCat->fetchAll(PDO::FETCH_ASSOC);

// Récupérer les informations de l'événement (on s'assure que cet événement appartient bien à l'annonceur en session)
$stmtEvt = $pdo->prepare('SELECT * FROM Evenement WHERE id_evenement = ? AND id_annonceur = ?');
$stmtEvt->execute([$id_evenement, $_SESSION['id_annonceur']]);
$evenement = $stmtEvt->fetch();

if (!$evenement) {
    echo 'Événement non trouvé ou non autorisé.';
    exit();
}

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérer et sécuriser les données du formulaire
    $nom = htmlspecialchars($_POST['nom']);
    $description = htmlspecialchars($_POST['description']);
    $date_evenement = $_POST['date_evenement'];
    $heure = $_POST['heure'];
    $lieu = htmlspecialchars($_POST['lieu']);
    $id_categorie_evenement = (int) $_POST['id_categorie_evenement'];

    // Mettre à jour l'événement dans la base de données
    $stmtUpdate = $pdo->prepare('
        UPDATE Evenement 
        SET id_categorie_evenement = ?, 
            nom = ?, 
            description = ?, 
            date_evenement = ?, 
            heure = ?, 
            lieu = ?
        WHERE id_evenement = ? 
          AND id_annonceur = ?
    ');
    $stmtUpdate->execute([
        $id_categorie_evenement,
        $nom,
        $description,
        $date_evenement,
        $heure,
        $lieu,
        $id_evenement,
        $_SESSION['id_annonceur']
    ]);

    // Rediriger vers la page d'accueil ou la liste des événements
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier l'Événement - Événements Aurillac</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
    <!-- Fichier CSS personnalisé -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="container mt-5 modifier-evenement-container">
        <!-- Titre centré -->
        <h2 class="text-center mb-4 modifier-evenement-title">Modifier l'événement</h2>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <form method="POST" action="" class="p-4 form-evenement">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom de l'événement :</label>
                        <input type="text" 
                               class="form-control form-control-sm"
                               id="nom" 
                               name="nom" 
                               value="<?php echo htmlspecialchars($evenement['nom']); ?>" 
                               required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description :</label>
                        <textarea class="form-control form-control-sm" 
                                  id="description" 
                                  name="description" 
                                  rows="5" 
                                  required><?php echo htmlspecialchars($evenement['description']); ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="date_evenement" class="form-label">Date :</label>
                        <input type="date" 
                               class="form-control form-control-sm"
                               id="date_evenement" 
                               name="date_evenement"
                               value="<?php echo $evenement['date_evenement']; ?>"
                               required>
                    </div>

                    <div class="mb-3">
                        <label for="heure" class="form-label">Heure :</label>
                        <input type="time" 
                               class="form-control form-control-sm"
                               id="heure" 
                               name="heure"
                               value="<?php echo $evenement['heure']; ?>">
                    </div>

                    <div class="mb-3">
                        <label for="lieu" class="form-label">Lieu :</label>
                        <input type="text" 
                               class="form-control form-control-sm"
                               id="lieu" 
                               name="lieu" 
                               value="<?php echo htmlspecialchars($evenement['lieu']); ?>" 
                               required>
                    </div>

                    <div class="mb-3">
                        <label for="id_categorie_evenement" class="form-label">Catégorie :</label>
                        <select class="form-select form-select-sm"
                                id="id_categorie_evenement"
                                name="id_categorie_evenement"
                                required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id_categorie_evenement']; ?>"
                                        <?php if ($evenement['id_categorie_evenement'] == $cat['id_categorie_evenement']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($cat['nom']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Bouton centré -->
                    <button type="submit" class="btn btn-primary d-block mx-auto btn-creer">
                        Enregistrer les modifications
                    </button>
                </form>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>
    <script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
