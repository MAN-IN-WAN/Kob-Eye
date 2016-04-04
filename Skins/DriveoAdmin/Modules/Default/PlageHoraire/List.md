          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Heure début</th>
                    <th>Heure fin</th>
                    <th>Actions</th>
                </tr>
              </thead>
              <tbody>
              [STORPROC Pharmacie/PlageHoraire|C|0|100|tmsCreate|DESC]
                <tr>
                    <td>[!C::HeureDebut!]h00</td>
                    <td>[!C::HeureFin!]h00</td>
                  <td style="min-width: 200px;">
                      <div class="btn-group" role="group">
                          <a class="btn btn-success" href="/[!Sys::getMenu(Pharmacie/PlageHoraire)!]/[!C::Id!]">Editer</a>
                          <a class="btn btn-danger confirm" data-confirm="Voulez-vous vraiment supprimer cette plage horaire ?" href="/[!Sys::getMenu(Pharmacie/PlageHoraire)!]/[!C::Id!]/Supprimer">Supprimer</a>
                      </div>
                  </td>
                </tr>
             [/STORPROC]
              </tbody>
            </table>
          </div>