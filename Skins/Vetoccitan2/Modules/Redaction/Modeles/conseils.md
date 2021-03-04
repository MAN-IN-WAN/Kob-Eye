<div class="Conseils">
  <div class="row Filtres" style="margin-left:0px!important">
    <div class="col-lg-10 col-md-10 col-sm-10">
            [MODULE Redaction/Filtres/Filtres]
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1">
            // vide conforme à la maquette
    </div>
   </div>
    [!Titre1:=titre1!]
    [!Titre2:=titre2!]
    [!Titre3:=titre3!]
    [!Image1:=imgnews1!]
    [!Image2:=imgnews2!]
    [!Image3:=imgnews3!]
    [!Texte1:=Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce vitae tincidunt ipsum.!]
    [!Texte2:=2Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce vitae tincidunt ipsum.!]
    [!Texte3:=3Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce vitae tincidunt ipsum.!]
  <div class="row" style="margin-left:0px!important">
    <div class="col-lg-12 col-md-12 col-sm-12">
        [STORPROC 3] 
      <div class="row conseils">
        <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
          <img src="[!Domaine!]/Skins/Vetoccitan1/Images/[!Image[!Pos!]!].jpg" class="img-responsive imgconseils" alt="photo-[!Pos!]" title="photo-[!Pos!]" />
        </div>
        <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
          <div class="titreArticle">
            <h1>[!Titre[!Pos!]!]</h1>
          </div>
          <div class="textArticle">
            <p class="texte"> [!Texte[!Pos!]!]</p>
            <a href="/[!Lien!]/Article" alt="" title="" >Voir plus...</a>
          </div>
        </div>         
      </div>
        [/STORPROC]
    </div>
  </div>
</div>
