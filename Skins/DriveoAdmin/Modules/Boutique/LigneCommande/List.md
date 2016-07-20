          <div class="table-responsive">
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>Image</th>
                  <th>Référence</th>
                  <th>Produit</th>
                  <th>Quantité</th>
                  <th>Montant TTC</th>
                </tr>
              </thead>
              <tbody>
              [STORPROC [!Query!]/LigneCommande|LC|0|100|tmsCreate|DESC]
                <tr>
                    [!Ref:=[!LC::getReference()!]!]
                    [!Prod:=[!Ref::getProd()!]!]
                  <td><img class="img-responsive" src="/[!Prod::Image!]" style="width: 100px;" /></td>
                  <td>[!Ref::Reference!]</td>
                  <td>[!LC::Titre!]</td>
                    <td>[!LC::Quantite!]</td>
                    <td>[!LC::MontantTTC!]</td>
                </tr>
             [/STORPROC]
              </tbody>
            </table>
          </div>