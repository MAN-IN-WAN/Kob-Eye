[STORPROC [!Query!]|A|0|1]
	<div class="row-fluid">
		<div class="[IF [!A::Image!]]span8[ELSE]span12[/IF] well">
			<h2>[DATE d.m.Y][!A::Date!][/DATE]<br />[!A::Titre!]</h2>
			<p>	[!A::Contenu!]</p>
		</div>
		[IF [!A::Image!]]
			<div class="well span4">
				<img class="media-object" src="/[!A::Image!].mini.250x250.jpg" />
				[STORPROC News/Nouvelle/[!A::Id!]/Fichier|I]
					<img class="media-object" src="/[!I::URL!].mini.250x250.jpg" />
				[/STORPROC]
			</div>
		[/IF]
	</div>
[/STORPROC]
<div class="well">
	<a href="/[!Systeme::CurrentMenu::Url!]" class="btn btn-primary btn-block">Retour aux actualités</a>
</div>
