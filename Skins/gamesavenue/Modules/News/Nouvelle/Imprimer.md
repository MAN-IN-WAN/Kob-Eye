<div class="PagePrint">
	<div class="EntetePrint">
		<div class="LogoPrint"><img src="" alt="LOGO"/></div>
	</div>
	<h1>Actualit&eacute;s</h1>
	[STORPROC [!Query!]|N]
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
	[/STORPROC]
	<div class="PiedPrint">
		<h3>Retrouvez-nous sur le site : [!Domaine!]</h3>
	</div>
</div>