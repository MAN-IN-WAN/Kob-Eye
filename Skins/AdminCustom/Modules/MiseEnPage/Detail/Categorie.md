[INFO [!Query!]|I]

[IF [!I::LastDirect!]=[!I::Module!]]
        //On vient directement sur ce type d'objet
        [IF [!I::TypeSearch!]=Direct]
                //Cet objet en particulier
                [STORPROC MiseEnPage/[!I::LastDirectObjectClass!]/[!I::LastId!]|Cat|0|1]
                        [STORPROC MiseEnPage/[!I::LastDirectObjectClass!]/[!I::LastId!]/[!I::LastDirectObjectClass!]|subCat]
                        <div id="subItems" class="col-md-12">
                                [LIMIT 0|1000]
                                        <a href="/MiseEnPage/Categorie/[!subCat::Url!]" title="[!subCat::Nom!]" >[!subCat::Nom!]</a>
                                [/LIMIT]
                        </div>
                        [/STORPROC]

                        <div id="DetailDirect" class="col-md-12">
                                //[!DEBUG::Cat!]
                                <h2>Caractéristiques de la catégorie <span class="champval">[!Cat::Nom!]</span></h2>
                                <div id="InfoCat" class="infoObject">
                                        <p>Titre : <span class="champval">[IF [!Cat::Titre!]=]Non renseigné[ELSE][!Cat::Titre!][/IF]</span></p>
                                        <p>Description : <span class="champval">[IF [!Cat::Description!]=]Non renseigné[ELSE][!Cat::Description!][/IF]</span>
                                        <p>Publier : <span class="champval">[IF [!Cat::Publier!]=0]Non[ELSE]Oui[/IF]</span>
                                        <p>Ordre : <span class="champval">[IF [!Cat::Ordre!]=]Non renseigné[ELSE][!Cat::Ordre!][/IF]</span>
                                </div>
                                <a href="[!I::LastId!]/Supprimer" class="delButton disabled" disabled>Supprimer</a>
                                <a href="[!I::LastId!]/Modifier" class="modButton">Modifier</a>
                        </div>
                        
                       
                        [IF [!I::ObjectType!]=Categorie]
                        <div id="ChildrenDirect" class="col-md-12">
                                [MODULE MiseEnPage/Categorie/ChildCat]
                        </div>
                        [/IF]
                        [NORESULT]
                        [/NORESULT]
                [/STORPROC]
        [/IF]
        [IF [!I::TypeSearch!]=Child]
                //[!I::LastDirectObjectClass!]
                //La liste de ces objets
                <div id="ChildWrapper">
                        <h2 id="selectChild" class="col-md-12"><span class="glyphicon glyphicon-eye-open"> </span> Sélectionnez une catégorie</span></h2>
                [STORPROC MiseEnPage/[!I::LastDirectObjectClass!]|Child]
                        <div class="col-md-4 children">
                                <a href="/MiseEnPage/Categorie/[!Child::Id!]" class="childsGoTo">
                                        <p class="childName">[!Child::Nom!]</p>
                                        [IF [!Child::Description!]]<p class="childDesc">[!Child::Description!]</p>[/IF]
                                </a>
                        </div>        
                        [NORESULT]
                        [/NORESULT]
                [/STORPROC]
                </div>
        [/IF]
[ELSE]
        <p class="error">
                Vous ne devriez pas être ici !
                <a href="/">Ouste !</a>
        </p>
        ////On viens depuis un parent
        //[IF [!I::TypeSearch!]=Direct]
        //        //Cet objet en particulier
        //        [STORPROC MiseEnPage/[!I::LastDirectObjectClass!]/[!I::LastId!]|Direct]
        //                <a href="">[!Direct::Nom!]</a>
        //        [/STORPROC]
        //[/IF]
        //[IF [!I::TypeSearch!]=Child]
        //        //La liste de ces objets
        //        [STORPROC MiseEnPage/[!I::LastDirectObjectClass!]|Child]
        //                <a href="">[!Child::Nom!]</a>
        //        [/STORPROC]
        //[/IF]
[/IF]

