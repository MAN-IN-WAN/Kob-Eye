
<div id="newsGauche"  class="scrollbar-transparent [IF [!Lien!]!=Accueil][IF [!Lien!]!=contact]hidden-xs hidden-sm[/IF][/IF]" style="overflow: hidden;position:relative">
<div style="height: 100%;width: 100%;right: -17px;overflow: auto;padding-right: 15px;position:absolute">
     <div class="responsiveaccueil " ><div class="bloc-infos ">
         <h2 class="nomClinique [IF [!Lien!]=Accueil]borderTop[/IF]">Clinique Vétérinaire XXX</h2>
         <p>Adresse</p>
         <p>CP Ville</p>
      </div>
     <div class="bloc-infos">
         <h2>Lundi-Samedi</h2>
         <p>hh- hh</p>
     </div>
     <div class="bloc-infos">
         <p>Tél : +33 xx xx xx xx xx</p>
         <p>courriel:xx@xx.xx</p>
     </div>
 <div class="bloc-Horaires">
     <div class="titreCentre"><p>Horaires</p></div>
     [STORPROC 6]
         <div class="row bloc-infos">
             <div class="col-lg-3 col-md-3 col-sm-3 col-xs-3">
                 [IF [!Pos!]=1]Lundi :[/IF]
                 [IF [!Pos!]=2]Mardi :[/IF]
                 [IF [!Pos!]=3]Mercredi :[/IF]
                 [IF [!Pos!]=4]Jeudi :[/IF]
                 [IF [!Pos!]=5]Vendredi :[/IF]
                 [IF [!Pos!]=6]Samedi :[/IF]
             </div>
             <div class="col-lg-9 col-md-9 col-sm-9 col-xs-9">
                 08h00 - 12h00 / 14h00 -19h00
             </div>
         </div>
     [/STORPROC]
     <div class="boutonCentre"><a href="/contact"  >Prendre rendez-vous</a></div>
 </div>
</div>
    <div class="bloc-infos news hidden-xs hidden-sm">
        <h2 class="news">News</h2>
        [STORPROC 5]
            <div class="bloc-infos">
                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce vitae tincidunt ipsum. Aenean nec malesuada ipsum. </p>
            </div>
        [/STORPROC]
        <div class="boutonCentre"><a href="/news"  >Voir toutes les news</a></div>
    </div>
    </div>
</div>
