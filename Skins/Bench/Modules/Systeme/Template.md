<ul>
[STORPROC Boutique/Categorie|C|0|1000|Nom|ASC]
        <li>
            <h1>[!C::Nom!]</h1>
            [STORPROC Boutique/Categorie/[!C::Id!]/Categorie|C2|0|1000|Nom|ASC]
                <ul>
                      [LIMIT 0|100]
                                <li>
                                    <h2>[!C2::Nom!]</h2>
                                    [STORPROC Boutique/Categorie/[!C2::Id!]/Categorie|C3|0|1000|Nom|ASC]
                                    <ul>
                                        [LIMIT 0|100]
                                        <li>
                                            <h3>[!C3::Nom!]</h3>
                                            [STORPROC Boutique/Categorie/[!C3::Id!]/Categorie|C4|0|1000|Nom|ASC]
                                            <ul>
                                                [LIMIT 0|100]
                                                <li>
                                                    <h4>[!C4::Nom!]</h4>
                                                    [STORPROC Boutique/Categorie/[!C4::Id!]/Categorie|C5|0|1000|Nom|ASC]
                                                    <ul>
                                                        [LIMIT 0|100]
                                                        <li>
                                                            <h1>[!C5::Nom!]</h1>
                                                        </li>
                                                        [/LIMIT]
                                                    </ul>
                                          [/STORPROC]
                                        </li>
                                       [/LIMIT]
                                   </ul>
                                  [/STORPROC]
                          </li>
                          [/LIMIT]
                      </ul>
                      [/STORPROC]
                  </li>
                  [/LIMIT]
              </ul>
            [/STORPROC]
        </li>
[/STORPROC]
</ul>
