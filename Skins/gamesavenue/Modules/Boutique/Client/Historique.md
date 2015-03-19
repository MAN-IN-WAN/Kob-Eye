// Historique des annonces
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
<div class="centre">
	<div class="MonCompte"><h1>HISTORIQUE DES ACHATS ET DES VENTES</h1></div>
	<form action="">
		// Bloc de selection 
		<div class="ligneSelectGris" style="height:30px;">
			<div class="ligneSelectGrisCoteG"><img src="/Skins/gamesavenue/Images/gauchebarreproduits.jpg"></div>
			<div class="ligneSelectGrisCoteD" ><img src="/Skins/gamesavenue/Images/droitebarreproduits.jpg"></div>
			<div class="ligneSelectGrisCentre"  style="overflow:hidden; padding-top:2px;">
				<div class="ligneSelectGrisLeftElements" style="padding-left:5px;width:47%;border-right:1px dashed #c4c4c4;">
					Trier par <select name="Tri" class="selectfin">
						<option value="1">Date</option>
						<option value="2">Prix</option>
						<option value="3">N° Annonce</option>
					</select>
				</div>
				<div class="ligneSelectGrisLeftElements"  style="float:right;padding-left:5px;width:47%;height:30px;">
					Afficher <select name="Type" class="selectfin">
						<option value="">Tout Afficher</option>
						<option value="1">Vente</option>
						<option value="2">Achat</option>
					</select>
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
					<td class="tableEvalEntete" style="text-align:left;width:20px">N°Produit</td>
					<td class="tableEvalEntete" colspan="2">Jeux</td>
					<td class="tableEvalEntete">N° Annonce</td>
					<td class="tableEvalEntete">Date mise en ligne</td>
					<td class="tableEvalEntete">Prix</td>
					<td class="tableEvalEntete"  >Vente</td>
					<td class="tableEvalEntete" style="border:none;">Achat</td>
					<td class="tableEvalEnteteCote" style="border:none;">
						<img src="/Skins/gamesavenue/Images/bando-vendeur-droite.png">
					</td>
				</tr>
			[STORPROC  Boutique/Client/[!V::Id!]/Reference|R|[![!Page[!TypeEnf!]:-1!]:*[!MaxLine!]!]|[!MaxLine!]|tmsCreate|DESC]
				[STORPROC Boutique/Produit/Reference/[!R::Id!]|P][/STORPROC]
				[IF [!Utils::isPair([!LigneResultats!])!]]
					<tr class="tableEval" cellspacing="0" cellspading="0">
				[ELSE]
					<tr class="tableEval" cellspacing="0" cellspading="0" style="background:#ebebeb;">
				[/IF]
					<td class="tableEvalContenuCoteLeft"></td>
					<td class="tableEvalContenu">[!P::Reference!]</td>
					<td class="tableEvalContenu" style="text-align:center;width:50px"><img src="/[!P::Image!]" class="img_liste"></td>
					<td class="tableEvalContenu" style="text-align:left;">[!P::Nom!]</td>
					<td class="tableEvalContenu"  style="text-align:center">[!R::Reference!]</td>
					<td class="tableEvalContenu"  style="text-align:center"  style="text-align:center;width:50px">[!Utils::getDate(d/m/Y,[!R::tmsCreate!])!]</td>
					<td class="tableEvalContenu" style="text-align:right;width:50px">[!R::Tarif!]</td>
					<td class="tableEvalContenu"  style="text-align:center;width:50px;"></td>
					<td class="tableEvalContenu"  style="text-align:center;width:50px;border:none;"><img src="/skins/gamesavenue/Images/achat.png"></td>
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