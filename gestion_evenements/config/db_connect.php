<?php
// Paramètres de connexion à la base de données PostgreSQL (via Supabase)
$host     = 'aws-0-eu-central-1.pooler.supabase.com'; // hôte
$port     = '6543';                                    // port
$db       = 'postgres';                                // nom de la base (souvent "postgres")
$user     = 'postgres.eeqyxvniltxpghyzulrd';           // nom d'utilisateur
$pass     = '1997SCID@sup';                   // votre mot de passe Supabase (à adapter)

// DSN (Data Source Name) pour la connexion PDO en PostgreSQL
$dsn = "pgsql:host=$host;port=$port;dbname=$db";

// Options pour PDO
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Pour afficher les erreurs
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Pour récupérer les résultats sous forme de tableau associatif
    PDO::ATTR_EMULATE_PREPARES   => false,                  // Pour utiliser les requêtes préparées natives
];

try {
    // Création de l'objet PDO
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // En cas d'erreur de connexion, afficher le message et arrêter le script
    echo 'Erreur de connexion à la base de données PostgreSQL (Supabase) : ' . $e->getMessage();
    exit();
}
?>
