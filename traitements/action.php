<?php
// On charge la connexion (assure-toi que db.php est dans le même dossier)
require_once 'db.php'; 

$fichierJson = '../data/taches.json';
$action = $_POST['action'] ?? $_GET['action'] ?? null;
$taches = file_exists($fichierJson) ? json_decode(file_get_contents($fichierJson), true) : [];

// --- 1. ACTION : CHANGER LE STATUT (ROTATION AU CLIC) ---
if ($action == "changer_statut") {
    $id = $_GET['id'];
    foreach ($taches as &$t) {
        if ($t['id'] === $id) {
            if ($t['statut'] === "à faire") $t['statut'] = "en cours";
            elseif ($t['statut'] === "en cours") $t['statut'] = "terminée";
            else $t['statut'] = "à faire";
            break;
        }
    }
}

if ($action == "modifier_tache_json") {
    $id = $_POST['id'];
    foreach ($taches as &$t) {
        if ($t['id'] === $id) {
            // VERIFICATION : On ne modifie QUE si le statut n'est pas 'terminée'
            if ($t['statut'] !== 'terminée') {
                $t['titre'] = htmlspecialchars($_POST['titre']);
                $t['description'] = htmlspecialchars($_POST['description']);
                $t['priorite'] = $_POST['priorite'];
                $t['date_limite'] = $_POST['date_limite'];
                $t['responsable_id'] = $_POST['responsable_id'];
            }
            break;
        }
    }
}

file_put_contents($fichierJson, json_encode(array_values($taches), JSON_PRETTY_PRINT));
header("Location: ../index.php?page=indexTache");
// --- 2. ACTION : SUPPRIMER ---
if ($action == "supprimer_tache") {
    $id = $_GET['id'];
    $taches = array_filter($taches, function($t) use ($id) {
        return $t['id'] !== $id;
    });
}

// --- 3. ACTION : AJOUTER ---
if ($action == "ajouter_tache_json") {
    $taches[] = [
        "id" => uniqid(),
        "titre" => htmlspecialchars($_POST['titre']),
        "description" => htmlspecialchars($_POST['description']),
        "priorite" => $_POST['priorite'],
        "statut" => "à faire",
        "date_limite" => $_POST['date_limite'],
        "responsable_id" => $_POST['responsable_id']
    ];
}

if ($action == "ajouter_utilisateur") {
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);

    if (!empty($nom) && !empty($prenom)) {
        $stmt = $pdo->prepare("INSERT INTO utilisateur (nom, prenom) VALUES (?, ?)");
        $stmt->execute([$nom, $prenom]);
    }
    header("Location: ../index.php?page=indexUser");
    exit();
}

// --- ACTION : SUPPRIMER UTILISATEUR (SQL) ---
if ($action == "supprimer_utilisateur") {
    $id = $_GET['id'];
    if (!empty($id)) {
        $stmt = $pdo->prepare("DELETE FROM utilisateur WHERE id = ?");
        $stmt->execute([$id]);
    }
    header("Location: ../index.php?page=indexUser");
    exit();
}
// Sauvegarde et redirection vers la vue
file_put_contents($fichierJson, json_encode(array_values($taches), JSON_PRETTY_PRINT));
header("Location: ../index.php?page=indexTache");
exit();