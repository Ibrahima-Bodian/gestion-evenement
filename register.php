<?php
// Démarrer la session
session_start();

// Inclure la connexion à la base de données
require_once 'config/db_connect.php';

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérer et sécuriser les données du formulaire
    $nom = htmlspecialchars($_POST['nom']);
    $email = htmlspecialchars($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];
    $mot_de_passe_confirme = $_POST['mot_de_passe_confirme'];
    $telephone = htmlspecialchars($_POST['telephone']);

    // Vérifier que les mots de passe correspondent
    if ($mot_de_passe !== $mot_de_passe_confirme) {
        $erreur = "Les mots de passe ne correspondent pas.";
    } else {
        // Vérifier si l'email existe déjà
        $stmt = $pdo->prepare('SELECT id_annonceur FROM Annonceur WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $erreur = "Cet email est déjà utilisé.";
        } else {
            // Hacher le mot de passe
            $mot_de_passe_hache = password_hash($mot_de_passe, PASSWORD_BCRYPT);

            // Insérer le nouvel annonceur dans la base de données
            $stmt = $pdo->prepare('INSERT INTO Annonceur (nom, email, mot_de_passe, telephone, id_categorie_annonceur) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$nom, $email, $mot_de_passe_hache, $telephone, 2]); // 2 pour la catégorie "Entreprise" par défaut

            // Rediriger vers la page de connexion
            header('Location: login.php');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - Événements Aurillac</title>
    <!-- Inclure Bootstrap CSS -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <!-- Inclure le fichier CSS personnalisé -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Inclure l'en-tête -->
    <?php include 'includes/header.php'; ?>

    <div class="container mt-5 admin-page-container">
        <h2 class="text-center mb-4 admin-page-title">Créer un compte</h2>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <!-- Afficher une erreur si elle existe -->
                <?php if (isset($erreur)): ?>
                    <div class="alert alert-danger text-center" role="alert">
                        <?php echo $erreur; ?>
                    </div>
                <?php endif; ?>

                <!-- Formulaire d'inscription -->
                <form method="POST" action="" class="p-4 form-evenement">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom :</label>
                        <input type="text" class="form-control form-control-sm" id="nom" name="nom" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email :</label>
                        <input type="email" class="form-control form-control-sm" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="mot_de_passe" class="form-label">Mot de passe :</label>
                        <input type="password" class="form-control form-control-sm" id="mot_de_passe" name="mot_de_passe" required>
                    </div>
                    <div class="mb-3">
                        <label for="mot_de_passe_confirme" class="form-label">Confirmez le mot de passe :</label>
                        <input type="password" class="form-control form-control-sm" id="mot_de_passe_confirme" name="mot_de_passe_confirme" required>
                    </div>
                    <div class="mb-3">
                        <label for="telephone" class="form-label">Téléphone :</label>
                        <input type="text" class="form-control form-control-sm" id="telephone" name="telephone">
                    </div>
                    <button type="submit" class="btn btn-primary d-block mx-auto btn-creer">
                        S'inscrire
                    </button>
                </form>

                <!-- Lien vers la connexion -->
                <p class="mt-3 text-center">
                    Déjà inscrit ? <a href="login.php">Connectez-vous ici</a>.
                </p>
            </div>
        </div>
    </div>

    <!-- Inclure le pied de page -->
    <?php include 'includes/footer.php'; ?>

    <!-- Inclure Bootstrap JS -->
    <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
