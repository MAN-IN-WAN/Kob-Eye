[!Req:=[!Query!]:ResidenceList!]
[IF [!Type!]][ELSE][!Type:=[!Sys::CurrentMenu::Titre!]!][/IF]
[IF [!prixMin!]]
    [IF [!Premier!]][!Req+=&!][ELSE][!Req+=/!][/IF]
    [!Premier:=1!]
    [IF [!Type!]=Vente]
        [!Req+=Prix>=[!prixMin!]&Prix<=[!prixMax!]!]
    [ELSE]
        [!Req+=Loyer>=[!prixMin!]&Loyer<=[!prixMax!]!]
    [/IF]
[/IF]

[IF [!Ville!]]
    [IF [!Premier!]][!Req+=&!][ELSE][!Req+=/!][/IF]
    [!Premier:=1!]
    [!Req+=Ville.VilleId([!Ville!])!]
[/IF]
[IF [!TypeBien!]]
    [IF [!Premier!]][!Req+=&!][ELSE][!Req+=/!][/IF]
    [!Premier:=1!]
    [!Req+=TypeBien=[!TypeBien!]!]
    [IF [!TypeBien!]=Appartement]
        [!TitrePage+= d'appartement!]
    [/IF]
    [IF [!TypeBien!]=Maison]
        [!TitrePage+= de maison!]
    [/IF]
    [IF [!TypeBien!]=Terrain]
        [!TitrePage+= de terrain!]
    [/IF]
    [IF [!TypeBien!]=Immeuble]
        [!TitrePage+= d'immeuble!]
    [/IF]
    [IF [!TypeBien!]=Garage]
        [!TitrePage+= de garage!]
    [/IF]
    [IF [!TypeBien!]=Bureau]
        [!TitrePage+= de bureau!]
    [/IF]
    [IF [!TypeBien!]=Commerce]
        [!TitrePage+= de commerce!]
    [/IF]
[/IF]
[IF [!Chambres!]]
    [IF [!Premier!]][!Req+=&!][ELSE][!Req+=/!][/IF]
    [!Premier:=1!]
    [!Req+=Chambres=[!Chambres!]!]
[/IF]


[IF [!SuperficieCarrez!]]
    [IF [!Premier!]][!Req+=&!][ELSE][!Req+=/!][/IF]
    [!Premier:=1!]
    [IF [!SuperficieCarrez!]=30][!Req+=SuperficieCarrez<=30!][!SuperficieSeo:= d'une supercifie inférieure à 30m2!][/IF]
    [IF [!SuperficieCarrez!]=50][!Req+=SuperficieCarrez>=30&SuperficieCarrez<=50!][!SuperficieSeo:= de 30m2 à 50m2 de superficie!][/IF]
    [IF [!SuperficieCarrez!]=75][!Req+=SuperficieCarrez>=50&SuperficieCarrez<=75!][!SuperficieSeo:= de 50m2 à 75m2 de superficie!][/IF]
    [IF [!SuperficieCarrez!]=100][!Req+=SuperficieCarrez>=75&SuperficieCarrez<=100!][!SuperficieSeo:= de 75m2 à 100m2 de superficie!][/IF]
    [IF [!SuperficieCarrez!]=150][!Req+=SuperficieCarrez>=100&SuperficieCarrez<=150!][!SuperficieSeo:= de 100m2 à 150m2 de superficie!][/IF]
    [IF [!SuperficieCarrez!]=200][!Req+=SuperficieCarrez>=150&SuperficieCarrez<=200!][!SuperficieSeo:= de 150m2 à 200m2 de superficie!][/IF]
    [IF [!SuperficieCarrez!]=300][!Req+=SuperficieCarrez>=200!][!SuperficieSeo:= d'une supercifie supérieure à 200m2!][/IF]
[ELSE]
    [!SuperficieSeo:= !]
[/IF]
[IF [!startAt!]&&[!endAt!]]
    [!deb:=[!Utils::getTms([!startAt!])!]!]
    [!fin:=[!Utils::getTms([!endAt!])!]!]
    [OBJ ParcImmobilier|Residence|R]
    [!R::editView([!deb!],[!fin!])!]
    [IF [!Premier!]][!Req+=&!][ELSE][!Req+=/!][/IF]
    [!Premier:=1!]
    [!Req+=PayeLe=&PaiementAgence=!]
[/IF]
[IF [!Type!]]
    [IF [!Premier!]][!Req+=&!][ELSE][!Req+=/!][/IF]
    [!Premier:=1!]
    [!Req+=Type=[!Type!]!]
    [!TitrePage:=[!Type!]!]
[/IF]

[IF [!Sys::CurrentMenu::Filters!]]
    [IF [!Premier!]][!Req+=&!][ELSE][!Req+=/!][/IF]
    [!Premier:=1!]
    [!Req+=[!Sys::CurrentMenu::Filters!]!]
    [!TitrePage:=[!Sys::CurrentMenu::Titre!]!]
[/IF]

[IF [!order!]]
    [IF [!order!]=pluscher]
        [IF [!Type!]=Vente]
             [!Ordre:=Prix!][!OrdreSens:=DESC!]
        [ELSE]
             [!Ordre:=Loyer!][!OrdreSens:=DESC!]
        [/IF]
    [/IF]
    [IF [!order!]=moinscher]
        [IF [!Type!]=Vente]
             [!Ordre:=Prix!][!OrdreSens:=ASC!]
        [ELSE]
             [!Ordre:=Loyer!][!OrdreSens:=ASC!]
        [/IF]
    [/IF]
    [IF [!order!]=plusrecent][!Ordre:=Date!][!OrdreSens:=DESC!][/IF]
    [IF [!order!]=plusgrand][!Ordre:=SuperficieCarrez!][!OrdreSens:=DESC!][/IF]
    [IF [!order!]=pluspetit][!Ordre:=SuperficieCarrez!][!OrdreSens:=ASC!][/IF]
[ELSE]
    [!Ordre:=Id!]
    [!OrdreSens:=DESC!]
[/IF]

[IF [!TypeBien!]]
    [!TypeSeo:= [!TypeBien!]!]
[ELSE]
    [!TypeSeo:= de bien immobilier!]
[/IF]

[!DescriptionSeo:= Liste [!TypeSeo!]!]

[IF [!Type!]==Location saisonnière d'appartement][!TitreSeo:=Location de vacance!][!DescriptionSeo.= en location de vacances!][/IF]
[IF [!Type!]==Location][!TitreSeo:=Location!][!DescriptionSeo.= en location!][/IF]
[IF [!Type!]==Vente][!TitreSeo:=Vente!][!DescriptionSeo.= en vente!][/IF]

[!TitreSeo.= [!TypeSeo!]!]

[IF [!Chambres!]]
    [!TitreSeo.= [!Chambres!] chambre(s) !]
    [!DescriptionSeo.= avec [!Chambres!] chambre(s)!]
[/IF]

[STORPROC ParcImmobilier/Ville/[!Ville!]|ObjVille][/STORPROC]
[IF [!Ville!]]
    [!TitreSeo.= à [!ObjVille::Nom!]!]
    [!DescriptionSeo.= à [!ObjVille::Nom!] dans l'Hérault!]
[ELSE]
    [!TitreSeo.= dans l'Hérault!]
    [!DescriptionSeo.= dans l'Hérault!]
[/IF]

[!DescriptionSeo.= [!SuperficieSeo!] - Bertrand Immobilier à Palavas 04.67.50.79.12!]

// CAS SPECIAL DE RECHERCHE PAR REFERENCE
[IF [!Reference!]]
        [!TitreSeo:=Bertrand Immobilier, recherche annonce immoblière n°[!Reference!]!]
        [!TitrePage:=Recherche annonce immoblière n°[!Reference!]!]
        [!DescriptionSeo:=Recherche de l'annonce immobilière n°[!Reference!]. Agence immobilière à Palavas-Les-Flots (34 - Hérault) location, vente, locations saisonnières et gestion de syndic.!]
        [!Req:=[!Query!]!]
        [!Req+=/Reference=[!Reference!]!]
[/IF]
// FIN REFERENCE

[TITLE][!TitreSeo!][/TITLE]
[DESCRIPTION][!DescriptionSeo!][/DESCRIPTION]

[IF [!Page!]=][!Page:=1!][/IF]
[COUNT [!Req!]|Nb]
[!NbParPage:=10!]
[!NbNumParPage:=10!]
[!NbPage:=[!Math::Floor([!Nb:/[!NbParPage!]!])!]!]
[IF [!NbPage!]!=[!Nb:/[!NbParPage!]!]][!NbPage+=1!][/IF]


<h1 class="page-header">[!TitrePage!]</h1>
<div class="properties-rows">
    <div class="filter">
        <form action="" method="get" class="form-horizontal" id="formOrder">
            [IF [!Type!]]<input type="hidden" name="Type" value="[!Type!]">[/IF]
            [IF [!TypeBien!]]<input type="hidden" name="TypeBien" value="[!TypeBien!]">[/IF]
            [IF [!prixMin!]]<input type="hidden" name="prixMin" value="[!prixMin!]"><input type="hidden" name="prixMax" value="[!prixMax!]">[/IF]
            [IF [!Ville!]]<input type="hidden" name="Ville" value="[!Ville!]">[/IF]
            [IF [!Chambres!]]<input type="hidden" name="Chambres" value="[!Chambres!]">[/IF]
            [IF [!SuperficieCarrez!]]<input type="hidden" name="SuperficieCarrez" value="[!SuperficieCarrez!]">[/IF]
            <div class="control-group">
                <label class="control-label" for="order">
                    Classer :
                </label>
                <div class="controls">
                    <select name="order" id="order" style="width:200px" onchange="this.form.submit()">
                        <option value=""> - </option>
                        <option value="moinscher" [IF [!order!]==moinscher]selected[/IF]>Du - cher au + cher</option>
                        <option value="pluscher" [IF [!order!]==pluscher]selected[/IF]>Du + cher au - cher</option>
                        <option value="plusrecent" [IF [!order!]==plusrecent]selected[/IF]>Du + récent au + vieux</option>
                        <option value="plusgrand" [IF [!order!]==plusgrand]selected[/IF]>Du + grand au + petit</option>
                        <option value="pluspetit" [IF [!order!]==pluspetit]selected[/IF]>Du + petit au + grand</option>
                    </select>
                </div><!-- /.controls -->
            </div><!-- /.control-group -->
        </form>
    </div><!-- /.filter -->
</div>
[IF [!Nb!]=0]
    <p>Aucune annonce ne correspond actuellement à votre recherche.</p>
[ELSE]
    <div class="properties-rows">
        <div class="row">
            [STORPROC [!Req!]|residence|0|1000|[!Ordre!]|[!OrdreSens!]||]

            <div class="property span9">
                <div class="row">
                    <div class="image span3">
                        <div class="content">
                            <a href="fiche-residence/[!residence::Url!]"></a>
                            <img src="[!residence::Photo1!].mini.270x200.jpg" alt="">
                        </div><!-- /.content -->
                    </div><!-- /.image -->

                    <div class="body span6">
                        <div class="title-price row">
                            <div class="title span4">
                                <h2><a href="/fiche-residence/[!residence::Url!]">[!residence::Titre!]</a></h2>
                            </div><!-- /.title -->

                            <div class="price">
                                [IF[!residence::Prix!]!=0][!Utils::getFormatedPrice([!residence::Prix!])!][/IF]
                                [IF[!residence::Loyer!]!=0][!residence::Loyer!][/IF] €
                            </div><!-- /.price -->
                        </div><!-- /.title -->

                        <div class="location">[!residence::SousTitre!]</div><!-- /.location -->
                        <div style="margin-bottom: 7px">[SUBSTR 300][!residence::Descriptif!][/SUBSTR]</div>
                        [IF [!residence::SuperficieCarrez!]]
                        <div class="area">
                            <span class="key">Surface loi Carrez :</span><!-- /.key -->
                            <span class="value">[!residence::SuperficieCarrez!] m²</span><!-- /.value -->
                        </div><!-- /.area -->
                        [/IF]
                        [IF [!residence::Chambres!]]<div class="bedrooms"><div class="content">[!residence::Chambres!]</div></div>[/IF]<!-- /.bedrooms -->
                        [IF [!residence::SallesDeBains!]]<div class="bathrooms"><div class="content">[!residence::SallesDeBains!]</div></div>[/IF]<!-- /.bathrooms -->
                        <div class="list-city"><div class="content">[STORPROC [!residence::getParents(Ville)!]|Ville][!Ville::Nom!][/STORPROC]</div></div>
                    </div><!-- /.body -->
                </div><!-- /.property -->
            </div><!-- /.row -->
            [/STORPROC]
        </div>
    </div>

    <div id="pagination" class="pagination"> <!-- Start Paging -->
        <ul>
                //<li><button class="active">Page 1 sur [!NbPage!] </button></li>
                [IF [!Page!]>1]
                        <li><a href="/[!Lien!]" class=""><span>&laquo;</span></a></li>
                        <li><a href="[IF [!Page!]=2]/[!Lien!][ELSE]?Page=[!Page:-1!][/IF]" class="">&lsaquo;</a>
                        [IF [!Page!]>[!Math::Round([!NbNumParPage:/2!])!]]
                                <li><a href="/[!Lien!]" class=""><span>1</span></a></li>
                                <li><a href="#" class=""><span>...</span></a></li>
                        [/IF]
                [/IF]
                [!start:=1!]
                [IF [!Page!]>[!start:+[!NbNumParPage:/[!NbParPage!]!]!]][!start:=[!Math::Round([!Page:-[!NbNumParPage:/2!]!])!]!][/IF]
                [STORPROC [!NbPage:+1!]|P|[!start!]|[!NbNumParPage!]]
                <li class=" [IF [!P!]=[!Page!]]active[/IF]"><a href="[IF [!P!]!=1]?Page=[!P!][ELSE]/[!Lien!][/IF]" class="">[!P!]</a></li>
                [/STORPROC]
                [IF [!Page!]<[!NbPage!]]
                        [IF [!Page:+[!NbNumParPage:/2!]!]<[!NbPage!]]
                                <li><a href="#" class=""><span>...</span></a></li>
                                <li><a href="?Page=[!NbPage!]" class="">[!NbPage!]</a></li>
                        [/IF]
                        <li><a href="?Page=[!Page:+1!]" class=""><span>&rsaquo;</span></a></li>
                        <li><a href="?Page=[!NbPage!]" class="">&raquo;</a></li>
                [/IF]
        </ul>
    </div>	<!-- End Paging -->
[/IF]

