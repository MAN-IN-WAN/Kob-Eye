[STORPROC [!Query!]|Cat|||Ordre|ASC]
	<div class="BlocPost">		
		[IF [!Cat::Icone!]!=]<div class="ImageCat"><img src="/[!Cat::Icone!]" alt="[!Cat::Titre!]" ></div>[/IF]
		<h1>[!Cat::Titre!]</h1>
	</div>
	[!Chemin:=[!Query!]/Post/Actif=1!]
	[MODULE Blog/Post/Liste?Chemin=[!Chemin!]]
[/STORPROC]
