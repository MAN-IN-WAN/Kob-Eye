[STORPROC [!Query!]|Prod|0|1][/STORPROC]
[!Promo:=0!]
[!Promo:=[!Prod::GetPromo!]!]
// --> pour le produit à plusieurs références uniques 
//      --> c'est dans listereferences.md que cette référence est renvoyée 
<div class="BlocFichTitre">
</div>
<div class="BlocFichContent">
	<div class="BlocFichPrix">
		// Gestion visibilité à partir de
		<div class="PrixDansFiche" id="tarif">[!Math::PriceV([!P::getTarif!])!] [!CurrentDevise::Sigle!]</div>
		[IF [!Promo!]!=0&&[!Promo!]!=]
			<div style="display:block;color:#01ACCF;font-size:14px;position:absolute;right:10px;text-decoration:line-through;top:44px;" id="tarifNonPromo">[!Math::PriceV([!Prod::getTarifHorsPromo!])!][!CurrentDevise::Sigle!]</div>
		[/IF]
	</div>
		<p>[!P::Description!]</p>
	<div class="BlocFichCaracteristique" >
		[STORPROC Boutique/Produit/[!Prod::Id!]/Donnee/Type=Caracteristique|CAR|||Ordre|ASC]
			[LIMIT 0|100]
				<div class="BlocFichLigneCaract">
					<span class="FichLibelle" >[!CAR::TypeCaracteristique!] : </span><br />
					<span class="FichValeurCaract">[!CAR::Valeur!]</span>
				</div>
			[/LIMIT]
		[/STORPROC]
	</div>
	[SWITCH [!Prod::TypeProduit!]|=]
		[CASE 1]
			//******************************
			// Cas produit reference unique 
			//******************************
			
		[/CASE]
		[CASE 2]
			//******************************
			// Cas produit reference declinées
			//******************************
			[IF [!Prod::StockReference!]>0]
				[!LaPos:=0!]
				[STORPROC Boutique/Produit/[!Prod::Id!]/Attribut|Att|||Ordre|ASC]
					<div class="BlocFichDeclinaisons">
						<div class="BlocFichDeclinaisonsLibelle">[IF [!Att::NomPublic!]=][!Att::Nom!][ELSE][!Att::NomPublic!][/IF] </div>
						<div class="BlocFichDeclinaisonsLibelle">
							[LIMIT 0|100]
								[SWITCH [!Att::TypeAttribut!]|=]
									[CASE 1]
										//Type attribut texte
										<select name="P[!Prod::Id!]A[!Att::Id!]" class="AttributTexte CalculPrix" onchange="VerifieSelection();" >
											<option value="-1">Sélectionnez une valeur</option>
											[STORPROC Boutique/Attribut/[!Att::Id!]/Declinaison|Decli]
												[COUNT Boutique/Produit/[!Prod::Id!]/Reference/Declinaison.DeclinaisonId([!Decli::Id!])&&Quantite>0&&Tarif>0|Rdec]
												[IF [!Rdec!]>0]
													[!LaPos+=1!]
													<option value="[!Decli::Id!]"  >[IF [!Decli::NomPublic!]=][!Decli::Nom!][ELSE][!Decli::NomPublic!][/IF]</option>
												[/IF]
											[/STORPROC]
										</select>
									[/CASE]
									[CASE 2]
										[STORPROC Boutique/Attribut/[!Att::Id!]/Declinaison|Decli]
											//Type attribut graphique
											[COUNT Boutique/Produit/[!Prod::Id!]/Reference/Declinaison.DeclinaisonId([!Decli::Id!])&&Quantite>0&&Tarif>0|Rdec]
											[IF [!Rdec!]>0]
												[!LaPos+=1!]
												<div class="AttributGraphique ">
													<div class="AttributGraphiqueNom">[IF [!Decli::NomPublic!]=][!Decli::Nom!][ELSE][!Decli::NomPublic!][/IF]</div>
													<div class="AttributGraphiqueImg">
														<a class="mb" href="[!Domaine!]/[IF [!Decli::Image!]!=][!Decli::Image!][ELSE]Skins/[!Systeme::Skin!]/Img/image_def.jpg[/IF].limit.560x533.jpg" style="margin:0;" title="[!Decli::NomPublic!]" ><img src="[!Domaine!]/[IF [!Decli::Image!]!=][!Decli::Image!].mini.53x49.jpg[ELSE]Skins/[!Systeme::Skin!]/Img/image_def.jpg.mini.53x49.jpg[/IF]" /></a>
													</div>
			
													<div class="AttributGraphiqueChoix"><input type="radio" name="P[!Prod::Id!]A[!Att::Id!]"  value="[!Decli::Id!]"  id="A[!Att::Id!]D[!Decli::Id!]" class="CalculPrix" onchange="VerifieSelection();" />
			// on ne change plus l'image principal quand on clique sur une déclinaison
			//onchange="VerifieSelection();return apercu('[!Domaine!]/[!Decli::Image!].mini.295x281.jpg','[UTIL SANSCOTEESPACE][!Decli::NomPublic!][/UTIL]','[!Domaine!]/[!Decli::Image!]');
													</div>
												</div>
											[/IF]
										[/STORPROC]
									[/CASE]
								[/SWITCH]
							[/LIMIT]
						</div>
					</div>
					
					[NORESULT]
						// Pas d'attribut donc on prend la référence directement
						[STORPROC Boutique/Produit/[!Prod::Id!]/Reference|Re|0|1][/STORPROC]
						<input type="hidden" name="Reference" value="[!Re::Reference!]" >
						<input type="hidden" name="StockAvailable" value="1" >
						<input type="hidden" name="IdReference" value="[!Re::Id!]" >
					[/NORESULT]
				[/STORPROC]
			[/IF]
		[/CASE]
		[CASE 3]
			[IF [!Prod::StockReference!]>0]
				//******************************
				// Cas produit unique
				//******************************
				[STORPROC Boutique/Produit/[!Prod::Id!]/Reference|Re|0|1][/STORPROC]
				<input type="hidden" name="Reference" value="[!Re::Reference!]" >
				<input type="hidden" name="IdReference" value="[!Re::Id!]" >
				<input type="hidden" name="StockAvailable" value="1" >
			[/IF]
		[/CASE]
	[/SWITCH]
	
	
	
	//******************************
	// AFFICHAGE PANIER + QUANTITE
	//******************************
	[IF [!Prod::StockReference!]>0]
		<div class="EspaceVente">
			[IF [!Prod::TypeProduit!]=2]
				<div class="GestionQuantite"  >
					<div class="FichLibelle LibQte" >Quantité</div>
					<div class="FichQuantite">
						<input name="Qte" id="Qte" value="1" size="2" onchange="VerifieSelection();" class="input-small" />
					</div>
					<div class="LesBoutons">
						<input type="button" class="InputBtnMoins" value="-" onclick="CalculQte(-1);">
						<input type="button" class="InputBtnPlus" value="+" onclick="CalculQte(+1);">
					</div>
				</div>
			[/IF]
			[IF [!Prod::TypeProduit!]!=1&&[!Prod::StockReference!]>0]
				<button type="submit" class="btnPanier btn btn-success" id="AchatAjouterPanier" value="Ajouter au panier" >Ajouter au panier<span class="IconePanier"></span></button>
				<div class="libelleTtc">Tous nos prix sont TTC</div>
			[/IF]
		</div>

	[/IF]

</div>



// Surcouche JS
<script type="text/javascript">

	$(document).ready(function () {
		$('#FicheProduit').submit(function(e) {
			//VerifieSelection();
			// on vérifie qu'on a sélectionné le produit que l'on voulait acheté
			var sel = $('.CalculPrix');
			var req = {};
			var initI=0;
			//On va chercher tous les combos et radios d'attributs
			$(sel).each(function (index,item){
				var attribut = $(item).get('name');
				var valeurattribut = -1;
				var attributclass = $(item).get('class');
				initI=1;	

				switch ($(item).get('type')){
					case "radio":
						if ($(item).get('checked')){
							
							valeurattribut = $(item).get('value');
							//On stocke les informations dans le tableau de requete
							req[attribut] = valeurattribut;
						}
					break;
					default:
						valeurattribut = $(item).val();
						//On stocke les informations dans le tableau de requete
						req[attribut] = valeurattribut;
					break;
				}
			});
			//On boucle sur req si une valeur est égale à -1 on sort
			for (var i in req){
				if (req[i]==-1) {
					toastr.warning("Merci de faire votre sélection avant d'ajouter au panier ");
					e.preventDefault();
					return;
				}
			};
			if (!(i)&& initI==1) {
				toastr.warning("Merci de faire votre sélection avant d'ajouter au panier ");
				e.preventDefault();
				return;
			}


		});

	});


	function VerifieSelection () {
		//initialisation
		var sel = $('.CalculPrix');
		var req = {};

		//On va chercher tous les combos et radios d'attributs
		sel.each(function (index,item){
			var attribut = $(item).attr('name');
			var valeurattribut = -1;
			var attributclass = $(item).attr('class');
			switch ($(item).attr('type')){
				case "radio":
					if ($(item).attr('checked')){
						valeurattribut = $(item).attr('value');
						//On stocke les informations dans le tableau de requete
						req[attribut] = valeurattribut;
					}
				break;
				default:
					valeurattribut = $(item).val();
					//On stocke les informations dans le tableau de requete
					req[attribut] = valeurattribut;
				break;
			}
		});
		//On boucle sur req si une valeur est égale à -1 on sort
		for (var i in req){
			if (req[i]==-1)return;
		};

		//On va chercher la quantite
		req.quantite = $('#Qte').val();

		// Desactive le bouton ajouter au panier tant qu'on a pas le retour JSON
		if($('#AchatAjouterPanier') != null){
			$('#AchatAjouterPanier').addClass('Disabled');
			$('#AchatAjouterPanier').attr('disabled','disabled');
		}

		//On execute la requete
		var r = $.getJSON('/Boutique/Produit/[!Prod::Id!]/getTarif.json',req)
			.fail(function (){
				toastr.error('probleme de connexion');
			})
			.done (function(json){
				//mettre à jour le champ tarif
				$('#tarif').html(json.price+' €');
				if($('#promo')==1) $('#tarifNonPromo').css('display', 'block');
				else {
					if($('#tarifNonPromo') != null) $('#tarifNonPromo').css('display', 'none');
				}
				if($('#tarifvisible') != null) $('#tarifvisible').css('display', 'none');
				$('#Reference').val(json.reference);
				
				//reactive le bouton ajouter au panier
				if($('#AchatAjouterPanier') != null && parseInt(json.StockAvailable)==1){
					$('#AchatAjouterPanier').unbind('click');
					 $('#AchatAjouterPanier').removeClass('Disabled');
					 $('#AchatAjouterPanier').removeAttr("disabled");
				}else if ($('#AchatAjouterPanier') != null){
					//on supprime tout evenement de click
					$('#AchatAjouterPanier').unbind('click');
					 $('#AchatAjouterPanier').removeAttr("disabled");
					$('#AchatAjouterPanier').click(function (e){
						e.preventDefault();
						toastr.error('stock insuffisant pour ce produit.');
					});
					
				}
		});	
	}


	function CalculQte(PlusMoins) {
		var Quantite= parseInt($('#Qte').val());
		var total= Quantite+parseFloat(PlusMoins);
		$('#Qte').val(total);
		if (total < 1) $('#Qte').val(1);
		VerifieSelection ();
	}



	// Gestion des onglets
//	window.addEvent("domready", function () { 
//		var onglets = $$('.ongletBloc');
//		var ongletsContenu = $$('.ongletDesc');
//		$('toutonglet').setStyle('display', 'block');
//		var rang=0;
//		ongletsContenu.each( function(contenu) {
//			if (rang!=0)contenu.setStyle('display', 'none');
//			rang++;
//		});
//		onglets.each(function(onglet) {
//			onglet.addEvent("click", function() { 
//				onglets.each( function(onglet) {
 //					onglet.removeClass('onglet_actif');
//				});
//				ongletsContenu.each( function(contenu) {
 //					contenu.setStyle('display', 'none');
//				});
//				this.addClass('onglet_actif');
//				var ladiv = 'ongletDesc' +this.id.substring(8);
//				$(ladiv).setStyle('display', 'block');
//			});
//		 });
//	});
</script>