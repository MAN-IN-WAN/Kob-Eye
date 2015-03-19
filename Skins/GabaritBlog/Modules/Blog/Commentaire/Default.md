<div class="getcomments">
	[STORPROC [!Query!]/Actif=1|Comm|0|100]
		[!unComm:=[!Comm::Id!]!]
		//[STORPROC Blog/Auteur/Commentaire/[!Comm::Id!]|Aut][/STORPROC]
		<div class="onecomment">
			[IF [!Comm::Site!]!=]
				<h2>[!Comm::Titre!] par <a href="http://[!Comm::Site!]" title="Site de [!Aut::Nom!]" onclick="window.open(this.href); return false;">[!Aut::Nom!]</a> (Le [UTIL FULLDATEFR][!Comm::tmsCreate!][/UTIL] &agrave; [UTIL HOUR][!Comm::tmsCreate!][/UTIL])</h2>
			[ELSE]
				<h2>[!Comm::Titre!] par [!Aut::Nom!] (Le [UTIL FULLDATEFR][!Comm::tmsCreate!][/UTIL] &agrave; [UTIL HOUR][!Comm::tmsCreate!][/UTIL])</h2>
			[/IF]
			<div class="content">[UTIL BBCODE][!Comm::Contenu!][/UTIL]
			</div>
		</div>
	[/STORPROC]
</div>
//[MODULE Blog/Commentaire/Ajouter]

