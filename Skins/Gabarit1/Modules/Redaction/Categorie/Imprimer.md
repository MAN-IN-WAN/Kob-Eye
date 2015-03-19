<div class="PagePrint">
	<div class="EntetePrint">
		<div class="LogoPrint"><img src="" alt="Logo"/></div>
	</div>
	[STORPROC [!Query!]|Cat]
		<h1>[!Cat::Nom!]</h1>
		[IF [!Cat::Description!]]
			<p class="DescriPrint">[!Cat::Description!]</p>
		[/IF]
		[STORPROC Redaction/Categorie/[!Cat::Id!]/Article/Publier=1|Art]
			<div class="ArtPrint">
				<h2>[!Art::Titre!]</h2>
				[IF [!Art::Chapo!]]
					<p class="ChapoPrint">[!Art::Chapo!]</p>
				[/IF]
				[COUNT Redaction/Article/[!Art::Id!]/Image|NbImg]
				[STORPROC Redaction/Article/[!Art::Id!]/Image|Img|0|1]
					<div class="ImgPrint">
						<img src="/[!Img::URL!].limit.80x100.jpg" alt="[!Art::Titre!]" title="[!Art::Titre!]" />
					</div>
				[/STORPROC]
				<div [IF [!NbImg!]>0]class="TextImgPrint"[ELSE]class="TextPrint"[/IF]>[!Art::Contenu!]</div>
			</div>
			[NORESULT]
				<ul>
					[STORPROC Redaction/Categorie/[!Cat::Id!]/Categorie/Publier=1|Cato|0|10|Id|ASC]
						<li>
							[!Cato::Nom!]
						</li>
					[/STORPROC]
				</ul>
			[/NORESULT]
		[/STORPROC]
	[/STORPROC]
	<div class="PiedPrint">
		<h3>Retrouvez-nous sur le site : [!Domaine!]</h3>
	</div>
</div>