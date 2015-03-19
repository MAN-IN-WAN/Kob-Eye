[STORPROC [!Query!]|A|0|20|Date|DESC]
	<div class="row-fluid">
		<div class="[IF [!A::Image!]]span8[ELSE]span12[/IF] well">
			<h2>[DATE d.m.Y][!A::Date!][/DATE]<br />[!A::Titre!]</h2>
			<p>	[!A::Contenu!]</p>
			<a href="/[!Systeme::CurrentMenu::Url!]/[!A::Url!]" class="btn btn-primary  offset7 span4">Plus de détail</a>
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
