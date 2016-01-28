[INFO [!Query!]|I]
[IF [!I::TypeSearch!]=Child]
    [COUNT Boutique/Marque|Nb]
    [!NbCol:=[!Math::Floor([!Nb:/3!])!]!]
    <div class="contenttop block">
        <h1 class="title_block"> Marques / Laboratoires <span class="resumecat category-product-count"> / __THERE_IS__ [!Nb!] __BRANDS__. </span></h1>
        [STORPROC Boutique/Marque|M|0|1000|Nom|ASC]
        <div class=" block_content">
            <div class="row">
                <div class="col-md-4">
                    <ul>
                        [LIMIT 0|[!NbCol!]]
                        <li>
                            [COUNT Boutique/Marque/[!M::Id!]/Produit/Actif=1|NbProd]
                            <a href="/Marque/[!M::Url!]">[!M::Nom!] ([!NbProd!])</a>
                        </li>
                        [/LIMIT]
                    </ul>
                </div>
                <div class="col-md-4">
                    <ul>
                        [LIMIT [!NbCol!]|[!NbCol!]]
                        <li>
                            [COUNT Boutique/Marque/[!M::Id!]/Produit/Actif=1|NbProd]
                            <a href="/Marque/[!M::Url!]">[!M::Nom!] ([!NbProd!])</a>
                        </li>
                        [/LIMIT]
                    </ul>
                </div>
                <div class="col-md-4">
                    <ul>
                        [LIMIT [!NbCol:*2!]|[!NbCol:+2!]]
                        <li>
                            [COUNT Boutique/Marque/[!M::Id!]/Produit/Actif=1|NbProd]
                            <a href="/Marque/[!M::Url!]">[!M::Nom!] ([!NbProd!])</a>
                        </li>
                        [/LIMIT]
                    </ul>
                </div>
            </div>
        </div>
        [/STORPROC]
    </div>


[ELSE]
	[MODULE Boutique/Categorie?Chemin=[!Query!]]
[/IF]