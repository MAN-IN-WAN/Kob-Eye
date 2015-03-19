<div class="Aide">
	<div class="BlocIcone">
		<img src="/Skins/AdminV2/Img/Modules/[!Module::Actuel::Nom!]/Gros.png">
		<h1>[!Module::Actuel::Nom!]</h1>
		<div>[!Module::Actuel::Description!]</div>
	</div>
	[STORPROC [!Query!]|Cat|0|1]
	[BLOC Rounded||position:absolute;top:80px;bottom:0;left:0;right:0;|position:absolute;top:2px;bottom:2px;left:0;right:0;]
		[STORPROC [!Query!]/Article|Art|0|100|Ordre|ASC]
			<div class="AideArticle">
				<img src="/Skins/[!Systeme::User::Skin!]/Img/Aide/Petit[!Pos!].jpg">
				<h1>[!Art::Titre!]</h1>
				<div >[!Art::Contenu!]</div>
			</div>
		[/STORPROC]
	[/BLOC]
	[/STORPROC]
</div>
