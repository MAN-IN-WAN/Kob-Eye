[IF [!REQUETE!]=]
	[!REQUETE:=Catalogue/Categorie/AfficheCarousel=1!]
[/IF]
<div class="[!NOMDIV!] CarouselTotal">
    <div class="[!CSSFOND!] CarouselFond" >
        <div class="titre">[!TITREBLOC!]</div>
        <div id="myCarousel" class="carousel slide ">
            <!-- Indicators -->
            [IF [!INDICATORS!]=1]
                <ol class="carousel-indicators">
                    [STORPROC [!Cpt!]|C]
                        <li data-target="#myCarousel" data-slide-to="[!Pos!]" class="active"></li>
                    [/STORPROC]
                </ol>
            [/IF]
            <!-- Wrapper for slides -->
            <div class="carousel-inner" [IF [!IMAGEFND!]!=]style="background-image:url([!IMAGEFND!]);background-repeat:no-repeat; background-position:right bottom;"[/IF]>
                [!Cpt:=0!][!CptRefCol:=4!][!FontCol:=1em!]
                [IF [!NBCATPARLIGNE!]=1][!CptRefCol:=12!][!FontCol:=1.5em!][/IF]
                [IF [!NBCATPARLIGNE!]=2][!CptRefCol:=6!][!FontCol:=1.4em!][/IF]
                [IF [!NBCATPARLIGNE!]=3][!CptRefCol:=4!][!FontCol:=1.3em!][/IF]
                 <div class="row Categories">
                    [STORPROC [!REQUETE!]/Publier=1&&Carousel=1|R|||Ordre|ASC]
                        [IF [!CHOIXOBJET!]]
                            // On affiche les produits sélectionnés donc on vérifie que dans la catégorie j'ai des produits sélectionnés
                            [COUNT Catalogue/Categorie/[!R::Id!]/Produit/Carousel=1|NbProd]
                        [ELSE]
                            [!NbProd:=1!]
                        [/IF]
                        [IF [!NbProd!]>0]
                            [IF [!Cpt!]=[!NBCATPARLIGNE!]]</div> <div class="row Categories">[!Cpt:=0!][/IF]
                            [!Cpt+=1!]
                            <div class="col-md-[!CptRefCol!]"><div class="Categorie [IF [!Pos!]=1]active[/IF]" id="cat-[!R::Id!]"> <a href="[!R::getUrl()!]" alt="[!R::Nom!]" style="font-size:[!FontCol!]">[!R::Nom!]</a></div></div>
                         [/IF]
                    [/STORPROC]
                </div>  // fin row
                <div class="Produits" >
                    [!Active:=1!][!Filtre:=!]
                    [!Card1:=!][!Card2:=!]
                   // [IF [!SERVER::REMOTE_ADDR!]=185.87.66.101][!Card1:=0!][!Card2:=1!][/IF]
                    [STORPROC [!REQUETE!]/Publier=1&&Carousel=1|R|[!Card1!]|[!Card2!]|Ordre|ASC]
                        [IF [!CHOIXOBJET!]][!Filtre:=&&Carousel=1!][/IF]
                        [COUNT [!REQUETE!]/[!R::Id!]/[!OBJET!]/Publier=1[!Filtre!]|CptZ]
                        [!First:=[!Utils::Random([!CptZ:-1!])!]!]
                        [STORPROC [!REQUETE!]/[!R::Id!]/[!OBJET!]/Publier=1&&Carousel=1|Obj|[!First!]|1|tmsEdit|DESC]
                            <div class="item [IF [!Active!]=1]active[!Active:=0!][/IF] itemObj" optcat="cat-[!R::Id!]">
                                <div class="row">
                                    <div class="col-md-5 col-xs-5 ">
                                        <div class="imggauche">
                                            // IMAGE PRINCIPALE DU PRODUIT ISSU DE LA FICHE PRODUIT
                                            <figure class="gdeimg"><img src="/[!Obj::Image!]" class="img-responsive"  alt="[!Obj::Titre!]" title="[!Obj::Titre!]"/></figure>
                                        </div>
                                         [IF [!Obj::Fabricant!]!=]
                                            [STORPROC Catalogue/Fabricant/[!Obj::Fabricant!]|Fab|0|1]
                                                <figure class="marque"><img src="/[!Fab::Logo!]" class="img-responsive"  alt="[!Fab::Nom!]" title="[!Fab::Nom!]"/></figure>
                                            [/STORPROC]
                                        [/IF]
                                    </div>
                                    <div class="col-lg-7 col-xs-7">
                                        <div class="InfosProduits">
                                            <h2 class="nomproduit">[!Obj::Titre!]</h2>
                                            [STORPROC Catalogue/PictoProduit/Position=Tarif&Produit.ProduitId([!Obj::Id!])|Pi|0|1]
                                                <figure class="img1"><img src="/[!Pi::Picto!]" alt="[!Obj::Titre!]" title="[!Obj::Titre!]" class="img-responsive" /></figure>
                                            [/STORPROC]
                                            [STORPROC Catalogue/PictoProduit/Position=Propriétés&Produit.ProduitId([!Obj::Id!])|Pi|0|1]
                                                <figure class="imgProprietes"><img src="/[!Pi::Picto!]" alt="[!Obj::Titre!]" title="[!Obj::Titre!]" class="img-responsive" /></figure>
                                            [/STORPROC]
                                         </div> 
                                    </div>
                                </div>
                                <a href="[!R::getUrl()!]/Produit/[!Obj::Url!]" class="lienrouge" alt="[!Obj::Nom!]">Voir le produit</a>
                            </div>
                        [/STORPROC]
                        [NORESULT]<h3>Très bientôt</h3>[/NORESULT]
                    [/STORPROC]
                </div>
            </div>
            [IF [!NAVIGATION!]=1]
                <!-- Controls -->
                [IF [!PRECEDENT!]=1]
                    <a class="left carousel-control" href="#myCarousel" data-slide="prev"  ><span class="icon-prev"></span></a>
                [/IF]
                [IF [!SUIVANT!]=1]
                    <a class="right carousel-control" href="#myCarousel" data-slide="next" ></a>
                [/IF]
            [/IF]
        </div>
    </div>
</div>


<script type="text/javascript">

	$(document).ready( function(){
        var currentitem = 0;
        var itemlist = $('.itemObj');
    
        function next(){
            //désativer le précédent

            try{
                $(itemlist[currentitem]).removeClass('active');
                $(itemlist[currentitem]).addClass('hiding');
                console.log('item',itemlist[currentitem]);
                //on désative la catéogrie en cours
                $('#'+$(itemlist[currentitem]).attr('optcat')).removeClass('active');
            } catch (err){
                console.error('pas de classe active pour ',itemlist[currentitem]);
            } 
            if (currentitem==itemlist.length-1)currentitem = 0;
            else currentitem++;
            //active le suivant
            $(itemlist[currentitem]).addClass('active');
            $(itemlist[currentitem]).removeClass('hiding');
            $('#'+$(itemlist[currentitem]).attr('optcat')).addClass('active');
            //console.log('item -> '+currentitem);

        }

	    setInterval(next,5000);
	});
</script>