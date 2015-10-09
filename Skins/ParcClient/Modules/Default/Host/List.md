          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Nom</th>
                  <th>Commentaires</th>
                  <th>Quota</th>
                  <th>Date de création</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
              [STORPROC [!ParcClient::getChildren(Host)!]|D|0|100|tmsCreate|DESC]
                <tr>
                  <td>[!D::Nom!]</td>
                  <td>[!D::Commentaires!]</td>
                  <td>[!D::Quota!]</td>
                  <td>[DATE d/m/Y H:i:s][!D::tmsCreate!][/DATE]</td>
                  <td><a class="btn btn-info  pull-right" href="/[!Sys::getMenu(Parc/Host)!]/[!D::Id!]">Editer</a></td>
                </tr>
             [/STORPROC]
              </tbody>
            </table>
          </div>