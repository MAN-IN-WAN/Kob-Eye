<div class="[!NOMDIV!]" style="padding-bottom:[!PADDINGBOTTOM!]px">
	<div class="EnteteNavigation">
		[!TITRE!]
	</div>
	<div class="ContenuComposantNavigation">	
		<ul>	
			[STORPROC Blog/CategegorieLien/[!CATEGLIEN!]|Cat]
				<ul>
					[STORPROC Blog/CategegorieLien/[!Cat::Id!]/Lien|Lie|0|[!NBLIEN!]|Ordre|ASC]
						<li>
							[IF [!Lie::Fichier!]!=]
								<a href="[!Lie::fichier!]" title="Télécharger le fichier">[!Lie::Titre!]</a>
							[ELSE]
								<a href="[!Lie::Url!]" target="_blank" title="Allez voir le site">[!Lie::Titre!]</a>

							[/IF]
						</li>
					[/STORPROC]
				</ul>
			[/STORPROC]
		</ul>
	</div>
</div>
