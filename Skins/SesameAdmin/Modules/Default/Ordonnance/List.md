          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Nom</th>
                  <th>Prenom</th>
                  <th>Date de création</th>
                  <th>Email</th>
                  <th>Téléphone</th>
                  <th>Etat</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
              [STORPROC Pharmacie/Ordonnance/Etat<4|C|0|100|tmsCreate|DESC]
                <tr>
                  <td>[!C::Nom!]</td>
                  <td>[!C::Prenom!]</td>
                  <td>[DATE d/m/Y H:i:s][!C::tmsCreate!][/DATE]</td>
                  <td>[!C::Email!]</td>
                  <td>[!C::Telephone!]</td>
                  <td>
                      [SWITCH [!C::Etat!]|=]
                            [CASE 1]
                                <div class="label label-danger">Non préparée</div>
                            [/CASE]
                          [CASE 2]
                            <div class="label label-warning">Préparée mais non retirée</div>
                          [/CASE]
                          [CASE 3]
                              <div class="label label-warning">Préparée, retirée mais non cloturée</div>
                          [/CASE]
                          [DEFAULT]
                            <div class="label label-warning">Cloturée</div>
                          [/DEFAULT]
                      [/SWITCH]
                  </td>
                  <td>
                      <div class="btn-group" role="group">
                          <a class="btn btn-info" href="/[!Sys::getMenu(Pharmacie/Ordonnance)!]/[!C::Id!]">Détails</a>
                          [IF [!C::Etat!]>2]
                          <a class="btn btn-danger" href="/[!Sys::getMenu(Pharmacie/Ordonnance)!]/[!C::Id!]">Cloturer</a>
                          [/IF]
                      </div>
                  </td>
                </tr>
             [/STORPROC]
              </tbody>
            </table>
          </div>