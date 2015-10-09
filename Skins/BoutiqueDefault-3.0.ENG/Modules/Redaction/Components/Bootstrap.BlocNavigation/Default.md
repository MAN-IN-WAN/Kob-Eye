[IF [!MENU!]=][!MENU:=[!Systeme::CurrentMenu::Url!]!][/IF]
[STORPROC Systeme/Menu/[!MENU!]|Menu|0|1][/STORPROC]
[STORPROC [!Menu::Alias!]|Cat|0|1][/STORPROC]
[INFO [!Menu::Alias!]|I]
[IF [!I::Module!]=Redaction]
	<!-- Block CMS module -->
	<div id="informations_block_left_1" class="block informations_block_left">
		<p class="title_block">
			[!TITRE!]
		</p>
		<div class="block_content">
			<ul>
			[STORPROC [!Menu::Alias!]/Categorie/Publier=1|Cato|0|20|Ordre|ASC]
					<li><a [IF [!Lien!]~[!Cato::Url!]]class="selected"[/IF] href="/[!MENU!]/[!Cato::Url!]">[!Cato::Nom!]</a></li>
			[/STORPROC]
			</ul>
		</div>
	</div>
	<!-- /Block CMS module -->
[/IF]