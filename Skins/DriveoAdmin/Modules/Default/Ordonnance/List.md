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
              [STORPROC Pharmacie/Ordonnance|C|0|100|tmsCreate|DESC]
                <tr>
                  <td>[!C::Nom!]</td>
                  <td>[!C::Prenom!]</td>
                  <td>[DATE d/m/Y H:i:s][!C::tmsCreate!][/DATE]</td>
                  <td>[!C::Email!]</td>
                  <td>[!C::Telephone!]</td>
                  <td>[!C::Etat!]</td>
                  <td><a class="btn btn-info pull-right" href="/[!Sys::getMenu(Pharmacie/Ordonnance)!]/[!C::Id!]">Editer</a></td>
                </tr>
             [/STORPROC]
              </tbody>
            </table>
          </div>