// fiche vendeur , page carrefour
[MODULE Systeme/Structure/Gauche]
<!--- contenu central -->
//CONSTRUCTION REQUETE
[!REQUETE:=Boutique!]
//GESTION DES CATEGORIES
[INFO [!Query!]|I]
//CONSTRUCTION REQUETE
[!REQUETE:=Boutique!]
//GESTION DES CATEGORIES
[STORPROC [!I::Historique!]|H|0|10]
	[IF [!H::DataSource!]!=Categorie]
		[!REQUETE+=/[!H::DataSource!]/[!H::Value!]!]
	[/IF]
[/STORPROC]
[STORPROC [!REQUETE!]|V|0|1][/STORPROC]
<div class="centre">
	// BLOCK CENTRAL
	<div class="ContenuPages">
		<div class="ligneCotePageG"><img src="/Skins/gamesavenue/Images/block_debut_ref.jpg"></div>
		<div class="ligneCotePageD"><img src="/Skins/gamesavenue/Images/block_fin_ref.jpg"></div>
		<div class="lignePageCentre" > 
			// BLOCK DE GAUCHE
			<div class="VendeurBlocGauche">
				<div class="BlockVendeur">
					<div class="blocVendeurImage">
						[IF [V::Avatar!]!=]
							<a href="/[!V::Avatar!].limit.1000x1000.jpg" title="[!V::Nom!]" class="mb"  rel="width:400,height:300"><img src="/[!V:Avatar!]" class="blocVendeurImage"></a>
						[ELSE]
							<img src="/Skins/gamesavenue/Images/defaut_avatar.jpg" class="blocVendeurImage"/>
						[/IF]
					</div>
					<div class="blocVendeurDescription">
						<span class="blocVendeurTitre" >
							[!V::Pseudonyme!]
						</span>
						<span class="blocVendeurtexte">
							[IF [!V::tmsCreate!]!=]<br/>Date inscription: &nbsp;&nbsp;<strong>[!Utils::getDate(d/m/Y,[!V::tmsCreate!])!]</strong>[/IF]
							[IF [!V::NoteMoyenne()!]!=]<br/>Note moyenne :&nbsp;&nbsp;<strong>[!V::NoteMoyenne()!]</strong>[/IF]
							[IF [!V::getNbVentes()!]!=]<br/>Nombre d'articles vendus :&nbsp;&nbsp;<strong>[!V::getNbVentes()!]</strong>[/IF]
							[IF [!V::Ville()!]!=]<br/>Localisation :&nbsp;&nbsp;<strong>[!V::Ville!]&nbsp;&nbsp;[!V::Pays!]</strong>[/IF]
						</span>
					</div>
				</div>
				<div class="BoutonsVendeur" >
					<div class="Bouton3colonnesVendeur">
						<b class="coinFinGrisbordertop">
							<b class="coinFinGris1">&nbsp;</b>
							<b class="coinFinGris2">&nbsp;</b>
							<b class="coinFinGris3">&nbsp;</b>
							<b class="coinFinGris4">&nbsp;</b>
						</b>
						<div class="coinFinGriscontent aumilieu btndessinescurrent">
							<a class="[IF [!Vue!]!=]btndessines[ELSE]btndessinescurrent blocambiance_color[/IF]"  href="/[!Lien!]">BOUTIQUE</a></div>
						<b class="coinFinGrisborderbottom">
							<b class="coinFinGris4">&nbsp;</b>
							<b class="coinFinGris3">&nbsp;</b><b class="coinFinGris2">&nbsp;</b><b class="coinFinGris1">&nbsp;</b>
						</b>
					</div>
					<div class="InterBoutonVendeur">&nbsp;</div>
					<div class="Bouton3colonnesVendeur">
						<b class="coinFinGrisbordertop">
							<b class="coinFinGris1">&nbsp;</b>
							<b class="coinFinGris2">&nbsp;</b>
							<b class="coinFinGris3">&nbsp;</b>
							<b class="coinFinGris4">&nbsp;</b>
						</b>
						<div class="coinFinGriscontent aumilieu">
							<a class="[IF [!Vue!]!=Evaluation]btndessines[ELSE]btndessinescurrent blocambiance_color[/IF]"  href="/[!Lien!]?Vue=Evaluation">
								EVALUATION
							</a>
						</div>
						<b class="coinFinGrisborderbottom">
							<b class="coinFinGris4">&nbsp;</b>
							<b class="coinFinGris3">&nbsp;</b><b class="coinFinGris2">&nbsp;</b><b class="coinFinGris1">&nbsp;</b>
						</b>
					</div>
					<div class="InterBoutonVendeur">&nbsp;</div>
					<div class="Bouton3colonnesVendeur">
						<b class="coinFinGrisbordertop">
							<b class="coinFinGris1">&nbsp;</b>
							<b class="coinFinGris2">&nbsp;</b>
							<b class="coinFinGris3">&nbsp;</b>
							<b class="coinFinGris4">&nbsp;</b>
						</b>
						<div class="coinFinGriscontent aumilieu">
							<a class="[IF [!Vue!]!=Details]btndessines[ELSE]btndessinescurrent blocambiance_color[/IF]"  href="/[!Lien!]?Vue=Details">Détails</a></div>
						<b class="coinFinGrisborderbottom">
							<b class="coinFinGris4">&nbsp;</b>
							<b class="coinFinGris3">&nbsp;</b><b class="coinFinGris2">&nbsp;</b><b class="coinFinGris1">&nbsp;</b>
						</b>
					</div>
				</div> // fin 3 boutons
				[SWITCH [!Vue!]|=]
					[CASE Evaluation][MODULE [!Query!]/Evaluations][/CASE]
					[CASE Details][MODULE [!Query!]/Details][/CASE]
					[DEFAULT][MODULE [!Query!]/Boutique][/DEFAULT]
				[/SWITCH]
				
			</div> // fin bloc gauche
			// BLOCK DE DROITE
			<div class="VendeurBlocDroite">
				<div class="bloccarregris"><p class="p10">
					//[MODULE Publicite/PubContenu]
				</p></div>
				[MODULE News/Colonne]			
			</div> // fin BLOCK DE DROITE
		</div>  // fin ligne centrale
	</div> // fin BLOCK CENTRE
</div>    // fin CONTENU COMPLET

