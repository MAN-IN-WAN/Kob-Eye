[IF [!MENU!]!=]
	[STORPROC Systeme/Menu/[!MENU!]|Menu|0|1][/STORPROC]
	[STORPROC [!Menu::Alias!]|Cat|0|1][/STORPROC]
	<div class="EntoureComposant">
		<div class="[!NOMDIV!]" style="padding-bottom:[!PADDINGBOTTOM!]px">
			<div class="[IF [!BLOCAFFICH!] ]BlocTop[/IF]"></div>
			<div class="[IF [!BLOCAFFICH!] ]BlocLine[/IF]">
				<div class="EnteteNavigation">
					[!TITRE!]
				</div>
				<div class="ContenuComposantNavigation">		
					[STORPROC [!Menu::Alias!]/Categorie/Publier=1|Cato|0|20|Ordre|ASC]
						[LIMIT 0|20]
							<a href="/[!MENU!]/[!Cato::Url!]">[!Cato::Nom!]</a>
						[/LIMIT]
					[/STORPROC]
				</div>
			</div>	
			<div class="[IF [!BLOCAFFICH!] ]BlocBottom[/IF]"></div>
		</div>
	</div>
	



[/IF]