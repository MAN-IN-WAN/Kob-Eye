          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Domaine</th>
                  <th>Date de création</th>
                  <th>DNS Serial</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
              [STORPROC [!ParcClient::getChildren(Domain)!]|D|0|100|tmsCreate|DESC]
                <tr>
                  <td>[!D::Url!]</td>
                  <td>[DATE d/m/Y H:i:s][!D::tmsCreate!][/DATE]</td>
                  <td>[!D::DNSSerial!]</td>
                  <td><a class="btn btn-success pull-right" href="/[!Sys::getMenu(Parc/Domain)!]/[!D::Id!]">Editer</a></td>
                </tr>
             [/STORPROC]
              </tbody>
            </table>
          </div>