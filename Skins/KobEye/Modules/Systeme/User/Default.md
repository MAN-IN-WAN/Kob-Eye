[MODULE Systeme/Ariane]
[MODULE Systeme/Structure/Droite]
<div id="Milieu">
	[STORPROC [!Query!]|Mbr]
		<h1 class="TitreCat">[!Mbr::Prenom!] [!Mbr::Nom!]</h1>
		<div class="LigneUsr">
			<div class="TofProfil">
				[IF [!Mbr::Photo!]!=]
					<img src="/[!Mbr::Photo!].limit.300x500.jpg" alt="Photo de [!Mbr::Nom!] [!Mbr::Prenom!]" title="Photo de [!Mbr::Nom!] [!Mbr::Prenom!]" />
				[ELSE]
					<img src="/Skins/Intranet/Img/TofUsr.gif" alt="Photo par d&eacute;faut" title="Photo par d&eacute;faut" />
				[/IF]
				<div class="FLeft">
					<p><span>Poste :</span> [!Mbr::Fonction!]</p>
					<p><span>E-mail :</span> <a href="mailto:[!Mbr::Mail!]">[!Mbr::Mail!]</a></p>
					[IF [!Mbr::Commentaire!]!=]
						<p><span style="display:block;">Commentaire :</span> [!Mbr::Commentaire!]</p>
					[/IF]
					[COUNT Forum/Post/userCreate=[!Mbr::Id!]|NbMsg]
					[IF [!NbMsg!]>0]
						<p><span>Messages sur le forum :</span> [!NbMsg!]</p>
					[/IF]
					[BLOC Bouton]
						<a href="/Membres" title="Retour &agrave; l'annuaire">Retour &agrave; l'annuaire</a>
					[/BLOC]
				</div>
			</div>
		</div>
		//Calcul du nombre de message pour cet utilisateur
		[COUNT Forum/Post/userCreate=[!Mbr::Id!]|NbMsg]
		[IF [!NbMsg!]]
			<h2 style="background:none;">Les derniers messages de [!Mbr::Nom!] [!Mbr::Prenom!]</h2>
			<table class="TableSuj">
				<thead>
					<tr>
						<td>Th&egrave;me</td>
						<td>Sujet</td>
						<td>Message</td>
						<td>Post&eacute; le</td>
					</tr>
				</thead>
				<tbody>
					[STORPROC Forum/Post/userCreate=[!Mbr::Id!]|Msg|0|10]
						[STORPROC Forum/Sujet/Post/[!Msg::Id!]|Suj]
							[STORPROC Forum/Categorie/Sujet/[!Suj::Id!]|Cat][/STORPROC]
						[/STORPROC]
						<tr>
							<td><a href="/Forum-Fidu/[!Cat::Url!]" title="Acc&egrave;s au th&egrave;me"><span class="Bold">[!Cat::Nom!]</span></a></td>
							<td><a href="/Forum-Fidu/[!Cat::Url!]/Sujet/[!Suj::Url!]" title="Acc&egrave;s au sujet" class="TitreSuj">[!Suj::Titre!]</a></td>
							<td>[!Msg::Contenu!]</td>
							<td>[UTIL FULLDATEFR][!Msg::tmsCreate!][/UTIL] &agrave; [UTIL HOUR][!Msg::tmsCreate!][/UTIL]</td>
						</tr>
					[/STORPROC]
				</tbody>
			</table>
		[/IF]
	[/STORPROC]
</div>
<div class="Clear"></div>