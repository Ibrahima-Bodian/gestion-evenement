<?php
// Paramètres de connexion à la base de données
$host = 'localhost';
$db   = 'evenement_aurillac';
$user = 'root';
$pass = ''; // Si vous avez un mot de passe pour MySQL, mettez-le ici
$charset = 'utf8mb4';

// DSN (Data Source Name) pour la connexion PDO
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

// Options pour PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Pour afficher les erreurs
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Pour récupérer les résultats sous forme de tableau associatif
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Pour utiliser les requêtes préparées natives
];

try {
    // Création de l'objet PDO
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // En cas d'erreur de connexion, afficher le message et arrêter le script
    echo 'Erreur de connexion à la base de données : ' . $e->getMessage();
    exit();
}
?>
