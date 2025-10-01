<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h4 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-edit"></i> Modifier Inscription
            </h4>
        </div>
        
        <div class="card-body">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Élève</label>
                    <input type="text" class="form-control" value="<?= 
                        htmlspecialchars($inscription['eleve_prenom'] . ' ' . $inscription['eleve_nom'])
                    ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label>Année scolaire</label>
                    <input type="text" class="form-control" value="<?= 
                        htmlspecialchars($inscription['annee_scolaire'])
                    ?>" readonly>
                </div>
                
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="classe_id">Classe</label>
                        <select class="form-control" id="classe_id" name="classe_id" required>
                            <?php foreach ($classes as $classe): ?>
                            <option value="<?= $classe['id'] ?>" <?= 
                                $classe['id'] == $inscription['classe_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($classe['nom']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="statut">Statut</label>
                        <select class="form-control" id="statut" name="statut" required>
                            <option value="actif" <?= $inscription['statut'] === 'actif' ? 'selected' : '' ?>>Actif</option>
                            <option value="inactif" <?= $inscription['statut'] === 'inactif' ? 'selected' : '' ?>>Inactif</option>
                            <option value="transféré" <?= $inscription['statut'] === 'transféré' ? 'selected' : '' ?>>Transféré</option>
                            <option value="abandon" <?= $inscription['statut'] === 'abandon' ? 'selected' : '' ?>>Abandon</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="frais_scolaires">Frais scolaires (€)</label>
                        <input type="number" step="0.01" class="form-control" 
                               id="frais_scolaires" name="frais_scolaires" 
                               value="<?= htmlspecialchars($inscription['frais_scolaires']) ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="remarques">Remarques</label>
                    <textarea class="form-control" id="remarques" name="remarques" rows="3"><?= 
                        htmlspecialchars($inscription['remarques'])
                    ?></textarea>
                </div>
                
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>