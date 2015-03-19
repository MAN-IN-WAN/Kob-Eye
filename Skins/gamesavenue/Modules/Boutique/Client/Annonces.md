// -- Gestion de la pagination
[IF [!Page!]=EMPTY]
	[!Page:=1!]
[/IF]
[!TypeEnf:=Annonces!]
//Definition des elements a afficher
[IF [!PagePos!]!=]
	[!Page[!TypeEnf!]:=[!PagePos!]!]
[ELSE]
	[!Page[!TypeEnf!]:=1!]
[/IF]
[!MaxLine:=20!] // nb d'annonces à afficher// -- Gestion de la pagination
//Definition des elements a afficher
[IF [!PagePos!]!=]
	[!Page[!TypeEnf!]:=[!PagePos!]!]
[ELSE]
	[!Page[!TypeEnf!]:=1!]
[/IF]
[!LigneResultats:=0!]
// recherche du vendeur
[STORPROC Boutique/Client/UserId=[!Systeme::User::Id!]|V|0|1|][/STORPROC]
// fiche vendeur , page carrefour
[MODULE Systeme/Structure/Gauche]
[!I_Error:=!]
//  LA GESTION D'ERREUR
[IF [!I_Valider!]!=||[!I_Confirm!]!=]
	[IF ![!I_Confirm!]]
		[!NbSupp:=0!]
		<div class="topp10">
			<form name="confirmation">
				[STORPROC [!I_Suppr!]|S]
					[!NbSupp+=1!]
					<input type="hidden" name="I_Suppr2[]" value="[!S!]">
				[/STORPROC]
				[IF [!NbSupp!]!=0]
					<div class="topp10" style="overflow:hidden">
						<div class="MonCompte">Confirmez vous la suppression des annonces :</div>
						<div class="topp10"> 
							[STORPROC [!I_Suppr!]|S]
								[STORPROC Boutique/Reference/[!S!]|SC]
									[!SC::Reference!]<br>
								[/STORPROC]
							[/STORPROC]
						</div>
						<div class="btnRouge" >
							<div class="btnRougeGauche"></div>
							<div class="btnRougeCentre">
								<input type="submit" name="I_Confirm" value="Je Confirme" class="btnRougeCentre" />
							</div>
							<div class="btnRougeDroite"></div>
						</div>
					</div>
				[/IF]
			</form>
		</div>
	[/IF]
	[IF [!I_Confirm!]!=]
		[STORPROC [!I_Suppr2!]|S]
			[STORPROC Boutique/Reference/[!S!]|D]
				[!D::Delete!]
			[/STORPROC]
		[/STORPROC]	
	
	[/IF]
[/IF]
<div class="centre">
	<div class="MonCompte"><h1>MES ANNONCES</h1></div>
	<form action="">
		// Bloc de selection 
		<div class="ligneSelectGris" style="height:30px;">
			<div class="ligneSelectGrisCoteG"><img src="/Skins/gamesavenue/Images/gauchebarreproduits.jpg"></div>
			<div class="ligneSelectGrisCoteD" ><img src="/Skins/gamesavenue/Images/droitebarreproduits.jpg"></div>
			<div class="ligneSelectGrisCentre" style="overflow:hidden; padding-top:2px;">
				<div class="ligneSelectGrisLeftElements" style="padding-left:5px;width:48%;border-right:1px dashed #c4c4c4;">
					Trier par <select name="Tri" class="selectfin">
						<option value="1">Date</option>
						<option value="2">Prix</option>
						<option value="3">N° Annonce</option>
					</select>
				</div>
				<div class="ligneSelectGrisLeftElements" style="width:48%;overflow:hidden;height:30px;">
					<div class="btnRouge" style="text-align:center">
						<div class="btnRougeGauche"></div>
						<div class="btnRougeCentre"><img src="/Skins/gamesavenue/Images/plusimg.png">
							<input type="hidden" name="I_Formulaire" id="I_Formulaire" value="OK" />
							<a href="/Mon_Compte/Nouvelle_Annonce" class="lienBlancMaj11" ><input type="submit" name="send" value="Saisir une annonce" class="btnRougeCentre"/></a>
						</div>
						<div class="btnRougeDroite"></div>
					</div>
				</div>
			</div>
			<div class="ligneSelectGrisCote" ><img src="/Skins/gamesavenue/Images/droitebarreproduits.jpg"></div>
		</div>
		// LISTE DES ANNONCES
		[COUNT Boutique/Client/[!V::Id!]/Reference|NbRep]
		[IF [!NbRep!]>0]
			//On compte le nombre total d element a affciher
			[!TotalPage:=[!NbRep:/[!MaxLine!]!]!]
			//On calcule le nombre total de page
			[IF [!TotalPage!]>[!Math::Floor([!TotalPage!])!]]
				//On arrondit au chiffre superieur le nombre total de page
				[!TotalPage:=[![!Math::Floor([!TotalPage!])!]:+1!]!]
			[/IF]
		[/IF]
		//PAGINATION
		[IF [!TotalPage!]>1]
			<div class="LignePagination topp10">
				<span class="ResultatPagination"><span>
						// Retour à la première page
						<a href="/[!Lien!]?Pos=1" /><img src="/skins/gamesavenue/Images/dble-fleche-gauche.png"></a>
					</span><span>
						// Page précédente
						<a href="/[!Lien!]?PagePos=[IF [!Page[!TypeEnf!]:-1!]<1]1[ELSE][!Page[!TypeEnf!]:-1!][/IF]" /><img src="/skins/gamesavenue/Images/fleche-gauche.png"></a>
					</span><span>
						// Aller à une page précise
						[STORPROC [!TotalPage!]|Pag]
							// Page 1
							[IF [!Pos!]=1]
								<span class="current[IF [!Page[!TypeEnf!]!]=1] blocambiance_color[/IF]">[!Pos!]</span>
							[/IF]
							// ...
							[IF [!Pos!]=1&&[!Page[!TypeEnf!]!]>3]...[/IF]
							// Page n-1
							[IF [!Pos!]=[!Page[!TypeEnf!]:-1!]&&[!Page[!TypeEnf!]!]>2]
								<span class="current">[!Pos!]</span>
							[/IF]
							// Page courante
							[IF [!Pos!]=[!Page[!TypeEnf!]!]&&[!Page[!TypeEnf!]!]>1]
								<span class="current blocambiance_color">[!Pos!]</span>
							[/IF]
							// Page n+1
							[IF [!Pos!]=[!Page[!TypeEnf!]:+1!]&&[!Page[!TypeEnf!]!]<[!TotalPage:-1!]]
								<span class="current">[!Pos!]</span>
							[/IF]
							// ...
							[IF [!Pos!]=[!TotalPage!]&&[!Page[!TypeEnf!]!]<[!TotalPage:-2!]]...[/IF]
							// Page n-1
							// Dernière page
							[IF [!Pos!]=[!TotalPage!]&&[!Page[!TypeEnf!]!]!=[!TotalPage!]]
								<span class="current">[!Pos!]</span>
							[/IF]
							// [IF [!Pos!]!=[!Page[!TypeEnf!]!]]
							// 	<a href="/[!Lien!]?PagePos=[!Pos!]" class="LesPages" />[!Pos!]</a>
							// [ELSE]
							//	<span class="current blocambiance_color">[!Page[!TypeEnf!]!]</span>
							// [/IF]
						[/STORPROC]
					</span><span>
						// Page suivante
						<a href="/[!Lien!]?PagePos=[IF [!Page[!TypeEnf!]:+1!]>[!TotalPage!]][!TotalPage!][ELSE][!Page[!TypeEnf!]:+1!][/IF]"/><img src="/skins/gamesavenue/Images/fleche-droite.png"></a>
					</span><span>
						// Dernière Page
						<a href="/[!Lien!]?PagePos=[!TotalPage!]"/ ><img src="/skins/gamesavenue/Images/dble-fleche-droite.png"></a>
					</span>
				</span>
			</div>
		[/IF]
		<div class="topp10">
			<table cellspacing="0" cellspading="0"  class="tableEval">
				<tr class="tableEval" cellspacing="0" cellspading="0">
					<td class="tableEvalEnteteCote" >
						<img src="/Skins/gamesavenue/Images/bando-vendeur-gauche.png">
					</td>
					<td class="tableEvalEntete" style="text-align:left;width:280px">Titre</td>
					<td class="tableEvalEntete">N° Annonce</td>
					<td class="tableEvalEntete">Date mise en ligne</td>
					<td class="tableEvalEntete">Prix</td>
					<td class="tableEvalEntete"  ><img src="/skins/gamesavenue/Images/modifier.png"></td>
					<td class="tableEvalEntete"><img src="/skins/gamesavenue/Images/activer.png"></td>
					<td class="tableEvalEntete"  style="border:none;"><img src="/skins/gamesavenue/Images/supprimer.png"></td>
					<td class="tableEvalEnteteCote" style="border:none;">
						<img src="/Skins/gamesavenue/Images/bando-vendeur-droite.png">
					</td>
				</tr>
			[STORPROC  Boutique/Client/[!V::Id!]/Reference|R|[![!Page[!TypeEnf!]:-1!]:*[!MaxLine!]!]|[!MaxLine!]|tmsCreate|DESC]
				[IF [!I_Valider!]!=]
					[!EstPresent:=0!]
					[STORPROC [!I_Activ!]|A][IF [!A!]=[!R::Id!]][!EstPresent:=1!][/IF][/STORPROC]
					[IF [!R::Actif!]=1&&[!EstPresent!]=0]
						[METHOD R|Set]
							[PARAM]Actif[/PARAM]
							[PARAM]0[/PARAM]
						[/METHOD]
						[METHOD R|Save][/METHOD]
					[/IF]
					[IF [!R::Actif!]=0&&[!EstPresent!]=1]
						[METHOD R|Set]
							[PARAM]Actif[/PARAM]
							[PARAM]1[/PARAM]
						[/METHOD]
						[METHOD R|Save][/METHOD]
					[/IF]
				[/IF]

				[STORPROC Boutique/Produit/Reference/[!R::Id!]|P][/STORPROC]
				[IF [!Utils::isPair([!LigneResultats!])!]]
					<tr class="tableEval" cellspacing="0" cellspading="0">
				[ELSE]
					<tr class="tableEval" cellspacing="0" cellspading="0" style="background:#ebebeb;">
				[/IF]
					<td class="tableEvalContenuCoteLeft"></td>
					<td class="tableEvalContenu">[!P::Nom!]</td>
					<td class="tableEvalContenu"  style="text-align:center">[!R::Reference!]</td>
					<td class="tableEvalContenu"  style="text-align:center">[!Utils::getDate(d/m/Y,[!R::tmsCreate!])!]</td>
					<td class="tableEvalContenu" style="text-align:right">[!R::Tarif!]</td>
					<td class="tableEvalContenu"  style="text-align:center"><a href="/Boutique/Reference/[!R::Id!]/Modifier" class="lienBlancMaj11" ><img src="/skins/gamesavenue/Images/btn-vert.png"></td>
					<td class="tableEvalContenu" style="text-align:center;">
						<input type="checkbox" [IF [!R::Actif!]=1]checked="checked"[/IF] name="I_Activ[]" value="[!R::Id!]"
					</td>
					<td class="tableEvalContenu" style="border:none;padding-right:none;text-align:center;">
						<input type="checkbox" [STORPROC [!I_Suppr!]|SD][IF [!SD!]=[!R::Id!] ]checked ="checked"[/IF][/STORPROC] name="I_Suppr[]" value="[!R::Id!]"
					</td>
					<td class="tableEvalContenuCoteRight"></td>
				</tr>
				[!LigneResultats+=1!]
			[/STORPROC]
			</table>
			<b class="coinFinGrisborderbottom">
				<b class="coinFinGris4">&nbsp;</b>
				<b class="coinFinGris3">&nbsp;</b>
				<b class="coinFinGris2">&nbsp;</b>
				<b class="coinFinGris1">&nbsp;</b>
			</b>
	
		</div>
		[IF [!NbRep!]>0]
			<div class="topp10" style="overflow:hidden">
				<div class="btnRouge" >
					<div class="btnRougeGauche"></div>
					<div class="btnRougeCentre">
						<input type="submit" name="I_Valider" value="Valider" class="btnRougeCentre" />
					</div>
					<div class="btnRougeDroite"></div>
				</div>
			</div>
		[/IF]
		//PAGINATION
		[IF [!TotalPage!]>1]
			<div class="LignePagination topp10">
				<span class="ResultatPagination"><span>
						// Retour à la première page
						<a href="/[!Lien!]?Pos=1" /><img src="/skins/gamesavenue/Images/dble-fleche-gauche.png"></a>
					</span><span>
						// Page précédente
						<a href="/[!Lien!]?PagePos=[IF [!Page[!TypeEnf!]:-1!]<1]1[ELSE][!Page[!TypeEnf!]:-1!][/IF]" /><img src="/skins/gamesavenue/Images/fleche-gauche.png"></a>
					</span><span>
						// Aller à une page précise
						[STORPROC [!TotalPage!]|Pag]
							// Page 1
							[IF [!Pos!]=1]
								<span class="current[IF [!Page[!TypeEnf!]!]=1] blocambiance_color[/IF]">[!Pos!]</span>
							[/IF]
							// ...
							[IF [!Pos!]=1&&[!Page[!TypeEnf!]!]>3]...[/IF]
							// Page n-1
							[IF [!Pos!]=[!Page[!TypeEnf!]:-1!]&&[!Page[!TypeEnf!]!]>2]
								<span class="current">[!Pos!]</span>
							[/IF]
							// Page courante
							[IF [!Pos!]=[!Page[!TypeEnf!]!]&&[!Page[!TypeEnf!]!]>1]
								<span class="current blocambiance_color">[!Pos!]</span>
							[/IF]
							// Page n+1
							[IF [!Pos!]=[!Page[!TypeEnf!]:+1!]&&[!Page[!TypeEnf!]!]<[!TotalPage:-1!]]
								<span class="current">[!Pos!]</span>
							[/IF]
							// ...
							[IF [!Pos!]=[!TotalPage!]&&[!Page[!TypeEnf!]!]<[!TotalPage:-2!]]...[/IF]
							// Page n-1
							// Dernière page
							[IF [!Pos!]=[!TotalPage!]&&[!Page[!TypeEnf!]!]!=[!TotalPage!]]
								<span class="current">[!Pos!]</span>
							[/IF]
							// [IF [!Pos!]!=[!Page[!TypeEnf!]!]]
							// 	<a href="/[!Lien!]?PagePos=[!Pos!]" class="LesPages" />[!Pos!]</a>
							// [ELSE]
							//	<span class="current blocambiance_color">[!Page[!TypeEnf!]!]</span>
							// [/IF]
						[/STORPROC]
					</span><span>
						// Page suivante
						<a href="/[!Lien!]?PagePos=[IF [!Page[!TypeEnf!]:+1!]>[!TotalPage!]][!TotalPage!][ELSE][!Page[!TypeEnf!]:+1!][/IF]"/><img src="/skins/gamesavenue/Images/fleche-droite.png"></a>
					</span><span>
						// Dernière Page
						<a href="/[!Lien!]?PagePos=[!TotalPage!]"/ ><img src="/skins/gamesavenue/Images/dble-fleche-droite.png"></a>
					</span>
				</span>
			</div>
		[/IF]
	</form>

</div>