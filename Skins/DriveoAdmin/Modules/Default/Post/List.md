          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Titre</th>
                  <th>Resume</th>
                  <th>Publier</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
              [STORPROC Blog/Post|C|0|100|tmsCreate|DESC]
                <tr>
                  <td>[DATE d/m/Y H:i:s][!C::Date!][/DATE]</td>
                  <td>[!C::Titre!]</td>
                  <td>[!C::Resume!]</td>
                  <td>[!C::Publier!]</td>
                  <td><a class="btn btn-success pull-right" href="/[!Sys::getMenu(Blog/Post)!]/[!C::Id!]">Editer</a></td>
                </tr>
             [/STORPROC]
              </tbody>
            </table>
          </div>