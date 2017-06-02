[IF [!MENU!]=][!MENU:=[!Systeme::CurrentMenu::Url!]!][/IF]
[STORPROC Systeme/Menu/[!MENU!]|Menu|0|1][/STORPROC]
[STORPROC [!Menu::Alias!]|Cat|0|1][/STORPROC]
<div class="well">
	<h4>[!TITRE!]</h4>
	<ul class="nav nav-pills nav-stacked">
		[STORPROC [!Menu::Alias!]/Categorie/Publier=1|Cato|0|20|Ordre|ASC]
				<li><a [IF [!Lien!]~[!Cato::Url!]]class="active"[/IF] href="/[!MENU!]/[!Cato::Url!]">[!Cato::Nom!]</a></li>
		[/STORPROC]
	</ul>
</div>