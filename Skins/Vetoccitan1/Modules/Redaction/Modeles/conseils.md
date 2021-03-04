<div class="Conseils">
    <div class="row filtres" style="margin-left:0px!important" >
        <div class="col-lg-10 col-md-10 col-sm-10">
            [MODULE Redaction/Filtres/Filtres]
        </div>
         <div class="col-lg-2 col-md-2 col-sm-2">
            // vide conforme à la maquette
         </div>
    </div>
    <div class="row" style="margin-left:0px!important">
       <div class="col-lg-10 col-md-10 col-sm-10 col-xs-12">
    <div class="encours" >
       [STORPROC 3] 
           <div class="row conseils borderTop ">
                  <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                      <img src="[!Domaine!]/Skins/Vetoccitan1/Images/imgnews[!Pos!].jpg" class="img-responsive " alt="photo-[!Pos!]" title="photo-[!Pos!]"  />
                  </div>
                  <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="titreImage">
            <p class="legende">Titre de l'image</p>
                </div>
                       <div class="titreArticle">
                            <h2>Titre de l'article</h2>
                       </div>
                       <div class="textArticle">
                            <p class="texte">
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce vitae tincidunt ipsum. 
                            </p>
                            <a href="/[!Lien!]/Article" alt="" title="" >Voir plus...</a>
                      </div>
                  </div>
                  <div class="col-lg-2 col-md-2 col-sm-2 hidden-xs">
                         // vide conforme à la maquette
                  </div>
           </div>
       [/STORPROC]
     </div>  
     <div class="encours2" style="display:none;">
          [STORPROC 3] 
              [!col:=[!Pos!]!]
              [!col=+1!]
              [IF [!col!]=4][col:=1!][/IF]
              <div class="row conseils borderTop">
                     <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
                         <img src="[!Domaine!]/Skins/Vetoccitan1/Images/imgnews[!Pos!].jpg" class="img-responsive " alt="photo-[!col!]" title="photo-[!col!]"  />
                     </div>
                     <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                      <div class="titreImage">
            <p class="legende">Titre de l'image</p>
                </div>
                          <div class="titreArticle">
                               <h2>Titre de l'article</h2>
                          </div>
                          <div class="textArticle">
                               <p class="texte">
                                   Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce vitae tincidunt ipsum. 
                               </p>
                               <a href="/[!Lien!]/Article" alt="" title="" >Voir plus...</a>
                         </div>
                     </div>
                     <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs">
                            // vide conforme à la maquette
                     </div>
              </div>
          [/STORPROC]
     </div>  
   </div>
       <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs">
            // vide conforme à la maquette
       </div>
 </div>
</div>
