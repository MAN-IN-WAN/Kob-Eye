// IL FAUDRA FAIRE UNE VERSION BOOTSTRAP AVEC UN CAROUSEL ?

[STORPROC [!Query!]|Prod|0|1][/STORPROC]


[COUNT Boutique/Produit/[!Prod::Id!]/Donnee/Type=Image|NbImg]

[IF [!NbImg!]]
	<div class="listeimages">
		[STORPROC Boutique/Produit/[!Prod::Id!]/Donnee/Type=Image|Img|||Ordre|ASC]
			<div class="AfficheInfo" >
				<a href="[!Domaine!]/[!Img::Fichier!]" rel="shadowbox;" >
					//<img src="[!Domaine!]/[!Img::Fichier!].mini.[!FICH_LgUneInfo!]x[!FICH_HgUneInfo!].jpg" class="img-thumbnail" />
					<img src="[!Domaine!]/[!Img::Fichier!].mini.164x133.jpg" style="float:left; margin:0px 10px 10px 0px;" class="img-thumbnail  image-responsive" />
				</a>
			</div>
		[/STORPROC]
	</div>
[/IF]



