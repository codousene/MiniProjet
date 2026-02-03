<?php
$fichierJson = 'data/taches.json';
$taches = file_exists($fichierJson) ? json_decode(file_get_contents($fichierJson), true) : [];
$aujourdhui = date('Y-m-d');

// --- CALCUL DES STATISTIQUES ---
$total = count($taches);
$terminees = 0;
$en_cours = 0;
$a_faire = 0;
$en_retard = 0;

foreach ($taches as $t) {
    // Compte par statut
    if ($t['statut'] == 'terminée') $terminees++;
    if ($t['statut'] == 'en cours') $en_cours++;
    if ($t['statut'] == 'à faire') $a_faire++;

    // Compte des retards : Pas terminée ET date dépassée
    if ($t['statut'] != 'terminée' && $t['date_limite'] < $aujourdhui) {
        $en_retard++;
    }
}

// Calcul du pourcentage de progression
$pourcentage = ($total > 0) ? round(($terminees / $total) * 100) : 0;
?>

<div class="container-fluid px-4">
    <h1 class="mt-4 text-dark font-weight-bold">Tableau de Bord</h1>


    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tâches totales</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-tasks fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2 bg-light">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Tâches en retard</div>
                            <div class="h5 mb-0 font-weight-bold text-danger"><?= $en_retard ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-exclamation-circle fa-2x text-danger animate-pulse"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Taux de réalisation</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto"><div class="h5 mb-0 mr-3 font-weight-bold text-gray-800"><?= $pourcentage ?>%</div></div>
                                <div class="col"><div class="progress progress-sm mr-2"><div class="progress-bar bg-success" style="width: <?= $pourcentage ?>%"></div></div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">En cours d'exécution</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $en_cours ?></div>
                        </div>
                        <div class="col-auto"><i class="fas fa-spinner fa-2x text-info"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-dark">
            <h6 class="m-0 font-weight-bold text-white">Répartition par Statut</h6>
        </div>
        <div class="card-body">
            <h4 class="small font-weight-bold">À faire <span class="float-right"><?= $a_faire ?></span></h4>
            <div class="progress mb-4"><div class="progress-bar bg-primary" style="width: <?= ($total>0)?($a_faire/$total*100):0 ?>%"></div></div>
            
            <h4 class="small font-weight-bold">En cours <span class="float-right"><?= $en_cours ?></span></h4>
            <div class="progress mb-4"><div class="progress-bar bg-info" style="width: <?= ($total>0)?($en_cours/$total*100):0 ?>%"></div></div>
            
            <h4 class="small font-weight-bold">Terminées <span class="float-right text-success"><?= $terminees ?></span></h4>
            <div class="progress"><div class="progress-bar bg-success" style="width: <?= $pourcentage ?>%"></div></div>
        </div>
    </div>
</div>

<style>
.border-left-primary { border-left: .25rem solid #4e73df!important; }
.border-left-success { border-left: .25rem solid #1cc88a!important; }
.border-left-info { border-left: .25rem solid #36b9cc!important; }
.border-left-danger { border-left: .25rem solid #e74a3b!important; }
.animate-pulse { animation: pulse 2s infinite; }
@keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.4; } 100% { opacity: 1; } }
</style>