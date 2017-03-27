[INFO [!Query!]|I]

[IF [!I::LastDirect!]=[!I::Module!]]
        //On vient directement sur ce type d'objet
        [IF [!I::TypeSearch!]=Direct]
                //Cet objet en particulier
                [STORPROC MiseEnPage/[!I::LastDirectObjectClass!]/[!I::LastId!]|Art|0|1]
                        <div id="DetailDirect" class="col-md-12">
                                //[!DEBUG::Direct!]
                                <h2>[!Art::Titre!]</h2>
                                <div id="InfoCat" class="infoObject">
                                        //[!DEBUG::Art!]
                                        <p>Chapo : [IF [!Art::Chapo!]!=]<span class="champval">[!Art::Chapo!] [ELSE]<span class="champvide">Non renseigné[/IF]</span></p>
                                        <p>Date de rédaction: <span class="champval">[!Utils::getDate(d/m/Y H:i:s,[!Art::Date!])!]</span></p>
                                        <p>Auteur : <span class="champval">[!Art::Auteur!]</span></p>
                                        <p>Afficher le titre : [IF [!Art::AfficheTitre!]=0]<span class="champval">Non[ELSE]<span class="champval">Oui[/IF]</span></p>
                                        <p>Contenu simple: <span class="champval">[!Art::Contenu!]</span></p>
                                        <p>A la une : <span class="champval"> [IF [!Art::ALaUne!]=0]<span class="champval">Non[ELSE]<span class="champval">Oui[/IF]</span></p>
                                        <p>Publier : <span class="champval"> [IF [!Art::Publier!]=0]<span class="champval">Non[ELSE]<span class="champval">Oui[/IF]</span></p>
                                        <p>Ordre : <span class="champval">[IF [!Art::Ordre!]!=]<span class="champval">[!Art::Ordre!][ELSE]<span class="champvide">Non renseigné[/IF]</span></p>
                                </div>
                                <a href="[!I::LastId!]/Supprimer" class="delButton">Supprimer</a>
                                <a href="[!I::LastId!]/Modifier" class="modButton">Modifier</a>
                        </div>
                        
                       
                        [IF [!I::ObjectType!]=Article]
                        <div id="ChildrenDirect"  class="col-md-12">
                                [MODULE MiseEnPage/Categorie/ChildArt]
            	        </div>
                        [/IF]
                        [NORESULT]
                        [/NORESULT]
                [/STORPROC]
                
                [!Cat:=[!Art::getOneParent(Categorie)!]!]
                [IF [!Cat!]]
                [ELSE]
                        [!Cat:=[!Child::getOneParent(Categorie)!]!]
                [/IF]
                <a href="/MiseEnPage/Categorie/[!Cat::Id!]" id="backToCat" class="genButton" title="Voir la catégorie parente">Voir la catégorie</a> 
                
        [/IF]
        [IF [!I::TypeSearch!]=Child]
                //[!I::LastDirectObjectClass!]
                [!Cats:=[!Array::newArray()!]!]
                //La liste de ces objets
                <div id="ChildWrapper">
                        <h2 id="selectChild" class="col-md-12"><span class="glyphicon glyphicon-eye-open"> </span> Sélectionnez un article</span></h2>
                        [STORPROC MiseEnPage/[!I::LastDirectObjectClass!]|Child]
                                [!pCat:=[!Child::getOneParent(Categorie)!]!]
                                [!Cats:=[!Array::push([!Cats!],[!pCat!],[!pCat::Id!])!]!]
                                <div class="col-md-6 children f_[!pCat::Id!]">
                                        <a href="/MiseEnPage/Article/[!Child::Id!]" class="childsGoTo">
                                                <h3 class="childName">[!Child::Titre!]</h3>
                                                [IF [!Child::Chapo!]!=]<div class="childDesc">[!Child::Chapo!]</div>[/IF]
                                        </a>
                                </div>
                                [NORESULT]
                                [/NORESULT]
                        [/STORPROC]
                </div>
                <div id="sortChild" class="col-md-12">
                [STORPROC [!Cats!]|Cat]
                        <a href="/[!Cat::getUrl()!]" data-filter=".f_[!Cat::Id!]">
                                [IF [!Cat::Icone!]!=]
                                        <img src="[!Domaine!]/[!Cat::Icone!]" alt="[!Cat::Nom!]" title="[!Cat::Nom!]" class="icoFiltre">
                                [ELSE]
                                        [!Cat::Nom!]

                                [/IF]
                        </a>
                [/STORPROC]
                </div>
                <script type="text/javascript">
                        $('#sortChild').insertBefore('#ChildWrapper h2');
                        setTimeout(function(){
                                var iso =$('#ChildWrapper').isotope({
                                        // options
                                        itemSelector: '.children',
                                        layoutMode: 'masonry',
                                        stamp: $('#ChildWrapper h2, #sortChild')
                                });
                                $('#sortChild').on('click','a',function(e){
                                        e.preventDefault();
                                        e.stopPropagation();
                                        var filterValue = $(this).attr('data-filter');
                                        iso.isotope({ filter: filterValue });
                                });
                                var showall ='<a href="#" data-filter="*"> \
                                                                <img src="[!Domaine!]/Skins/[!Sys::Skin!]/Images/tout.png" alt="Tout voir" title="Tout voir" class="icoFiltre"> \
                                                        </a>';
                                $('#sortChild').append(showall);
                        },500);
                </script>
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

