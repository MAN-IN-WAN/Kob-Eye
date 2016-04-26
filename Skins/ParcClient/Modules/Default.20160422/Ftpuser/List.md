          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Identifiant</th>
                  <th>Mot de passe</th>
                  <th>Dossier</th>
                  <th>Date de création</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
              [STORPROC [!Query!]/Ftpuser|D|0|100|tmsCreate|DESC]
                <tr>
                  <td>[!D::Identifiant!]</td>
                  <td>[!D::Password!]</td>
                  <td>[!D::DocumentRoot!]</td>
                  <td>[DATE d/m/Y H:i:s][!D::tmsCreate!][/DATE]</td>
                      <td>
                        <div class="btn-group pull-right">
                            <a class="btn btn-warning popup" href="/[!Sys::getMenu(Parc/Host)!]/[!D::Id!]/Ftpuser/[!D::Id!]/Form.htm">Editer</a>
                            <a class="btn btn-danger popup" href="/[!Sys::getMenu(Parc/Host)!]/[!D::Id!]/Ftpuser/[!D::Id!]/Delete.htm">Supprimer</a>
                        </div>
                    </td>
                </tr>
             [/STORPROC]
              </tbody>
            </table>
          </div>