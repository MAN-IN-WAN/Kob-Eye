[IF [!Req!]=][!Req:=Redaction/Categorie/2!][/IF]
[STORPROC [!Req!]/Article/Publier=1|Art|[!Offset!]|[!Limit!]|tmsCreate|DESC]
	[STORPROC Redaction/Categorie/Article/[!Art::Id!]|CatM][/STORPROC]
	<div class="fone-item item-large element [!CatM::Url!]">		
		<div class="category">
			<div class="cat-bloc">
				<a href="/Informations/Article/[!Art::Url!]">
					Informations | [!Art::Titre!]
				</a>
			</div>
		</div>
		<div class="produits ">
			[STORPROC Redaction/Article/[!Art::Id!]/Image|ImgArt|0|1]
				<img class="img-responsive" src="/[!ImgArt::URL!].mini.573x350.jpg" alt="[!ImgArt::Titre!]" />
			[/STORPROC]
			<div class="BlocCouleur BlocCouleur-[!CatM::Couleur!]">				
				<h2>		
					[!Art::Titre!]
				</h2>
			</div>
			<div class="teaser-blog">
				<div class="teaser">
					<div class="texteaser" [IF [!HAUTEURBLOCTEXTE!]!=] style="height:290px;"[/IF]> 
						[SUBSTR 350|[...]][!Art::Contenu!][/SUBSTR]
					</div>
					<div class="teaser-info">
						<div class="more-BlocCouleur-[!CatM::Couleur!]"><a href="/[!Systeme::getMenu(Redaction/Categorie/2)!]/[!CatM::Url!]/Article/[!Art::Url!]">MORE DETAILS</a></div>
					</div>
				</div>
			</div>


		</div>
	</div>
[/STORPROC]

