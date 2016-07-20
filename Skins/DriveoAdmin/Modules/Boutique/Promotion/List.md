          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Date début</th>
                    <th>Date fin</th>
                  <th>Intitule</th>
                  <th>Image</th>
                    <th>Actions</th>
                </tr>
              </thead>
              <tbody>
              [STORPROC Boutique/Promotion|C|0|100|tmsCreate|DESC]
                <tr>
                    <td>[DATE d/m/Y][!C::DateDebutPromo!][/DATE]</td>
                    <td>[DATE d/m/Y][!C::DateFinPromo!][/DATE]</td>
                    <td>[!C::Intitule!]</td>
                    [IF [!C::Image!]]
                    <td><img src="/[!C::Image!].mini.200x100.jpg"/></td>
                    [ELSE]
                    <td>Pas d'image</td>
                    [/IF]
                  <td style="min-width: 200px;">
                      <div class="btn-group" role="group">
                          <a class="btn btn-success" href="/[!Sys::getMenu(Boutique/Promotion)!]/[!C::Id!]">Editer</a>
                          <a class="btn btn-danger confirm" data-confirm="Voulez-vous vraiment supprimer cette promotion ?" href="/[!Sys::getMenu(Boutique/Promotion)!]/[!C::Id!]/Supprimer">Supprimer</a>
                      </div>
                  </td>
                </tr>
             [/STORPROC]
              </tbody>
            </table>
          </div>