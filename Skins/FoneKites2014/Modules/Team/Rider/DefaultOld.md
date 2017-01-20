[STORPROC [!Query!]|Rid|0|1][/STORPROC]
[STORPROC Team/Equipe/Rider/[!Rid::Id!]|Eq|0|1][/STORPROC]
[!lelienS:=!][!lelienP:=!]
[!NomRidP:=!][!NomRidS:=!][!PrenomRidP:=!][!PrenomRidS:=!]


//[STORPROC Team/Equipe/[!Eq::Id!]/Rider/Id>[!Rid::Id!]|RidS|0|1]
[STORPROC Team/Rider/Id>[!Rid::Id!]|RidS|0|1]
	[!lelienS:=Team_/[!Eq::Url!]/Rider/[!RidS::Url!]!]
	[!NomRidS:=[!RidS::Nom!]!]
	[!PrenomRidS:=[!RidS::Prenom!]!]
[/STORPROC]

//[STORPROC Team/Equipe/[!Eq::Id!]/Rider/Id<[!Rid::Id!]|RidP|0|1]
[STORPROC Team/Rider/Id<[!Rid::Id!]|RidP|0|1]
	[!lelienP:=Team_/[!Eq::Url!]/Rider/[!RidP::Url!]!]
	[!NomRidP:=[!RidP::Nom!]!]
	[!PrenomRidP:=[!RidP::Prenom!]!]

[/STORPROC]



<div class="titre-product gris-clair">
	<div class="container title-product">
		<div class="nav-product">
			<div class="nav-product-btn">
				[IF [!lelienP!]!=]<a class="left" href="/[!lelienP!]">left</a>[/IF]
			</div>
			<div class="nav-product-btn">
				[IF [!lelienS!]!=]<a class="right" href="/[!lelienS!]">right</a>[/IF]
			</div>
			[IF [!NomRidP!]!=&&[!NomRidS!]=]
				<div class="next-prod">
					[!RidP::Prenom!] [!RidP::Nom!]
				</div>
			[/IF]
			[IF [!NomRidS!]!=]
				<div class="next-prod">
					[!RidS::Prenom!] [!RidS::Nom!]
				</div>
			[/IF]
		
		</div>
		<h1 class="title_prod">[!Rid::Prenom!] <span class="title">[!Rid::Nom!]</span></h1>
		<div class="caract">[!Eq::Titre!]</div>
	</div>
</div>



<div class="featured">
	<div class="container">
	        <div class="col-lg-12  col">
			<img class="img-responsive" src="/[!Rid::Avatar!]" alt="[!Rid::Nom!]"/>
		</div>
		<h3>Profile</h3>
		<div class="col-lg-6 ">
			<p class="identity">[!Rid::Description1!]</p>
		</div>
		<div class="col-lg-6-1 ">
			<div class="partners">SPONSORS </div>
			<div class="partners_1">[!Rid::Description2!]</div>
			<div class="palmares">PALMARES</div> 
			<div class="palmares_1">[!Rid::Description3!]</div>
		</div>
	</div>
</div>

????????ICI QU'EST CE QUI DOIT APPARAITRE J AI TOUT LAISSE EN FIXE ???????????

<div class="gris-fonce">
	<div class="container">
		<h1> ANOTHER THING WAS IMMEDIATELY VERY IMPRESSIVE IT WAS THE STRENGTH OF THE WAVES AND THE APPALLING VIOLENCE WITH WHICH THEY BREAK ON THE REEF.</h1>
	</div>
</div>

?????????? MANQUE LA GALLERY , ELLE VIENT D'OU ???????????

[COMPONENT Blog/Bootstrap.DernierPostFone2014/Default?NOMDIV=last-news&TITRE=Last News&URLBLOG=LeBlog&BLOGCATEGORIE=2&LARGEURIMAGE=570&HAUTEURIMAGE=350&HAUTEURBLOCTEXTE=170]
