          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Sous-Domaine</th>
                  <th>Domaine cible</th>
                  <th>Date de création</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
              [STORPROC [!Query!]/CNAME|D|0|100|tmsCreate|DESC]
                <tr>
                  <td>[!D::Nom!]</td>
                  <td>[!D::Dnscname!]</td>
                  <td>[DATE d/m/Y H:i:s][!D::tmsCreate!][/DATE]</td>
                      <td>
                        <div class="btn-group pull-right">
                            <a class="btn btn-warning popup" href="/[!Sys::getMenu(Parc/Domain)!]/[!D::Id!]/CNAME/[!D::Id!]/Form.htm">Editer</a>
                            <a class="btn btn-danger popup" href="/[!Sys::getMenu(Parc/Domain)!]/[!D::Id!]/CNAME/[!D::Id!]/Delete.htm">Supprimer</a>
                        </div>
                    </td>
                </tr>
             [/STORPROC]
              </tbody>
            </table>
          </div>