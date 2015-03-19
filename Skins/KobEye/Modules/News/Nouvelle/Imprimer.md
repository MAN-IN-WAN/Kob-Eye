<div class="PagePrint">
	[STORPROC [!Query!]|N][/STORPROC]
	<div class="EntetePrint">
		<div class="LogoPrint"><img src="/Skins/Gabarit1/Img/Logo.jpg" alt="Logo"/></div>
	</div>
	<h1>[!N::Titre!]</h1>
	<h2>[!N::Chapo!]</h2>
	
		<div class="ArtPrint">
			[IF [!N::Image!]!=]
				<div class="ImgPrint">
					<img src="/[!N::Image!].limit.100x80.jpg" alt="[!N::Titre!]" />
				</div>
			[/IF]
			<div [IF [!N::Image!]!=]class="TextImgPrint"[ELSE]class="TextPrint"[/IF]>
				<p>[!N::Contenu!]</p>
			</div>
		</div>
	<div class="PiedPrint">
		<h3>Retrouvez-nous sur le site : [!Domaine!]</h3>
	</div>
</div>