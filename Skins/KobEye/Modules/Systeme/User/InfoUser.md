[STORPROC [!Query!]|Usr|0|1]
	//Calcul du nombre de messaeg pour cet utilisateur
	[STORPROC Forum/Post/userCreate=[!Usr::Id!]|Po]
		[!NbMessage:=[!NbResult!]!]
	[/STORPROC]
	<div>
		<div style="width:150px;">
			[IF [!Po::userCreate!]==[!Systeme::User::Id!]]
				<a href="/Espace-perso" title="Mon Compte">
					[IF [!Usr::Avatar!]=]
						<img src="/Skins/KobEye/Img/Forum/TofUsr.gif" alt="Photo par d&eacute;faut"/>
					[ELSE]	
						<img src="/[!Usr::Avatar!].limit.100x100.jpg" alt="Photo de [!Usr::Nom!] [!Usr::Prenom!]" title="Photo de [!Usr::Nom!] [!Usr::Prenom!]" />
					[/IF]
				</a>
			[ELSE]
				<a href="/Profil/[!Usr::Id!]" title="Voir le profil de ce membre">
					[IF [!Usr::Avatar!]=]	
						<img src="/Skins/KobEye/Img/Forum/TofUsr.gif" alt="Photo par d&eacute;faut"/>
					[ELSE]	
						<img src="/[!Usr::Avatar!].limit.50x50.jpg" alt="Photo de [!Usr::Nom!] [!Usr::Prenom!]" title="Photo de [!Usr::Nom!] [!Usr::Prenom!]" />
					[/IF]
				</a>
			[/IF]
		</div>
		<div>
			[IF [!Po::userCreate!]==[!Systeme::User::Id!]]
				<a href="/Espace-perso" title="Mon Compte">[!Usr::Login!]</a> Inscrit depuis le : [UTIL NUMERICDATE][!Usr::tmsCreate!][/UTIL]<br />Messages : [!NbMessage!]<br />
				Pr&eacute;nom : [!Usr::Prenom!]<br />
				Nom : [!Usr::Nom!]<br />
				Ville : [!Usr::Ville!]<br />
				Pays : [!Usr::Pays!]<br />
			[ELSE]
				<a href="/Profil/[!Usr::Id!]" title="Voir le profil de ce membre">[!Usr::Nom!] [!Usr::Prenom!]</a>
			[/IF]
			//<p>Inscrit depuis le : [UTIL NUMERICDATE][!Usr::tmsCreate!][/UTIL]<br />Messages : [!NbMessage!]</p>
		</div>
	</div>
[/STORPROC]
