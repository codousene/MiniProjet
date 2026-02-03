<?php
require_once 'traitements/db.php'; 
$fichierJson = 'data/taches.json';
$taches_brutes = file_exists($fichierJson) ? json_decode(file_get_contents($fichierJson), true) : [];
$aujourdhui = date('Y-m-d');

$users_map = [];
try {
    $stmt = $pdo->query("SELECT id, nom, prenom FROM utilisateur");
    $utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($utilisateurs as $u) { $users_map[$u['id']] = $u['prenom'] . ' ' . $u['nom']; }
} catch (Exception $e) { $utilisateurs = []; }


$recherche = $_GET['search'] ?? '';
$filtre_statut = $_GET['f_statut'] ?? '';
$filtre_priorite = $_GET['f_priorite'] ?? '';

$taches = array_filter($taches_brutes, function($t) use ($recherche, $filtre_statut, $filtre_priorite) {
    if ($recherche != '' && stripos($t['titre'], $recherche) === false && stripos($t['description'], $recherche) === false) return false;
    if ($filtre_statut != '' && $t['statut'] !== $filtre_statut) return false;
    if ($filtre_priorite != '' && $t['priorite'] !== $filtre_priorite) return false;
    return true;
});
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Gestion des Tâches</h1>

    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-primary text-white fw-bold">Ajout Tâche</div>
        <div class="card-body bg-light">
            <form action="traitements/action.php" method="POST" class="row g-3">
                <input type="hidden" name="action" value="ajouter_tache_json">
                <div class="col-md-4"><input type="text" name="titre" class="form-control" placeholder="Titre" required></div>
                <div class="col-md-3">
                    <select name="responsable_id" class="form-select" required>
                        <option value="">Responsable...</option>
                        <?php foreach ($utilisateurs as $u): ?>
                            <option value="<?= $u['id'] ?>"><?= $u['prenom'].' '.$u['nom'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="priorite" class="form-select">
                        <option value="basse">Basse</option>
                        <option value="moyenne" selected>Moyenne</option>
                        <option value="haute">Haute</option>
                    </select>
                </div>
                <div class="col-md-3"><input type="date" name="date_limite" class="form-control" required></div>
                <div class="col-12"><textarea name="description" class="form-control" placeholder="Description"></textarea></div>
                <div class="col-12 text-end"><button type="submit" class="btn btn-success">Ajouter</button></div>
            </form>
        </div>
    </div>
    <div class="card mb-3 shadow-sm border-0">
    <div class="card-header bg-dark text-white fw-bold">Recherche et Filtrage</div>
    <div class="card-body bg-light">
        <form method="GET" class="row g-2">
            <input type="hidden" name="page" value="indexTache">
            
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="<?= htmlspecialchars($recherche) ?>">
            </div>
            
            <div class="col-md-3">
                <select name="f_statut" class="form-select">
                    <option value="">Tous les statuts</option>
                    <option value="à faire" <?= $filtre_statut == 'à faire' ? 'selected' : '' ?>>À faire</option>
                    <option value="en cours" <?= $filtre_statut == 'en cours' ? 'selected' : '' ?>>En cours</option>
                    <option value="terminée" <?= $filtre_statut == 'terminée' ? 'selected' : '' ?>>Terminée</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <select name="f_priorite" class="form-select">
                    <option value="">Priorités</option>
                    <option value="basse" <?= $filtre_priorite == 'basse' ? 'selected' : '' ?>>Basse</option>
                    <option value="moyenne" <?= $filtre_priorite == 'moyenne' ? 'selected' : '' ?>>Moyenne</option>
                    <option value="haute" <?= $filtre_priorite == 'haute' ? 'selected' : '' ?>>Haute</option>
                </select>
            </div>

            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
                
                <a href="index.php?page=indexTache" class="btn btn-outline-danger" title="Effacer les filtres">
                    <i class="fas fa-times"></i> Effacer
                </a>
            </div>
        </form>
    </div>
</div>

    <div class="card shadow border-0 mb-4">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Titre & Description</th>
                        <th>Responsable</th>
                        <th>Priorité</th> <th>Statut (Clic)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($taches as $t): 
                        $estEnRetard = ($t['statut'] !== 'terminée' && $t['date_limite'] < $aujourdhui);
                        
                        // Détermination de la couleur du badge de priorité
                        $prio_class = 'bg-secondary'; // basse
                        if($t['priorite'] == 'moyenne') $prio_class = 'bg-warning text-dark';
                        if($t['priorite'] == 'haute') $prio_class = 'bg-danger';
                    ?>
                    <tr class="<?= $estEnRetard ? 'table-danger' : '' ?>">
                        <td class="ps-3">
                            <strong><?= htmlspecialchars($t['titre']) ?></strong><br>
                            <small class="text-muted"><?= htmlspecialchars($t['description'] ?? '') ?></small>
                        </td>
                        <td><?= $users_map[$t['responsable_id'] ?? ''] ?? 'Non assigné' ?></td>
                        
                        <td class="text-center">
                            <span class="badge <?= $prio_class ?> rounded-pill px-3">
                                <?= strtoupper($t['priorite']) ?>
                            </span>
                        </td>

                        <td class="text-center">
                            <a href="traitements/action.php?action=changer_statut&id=<?= $t['id'] ?>" class="text-decoration-none">
                                <?php $c = ($t['statut']=='terminée') ? 'success' : (($t['statut']=='en cours') ? 'info' : 'primary'); ?>
                                <span class="badge bg-<?= $c ?> p-2 w-100"><?= strtoupper($t['statut']) ?></span>
                            </a>
                        </td>
                        <td class="text-center">
                            <?php if ($t['statut'] !== 'terminée'): ?>
                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#edit<?= $t['id'] ?>">Modifier</button>
                                
                                <div class="modal fade" id="edit<?= $t['id'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <form action="traitements/action.php" method="POST" class="modal-content">
                                            <div class="modal-header"><h5 class="modal-title">Modifier la tâche</h5></div>
                                            <div class="modal-body text-start">
                                                <input type="hidden" name="action" value="modifier_tache_json">
                                                <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                                <div class="mb-2"><label>Titre</label><input type="text" name="titre" class="form-control" value="<?= $t['titre'] ?>" required></div>
                                                <div class="mb-2"><label>Description</label><textarea name="description" class="form-control"><?= $t['description'] ?></textarea></div>
                                                <div class="mb-2">
                                                    <label>Responsable</label>
                                                    <select name="responsable_id" class="form-select">
                                                        <?php foreach ($utilisateurs as $u): ?>
                                                            <option value="<?= $u['id'] ?>" <?= ($t['responsable_id'] == $u['id']) ? 'selected' : '' ?>><?= $u['prenom'].' '.$u['nom'] ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="mb-2"><label>Priorité</label>
                                                    <select name="priorite" class="form-select">
                                                        <option value="basse" <?= $t['priorite']=='basse'?'selected':'' ?>>Basse</option>
                                                        <option value="moyenne" <?= $t['priorite']=='moyenne'?'selected':'' ?>>Moyenne</option>
                                                        <option value="haute" <?= $t['priorite']=='haute'?'selected':'' ?>>Haute</option>
                                                    </select>
                                                </div>
                                                <div class="mb-2"><label>Date limite</label><input type="date" name="date_limite" class="form-control" value="<?= $t['date_limite'] ?>" required></div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                <button type="submit" class="btn btn-primary">Sauvegarder</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <a href="traitements/action.php?action=supprimer_tache&id=<?= $t['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ?')">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    