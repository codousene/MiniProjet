<?php
require_once 'traitements/db.php';
try {
    $stmt = $pdo->query("SELECT * FROM utilisateur ORDER BY nom Desc");
    $utilisateurs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $utilisateurs = [];
    $erreur = "Erreur SQL : " . $e->getMessage();
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Gestion des Utilisateurs</h1>


    <?php if (isset($erreur)): ?>
        <div class="alert alert-danger"><?= $erreur ?></div>
    <?php endif; ?>

    <div class="card mb-4 shadow-sm border-0">
        <div class="card-header bg-primary text-white fw-bold">
            <i class="fas fa-user-plus me-1"></i> Ajout utilisateur
        </div>
        <div class="card-body bg-light">
            <form action="traitements/action.php" method="POST" class="row g-3">
                <input type="hidden" name="action" value="ajouter_utilisateur">
                
                <div class="col-md-5">
                    <label class="form-label fw-bold">Prénom</label>
                    <input type="text" name="prenom" class="form-control" placeholder="" required>
                </div>
                
                <div class="col-md-5">
                    <label class="form-label fw-bold">Nom</label>
                    <input type="text" name="nom" class="form-control" placeholder="" required>
                </div>
                
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-check"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Prénom</th>
                            <th>Nom</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($utilisateurs)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Aucun utilisateur enregistré.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($utilisateurs as $u): ?>
                            <tr>
                                <td class="ps-4 text-muted"><?= $u['id'] ?></td>
                                <td class="fw-bold text-dark"><?= htmlspecialchars($u['prenom']) ?></td>
                                <td class="text-uppercase"><?= htmlspecialchars($u['nom']) ?></td>
                                <td class="text-center">
                                    <a href="traitements/action.php?action=supprimer_utilisateur&id=<?= $u['id'] ?>" 
                                       class="btn btn-outline-danger btn-sm" 
                                       onclick="return confirm('Attention : Supprimer cet utilisateur peut rendre ses tâches orphelines. Continuer ?')">
                                        <i class="fas fa-trash-alt"></i> Supprimer
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>