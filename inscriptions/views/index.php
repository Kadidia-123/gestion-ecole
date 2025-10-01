<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h4 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-user-graduate"></i> Gestion des Inscriptions
            </h4>
            <div>
                <form class="form-inline">
                    <label class="mr-2">Année scolaire:</label>
                    <select class="form-control" onchange="window.location.href='?annee='+this.value">
                        <?php foreach ($inscriptionModel->getYearsList() as $year): ?>
                        <option value="<?= $year ?>" <?= $year === $annee_scolaire ? 'selected' : '' ?>>
                            <?= $year ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
        </div>
        
        <div class="card-body">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            
            <div class="accordion" id="classesAccordion">
                <?php foreach ($inscriptionsParClasse as $classeId => $data): 
                    $classe = $data['classe'];
                    $eleves = $data['eleves'];
                ?>
                <div class="card">
                    <div class="card-header" id="heading<?= $classeId ?>">
                        <h5 class="mb-0">
                            <button class="btn btn-link" type="button" data-toggle="collapse" 
                                    data-target="#collapse<?= $classeId ?>" 
                                    aria-expanded="true" aria-controls="collapse<?= $classeId ?>">
                                <?= htmlspecialchars($classe['nom']) ?> 
                                <span class="badge badge-primary ml-2"><?= count($eleves) ?></span>
                            </button>
                        </h5>
                    </div>
                    
                    <div id="collapse<?= $classeId ?>" class="collapse" aria-labelledby="heading<?= $classeId ?>" 
                         data-parent="#classesAccordion">
                        <div class="card-body">
                            <?php if (!empty($eleves)): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Matricule</th>
                                            <th>Nom Complet</th>
                                            <th>Statut</th>
                                            <th>Frais</th>
                                            <th>Date Inscription</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($eleves as $eleve): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($eleve['matricule']) ?></td>
                                            <td><?= htmlspecialchars($eleve['eleve_prenom'] . ' ' . $eleve['eleve_nom']) ?></td>
                                            <td>
                                                <span class="badge badge-<?= 
                                                    $eleve['statut'] === 'actif' ? 'success' : 
                                                    ($eleve['statut'] === 'inactif' ? 'warning' : 'danger')
                                                ?>">
                                                    <?= ucfirst($eleve['statut']) ?>
                                                </span>
                                            </td>
                                            <td><?= number_format($eleve['frais_scolaires'], 2) ?> €</td>
                                            <td><?= date('d/m/Y', strtotime($eleve['date_inscription'])) ?></td>
                                            <td>
                                                <a href="inscriptions/edit.php?id=<?= $eleve['id'] ?>" 
                                                   class="btn btn-sm btn-primary" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="inscriptions/delete.php?id=<?= $eleve['id'] ?>" 
                                                   class="btn btn-sm btn-danger" title="Supprimer"
                                                   onclick="return confirm('Confirmer la suppression ?')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-info">
                                Aucun élève inscrit dans cette classe pour l'année <?= $annee_scolaire ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Ouvrir la première classe par défaut
    $('.collapse').first().addClass('show');
});
</script>