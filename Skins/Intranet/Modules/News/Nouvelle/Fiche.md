// Données Pagination
[!Req:=News/Nouvelle!]
[!Limit:=5!]
[COUNT [!Req!]|Total]
[IF [!Page!]=][!Page:=1!][/IF]
[!Start:=[!Page:-1!]!][!Start*=10!]
[!NbPages:=[!Total:/[!Limit!]!]!]
[IF [!Math::Floor([!NbPages!])!]!=[!NbPages!]]
	[!NbPages:=[!Math::Floor([!NbPages!])!]!]
	[!NbPages+=1!]
[/IF]
<div class="ListeNews">
	<div class=" TitreActus" style="overflow:hidden;">
		<h1>Nos actualités</h1>
	</div>	
	[STORPROC [!Query!]|Ne|0|1]
		<div class="News Pair UneNews">
			<div class="Titre">Le [DATE d/m/Y][!Ne::tmsCreate!][/DATE]</div>
			<div class="ContenuNews">
				[IF [!Ne::Image!]!=]
					<div class="ImageNews">
						<img src="/[!Ne::Image!].limit.150x90.jpg" alt="[!Ne::Titre!]" />
					</div>
				[/IF]
				<div [IF [!Ne::Image!]!=] style ="display:block;float:left;overflow:hidden"[/IF]>
					<h2>[!Ne::Titre!]</h2>
					<p class="Chapo">[!Ne::Chapo!]</p>
				
					<div>
						<p>[!Ne::Contenu!]</p>
					</div>
					[STORPROC News/Nouvelle/[!Ne::Id!]/Lien|NeL]
						<div>
							<a href="/[!NeL::URL!]" alt="[!NeL::Titre!]" target="_blank" class="lienfichenews">[!NeL::Titre!]</a>
						</div>
	
					[/STORPROC]
	
					[STORPROC News/Nouvelle/[!Ne::Id!]/Fichier|NeF]
						<div>
							<a href="/[!NeF::URL!]" alt="[!NeF::Titre!]" target="_blank" class="lienfichenews">[!NeF::Titre!]</a>
						</div>
	
					[/STORPROC]

				</div>
			</div>
		</div>
	[/STORPROC]
	<a class="listeactus" href="/Actualites"  >Retour à la liste des actus</a>
</div>
