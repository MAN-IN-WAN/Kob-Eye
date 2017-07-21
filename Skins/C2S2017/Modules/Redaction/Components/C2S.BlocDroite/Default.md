<div id="telRight" class="row">
        <div class="col-md-12">[!Systeme::User::Tel!] <a href="tel:[!Systeme::User::Tel!]" id="icopPhoneMobile"></a></div>
</div>
<div id="txtRight" class="row">
        <a href="/Devis" class="textrightDevis"><div id="devisRight" class="col-md-12">Devis gratuit</div></a>
        <a href="/Contact" class="textrightDevis2"><div id="telRight2" class="col-md-12">Rappel</div></a>
        <a href="/Charte-Parrainage" class="textrightDevis3"><div id="parrainRight" class="col-md-12">Parrainez un proche</div></a>
</div>
[COMPONENT Redaction/ArticleModele/Default?CATEGORIE=20&NOMDIV=Focus&FORCEMODELE=MenuFOCUS]
// demande de retrait en mars2017
//[COMPONENT MiseEnAvant/Boostrap.AfficheMiseEnAvant/Default?CATEGORIE=3&NOMDIV=ZonesInterventions]

// fevr 2017 :  en attendant d'avoir prochain
// demande de retrait en mars2017
//[MODULE MiseEnAvant/Categorie/ListePartenaires]


//<div id="linksRight"class="row">
//        <a href="/Charte-Paiement"><img src="/Skins/C2S/Img/cesu_60.png" alt="CESU" title="CESU" ></a>
//       <a href="/PAJE"><img src="/Skins/C2S/Img/paje_60.png" alt="PAJE" title="PAJE" ></a>
//        <a href="/Teleassistance"><img src="/Skins/C2S/Img/LOGO_EUROP_ASSISTANCE.png" alt="Europ Assistance" title="Europ Assistance" ></a>
//        <a href="/Avantages-fiscaux"><img src="/Skins/C2S/Img/credit-impots_60.png" alt="Crédit d'impôts" title="Crédit d'impôts" ></a>
//</div>

[IF [!Systeme::CurrentMenu::Url!]~Parrainage]
    <div id="parrainageRight"class="row">
        <div class="col-md-12">
               <a href="/Demande-parrainage">Faire votre demande de parrainage</a> 
        </div>
    </div>
[/IF]


//<div id="txtRight" class="row">
//        <a href="/Devis" class="textrightDevis"><div id="devisRight" class="col-md-12">Devis gratuit</div></a>
//       	<div class="col-md-12">Rappel gratuit</div>
//       	<div class="col-md-3" id="textrightRappel">
//   	        <a id="appelHead" href="/Contact"><img src="/Skins/C2S/Img/C2S_Rappel.png" alt="Rappel" title="Rappel" /></a>
//       	</div>
//        <a href="/Charte-Parrainage"><div id="parrainRight" class="col-md-12">Parrainez un proche</div></a>
//</div>