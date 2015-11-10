          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Reference</th>
                  <th>Client</th>
                  <th>Date de création</th>
                  <th>Montant TTC</th>
                  <th>Préparé</th>
                  <th>Retiré</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
              [STORPROC Boutique/Commande/Valide=1&Cloture=0|C|0|100|tmsCreate|DESC]
                <tr>
                    [!Client:=[!C::getClient()!]!]
                  <td>[!C::RefCommande!]</td>
                  <td>[!Client::Nom!] [!Client::Prenom!]</td>
                  <td>[DATE d/m/Y H:i:s][!C::tmsCreate!][/DATE]</td>
                  <td>[!C::MontantTTC!]</td>
                  <td>
                      [IF [!C::Prepare!]]
                        <span class="label label-success">[DATE d/m/Y H:i:s][!C::PrepareLe!][/DATE]</span>
                      [ELSE]
                          <span class="label label-warning">Pas encore préparée</span>
                      [/IF]
                  </td>
                    <td>
                        [IF [!C::Expedie!]]
                        <span class="label label-success">[DATE d/m/Y H:i:s][!C::ExpedieLe!][/DATE]</span>
                        [ELSE]
                        <span class="label label-warning">Pas encore retirée</span>
                        [/IF]
                    </td>
                  <td>
                      <div class="btn-group" role="group">
                          <a class="btn btn-success" href="/[!Sys::getMenu(Boutique/Commande)!]/[!C::Id!]">Détails</a>
                          [IF [!C::Expedie!]]
                            <a class="btn btn-danger" href="/[!Sys::getMenu(Boutique/Commande)!]/[!C::Id!]">Cloturer</a>
                          [/IF]
                      </div>
                  </td>
                </tr>
             [/STORPROC]
              </tbody>
            </table>
          </div>