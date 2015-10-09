// affichage des references vendues
// en fait on fera afficher les attributs et leur déclinaison en balayant les liens à partir de la table produit
// on grisera les choix pour les déclinaisons avec stock à 0 ou pas de ref à vendre
// pour voie romaine on n'a pas besoin des attributs car un produit vendu = une reference vendu
//          la reference ici ne sert qu'à gérer le stock 
//TEMPLATES affichage fiche produit
// initialisation

// Devise en cours


// CALCUL des hauteurs et largeur des blocs
[!FICH_INTERVALLE:=10!]
[!FICH_LARGEURUNEINFO:=55!]
[!FICH_NBINFOS:=4!]

[!FICH_LgUneInfo:=[!FICH_LARGEURUNEINFO!]!]
[!FICH_LgUneInfo+=[!FICH_INTERVALLE!]!]
[!FICH_HgUneInfo:=[!FICH_LARGEURUNEINFO!]!]
[!FICH_HgUneInfo+=20!]
	
[!FICH_LgConteneurVisible:=[!FICH_NBINFOS!]!]
[!FICH_LgConteneurVisible*=[!FICH_LgUneInfo!]!]
[!FICH_LgConteneurVisible-=[!FICH_INTERVALLE!]!]

[IF [!Qte!]][ELSE][!Qte:=1!][/IF]

<div class="FicheProduit">
	[STORPROC [!Query!]|Prod|0|1]
		<form method="post" action="/[!Lien!]" name="achat">
			[!AVendre:=[!Prod::CheckStock!]!]
			[STORPROC Boutique/Produit/[!Prod::Id!]/Donnee/TypeCaracteristique=Bouteille|B|0|1][/STORPROC]

			//recup du prix
			[!TxTva:=[!Prod::TypeTva!]!]
			[!Promotion:=[!Prod::GetPromo()!]!]
			[!NbUnite:=1!]
			[!PrixNormal:=[!Prod::Tarif!]!]
			[!PrixNormal*=[!NbUnite!]!]

			[!PrixPromo:=[!PrixNormal!]!]
			[!PromoUnit:=0!]

			[IF [!Promotion!]]
				[!EnPromo:=1!]
				[!Reduction:=[!Promotion::GetNiveauReduction([!NbUnite!]],[!Prod::Tarif!])!]!]
				[!PrixPromo:=[!Promotion::GetTarifPromo([!NbUnite!],[!Prod::Tarif!])!]!]
				[!LePrix:=[!Utils::getMontantTTC([!PrixPromo!],[!TxTva!])!]!]
				[!PromoUnit:=[!Promotion::APartirNbUnite!]!]
			[ELSE]
				[!EnPromo:=0!]
				[!LePrix:=[!Utils::getMontantTTC([!PrixNormal!],[!TxTva!])!]!]
			[/IF]
			[!Arr_Prix:=[!Prod::DecoupePrix([!LePrix!])!]!]	
			[COUNT Boutique/Produit/[!Prod::Id!]/Donnee/Type=Image|NbImg]
			[IF [!Prod::Image!]!=][!NbImg+=1!][/IF]

			[!FICH_LIMITAFFICHAGE:=[!NbImg!]!]
			[!FICH_LgConteneurTotal:=[!FICH_LIMITAFFICHAGE!]!]
			[!FICH_LgConteneurTotal*=[!FICH_LgUneInfo!]!]
			[!FICH_LgConteneurTotal-=[!FICH_INTERVALLE!]!]


			<div class="BlocFichProduit">
				<div class="BlocFichImage" style="height:360px;overflow:hidden">
					[IF [!Promotion!]]<div class="PromoProduit"></div>[/IF]
					<div id="FICH_imgEc" style="overflow:hidden"><a class="mb" href="/[!Prod::Image!].limit.800x600.jpg" title="[!Prod::Nom!]" >
						<img src="/[IF [!Prod::Image!]!=][!Prod::Image!].mini.295x281.jpg[ELSE]Skins/[!Systeme::Skin!]/Img/image_def.jpg[/IF]" alt="[!Prod::Nom!]" title="[!Prod::Nom!]"  />
					</a></div>
					[IF [!NbImg!]>0]
						<div class="ContenuVisible"  style="overflow:hidden;position:absolute; bottom:0; right: 0; left: 0;height:60px">
							<div class="ContenuDeplace">
								<div class="ContenuTotal" id="FICH_ladivadeplacer" style="overflow:hidden;width:[!FICH_LgConteneurTotal!]px;" >
									[STORPROC Boutique/Produit/[!Prod::Id!]/Donnee/Type=Image|Img|||Ordre|ASC]
										<div class="AfficheInfo" style="cursor:pointer;float:left;position:relative;[IF [!Pos!]!=[!NbResult!]]margin-right:[!FICH_INTERVALLE!]px;[/IF]">
											<a href="javascript:;" >
												<img src="[!Domaine!]/[!Img::Fichier!].mini.53x53.jpg" alt="[!Prod::Nom!]" title="[!Prod::Nom!]" onclick="return apercu('[!Domaine!]/[!Img::Fichier!].mini.295x281.jpg','[IF [!Img::Titre!]=][!Prod::Nom!]!][ELSE][!Img::Titre!][/IF]','[!Domaine!]/[!Img::Fichier!].mini.800x600.jpg')" style="border:1px solid #747476"/>
											</a>
										</div>
									[/STORPROC]
									<div class="AfficheInfo" style="cursor:pointer;float:left;position:relative;">
										<a href="javascript:;" >
											<img src="[!Domaine!]/[!Prod::Image!].mini.53x53.jpg" alt="[!Prod::Nom!]" title="[!Prod::Nom!]" onclick="return apercu('[!Domaine!]/[!Prod::Image!].mini.295x281.jpg','[!Prod::Nom!]','[!Domaine!]/[!Prod::Image!].mini.800x600.jpg')" style="border:1px solid #747476" />
										</a>
									</div>
								</div>
							</div>
							[IF [!NbImg!]>3]
								<a href="javascript:;" class="precedent" onclick="FICH_deplacediv('P',[!FICH_LgUneInfo!]);"  ></a>
								<a href="javascript:;" class="suivant"   onclick="FICH_deplacediv('S',[!FICH_LgUneInfo!]);" ></a>
							[/IF]
						</div>
					[/IF]
				</div>
				<div class="BlocFichContenu" >
					<div class="BlocFichTitre">
						<div class="TitreArticle">
							<h1 style="padding-bottom:10px;" >[!Prod::Nom!]</h1>
						</div>
					</div>
					<div class="BlocFichContent" >
						<div class="BlocAcroche">[!Prod::Accroche!]</div>
						[IF [!LePrix!]>0]
							<div class="BlocFichPrix">	
								<div class="APartirPrixDansFiche">à partir de</div>
								<div class="PrixDansFiche">[!Arr_Prix::Entier!],[!Arr_Prix::2Decimale!][!CurrentDevise::Sigle!]</div>
							</div>
						[/IF]
						<div class="BlocFichCaracteristique" >	
							[STORPROC Boutique/Produit/[!Prod::Id!]/Donnee/Type=Caracteristique|CAR|||Ordre|ASC]
								<div class="BlocFichLigneCaract">
									<span class="FichLibelle" >[!CAR::TypeCaracteristique!] : </span><br />
									<span class="FichValeurCaract">[!CAR::Valeur!]</span>
								</div>
							[/STORPROC]
						</div>
						[IF [!AVendre!]>0||[!Prod::StockZeroDepublier!]=0]
							<div class="EspaceVente">

								<div class="FichLibelle LibQte" >Quantite</div>
								<div class="FichQuantite">
									<input name="Qte" id="Qte" value="1" size="2" onchange="CalculQte(0,[!NbUnite!],[!PrixNormal!],0,[!TxTva!],[!PromoUnit!],[!PrixPromo!],[!EnPromo!]);"  />
								</div>
								<div class="LesBoutons">
									<input type="button" class="InputBtnMoins" value="-" onclick="CalculQte(-1,[!NbUnite!],[!PrixNormal!],0,[!TxTva!],[!PromoUnit!],[!PrixPromo!],[!EnPromo!]);">
									<input type="button" class="InputBtnPlus" value="+" onclick="CalculQte(+1,[!NbUnite!],[!PrixNormal!],0,[!TxTva!],[!PromoUnit!],[!PrixPromo!],[!EnPromo!]);"> 
								</div>
								<input type="hidden" name="RefProduit" value="[!Prod::Reference!]">
								<div class="Achat">
									<input type="submit" class="btnPanier" value="Ajouter au panier" />
								</div>
							</div>
						[/IF]
						
						<div class="EspacefbtwLv" style="position:relative; height:20px;">

							// Facebook
							<div style="position:absolute; left:0; top: 0">
								<iframe src="http://www.facebook.com/plugins/like.php?href=[!Domaine!]/[!Lien!]&amp;layout=button_count&amp;show_faces=false&amp;width=90&amp;action=like&amp;font=arial&amp;colorscheme=light&amp;height=20" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:90px; height:20px" allowTransparency="true"></iframe>
							</div>

							// Google
							<div style="position:absolute; left:90px; top: 3px">
								<script type="text/javascript">document.write('<g:plusone size="small"></g:plusone>')</script>
								<script type="text/javascript" src="https://apis.google.com/js/plusone.js">{lang: 'fr'}</script>
							</div>

							// Twitter
							<div style="position:absolute; left:145px; top: 0">
								<a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal" data-via="InfoWebMaster">Tweet</a>
								<script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
							</div>

							// Envoyer à un ami
							<div style="position:absolute; left:247px; top: 0">
								<a class="SocialEA" href="[!Domaine!]/Envoyer-a-un-ami?C_Lien=[!Lien!]">Envoyer à un ami</a>
							</div>

						</div>
					</div>
				</div>
			</div>
			<div class="BlocFichProduitDescription">
				[IF [!AVendre!]<1&&[!Prod::StockZeroDepublier!]=0]
					<div class="BlocFichContenu">Message attention produit en cours de réapprovisionnement le délai de livraison sera plus long, pour plus de renseignement sur ce délai n'hésitez pas à nous contacter ...</div>
				[ELSE]
					[IF [!AVendre!]<1]
					<div class="BlocFichContenu">Produit momentanément épuisé, n'hésitez pas revisiter cette fiche d'ici quelques jours...</div>[/IF]
				[/IF]
				<div class="BlocFichContenu">[!Prod::Description!]</div>
				// Retour à la liste
				<div class="BlocFichContenu"><a href="/[!Prod::getUrl()!]" class="RetourListe">Retour à la liste des produits</a></div>
			</div>
		</form>
	[/STORPROC]

</div>

// Surcouche JS
<script type="text/javascript">


	var FICH_marginMEA = 0;
	var FICH_indiceMEA = 0;
	var FICH_limitMEA =[IF [!NbImg!]<[!FICH_LIMITAFFICHAGE!]][!NbImg!][ELSE][!FICH_LIMITAFFICHAGE!][/IF];


	function FICH_deplacediv(lechoix,largeurinfo) {
		// fonction pour déplacer quand il y a plusieurs blocks affichés
		if (lechoix=='P' && FICH_indiceMEA>0) {
			FICH_marginMEA += largeurinfo;
			FICH_indiceMEA--;
		}
		if (lechoix=='S' && FICH_indiceMEA<FICH_limitMEA-[!FICH_NBINFOS!] ) {
			FICH_marginMEA -= largeurinfo;
			FICH_indiceMEA++;
		}

		$('FICH_ladivadeplacer').tween('margin-left', FICH_marginMEA+'px'); 
	
	}
	function FICH_afficheimage(limage) {
		$('FICH_imgEc').src=limage;

	}


	// Clic sur miniature
	function apercu(img,legende, zhref) {
		var lien = new Element('a', {
			'href': zhref,
			'title': legende,
			'class': 'mb'
		});
		var image = new Element('img', {
			'src': img,
			'alt': legende,
			'title': legende
		}).inject(lien);

		$('FICH_imgEc').empty();

		lien.inject($('FICH_imgEc'));

		var initMultiBox = new multiBox({
				mbClass: '.mb',
				container: $(document.body),
				descClassName: 'multiBoxDesc',
				useOverlay: true,
				maxSize: {w:800, h:600},
				addRollover: true
			});
		return false;
	}
	// Clic sur apercu
	function openModal(lien) {
		SqueezeBox.fromElement(lien);
		return false;
	}


	function CalculQte(PlusMoins,QteMini,PrixNormalHT,SurcoutHT,TxTva,QtePromoMini,PrixPromoHT,EnPromo) {
		var Quantite= parseInt($('Qte').value);
		var total= Quantite+parseFloat(PlusMoins);

		if (total < 1) $('Qte').value=1;
		else $('Qte').value=total;

		var total= $('Qte').value*QteMini;



		if (EnPromo) {
			$('TotalPrixBarre').innerHTML = (total * (PrixNormalHT * ( 1 + TxTva/100 ))).toFixed(2);  // 2 chiffres apres la virgule
			if (total >=QtePromoMini) $('PrixBarre').setStyle('display', 'block');

			if (total <QtePromoMini) $('PrixBarre').setStyle('display', 'none');
			if (total >=QtePromoMini) {
				$('totalVente').innerHTML = (total * (PrixPromoHT * ( 1 + TxTva/100 ))).toFixed(2);  // 2 chiffres apres la virgule
				if ($('Qte').value>1) {
					 $('TotalPrixPromo').setStyle('display', 'block');
					$('TotalPrixUnitaire').setStyle('display', 'none');
				}
			} else {
				$('totalVente').innerHTML = (total * (PrixNormalHT * ( 1 + TxTva/100 ))).toFixed(2);  // 2 chiffres apres la virgule
				if ($('Qte').value>1) {
					$('TotalPrixUnitaire').setStyle('display', 'block');
					$('TotalPrixPromo').setStyle('display', 'none');
				}
			}

		} else {
			$('totalVente').innerHTML = (total * (PrixNormalHT * ( 1 + TxTva/100 ))).toFixed(2);  // 2 chiffres apres la virgule
			$('TotalPrixPromo').setStyle('display', 'none');
		}
		
		if ($('Qte').value==1)  {
			$('TotalPrixUnitaire').setStyle('display', 'none');
			$('TotalPrixPromo').setStyle('display', 'none');
		}

		
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


