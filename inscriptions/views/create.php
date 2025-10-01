<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h4 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-user-plus"></i> Nouvelle Inscription
            </h4>
        </div>
        
        <div class="card-body">
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
                <?php if ($eleve): ?>
                <div class="form-group">
                    <label>Élève</label>
                    <input type="text" class="form-control" value="<?= 
                        htmlspecialchars($eleve['prenom'] . ' ' . $eleve['nom'] . ' (' . $eleve['matricule'] . ')')
                    ?>" readonly>
                    <input type="hidden" name="eleve_id" value="<?= $eleve['id'] ?>">
                </div>
                <?php else: ?>
                <div class="form-group">
                    <label for="eleve_id">Élève</label>
                    <select class="form-control" id="eleve_id" name="eleve_id" required>
                        <option value="">-- Sélectionner un élève --</option>
                        <?php foreach ($eleveModel->getAll() as $e): ?>
                        <option value="<?= $e['id'] ?>">
                            <?= htmlspecialchars($e['prenom'] . ' ' . $e['nom'] . ' (' . $e['matricule'] . ')') ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="classe_id">Classe</label>
                        <select class="form-control" id="classe_id" name="classe_id" required>
                            <option value="">-- Sélectionner une classe --</option>
                            <?php foreach ($classes as $classe): ?>
                            <option value="<?= $classe['id'] ?>">
                                <?= htmlspecialchars($classe['nom']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="annee_scolaire">Année scolaire</label>
                        <select class="form-control" id="annee_scolaire" name="annee_scolaire" required>
                            <?php foreach ($annees as $annee): ?>
                            <option value="<?= $annee ?>" <?= 
                                $annee === $inscriptionModel->getCurrentYear() ? 'selected' : '' ?>>
                                <?= $annee ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="frais_scolaires">Frais scolaires (€)</label>
                        <input type="number" step="0.01" class="form-control" 
                               id="frais_scolaires" name="frais_scolaires" value="0">
                    </div>
                    
                    <div class="form-group col-md-6">
                        <label for="statut">Statut</label>
                        <select class="form-control" id="statut" name="statut">
                            <option value="actif" selected>Actif</option>
                            <option value="inactif">Inactif</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="remarques">Remarques</label>
                    <textarea class="form-control" id="remarques" name="remarques" rows="3"></textarea>
                </div>
                
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>