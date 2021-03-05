
<div class="row filtresarticle">
        <div class="col-lg-11 col-md-11 col-sm-11">
            [MODULE Redaction/Filtres/Filtres]
        </div>
         <div class="col-lg-1 col-md-1 col-sm-1">
            // vide conforme à la maquette
         </div>
</div>
<div class="row Articles ">
    <div class="col-lg-10 col-md-10 col-sm-10 borderTopContenu">
      <div class="boutonretour">
      [IF [!Lien!]=services/Article]
        <a href="/services">Retour</a>
      [ELSE]
      [IF [!Lien!]=news/Article]
        <a href="/news">Retour</a>
      [ELSE]
        <a href="/conseils">Retour</a>
      [/IF]    
      [/IF]
      </div>
        <div class="imgArticle">
           <img src="[!Domaine!]/Skins/Vetoccitan1/Images/ratioarticle.jpg" class="img-responsive" alt="exemple" title="exemple" />
          </div>
      <div class="titreImage">
           <p class="legende">Titre de l'image</p>
       </div>
       <div class="titreArticle">
           <h2>Titre de l'article</h2>
       </div>
       <div class="textArticle">
           <p class="texte">     Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce vitae tincidunt ipsum. Aenean nec malesuada ipsum. Nam maximus fringilla feugiat. Suspendisse potenti. Nulla pharetra lorem vitae mauris tempor sollicitudin. Duis eget lacinia tortor. Donec varius tempus ligula, ut interdum odio tincidunt id. Etiam vel nisl nec tortor semper auctor eget at massa.
            </p>
        </div>
    </div>
     <div class="col-lg-2 col-md-2 col-sm-2">
        // vide conforme à la maquette
     </div>
</div>
</div>