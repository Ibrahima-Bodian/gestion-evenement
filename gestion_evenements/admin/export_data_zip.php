<?php
session_start();

// Vérifier que l'utilisateur est admin
if (!isset($_SESSION['id_annonceur']) || $_SESSION['role_annonceur'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

require_once '../config/db_connect.php';

/**
 * Fonction utilitaire pour créer un CSV en mémoire à partir d'une requête SQL
 * et retourner le contenu CSV sous forme de string.
 *
 * @param PDO $pdo        : connexion PDO
 * @param string $sql     : requête SQL
 * @param array  $entetes : tableau associatif ['colonne' => 'alias_affichage']
 * @param string $nom_fichier : nom du fichier CSV (ex. "evenements.csv")
 * @return string         : contenu brut du CSV
 */
function genererCSV(PDO $pdo, $sql, array $entetes, $nom_fichier = 'data.csv')
{
    // Exécuter la requête
    $stmt = $pdo->query($sql);

    // Ouvrir un flux temporaire en écriture
    $temp = fopen('php://temp', 'w+');

    // Écrire l'entête (les alias d'affichage)
    fputcsv($temp, array_values($entetes));

    // Pour chaque ligne
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Reconstruire un tableau dans l'ordre des colonnes entêtes
        $ligne = [];
        foreach ($entetes as $col => $_alias) {
            $ligne[] = isset($row[$col]) ? $row[$col] : '';
        }
        fputcsv($temp, $ligne);
    }

    // Récupérer le contenu du flux
    rewind($temp);
    $csv_data = stream_get_contents($temp);
    fclose($temp);

    return $csv_data;
}

// Préparer un objet ZipArchive
$zip = new ZipArchive();
$zipFileName = 'export_complet.zip';

// Créer le ZIP en mémoire
$tmpFile = tempnam(sys_get_temp_dir(), 'zip');
$zip->open($tmpFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);

/* -------------------------------------------------------------------------
   1) Export des VISITES (table "statistiques")
   ------------------------------------------------------------------------- */
$sql = "SELECT id_statistique, id_evenement, date_consultation, id_annonceur
        FROM statistiques
        ORDER BY id_statistique";
$entetes = [
    'id_statistique'   => 'ID_Stat',
    'id_evenement'     => 'ID_Evenement',
    'date_consultation'=> 'DateConsultation',
    'id_annonceur'     => 'ID_Visiteur'
];
$csvVisites = genererCSV($pdo, $sql, $entetes, 'visites.csv');
// Ajouter dans le zip
$zip->addFromString('visites.csv', $csvVisites);

/* -------------------------------------------------------------------------
   2) Export des EVENEMENTS
   ------------------------------------------------------------------------- */
$sql = "SELECT id_evenement, id_annonceur, id_categorie_evenement, id_sous_categorie_evenement,
               nom, description, date_evenement, heure, lieu, image, statut, compteur_consultations, date_creation
        FROM evenement
        ORDER BY id_evenement";
$entetes = [
    'id_evenement'                => 'ID_Evenement',
    'id_annonceur'                => 'ID_Annonceur',
    'id_categorie_evenement'      => 'Cat_Evt',
    'id_sous_categorie_evenement' => 'SousCat_Evt',
    'nom'                         => 'Nom',
    'description'                 => 'Description',
    'date_evenement'              => 'Date',
    'heure'                       => 'Heure',
    'lieu'                        => 'Lieu',
    'image'                       => 'Image',
    'statut'                      => 'Statut',
    'compteur_consultations'      => 'Compteur',
    'date_creation'               => 'DateCreation'
];
$csvEvenements = genererCSV($pdo, $sql, $entetes, 'evenements.csv');
$zip->addFromString('evenements.csv', $csvEvenements);

/* -------------------------------------------------------------------------
   3) CATEGORIES EVENEMENT
   ------------------------------------------------------------------------- */
$sql = "SELECT id_categorie_evenement, nom FROM categorieevenement ORDER BY id_categorie_evenement";
$entetes = [
    'id_categorie_evenement' => 'ID_CatEvt',
    'nom'                    => 'Nom'
];
$csvCatEvt = genererCSV($pdo, $sql, $entetes, 'categories_evenement.csv');
$zip->addFromString('categories_evenement.csv', $csvCatEvt);

/* -------------------------------------------------------------------------
   4) SOUS-CATEGORIES EVENEMENT
   ------------------------------------------------------------------------- */
$sql = "SELECT id_sous_categorie_evenement, id_categorie_evenement, nom
        FROM souscategorieevenement
        ORDER BY id_sous_categorie_evenement";
$entetes = [
    'id_sous_categorie_evenement' => 'ID_SousCatEvt',
    'id_categorie_evenement'      => 'ID_CatEvt',
    'nom'                         => 'Nom'
];
$csvSousCatEvt = genererCSV($pdo, $sql, $entetes, 'sous_categories_evenement.csv');
$zip->addFromString('sous_categories_evenement.csv', $csvSousCatEvt);

/* -------------------------------------------------------------------------
   5) ANNONCEURS
   ------------------------------------------------------------------------- */
$sql = "SELECT id_annonceur, nom, email, telephone, id_categorie_annonceur, id_sous_categorie_annonceur, role_annonceur, date_creation
        FROM annonceur
        ORDER BY id_annonceur";
$entetes = [
    'id_annonceur'                => 'ID_Annonceur',
    'nom'                          => 'Nom',
    'email'                        => 'Email',
    'telephone'                    => 'Telephone',
    'id_categorie_annonceur'       => 'Cat_Ann',
    'id_sous_categorie_annonceur'  => 'SousCat_Ann',
    'role_annonceur'               => 'Role',
    'date_creation'                => 'DateCreation'
];
$csvAnnonceurs = genererCSV($pdo, $sql, $entetes, 'annonceurs.csv');
$zip->addFromString('annonceurs.csv', $csvAnnonceurs);

/* -------------------------------------------------------------------------
   6) CATEGORIES ANNONCEUR
   ------------------------------------------------------------------------- */
$sql = "SELECT id_categorie_annonceur, nom
        FROM categorieannonceur
        ORDER BY id_categorie_annonceur";
$entetes = [
    'id_categorie_annonceur' => 'ID_CatAnn',
    'nom'                    => 'Nom'
];
$csvCatAnn = genererCSV($pdo, $sql, $entetes, 'categories_annonceur.csv');
$zip->addFromString('categories_annonceur.csv', $csvCatAnn);

/* -------------------------------------------------------------------------
   7) SOUS-CATEGORIES ANNONCEUR
   ------------------------------------------------------------------------- */
$sql = "SELECT id_sous_categorie_annonceur, id_categorie_annonceur, nom
        FROM souscategorieannonceur
        ORDER BY id_sous_categorie_annonceur";
$entetes = [
    'id_sous_categorie_annonceur' => 'ID_SousCatAnn',
    'id_categorie_annonceur'      => 'ID_CatAnn',
    'nom'                          => 'Nom'
];
$csvSousCatAnn = genererCSV($pdo, $sql, $entetes, 'sous_categories_annonceur.csv');
$zip->addFromString('sous_categories_annonceur.csv', $csvSousCatAnn);

/* -------------------------------------------------------------------------
   Fin : Fermer le ZIP
   ------------------------------------------------------------------------- */
$zip->close();

/* -------------------------------------------------------------------------
   8) Envoyer le fichier ZIP au navigateur
   ------------------------------------------------------------------------- */
header('Content-Type: application/zip');
header('Content-disposition: attachment; filename='.$zipFileName);
header('Content-Length: ' . filesize($tmpFile));

readfile($tmpFile);
unlink($tmpFile);
exit;
