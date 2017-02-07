[STORPROC [!Query!]|C|0|1][/STORPROC]




[IF [!C::AfficheTitre!]]
<div class="titre-product gris-clair">
	<div class="container title-product nopadding-right nopadding-left">
		<div class="row">
			<div class="col-lg-10 col-xs-6">
				<h1 class="title_prod">[!C::Nom!]<span class="title">[!C::SousTitre!]</span></h1>
			</div>
			<div class="col-lg-2 col-xs-6">
				<!--
				<div class="nav-product">
					<div class="nav-product-btn">
						<a class="left" href="/[!lelienP!]" title="[!NomProdP!]"  onmouseover='$("#Nom-P").css("display","block");' onmouseout='$("#Nom-P").css("display","none");' >
							<img src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/arrow-prod-left.png" class="img-responsive" alt="Fone"/>
						</a>
					</div>
					<div class="nav-product-btn">
						<a class="right" href="/[!lelienS!]" title="[!NomProdS!]"  onmouseover='$("#Nom-S").css("display","block");' onmouseout='$("#Nom-S").css("display","none");' >
							<img src="[!Domaine!]/Skins/[!Systeme::Skin!]/img/arrow-prod-right.png" class="img-responsive" alt="Fone"/>
						</a>
					</div>
				</div>
				-->
			</div>
		</div>
		<div class="row">
			<div class="col-lg-10 col-xs-10">
				[IF [!C::Chapo!]!=]<div class="caract">[!C::Chapo!]</div>[/IF]
			</div>
			<div class="col-lg-2 col-xs-2" >
				<!--<div class="Nom-Navigation" id="Nom-P"  style="display:none">[!ProdP::Nom!]</div>
				<div class="Nom-Navigation" id="Nom-S"  style="display:none" >[!ProdS::Nom!]</div>-->
			</div>

		</div>
	
	</div>
</div>
[/IF]

[STORPROC [!Query!]|Cat|0|1|Id|ASC]
	[IF [!Cat::Modele!]=]
		[MODULE Redaction/Modeles/Default?Chemin=[!Query!]]
	[ELSE]
		[MODULE Redaction/Modeles/[!Cat::Modele!]?Chemin=[!Query!]]
	[/IF]
[/STORPROC]

