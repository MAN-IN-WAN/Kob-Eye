[STORPROC [!Query!]|Prod|0|1][/STORPROC]
[!Promo:=0!]
[!Promo:=[!Prod::GetPromo!]!]
// --> pour le produit à plusieurs références uniques 
//      --> c'est dans listereferences.md que c'est référence est renvoyée 
<div class="BlocFichTitre">
	<div class="TitreArticle">
		<h1>[!Prod::Nom!]</h1>
	</div>
</div>
<div class="BlocFichContent" >
	<div class="BlocAcroche">[!Prod::Accroche!]</div>
	<div class="BlocFichPrix">
		// Gestion visibilité à partir de
		[IF [!Prod::MultiTarif!]=1]<div class="APartirPrixDansFiche" id="tarifvisible" >à partir de</div>[/IF]
		<div class="PrixDansFiche" id="tarif">[!Math::PriceV([!Prod::getTarif!])!][!De::Sigle!]</div>
		[IF [!Promo!]!=0]
			<div style="display:block;color:#01ACCF;font-size:14px;position:absolute;right:10px;text-decoration:line-through;top:44px;" id="tarifNonPromo">[!Math::PriceV([!Prod::getTarifHorsPromo!])!][!De::Sigle!]</div>
		[/IF]
	</div>
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
	[IF [!Prod::TypeProduit!]=2&&[!Prod::StockReference!]>0]
		[!LaPos:=0!]
		[STORPROC Boutique/Produit/[!Prod::Id!]/Attribut/TypeAttribut=1|Att|||Ordre|ASC]
			[STORPROC Boutique/Attribut/[!Att::Id!]/Declinaison|Decli]
				<div class="BlocFichDeclinaisons">
					<div class="BlocFichDeclinaisonsLibelle">[IF [!Att::NomPublic!]=][!Att::Nom!][ELSE][!Att::NomPublic!][/IF] </div>
					<div class="BlocFichDeclinaisonsLibelle">
						<select name="P[!Prod::Id!]A[!Att::Id!]" class="AttributTexte CalculPrix" onchange="VerifieSelection();" >
							<option value="-1">Sélectionnez une valeur</option>
							[LIMIT 0|100]
								[COUNT [!Query!]/Reference/Declinaison.DeclinaisonId([!Decli::Id!])&&Quantite>0&&Tarif>0|Rdec]
								[IF [!Rdec!]>0]
									[!LaPos+=1!]
									<option value="[!Decli::Id!]"  >[IF [!Decli::NomPublic!]=][!Decli::Nom!][ELSE][!Decli::NomPublic!][/IF]</option>
								[/IF]
							[/LIMIT]
						</select>
					</div>
				</div>
			[/STORPROC]
			[NORESULT]
				// Pas d'attribut donc on prend la référence directement
				[STORPROC Boutique/Produit/[!Prod::Id!]/Reference|Ref|0|1]
					<script type="text/javascript">
						window.addEvent('domready', function() {
							$('Reference').value = '[!Ref::Reference!]';
							$('IdReference').value = '[!Ref::Id!]';
							$('StockReference').value = '[!Ref::Quantite!]';
						});
					</script>
				[/STORPROC]
			[/NORESULT]
		[/STORPROC]
		[!LaPos:=0!]
		[STORPROC Boutique/Produit/[!Prod::Id!]/Attribut/TypeAttribut=2|Att|||Ordre|ASC]
			[STORPROC Boutique/Attribut/[!Att::Id!]/Declinaison|Decli]
				<div class="BlocFichDeclinaisons">
					<div class="BlocFichDeclinaisonsLibelle">[!Att::NomPublic!]</div>
					<div class="BlocFichDeclinaisonsLibelle" style="margin-left:0;">
						<div class="AttributGraphiqueTout ">
							[LIMIT 0|100]
								[COUNT [!Query!]/Reference/Declinaison.DeclinaisonId([!Decli::Id!])&&Quantite>0&&Tarif>0|Rdec]
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
							[/LIMIT]
		
						</div>
					</div>
				</div>

			[/STORPROC]
		[/STORPROC]
	[/IF]	
	[IF [!Prod::StockReference!]>0]
		<div class="EspaceVente">
			[IF [!Prod::TypeProduit!]!=1&&[!Prod::StockReference!]>0]
				<div class="AchatHaut" >
					<input type="submit" class="btnPanier" id="AchatAjouterPanier" value="Ajouter au panier"   />
				</div>
			[/IF]
			[IF [!Prod::TypeProduit!]=2]
				<div style="padding-top:4px" >
					<div class="FichLibelle LibQte" >Quantite</div>
					<div class="FichQuantite">
						<input name="Qte" id="Qte" value="1" size="2" onchange="VerifieSelection();" />
					</div>
					<div class="LesBoutons">
						<input type="button" class="InputBtnMoins" value="-" onclick="CalculQte(-1);">
						<input type="button" class="InputBtnPlus" value="+" onclick="CalculQte(+1);"> 
					</div>
					<input type="hidden" name="StockReference" id="StockReference" value="">
				</div>
			[/IF]
	
			[IF [!Prod::TypeProduit!]!=3]
				<input type="hidden" name="Reference" id="Reference" value="">
				<input type="hidden" name="IdReference" id="IdReference"value="" >
			[/IF]
	
			[IF [!Prod::TypeProduit!]=3]
				[STORPROC Boutique/Produit/[!Prod::Id!]/Reference|Re|0|1][/STORPROC]
					<input type="hidden" name="Qte" id="Qte" value="1" size="2"  />
					<input type="hidden" name="Reference" value="[!Re::Reference!]" >
					// ajout md en vu de chgt pour travailler sur id, non fait dans les class
					<input type="hidden" name="IdReference" value="[!Re::Id!]" >
			[/IF]
	
		</div>

	[/IF]
	
</div>



// Surcouche JS
<script type="text/javascript">

	window.addEvent("domready", function () { 
		
		$('FicheProduit').addEvent("submit", function(e) {
			//VerifieSelection();
			// on vérifie qu'on a sélectionné le produit que l'on voulait acheté
			var sel = $$('.CalculPrix');
			var req = {};
			var initI=0;
			//On va chercher tous les combos et radios d'attributs
			sel.each(function (item,index){
				var attribut = item.get('name');
				var valeurattribut = -1;
				var attributclass = item.get('class');
				initI=1;	
		
				switch (item.get('type')){
					case "radio":
						if (item.get('checked')){
							
							valeurattribut = item.get('value');
							//On stocke les informations dans le tableau de requete
							req[attribut] = valeurattribut;
						}
					break;
					default:
						valeurattribut = item.options[item.selectedIndex].value;
						//On stocke les informations dans le tableau de requete
						req[attribut] = valeurattribut;
					break;
				}
			});
			//On boucle sur req si une valeur est égale à -1 on sort
			for (var i in req){
				if (req[i]==-1) {
					alert("Merci de faire votre sélection avant d'ajouter au panier ");
					new Event(e).stop();
					return;
				}
			};
			if (!(i)&& initI==1) {
				alert("Merci de faire votre sélection avant d'ajouter au panier ");
				new Event(e).stop();
				return;
			}
			

		});

	});


	function VerifieSelection () {
		//initialisation
		var sel = $$('.CalculPrix');
		var req = {};

		//On va chercher tous les combos et radios d'attributs
		sel.each(function (item,index){
			var attribut = item.get('name');
			var valeurattribut = -1;
			var attributclass = item.get('class');
			switch (item.get('type')){
				case "radio":
					if (item.get('checked')){
						valeurattribut = item.get('value');
						//On stocke les informations dans le tableau de requete
						req[attribut] = valeurattribut;
					}
				break;
				default:

					valeurattribut = item.options[item.selectedIndex].value;
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
		req.quantite = $('Qte').value;

		// Desactive le bouton ajouter au panier tant qu'on a pas le retour JSON
		if($('AchatAjouterPanier') != null){
			$('AchatAjouterPanier').addClass('Disabled');
			$('AchatAjouterPanier').set('disabled','disabled');
		}

		//On execute la requete
		var r = new Request.JSON({
			url:'/Boutique/Produit/[!Prod::Id!]/getTarif.json',
			data:req,
			onSuccess: function (json,text){
				//mettre à jour le champ tarif
				$('tarif').set('html',json.price+' €');
				if($('promo')==1) $('tarifNonPromo').setStyle('display', 'block');
				else {
					if($('tarifNonPromo') != null) $('tarifNonPromo').setStyle('display', 'none');
				}
				if($('tarifvisible') != null) $('tarifvisible').setStyle('display', 'none');
				$('Reference').value=json.reference;
				$('StockReference').value=json.StockReference;

				//reactive le bouton ajouter au panier
				if($('AchatAjouterPanier') != null){
					 $('AchatAjouterPanier').removeClass('Disabled');
					 $('AchatAjouterPanier').disabled="";
				}
			},

			onError: function (error){
				alert('probleme de connexion');
			}
		}).send();
	}


	function CalculQte(PlusMoins) {
		var Quantite= parseInt($('Qte').value);
		var total= Quantite+parseFloat(PlusMoins);
		if (total<=$('StockReference').value) {
			$('Qte').value=total;
		}else {
			alert("Quantité en stock atteinte");
		}

		if (total < 1) $('Qte').value=1;

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