          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Reference</th>
                  <th>Client</th>
                  <th>Date de création</th>
                  <th>Montant TTC</th>
                  <th>Retirée</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
              [STORPROC Boutique/Commande/Valide=1|C|0|100|tmsCreate|DESC]
                <tr>
                    [!Client:=[!C::getClient()!]!]
                  <td>[!C::RefCommande!]</td>
                  <td>[!Client::Nom!] [!Client::Prenom!]</td>
                  <td>[DATE d/m/Y H:i:s][!C::tmsCreate!][/DATE]</td>
                  <td>[!C::MontantTTC!]</td>
                  <td>[!C::Expedie!]</td>
                  <td><a class="btn btn-success pull-right" href="/[!Sys::getMenu(Boutique/Commande)!]/[!C::Id!]">Editer</a></td>
                </tr>
             [/STORPROC]
              </tbody>
            </table>
          </div>