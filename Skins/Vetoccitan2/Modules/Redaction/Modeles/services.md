<div class="Services">
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
      <div class="row services">
          <div class="col-lg-7 col-md-7 col-sm-7 col-xs-12">
            <img src="[!Domaine!]/Skins/Vetoccitan1/Images/[!Image[!Pos!]!].jpg" class="img-responsive imgservices" alt="photo-[!Pos!]" title="photo-[!Pos!]" />
          </div>
          <div class="col-lg-5 col-md-5 col-sm-5 col-xs-12">
              <div class="titreArticle">
                <h1>[!Titre[!Pos!]!]</h1>
              </div>
              <div class="textArticle">
                <p class="texte"> [!Texte[!Pos!]!]</p>
                </br>
                <div class="spoiler" data-isOuvert="false">
                  <div class="boutonAfficher" onclick="this.parentNode.setAttribute('data-isOuvert', true);">Voir plus</div>
                  <div class="boutonMasquer" onclick="this.parentNode.setAttribute('data-isOuvert', false);">Voir moins</div>
                  <div class="contenuSpoiler">
                             Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce vitae tincidunt ipsum. Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
                  </div>
               </div>
            </div>
          </div>          
      </div>
   [/STORPROC]
 </div>
</div>
</div>


<script type="text/javascript">
function voirPlus(div) {
    var divContenu = div.nextSibling;
    if(divContenu.nodeType == 3) divContenu = divContenu.nextSibling;
    if(divContenu.style.display == 'block') {
        divContenu.style.display = 'none';
    } else {
        divContenu.style.display = 'block';
    }
}
</script>
