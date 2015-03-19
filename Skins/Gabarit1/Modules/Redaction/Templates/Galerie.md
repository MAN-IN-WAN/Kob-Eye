[STORPROC [!Query!]|Cat]
	<h1>[!Cat::Nom!]</h1>
	//Nombre d'images par ligne
	[!ImgParLigne:=6!]
	//Calcul du nombre total d'images
	[COUNT Redaction/Categorie/[!Cat::Id!]/Image|C]
	//On divise par le nombre de produit par ligne pour avoir le nombre total de ligne
	[!NbLigne:=[!C:/[!ImgParLigne!]!]!]
	//On supprime les decimales 
	[!NbLigneTemp:=[!Math::Floor([!NbLigne!])!]!]
	//On verifie si les decimales justifient d ajouter une ligne 
	[IF [!NbLigne!]>[!NbLigneTemp!]]
		[!NbLigne:=[!NbLigneTemp:+1!]!]
	[/IF]
	[STORPROC [!NbLigne!]|L]
		<div id="Milieu">
		[STORPROC Redaction/Categorie/[!Cat::Id!]/Image|Img|[!L:*[!ImgParLigne!]!]|6|TmsEdit|DESC]
			<div class="ImgGalerie">
				<a href="/[!Img::URL!]" rel="lightbox[acc]" title="[!Cat::Nom!] : [!Img::Titre!]"><img src="/[!Img::URL!]" alt="[!Cat::Nom!] [!Img::Titre!]" title="[!Img::Titre!]" style="width:110px;height:73px;"/></a>
			</div>
		[/STORPROC]
			<div class="Clear"></div>
		</div>
	[/STORPROC]
	<h2>Acc&eacute;s sous rubriques : [!Cat::Nom!]</h2>
	<div id="SousCat">
		<ul>
			[LIMIT 0|20]
				<li>
					<a href="/[!Lien!]/[!Cato::Link!]" title="[!Cato::Nom!]">[!Cato::Nom!]</a>
				</li>
			[/LIMIT]
		</ul>
	</div>
	[STORPROC Redaction/Categorie/[!Cat::Id!]/Article|Art|0|10|Id|ASC]
			<div>[!Art::Contenu!]</div>
		[/STORPROC]
[/STORPROC]
<a href="[!SERVER::HTTP_REFERER!]" title="Retour &agrave; la page pr&eacute;c&eacute;dente">Retour &agrave; la page pr&eacute;c&eacute;dente</a>