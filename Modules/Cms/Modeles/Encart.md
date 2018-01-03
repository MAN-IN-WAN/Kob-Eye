// AFFICHAGE DES ENCARTS, ZONE DE TEXTE, CHAMP DESCRIPTION DE LA CATEGORIE AFFICHÉE
[IF [!Chemin!]]
[ELSE]
	[!Chemin:=[!Query!]!]
[/IF]
<div class="[!NOMDIV!]" >
		[STORPROC [!Chemin!]|Cat|0|1]
			[IF [!Cat::Description!]!=]
				<blockquote><p>[!Cat::Description!]</p></blockquote>
			[ELSE]	
				<p class="encarsansquote">&nbsp;</p>
			[/IF]
		[/STORPROC]
</div>
 