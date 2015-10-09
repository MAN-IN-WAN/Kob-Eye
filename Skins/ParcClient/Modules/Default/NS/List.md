          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Nom</th>
                  <th>Serveur de nom</th>
                  <th>Date de création</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
              [STORPROC [!Query!]/NS|D|0|100|tmsCreate|DESC]
                <tr>
                  <td>[!D::Nom!]</td>
                  [STORPROC Parc/Server/NS/[!D::Id!]|S|0|1]
                  <td>[!S::DNSNom!]</td>
                 [/STORPROC]
                  <td>[DATE d/m/Y H:i:s][!D::tmsCreate!][/DATE]</td>
                      <td>
                        <div class="btn-group pull-right">
                            <a class="btn btn-warning popup" href="/[!Sys::getMenu(Parc/Domain)!]/[!D::Id!]/NS/[!D::Id!]/Form.htm">Editer</a>
                            <a class="btn btn-danger popup" href="/[!Sys::getMenu(Parc/Domain)!]/[!D::Id!]/NS/[!D::Id!]/Delete.htm">Supprimer</a>
                        </div>
                    </td>
                </tr>
             [/STORPROC]
              </tbody>
            </table>
          </div>