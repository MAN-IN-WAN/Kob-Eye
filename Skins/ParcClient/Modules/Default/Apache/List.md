          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Nom de domaine</th>
                  <th>Alias</th>
                  <th>Dossier</th>
                  <th>Dossier protégé</th>
                  <th>Date de création</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
              [STORPROC [!Query!]/Apache|D|0|100|tmsCreate|DESC]
                <tr>
                  <td>[!D::ApacheServerName!]</td>
                  <td>[UTIL NL2BR][!D::ApacheServerAlias!][/UTIL]</td>
                  <td>[!D::DocumentRoot!]</td>
                  <td>[IF [!D::PasswordProtected!]]<div class="badge badge-success"><span class="glyphicon glyphicon-ok" aria-hidden="true"></span></div>[ELSE]<div class="badge badge-warning"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></div>[/IF]</td>
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