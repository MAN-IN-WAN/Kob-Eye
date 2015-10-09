          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Sous-Domaine</th>
                  <th>IP</th>
                  <th>Date de création</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
              [STORPROC [!Query!]/Subdomain|D|0|100|tmsCreate|DESC]
                <tr>
                  <td>[!D::Url!]</td>
                  <td>[!D::IP!]</td>
                  <td>[DATE d/m/Y H:i:s][!D::tmsCreate!][/DATE]</td>
                      <td>
                        <div class="btn-group pull-right">
                            <a class="btn btn-warning popup" href="/[!Sys::getMenu([!Query!])!]/Subdomain/[!D::Id!]/Form.htm" data-title="Edition du sous domaine">Editer</a>
                            <a class="btn btn-danger popup" href="/[!Sys::getMenu([!Query!])!]/Subdomain/[!D::Id!]/Delete.htm" data-title="Suppression du sous domaine">Supprimer</a>
                        </div>
                    </td>
                </tr>
             [/STORPROC]
              </tbody>
            </table>
          </div>