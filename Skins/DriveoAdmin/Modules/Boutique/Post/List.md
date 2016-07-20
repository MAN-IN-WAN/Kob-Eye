          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Titre</th>
                  <th>Image</th>
                  <th>Resume</th>
                  <th>Categorie</th>
                    <th>Actions</th>
                </tr>
              </thead>
              <tbody>
              [STORPROC Blog/Post|C|0|100|tmsCreate|DESC]
                <tr>
                  <td>[DATE d/m/Y H:i:s][!C::Date!][/DATE]</td>
                    <td>[!C::Titre!]</td>
                    [STORPROC Blog/Post/[!C::Id!]/Donnees/Type=Image|D|0|1]
                    <td><img src="/[!D::Fichier!].mini.200x100.jpg"/></td>
                    [NORESULT]
                    <td>Pas d'image</td>
                    [/NORESULT]
                    [/STORPROC]
                  <td>[SUBSTR 300][!C::Resume!][/SUBSTR]</td>
                  <td>[!C::Categorie!]</td>
                  <td style="min-width: 200px;">
                      <div class="btn-group" role="group">
                          <a class="btn btn-success" href="/[!Sys::getMenu(Blog/Post)!]/[!C::Id!]">Editer</a>
                          <a class="btn btn-danger confirm" data-confirm="Voulez-vous vraiment supprimer ce post ?" href="/[!Sys::getMenu(Blog/Post)!]/[!C::Id!]/Supprimer">Supprimer</a>
                      </div>
                  </td>
                </tr>
             [/STORPROC]
              </tbody>
            </table>
          </div>