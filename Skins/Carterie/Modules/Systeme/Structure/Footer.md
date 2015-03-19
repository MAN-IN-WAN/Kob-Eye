[STORPROC Redaction/Categorie/_Pied-de-page/Article|Art|0|1|Ordre|ASC][/STORPROC]
<div class="colonne1">
	<div class="MoyensPaiements">[!Art::Titre!]</div>
	<div class="MoyensPaiementsL2">[!Art::Contenu!]</div>
	<div class="MoyensPaiementsLogo"> </div>
</div>

[STORPROC Redaction/Categorie/_Pied-de-page/Article|Art|1|1|Ordre|ASC][/STORPROC]
<div class="colonne2">
	<div class="Livraison">[!Art::Titre!]</div>
	<div class="LivraisonTexte">[!Art::Contenu!]</div>
</div>
[STORPROC Redaction/Categorie/_Pied-de-page/Article|Art|2|1|Ordre|ASC][/STORPROC]
<div class="colonne3">
	<div class="Satifsfait">[!Art::Titre!]</div>
	<div class="SatifsfaitTexte">[!Art::Contenu!]</div>
</div>
[STORPROC Redaction/Categorie/_Pied-de-page/Article|Art|3|1|Ordre|ASC][/STORPROC]
<div class="colonne4">
	<div class="InscriptionNewsletter">[!Art::Titre!]</div>
	<div class="InscriptionNewsletterTexte">[!Art::Contenu!]</div>
	<form action="/" method="post" enctype="application/x-www-form-urlencoded">
		<div class="input-append">
			<input  class="input-small" id="EmailNewsletter" name="EmailNewsletter" type="text" placeholder="Saisissez votre adresse mail" value="[!EmailNewsletter!]">
			<button class="btn"  name="Abo" >Ok</button>
			<input type="hidden" name="Popup" value="InscNewsletter">
		</div>
	</form>
</div>
[STORPROC Redaction/Categorie/_Pied-de-page/Article|Art|4|1|Ordre|ASC][/STORPROC]
<div class="colonne5">
	<div class="AvotreService">[!Art::Titre!]</div>
	<div class="AvotreServiceTexte">[!Art::Contenu!]</div>
	[STORPROC Redaction/Article/[!Art::Id!]/Lien|Lie|0|1]
		//<a href="/[!Lie::Url!]" alt="Contactez-nous" class="AvotreService"><i class="icon-envelope icon-white"></i>[!Lie::Titre!]</a>
		<button class="btn btn-gris"  name="AvotreService"  ><i class="icon-envelope icon-white"></i><a href="/[!Lie::Url!]" class="AvotreService">[!Lie::Titre!]</a></button>
	[/STORPROC]

</div>


