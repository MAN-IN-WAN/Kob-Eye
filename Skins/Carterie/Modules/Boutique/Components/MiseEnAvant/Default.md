// Module qui permet d'afficher une sélection
// Construction de la requete
[!Requete:=[!NOMMODULE!]/[!NOMTABLE!]!]
[!Requete+=/Actif=1!]
[IF [!LEFILTRE!]!=]
	[!Requete+=&&[!LEFILTRE!]!]
[/IF]

[!RequeteCount:=[!Requete!]!]
// SI ON PAGINE ON AFFICHE LES FLECHES POUR SE DEPLACER
//  SINON ON AFFICHE LE BLOC TEL QUEL
[COUNT [!RequeteCount!]|Total]
[IF [!Total!]>[!NBINFOS!]]
	[!OnPagine:=1!]
[/IF]
[IF [!LIMITAFFICHAGE!]=0||[!OnPagine!]!=1]
	[!Limit2:=[!Total!]!]
[ELSE]
	[!Limit2:=[!LIMITAFFICHAGE!]!]
[/IF]
[IF [!ORDRE!]!=]
	[IF [!SENS!]]
		[!LeSens:=ASC!]
	[ELSE]
		[!LeSens:=DESC!]
	[/IF]
[ELSE]
	[!ORDRE:=Id!]
[/IF]

[COUNT [!Requete!]|NbMeAv]
[IF [!NbMeAv!]>0]
	
	// Devise en cours
	[STORPROC Boutique/Devise/Defaut=1|De][/STORPROC]
	
	// CALCUL des hauteurs et largeur des blocs
	
	[!LgUneInfo:=[!LARGEURUNEINFO!]!]
	[!LgUneInfo+=[!INTERVALLE!]!]
	
	[!HgUneInfo:=[!HAUTEURUNEINFO!]!]
	[!HgUneInfo+=[!INTERVALLE!]!]
	
	[!LgAccroche:=[!LARGEURUNEINFO!]!]
	[!LgAccroche-=[!LARGEURUNEIMG!]!]
	[!LgAccroche-=5!]
	
	[IF [!Limit2!]=] [!Limit2:=1!][/IF]
	
	// calcul des valeurs en fonctions du sens de déplacement
	[!InitmarginPrec:=0!]
	[!InitmarginSuiv-=[!LARGEURUNEINFO!]!]
	
	[!InitmarginPrecS2:=0!]
	[!InitmarginSuivS2-=[!LgUneInfo!]!]
	
	[!lemargin:=margin-left!]
	[!LgNom:=[!LARGEURTITRE!]!]
		
	[!LgConteneurVisible:=[!NBINFOS!]!]
	[!LgConteneurVisible*=[!LgUneInfo!]!]
	[!LgConteneurVisible-=[!INTERVALLE!]!]
	
	[!LgConteneurTotal:=[!LIMITAFFICHAGE!]!]
		
	[!LgConteneurTotal*=[!LgUneInfo!]!]
	[!LgConteneurTotal-=[!INTERVALLE!]!]
	
	
	[!HgConteneurVisible:=[!HAUTEURUNEINFO!]!]
	[!HgConteneurTotal:=[!LIMITAFFICHAGE!]!]
	
	// affiche t'on le bloc d'achat
	[IF [!ACHATAFFICH!]][!BlocAfficheAchat:=block;!][ELSE][!BlocAfficheAchat:=none;!][/IF]
	<div class="EntoureComposant">
		<div class="[!NOMDIV!]">
			<div class="[IF [!BLOCAFFICH!] ]BlocTop[/IF]"></div>
			<div class="[IF [!BLOCAFFICH!] ]BlocLine[/IF]">
				<h2 class="TitreBloc">[!TITRE!]</h2>
				[!MarginLeft:=0!]
				<div class="ContenuVisible"  style="overflow:hidden;position:relative;width:[!LgConteneurVisible!]px;height:[!HgConteneurVisible!]px;">
					<div class="ContenuTotal" id="[!NOMDIV!]ladivadeplacer" style="height:[!HgConteneurTotal!]px;width:[!LgConteneurTotal!]px;" >
						[STORPROC [!Requete!]|Prod|0|[!Limit2!]|[!ORDRE!]|[!LeSens!]]
							[ORDER Id|RANDOM]
							[!Promo:=0!]
							[!Promo:=[!Prod::GetPromo!]!]
							<div class="AfficheInfo" style="float:left;position:relative;[IF [!Pos!]!=[!NbResult!]&&[!INTERVALLE!]!=]margin-right:[!INTERVALLE!]px;[/IF]">
								<a href="/[!Prod::getUrl()!]" >
									<div class="imgMEa" style="width:[!LARGEURUNEINFO!]px; ">
										[IF [!Promo!]!=0]<div class="PromoCat"></div>[/IF]
										<img src="[!Domaine!]/[IF [!Prod::Image!]!=][!Prod::Image!][ELSE]Skins/[!Systeme::Skin!]/Img/image_def.jpg[/IF].limit.[!LARGEURUNEIMG!]x[!HAUTEURUNEIMG!].jpg"   width="[!LARGEURUNEIMG!]" height="[!HAUTEURUNEIMG!]" alt="[!Prod::Nom!]" />
									</div>
									[IF [!TEXTEAFFICH!]!=0]
										<div class="Desc" style="width:[!LARGEURUNEINFO!]px; ">
											<div class="Prix" >
												[IF [!TEXTAPARTIR!]!=0]<div class="apartirde">[IF [!Prod::MultiTarif!]=1]à partir de[ELSE]&nbsp;[/IF]</div>[/IF]
												[!Math::PriceV([!Prod::getTarif!])!] [!De::Sigle!]
											</div>
											<div class="NomMea" style="width:[!LgNom!]px;padding-top:2px;">[!Prod::Nom!]</div>
											[IF [!TEXTEAFFICH!]!=0]
												<div class="Accroche" style="width:[!LgAccroche!]px;" >
													[SUBSTR 80][!Prod::[!CHAMPTEXTE!]!][/SUBSTR]
												</div>
											[/IF]
										</div>	
									[/IF]				
								</a>
								[IF [!OnPagine!]=1&&[!NBINFOS!]=1]

									<div class="pagination" style="width:[!LgConteneurVisible!]px;">
										[IF [!Pos!]>1]<a href="javascript:;" class="precedent" onclick="deplacediv2('[!lemargin!]','P',[!LgUneInfo!]);"  >Précédent</a>[/IF]
										[IF [!Pos!]!=[!NbResult!]]<a href="javascript:;" class="suivant"   onclick="deplacediv2('[!lemargin!]','S',[!LgUneInfo!]);" >Suivant</a>[/IF]
									</div>
								[/IF]
								[IF [!ACHATAFFICH!]]
									<div class="achat" style="display:[!BlocAfficheAchat!];width:[!LARGEURUNEINFO!]px;">
										[IF [!Prod::TypeProduit!]=3]
											//Produit unique 
											[STORPROC Boutique/Produit/[!Prod::Id!]/Reference|Re|0|1][/STORPROC]
											<form method="post" action="/Boutique/Commande/Etape1" name="achatcolonne">
												<input type="hidden" name="Qte" value="1">
												<input type="hidden" name="Reference" value="[!Re::Reference!]">
												<div class="ajoutpanier" ><input type="submit" value="Ajouter au Panier" /></div>
											</form>
												<div class="voirfiche" ><a href="/[!Prod::getUrl()!]" >Voir le détail</a></div>
										[ELSE]
											<div class="ajoutpanier" ><a href="/[!Prod::getUrl()!]">Acheter produit</a></div>
											<div class="voirfiche" ><a href="/[!Prod::getUrl()!]" >Voir le détail</a></div>
	
										[/IF]
										
										
									</div>	
								[/IF]

							</div>
							// a chaque info ajout du margin
							[IF [!Pos!]>1][!InitmarginPrec-=[!InitmarginSuiv!]!][/IF]
							[IF [!Pos!]!=[!NbResult!][!InitmarginSuiv-=[!LARGEURUNEINFO!]!][/IF]
							[/ORDER]

						[/STORPROC]
					</div>
				</div>
				[IF [!OnPagine!]=1&&[!NBINFOS!]>1]
		
					<div class="pagination" style="width:[!LgConteneurVisible!]px;">
						<a href="javascript:;" class="precedent" onclick="deplacediv2('[!lemargin!]','P',[!LgUneInfo!]);"  >Précédent</a>
						<a href="javascript:;" class="suivant"   onclick="deplacediv2('[!lemargin!]','S',[!LgUneInfo!]);" >Suivant</a>
					</div>
				[/IF]
			</div>	
			<div class="[IF [!BLOCAFFICH!] ]BlocBottom[/IF]"></div>
		</div>	
	</div>	
	
[/IF]


// Surcouche JS
<script type="text/javascript">

	var marginMEA = 0;
	var indiceMEA = 0;
	var limitMEA =[IF [!Total!]<[!LIMITAFFICHAGE!]][!Total!][ELSE][!LIMITAFFICHAGE!][/IF];

	function deplacediv2(lemargin, lechoix,largeurinfo) {
		// fonction pour déplacer quand il y a plusieurs blocks affichés
		if (lechoix=='P' && indiceMEA>0) {
			marginMEA += largeurinfo;
			indiceMEA--;

		}
		if (lechoix=='S' && indiceMEA<limitMEA-[!NBINFOS!] ) {
			 marginMEA -= largeurinfo;
			indiceMEA++;
		}

		//$('ladivadeplacer').tween(lemargin, marginMEA+'px'); 
  		$("#[!NOMDIV!]ladivadeplacer").animate({ marginLeft:  marginMEA+"px" }, 500 );


	}

</script>
	
