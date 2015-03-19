[IF [!valide!]=Proposer]
	[MODULE Blog/Commentaire/Ajouter]
[/IF]
[STORPROC [!Query!]|User|0|1]
	<div id="userinfo">
		<h1>[!User::Prenom!] [!User::Nom!]</h1>
		<div style="padding:5px;">
		<img src="/[!User::Avatar!].mini.150x150"/>
		<p>
			<span style="font-weight:bold;">Localisation : </span>[IF [!User::Ville!]!=""][!User::Ville!],[/IF][!User::Pays!]
		</p>
[STORPROC [!User::Proprietes!]|Prop|0|100]
		<p>
			[IF [!Prop::Nom!]!=Avatar]
			[IF [!Prop::Nom!]!=Message]
				[IF [!Prop::searchOrder!]=HE]
					//[!DEBUG::Prop!]
					<span style="font-weight:bold;">[!Prop::Titre!] : </span>[!Prop::Valeur!]
				[/IF]
			[/IF]
			[/IF]
		</p>
[/STORPROC]
		<div style="clear:left;"></div>
		<p>
			[!User::Message!]
		</p>
		</div>
	</div>
	[STORPROC Blog/Post/userCreate=[!User::Id!]|Post]
	[MODULE Blog/Post/[!Post::Id!]/Short]
	[NORESULT]Aucun billet ind&eacute;x&eacute; pour cette cat&eacute;gorie[/NORESULT]
	[/STORPROC]
[/STORPROC]