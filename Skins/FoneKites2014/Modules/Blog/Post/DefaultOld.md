[STORPROC [!Query!]|Pst|0|1][/STORPROC]
[STORPROC Blog/Categorie/Post/[!Pst::Id!]|Cat|0|1][/STORPROC]
[!lelienS:=!][!lelienP:=!]
[!TitrePostP:=!][!TitrePosS:=!]

[STORPROC Blog/Categorie/[!Cat::Id!]/Post/Id>[!Pst::Id!]|PstS|0|1|Id|ASC]
	[STORPROC Blog/Categorie/Post/[!PstS::Id!]|CatS|0|1][/STORPROC]
	[!lelienS:=LeBlog/[!CatS::Url!]/Post/[!PstS::Url!]!]
	[!TitrePosS:=[!PstS::Titre!]!]
[/STORPROC]

[STORPROC Blog/Categorie/[!Cat::Id!]/Post/Id<[!Pst::Id!]|PstP|0|1|Id|DESC]
	[STORPROC Blog/Categorie/Post/[!PstP::Id!]|CatP|0|1][/STORPROC]
	[!lelienP:=LeBlog/[!CatP::Url!]/Post/[!PstP::Url!]!]
	[!TitrePostP:=[!PstP::Titre!]!]
[/STORPROC]




<div class="titre-product gris-clair">
	<div class="container title-product">
		<div class="nav-product">
			<div class="nav-product-btn">
				[IF [!lelienP!]!=]<a class="left" href="/[!lelienP!]" alt="[!TitrePostP!]" ><img src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/arrow-prod-left.png" class="img-responsive" alt="Fone"/></a>[/IF]
			</div>
			<div class="nav-product-btn">
				[IF [!lelienS!]!=]<a class="right" href="/[!lelienS!]" alt="[!TitrePosS!]" ><img src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/arrow-prod-left.png" class="img-responsive" alt="Fone"/></a>[/IF]
			</div>
			[IF [!TitrePostP!]!=&&[!TitrePosS!]=]
				<div class="next-prod">
					[!PstP::Titre!] 
				</div>
			[/IF]
			[IF [!NomRidS!]!=]
				<div class="next-prod">
					[!PstS::Titre!] 
				</div>
			[/IF]
		</div>
		<h1 class="title_prod">[!Pst::Titre!] </h1>
		<div class="caract">[!Cat::Titre!]</div>
	</div>
</div>
<div class="gris-related">
	<div class="container">
		[COUNT Blog/Post/[!Pst::Id!]/Donnees/Type=ImagePrincipale|NbDo]
		[IF [!NbDo!]]
			[STORPROC Blog/Post/[!Pst::Id!]/Donnees/Type=ImagePrincipale|Do|0|1]
		        	<p><img src="/[!Do::Fichier!]" class="img-responsive" alt="[!Do::Titre!]" /></p>
			[/STORPROC]
		[/IF]
		<div class="row">
			<div class="col-lg-6 col-xs-12 txt-blog">
				[!Pst::Contenu!]
			</div>
			[COUNT Blog/Post/[!Pst::Id!]/Donnees/Type=Video|NbDo]
			[IF [!NbDo!]]
				<div class="col-lg-6 col-xs-12">
					[STORPROC Blog/Post/[!Pst::Id!]/Donnees/Type=Video|Do|0|1]
						<div class="Post-Aff">
							[IF [!Do::Iframe!]!=]
								[!Do::Iframe!]
							[/IF]
							[IF [!Do::Fichier!]!=]
								<iframe width="auto" height="auto" src="[!Domaine!]/[!Do::Fichier!]" class="youtube-player" frameborder="0"  type="text/html" allowfullscreen></iframe>
							[/IF]
						</div>
					[/STORPROC]
				</div>
			[/IF]
		</div>
		[COUNT Blog/Post/[!Pst::Id!]/Donnees/Type=Image|NbDo]
		<div class="row">
			[IF [!NbDo!]>1]
				<div class="col-lg-6 col-xs-12">
					[STORPROC Blog/Post/[!Pst::Id!]/Donnees/Type=Image|Do|0|1]
						<img src="/[!Do::Fichier!]" class="img-responsive" alt="[!Do::Titre!]" />
					[/STORPROC]
				</div>
			[/IF]
        		<div class="col-lg-6 col-xs-12 txt-blog">
					
				<div class="pull-right" style="line-height:42px;">
					[MODULE Systeme/Social/Likes?Lurl=[!Lien!]]
				</div>

		        </div>
		</div>
	 </div>
</div>

[STORPROC Blog/Post/[!Pst::Id!]/Donnees/Type=Video|Do|0|1]
	<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="position:absolute;left:200px;top:200px;">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<h3 id="myModalLabel">Modal header</h3>
		</div>
		<div class="modal-body">
			<p><iframe width="auto" height="auto" src="[!Domaine!]/[!Do::Fichier!]"  allowfullscreen></iframe></p>
		</div>
		<div class="modal-footer">
			<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
		</div>
	</div>
[/STORPROC]



 
