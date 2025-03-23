<?php
session_start();

// Afficher les erreurs (à retirer en production)
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Inclure la configuration pour BASE_URL
require_once 'config/config.php';

// Inclure la connexion à la base de données
require_once 'config/db_connect.php';

// -- AJOUT POUR COOKIE: Si un cookie "id_annonceur" existe et qu'on n'est pas déjà en session, on reconnecte automatiquement --
if (!isset($_SESSION['id_annonceur']) && isset($_COOKIE['id_annonceur']) && isset($_COOKIE['role_annonceur'])) {
    $_SESSION['id_annonceur'] = $_COOKIE['id_annonceur'];
    $_SESSION['nom'] = $_COOKIE['nom_annonceur'] ?? ''; // optionnel si vous enregistrez aussi le nom
    $_SESSION['role_annonceur'] = $_COOKIE['role_annonceur'];

    // Redirection selon le rôle
    if ($_SESSION['role_annonceur'] === 'admin') {
        header('Location: admin/index.php');
    } else {
        header('Location: annonceur/index.php');
    }
    exit();
}

// Définir le titre de la page (optionnel)
$titre_page = "Connexion";

$erreur = null;

// Logique de connexion
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérer et sécuriser les données du formulaire
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $mot_de_passe = isset($_POST['mot_de_passe']) ? $_POST['mot_de_passe'] : '';

    // Vérifier que le champ email n'est pas vide et est un email valide
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Email invalide.";
    } else {
        // Récupérer l'utilisateur correspondant à l'email
        $stmt = $pdo->prepare('SELECT * FROM Annonceur WHERE email = ?');
        $stmt->execute([$email]);
        $utilisateur = $stmt->fetch();

        // Vérification mot de passe
        if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
            // -- NOUVEAU : Vérifier si l'annonceur est bloqué --
            if (isset($utilisateur['statut']) && $utilisateur['statut'] === 'bloque') {
                $erreur = "Votre compte est bloqué. Contactez l'administrateur.";
            } else {
                // Connexion réussie
                $_SESSION['id_annonceur'] = $utilisateur['id_annonceur'];
                $_SESSION['nom'] = $utilisateur['nom'];
                $_SESSION['role_annonceur'] = $utilisateur['role_annonceur'];

                // -- AJOUT POUR COOKIE : Si "Se souvenir de moi" est coché --
                if (isset($_POST['remember_me'])) {
                    // On peut définir la durée (ex: 7 jours)
                    $duration = time() + (7 * 24 * 60 * 60);

                    // On enregistre les informations essentielles (id_annonceur, role, nom si besoin)
                    setcookie('id_annonceur', $utilisateur['id_annonceur'], $duration, '/');
                    setcookie('role_annonceur', $utilisateur['role_annonceur'], $duration, '/');
                    // Le nom est optionnel, mais vous pouvez le stocker aussi :
                    setcookie('nom_annonceur', $utilisateur['nom'], $duration, '/');
                }

                // Rediriger selon le rôle
                if ($utilisateur['role_annonceur'] == 'admin') {
                    header('Location: admin/index.php');
                } else {
                    header('Location: annonceur/index.php');
                }
                exit();
            }
        } else {
            $erreur = "Email ou mot de passe incorrect.";
        }
    }
}

// Inclure le head et le header
include 'includes/head.php'; // head.php charge Bootstrap, FontAwesome et style.css
include 'includes/header.php';
?>

<div class="container d-flex justify-content-center align-items-center mt-5">
    <div class="card p-4" style="max-width: 400px; width: 100%;">
        <h2 class="text-center mb-4">Connexion</h2>
        <?php if ($erreur): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($erreur); ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="mot_de_passe" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe" required>
            </div>

            <!-- AJOUT POUR COOKIE : Case à cocher "Se souvenir de moi" -->
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="remember_me" id="remember_me">
                <label class="form-check-label" for="remember_me">Se souvenir de moi</label>
            </div>

            <button type="submit" class="btn btn-primary w-100 d-flex align-items-center justify-content-center gap-2">
                <i class="fas fa-sign-in-alt"></i> <!-- Icône de connexion -->
                <span>Se connecter</span>
            </button>
        </form>
        <div class="d-flex justify-content-between align-items-center mt-3">
            <!-- Lien mot de passe oublié avec icône -->
            <a href="mot_de_passe_oublie.php" class="d-flex align-items-center gap-1">
                <i class="fas fa-unlock-alt"></i><span>Mot de passe oublié</span>
            </a>
            <!-- Lien s'inscrire avec icône -->
            <a href="register.php" class="d-flex align-items-center gap-1">
                <i class="fas fa-user-plus"></i><span>S'inscrire</span>
            </a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<script src="<?php echo BASE_URL; ?>assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
