[STORPROC [!Systeme::Menus!]/Affiche=1&MenuPrincipal=1|M|0|20]
	[!BackColor:=!]
	[!BackColor:=[!Systeme::User::Couleur!]!]
	[IF [!M::BackgroundColor!]!=]
		[!BackColor:=[!M::BackgroundColor!]!]
	[/IF]
[IF [!BackColor!]=#transp][!BackColor:=#transparent!][/IF]
	[!BackImg:=!]
	[IF [!M::BackgroundImage!]!=]
		[!BackImg:=[!M::BackgroundImage!]!]
	[/IF]
	<div class="pull-left" style="[IF [!BackColor!]!=]background-color:[!BackColor!];[/IF]">
		<div class="ElementMenu" style="[IF [!BackImg!]!=]background:url('[!BackImg!]') no-repeat right 0 [!BackColor!];[/IF]">
			[IF [!M::Url!]~http]
				<a href="[!M::Url!]" target="_blank" ><h2>[!M::Titre!]</h2>[IF [!M::SousTitre!]!=]<h3>[!M::SousTitre!]</h3>[/IF]</a>
			[ELSE]
				<a href="/[!M::Url!]" ><h2>[!M::Titre!]</h2>[IF [!M::SousTitre!]!=]<h3>[!M::SousTitre!]</h3>[/IF]</a>
			[/IF]
		</div>
	</div>
[/STORPROC]

