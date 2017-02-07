// image en dessus, col text et col image
[IF [!Chemin!]=]
	[!Chemin:=[!Query!]!]
[/IF]
[STORPROC [!Chemin!]|Pst|0|1][/STORPROC]
[STORPROC Blog/Categorie/Post/[!Pst::Id!]|CatB|0|1][/STORPROC]
[!lelienS:=!][!lelienP:=!]
[!TitrePostP:=!][!TitrePosS:=!]

//SUIVANT
[STORPROC Blog/Categorie/[!CatB::Id!]/Post/Date<[!Pst::Date!]&Display=1|PstS|0|1|Date|DESC]
	[!lelienS:=[!Systeme::CurrentMenu::Url!]/[!CatB::Url!]/Post/[!PstS::Url!]!]
	[!TitrePosS:=[!PstS::Titre!]!]
        [NORESULT]
            [STORPROC Blog/Categorie/[!CatB::Id!]/Post/Display=1|PstS|0|1|Date|DESC]
                [!lelienS:=[!Systeme::CurrentMenu::Url!]/[!CatB::Url!]/Post/[!PstS::Url!]!]
                [!TitrePosS:=[!PstS::Titre!]!]
            [/STORPROC]
        [/NORESULT]
[/STORPROC]

//PRECEDENT
[STORPROC Blog/Categorie/[!CatB::Id!]/Post/Date>[!Pst::Date!]&Display=1|PstP|0|1|Date|ASC]
	[!lelienP:=[!Systeme::CurrentMenu::Url!]/[!CatB::Url!]/Post/[!PstP::Url!]!]
	[!TitrePostP:=[!PstP::Titre!]!]
        [NORESULT]
            [STORPROC Blog/Categorie/[!CatB::Id!]/Post/Display=1|PstP|0|1|Date|ASC]
                    [!lelienP:=[!Systeme::CurrentMenu::Url!]/[!CatB::Url!]/Post/[!PstP::Url!]!]
                    [!TitrePostP:=[!PstP::Titre!]!]
            [/STORPROC]
        [/NORESULT]
[/STORPROC]
<div class="title-product container nopadding-right nopadding-left">
	<div class="row">
		<div class="col-lg-10 col-xs-6">
			<h1 class="title_prod">[!Pst::Titre!] </h1>
		</div>
		<div class="col-lg-2 col-xs-6">
			<div class="nav-product">
				<div class="nav-product-btn">
					<a class="left" href="/[!lelienP!]" alt="[!TitrePostP!]"  onmouseover='$("#Nom-P").css("display","block");' onmouseout='$("#Nom-P").css("display","none");' ><img src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/arrow-prod-left.png" class="img-responsive" alt="Fone"/></a>
				</div>
				<div class="nav-product-btn">
					<a class="right" href="/[!lelienS!]" alt="[!TitrePosS!]"  onmouseover='$("#Nom-S").css("display","block");' onmouseout='$("#Nom-S").css("display","none");' ><img src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/arrow-prod-right.png" class="img-responsive" alt="Fone"/></a>
				</div>
			</div>
			
		</div>
	</div>
	<div class="row">
		<div class="col-lg-10 col-xs-12">
			<div class="caract">[!CatB::Titre!]</div><span class="ke-extra-date">[DATE d/m/Y][!Pst::Date!][/DATE]</span> 
		</div>
		<div class="col-lg-2 hidden-xs" >
			<div class="Nom-Navigation" id="Nom-P"  style="display:none"><br />[!PstP::Titre!] </div>
			<div class="Nom-Navigation" id="Nom-S"  style="display:none" ><br />[!PstS::Titre!] </div>
		</div>

	</div>

</div>
<!-- Contenu -->
<div class="gris-related">
	<div class="container nopadding-right nopadding-left">
		[STORPROC Blog/Post/[!Pst::Id!]/Donnees/Type=ImagePrincipale|Do|0|1]
			<p><img src="/[!Do::Fichier!]" class="img-responsive" alt="[!Do::Titre!]" /></p>
			[NORESULT]
				[STORPROC Blog/Post/[!Pst::Id!]/Donnees/Type=Video|Do|0|1]
					[!Do::IFrame!]
				[/STORPROC]
			[/NORESULT]
		[/STORPROC]
		<div class="row">
			<div class="col-lg-12 col-xs-12 txt-blog">
				[!Pst::Contenu!]
			</div>
		</div>
	 </div>
</div>
<!-- Block -->
[STORPROC Blog/Post/[!Pst::Id!]/Block|Bl|0|100|Ordre|ASC]
    //COULEUR
    [IF [!Utils::isPair([!Pos!])!]]
        [!Couleur:=gris!]
    [ELSE]
        [!Couleur:=gris-clair!]
    [/IF]
    
    [SWITCH [!Bl::TextePosition!]|=]
        [CASE TexteDroite]
        <div class="[!Couleur!] block-post">
            <div class="container nopadding-right nopadding-left">
                <div class="row">
                    [SWITCH [!Bl::TypeMedia!]|=]
                        [CASE 0]
                        //image
                        <div class="col-lg-6 col-xs-12">
                                <img src="/[!Bl::Image!]" class="img-responsive"/>
                        </div>
                        [/CASE]
                        [CASE 1]
                        //iframe
                        <div class="col-lg-6 col-xs-12">
                                [!Bl::Iframe!]
                        </div>
                        [/CASE]
                    [/SWITCH]
                    <div class="[IF [!Bl::TypeMedia!]=2]col-lg-12[ELSE]col-lg-6[/IF] col-xs-12">
                            [!Bl::Texte!]
                    </div>
                </div>
            </div>
        </div>
        [/CASE]
        [CASE TexteDessous]
        <div class="[!Couleur!] block-post">
            <div class="container nopadding-right nopadding-left">
                <div class="row">
                    [SWITCH [!Bl::TypeMedia!]|=]
                        [CASE 0]
                        //image
                        <div class="col-lg-12 col-xs-12">
                                <img src="/[!Bl::Image!]" class="img-responsive"/>
                        </div>
                        [/CASE]
                        [CASE 1]
                        //iframe
                        <div class="col-lg-12 col-xs-12">
                                [!Bl::Iframe!]
                        </div>
                        [/CASE]
                    [/SWITCH]
                    <div class="col-lg-12 col-xs-12">
                            [!Bl::Texte!]
                    </div>
                </div>
            </div>
        </div>
        [/CASE]
        [CASE TexteGauche]
        <div class="[!Couleur!] block-post">
            <div class="container nopadding-right nopadding-left">
                <div class="row">
                    <div class="[IF [!Bl::TypeMedia!]=2]col-lg-12[ELSE]col-lg-6[/IF] col-xs-12">
                            [!Bl::Texte!]
                    </div>
                    [SWITCH [!Bl::TypeMedia!]|=]
                        [CASE 0]
                        //image
                        <div class="col-lg-6 col-xs-12">
                                <img src="/[!Bl::Image!]" class="img-responsive"/>
                        </div>
                        [/CASE]
                        [CASE 1]
                        //iframe
                        <div class="col-lg-6 col-xs-12">
                                [!Bl::Iframe!]
                        </div>
                        [/CASE]
                    [/SWITCH]
                </div>
            </div>
        </div>
        [/CASE]
        [CASE DoubleTexte]
        <div class="[!Couleur!] block-post">
            <div class="container nopadding-right nopadding-left">
                <div class="row">
                    <div class="col-lg-6 col-xs-12">
                            [!Bl::Texte!]
                    </div>
                    <div class="col-lg-6 col-xs-12">
                            [!Bl::Texte2!]
                    </div>
                </div>
            </div>
        </div>
        [/CASE]
        [CASE DoubleMedia]
        <div class="[!Couleur!] block-post">
            <div class="container nopadding-right nopadding-left">
                <div class="row">
                    [SWITCH [!Bl::TypeMedia!]|=]
                        [CASE 0]
                        //image
                        <div class="col-lg-6 col-xs-12">
	                            [!Bl::Texte!]
                                <img src="/[!Bl::Image!]" class="img-responsive"/>
                        </div>
                        [/CASE]
                        [CASE 1]
                        //iframe
                        <div class="col-lg-6 col-xs-12">
                            [!Bl::Texte!]
                                [!Bl::Iframe!]
                        </div>
                        [/CASE]
                    [/SWITCH]
                    [SWITCH [!Bl::TypeMedia2!]|=]
                        [CASE 0]
                        //image
                        <div class="col-lg-6 col-xs-12">
                            [!Bl::Texte2!]
                                <img src="/[!Bl::Image2!]" class="img-responsive"/>
                        </div>
                        [/CASE]
                        [CASE 1]
                        //iframe
                        <div class="col-lg-6 col-xs-12">
                            [!Bl::Texte2!]
                                [!Bl::Iframe2!]
                        </div>
                        [/CASE]
                    [/SWITCH]
                </div>
            </div>
        </div>
        [/CASE]
        [CASE PunchText]
        <div class="gris-fonce block-post">
            <div class="container nopadding-right nopadding-left">
                <h1>[!Bl::Texte!]</h1>
            </div>
        </div>
        [/CASE]
        [DEFAULT]
            <h1>BLOCK [!Bl::TextePosition!]</h1>
        [/DEFAULT]
    [/SWITCH]
[/STORPROC]

<div class="gris-related">
	<div class="container nopadding-right nopadding-left">
		
		[STORPROC Blog/Rider/Post/[!Pst::Id!]|R|0|10]
		<div class="row">
		    <div class="col-md-6">
			[!Pst::MotDuRider!]
				<div class="col-md-6" style="padding-top:10px; padding-bottom:10px;  padding-left:0px;">
				[MODULE Systeme/Social/Likes?Lurl=[!Lien!]]
				</div>
		    </div>
		    <div class="col-md-6">
			[LIMIT 0|10]
			[MODULE Team/Rider/MiniFiche?Rider=[!R!]]
			[/LIMIT]
		    </div>
		</div>
		[/STORPROC]
		
	 </div>
</div>
