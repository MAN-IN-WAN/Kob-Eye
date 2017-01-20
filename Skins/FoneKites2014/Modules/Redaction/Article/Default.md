[STORPROC [!Query!]|Art|0|1][/STORPROC]
[STORPROC Redaction/Categorie/Article/[!Art::Id!]|Cat|0|1][/STORPROC]
[!lelienS:=!][!lelienP:=!]
[!TitreArticleP:=!][!TitreArticleS:=!]
[STORPROC Redaction/Categorie/[!Cat::Id!]/Article/Id>[!Art::Id!]|ArtS|0|1|Id|ASC]
	[STORPROC Redaction/Categorie/Article/[!ArtS::Id!]|CatS|0|1][/STORPROC]
	[!lelienS:=Informations/[!CatS::Url!]/Article/[!ArtS::Url!]!]
	[!TitreArticleS:=[!ArtS::Titre!]!]
[/STORPROC]
[STORPROC Redaction/Categorie/[!Cat::Id!]/Article/Id<[!Art::Id!]|ArtP|0|1|Id|DESC]
	[STORPROC Redaction/Categorie/Article/[!ArtP::Id!]|CatP|0|1][/STORPROC]
	[!lelienP:=Informations/[!CatP::Url!]/Article/[!ArtP::Url!]!]
	[!TitreArticleP:=[!ArtP::Titre!]!]
[/STORPROC]
//[!Query!]
<div class="title-product container  nopadding-right nopadding-left" style="height:130px;">
	<div class="row">
		<div class="col-lg-10">
			<h1 class="title_prod">[!Art::Titre!]</h1>
		</div>
		<div class="col-lg-2">
			<div class="nav-product">
				<div class="nav-product-btn">
					[IF [!lelienP!]!=]
						<a class="left" href="/[!lelienP!]" alt="[!TitreArticleP!]"   onmouseover='$("#Nom-P").css("display","block");' onmouseout='$("#Nom-P").css("display","none");' ><img src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/arrow-prod-left.png" class="img-responsive" alt="Fone"/></a>
					[ELSE]
						<a class="leftrightvide" href="#"><img src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/vide.png" class="img-responsive" alt="Fone"/></a>
							
					[/IF]
				</div>
				<div class="nav-product-btn">
					[IF [!lelienS!]!=]
						<a class="right" href="/[!lelienS!]" alt="[!TitreArticleS!]"   onmouseover='$("#Nom-S").css("display","block");' onmouseout='$("#Nom-S").css("display","none");' ><img src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/arrow-prod-right.png" class="img-responsive" alt="Fone"/></a>
					[ELSE]
						<a class="leftrightvide" href="#"><img src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/vide.png" class="img-responsive" alt="Fone"/></a>
					[/IF]
				</div>
			</div>
			
		</div>
	</div>
	<div class="row">
		<div class="col-lg-10">
		</div>
		<div class="col-lg-2" >
			<div class="Nom-Navigation" id="Nom-P"  style="display:none"><br />[!ArtP::Titre!] </div>
			<div class="Nom-Navigation" id="Nom-S"  style="display:none" ><br />[!ArtS::Titre!] </div>
		</div>

	</div>

</div>


<div class="gris-related">
	<div class="container nopadding-right nopadding-left">
		<div class="row">
			[!OkImg:=0!]
			[STORPROC Redaction/Article/[!Art::Id!]/Image|ImgArt|0|1]
				<div class="col-lg-6 col-xs-12">
					<img src="/[!ImgArt::URL!]" class="img-responsive" alt="[!ImgArt::Titre!]" />
					[!OkImg:=1!]
				</div>
			[/STORPROC]			
			<div class="col-lg-[IF [!OkImg!]=1]6[ELSE]12[/IF] col-xs-12 txt-blog">
				[!Art::Contenu!]
				<div class="pull-right" style="line-height:42px;">
					[MODULE Systeme/Social/Likes?Lurl=[!Lien!]]
				</div>
			</div>
		</div>
	 </div>
</div>


