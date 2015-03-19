<form action="" method="post" name="rech" class="FormRech">
	<div id="Arbo">
		[BLOC Panneau]
				[BLOC Rounded|background-color:#9A9EA0;color:#FFFFFF;|margin-bottom:5px;]
					Importation dossier
				[/BLOC]
				S&eacute;lectionnez un ou plusieurs dossier contenant directement des images ainf de les importer dans ce slip Book.
				Le titre sera le nom du fichier par d&eacute;faut.
				- Il est possible de v&eacute;rifier l'existence du fichier afin d'&eacute;viter les doublons.
		[/BLOC]
	</div>
	<div id="Data">
		[STORPROC [!Query!]|C|0|1][/STORPROC]
		[BLOC Panneau]
			[IF [!TYarggla!]=Importer]
				<div style="overflow:auto;position:absolute;top:30px;bottom:30px;width:100%;background-color:white;">
				[STORPROC [!Doss!]|D]
					[BLOC Rounded|background-color:#9A9EA0;color:#FFFFFF;|margin-bottom:5px;]Importation de [!D!][/BLOC]
					//Importation des images
					<ul>
					[STORPROC Explorateur/_Dossier/Home/Flipbook/[!D!]/_Fichier|F|||Nom|ASC]
						<li>[!F::Nom!] - [!F::Type!]</li>
						[OBJ Flipbook|Page|Im]
						[METHOD Im|Set][PARAM]Titre[/PARAM][PARAM][!F::Mod!][/PARAM][/METHOD]
						[METHOD Im|Set][PARAM]Image[/PARAM][PARAM]Home/Flipbook/[!D!]/[!F::Nom!][/PARAM][/METHOD]
						[METHOD Im|AddParent][PARAM]Flipbook/Book/[!C::Id!][/PARAM][/METHOD]
						[METHOD Im|Save][/METHOD]
					[/STORPROC]
					</ul>
				[/STORPROC]
				</div>
				<h1></h1>
				<a href="/[!Query!]">Retour &agrave; la cat&eacute;gorie  [!C::Titre!] -  Fichiers import&eacute;s avec succ&eacute;s</a>
			[ELSE]
				[BLOC Rounded|background-color:#9A9EA0;color:#FFFFFF;|margin-bottom:5px;]Importation dossier, contenu de Home/Flipbook.
				[/BLOC]
				<div style="overflow:auto;position:absolute;top:30px;bottom:30px;width:100%;background-color:white;">
				<ul>
				[STORPROC Explorateur/_Dossier/Home/Flipbook/_Dossier|Dos]
					<li><input type="checkbox" value="[!Dos::Nom!]" name="Doss[]"/>[!Dos::Nom!]</li>
					<ul>
					[STORPROC Explorateur/_Dossier/Home/Flipbook/[!Dos::Nom!]/_Dossier|Dos2]
						<li><input type="checkbox" value="[!Dos::Nom!]/[!Dos2::Nom!]"  name="Doss[]"/>[!Dos2::Nom!]</li>
						<ul>
						[STORPROC Explorateur/_Dossier/Home/Flipbook/[!Dos::Nom!]/[!Dos2::Nom!]/_Dossier|Dos3]
							<li><input type="checkbox" value="[!Dos::Nom!]/[!Dos2::Nom!]/[!Dos3::Nom!]" name="Doss[]" />[!Dos3::Nom!]</li>
							<ul>
							[STORPROC Explorateur/_Dossier/Home/Flipbook/[!Dos::Nom!]/[!Dos2::Nom!]/[!Dos3::Nom!]/_Dossier|Dos4]
								<li><input type="checkbox" value="[!Dos::Nom!]/[!Dos2::Nom!]/[!Dos3::Nom!]/[!Dos4::Nom!]" name="Doss[]" />[!Dos4::Nom!]</li>
								<ul>
								[STORPROC Explorateur/_Dossier/Home/Flipbook/[!Dos::Nom!]/[!Dos2::Nom!]/[!Dos3::Nom!]/[!Dos4::Nom!]/_Dossier|Dos5]
									<li><input type="checkbox" value="[!Dos::Nom!]/[!Dos2::Nom!]/[!Dos3::Nom!]/[!Dos4::Nom!]/[!Dos5::Nom!]" name="Doss[]" />[!Dos5::Nom!]</li>
									
								[/STORPROC]
								</ul>
							[/STORPROC]
							</ul>
						[/STORPROC]
						</ul>
					[/STORPROC]
					</ul>
				[/STORPROC]
				</ul>
				</div>
				<div class="Enregistrer" style="position:absolute;bottom:0;"><INPUT TYPE="SUBMIT"  class="BoutonBlanc" VALUE="Importer" name="TYarggla"></div>
			[/IF]
		[/BLOC]
	</div>
</form>

