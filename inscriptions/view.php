<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="fas fa-history"></i> Historique des Inscriptions
        </h6>
        <a href="inscriptions/create.php?eleve_id=<?= $eleve['id'] ?>" class="btn btn-sm btn-success">
            <i class="fas fa-plus"></i> Nouvelle inscription
        </a>
    </div>
    
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Année scolaire</th>
                        <th>Classe</th>
                        <th>Statut</th>
                        <th>Frais</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($inscriptions as $inscription): ?>
                    <tr>
                        <td><?= htmlspecialchars($inscription['annee_scolaire']) ?></td>
                        <td><?= htmlspecialchars($inscription['classe_nom']) ?></td>
                        <td>
                            <span class="badge badge-<?= 
                                $inscription['statut'] === 'actif' ? 'success' : 
                                ($inscription['statut'] === 'inactif' ? 'warning' : 'danger')
                            ?>">
                                <?= ucfirst($inscription['statut']) ?>
                            </span>
                        </td>
                        <td><?= number_format($inscription['frais_scolaires'], 2) ?> €</td>
                        <td><?= date('d/m/Y', strtotime($inscription['date_inscription'])) ?></td>
                        <td>
                            <a href="inscriptions/edit.php?id=<?= $inscription['id'] ?>" 
                               class="btn btn-sm btn-primary" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="inscriptions/delete.php?id=<?= $inscription['id'] ?>" 
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
    </div>
</div>