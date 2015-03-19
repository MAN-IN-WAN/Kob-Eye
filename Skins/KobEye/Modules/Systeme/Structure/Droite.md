<div id="Droite">
	[COUNT Redaction/Categorie/[!Cata!]/Categorie|Cati]
	[IF [!Cati!]]
		[STORPROC Redaction/Categorie/[!Cata!]|Cate]
			<div id="SousCat">
				<ul>
					[STORPROC Redaction/Categorie/[!Cate::Id!]/Categorie/Publier=1|Cato|0|20]
						<li>
							<img src="/Skins/Gabarit1/Img/PuceBeige.png" style="padding-top:0;float:left;margin:0;width:18px;"  alt="puce"/>
							<a  href="/[!Systeme::CurrentMenu::Url!]/[!Cato::Link!]" title="[!Cato::Nom!]" [IF [!Lien!]=[!Systeme::CurrentMenu::Url!]/[!Cato::Link!]] class="ActifDr"[/IF] >[!Cato::Nom!]</a>
						</li>
					[/STORPROC]
				</ul>
			</div>
		[/STORPROC]
	[/IF]
	//[MODULE Systeme/Newsletter]
	[MODULE News/Colonne]
</div>
