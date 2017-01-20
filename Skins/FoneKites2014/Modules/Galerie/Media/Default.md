[STORPROC [!Query!]|Med|0|1][/STORPROC]
[STORPROC Galerie/Categorie/Media/[!Med::Id!]|Cat|0|1][/STORPROC]
[!lelienS:=!][!lelienP:=!]
[!TitreMediaP:=!][!TitreMediaS:=!]

//SUIVANT
[STORPROC Galerie/Categorie/[!Cat::Id!]/Media/Date<[!Med::Date!]&Display=1|MedS|0|1|Date|DESC]
	[!lelienS:=[!Systeme::CurrentMenu::Url!]/[!Cat::Url!]/Media/[!MedS::Url!]!]
	[!TitreMediaS:=[!MedS::Titre!]!]
	[NORESULT]
		[STORPROC Galerie/Categorie/[!Cat::Id!]/Media/Display=1|MedS|0|1|Date|DESC]
			[!lelienS:=[!Systeme::CurrentMenu::Url!]/[!Cat::Url!]/Media/[!MedS::Url!]!]
			[!TitreMediaS:=[!MedS::Titre!]!]
		[/STORPROC]
	[/NORESULT]
[/STORPROC]

//PRECEDENT
[STORPROC Galerie/Categorie/[!Cat::Id!]/Media/Date>[!Med::Date!]&Display=1|MedP|0|1|Date|ASC]
	[!lelienP:=[!Systeme::CurrentMenu::Url!]/[!Cat::Url!]/Media/[!MedP::Url!]!]
	[!TitreMediaP:=[!MedP::Titre!]!]
	[NORESULT]
		[STORPROC Galerie/Categorie/[!Cat::Id!]/Media/Display=1|MedP|0|1|Date|ASC]
			[!lelienP:=[!Systeme::CurrentMenu::Url!]/[!Cat::Url!]/Media/[!MedP::Url!]!]
			[!TitreMediaP:=[!MedP::Titre!]!]
		[/STORPROC]
	[/NORESULT]
[/STORPROC]
<div class="title-product container  nopadding-left nopadding-right">
	<div class="row">
		<div class="col-lg-10 col-xs-6">
		<h1 class="title_prod">[!Med::Titre!] </h1>
		</div>
		<div class="col-lg-2 col-xs-6">
			<div class="nav-product">
				<div class="nav-product-btn">
					<a class="left" href="/[!lelienP!]" alt="[!TitreMediaP!]"   onmouseover='$("#Nom-P").css("display","block");' onmouseout='$("#Nom-P").css("display","none");' >
						<img src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/arrow-prod-left.png" class="img-responsive" alt="Fone"/>
					</a>
				</div>
				<div class="nav-product-btn">
					<a class="right" href="/[!lelienS!]" alt="[!TitreMediaS!]"   onmouseover='$("#Nom-S").css("display","block");' onmouseout='$("#Nom-S").css("display","none");' >
						<img src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/arrow-prod-right.png" class="img-responsive" alt="Fone"/>
					</a>
				</div>
			</div>
			
		</div>
	</div>
	<div class="row">
		<div class="col-lg-10 col-xs-8">
			[IF [!Med::Chapo!]!=]<div class="caract">[!Med::Chapo!]</div>[/IF]
		</div>
		<div class="col-lg-2 col-xs-4" >
			<div class="Nom-Navigation" id="Nom-P"  style="display:none"><br />[!MedP::Titre!] </div>
			<div class="Nom-Navigation" id="Nom-S"  style="display:none" ><br />[!MedS::Titre!] </div>
		</div>

	</div>

</div>
// trois modeles 
// si categorie video : video en haut et text en dessous
// si categorie gallery : big image et petite image en dessous
// si wallpaper : big image et bouton download
<div class="gris-related">
	<div class="container nopadding-left nopadding-right fond-blanc" style="text-align:center;padding:0;">
		[IF [!Cat::Type!]=Video]
			[STORPROC Galerie/Media/[!Med::Id!]/Donnees|Do|0|10|Id|DESC]
				<div class="row"><div class="col-lg-12 video-large">
					[!Do::IFrame!]
				</div></div>
			[/STORPROC]
			[IF [!Med::Description!]]
			<div class="row"><div class="col-lg-12 description">
				[!Med::Description!]
			</div></div>
			[/IF]
		[/IF]
		[IF [!Cat::Type!]=Gallery]
		
			[HEADER JS]Skins/[!Systeme::Skin!]/js/fotorama.js[/HEADER]
			[HEADER CSS]Skins/[!Systeme::Skin!]/Css/fotorama.css[/HEADER]
			<div class="fotorama" data-width="100%" data-ratio="4/3" data-max-width="100%"  data-nav="thumbs">
				[STORPROC Galerie/Media/[!Med::Id!]/Donnees|Do|0|1000|Id|DESC]
					<img src="/[!Do::URL!]" alt="[!Do::Titre!]" />
				[/STORPROC]
			</div>
			<div class="row"><div class="col-lg-12 col-xs-12 description">
				[!Med::Description!]
			</div></div>
		[/IF]
		[IF [!Cat::Type!]=Wallpaper]
			[STORPROC Galerie/Media/[!Med::Id!]/Donnees|Do|0|1]
				<div class="row"><div class="col-lg-12 col-xs-12">
					<img src="/[!Do::URL!]" class="img-responsive" alt="[!Do::Titre!]"  alt="[!Do::Titre!]" >
				</div></div>
				<div class="row"><div class="col-lg-12 col-xs-12">
					<div class="load-more">
						<a href="/[!Do::URL!].download"  class="btn-more-Media btn-primary" id="LoadMore" target="_blank" >$DOWNLOAD$</a>
					</div> 
				</div></div>
			[/STORPROC]
			<div class="row"><div class="col-lg-12 col-xs-12 description">
				[!Med::Description!]
			</div></div>

		[/IF]
		
	 </div>
	 		<div class="container" style="padding-top:15px;">
			[MODULE Systeme/Social/Likes?Lurl=[!Lien!]]
	  </div>
					
</div>