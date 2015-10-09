// Affichage des choix des packs
[STORPROC [!Query!]|P|0|1][/STORPROC]
//<input id="tarifDepart" value="[!P::getTarif()!]" type="hidden" />
<div class="BlocChoixPackListe" >
	[STORPROC Boutique/Produit/[!P::Id!]/ConfigPack|Cpk|||Ordre|ASC]
		[!Total:=0!]
		[COUNT Boutique/ConfigPack/[!Cpk::Id!]/Reference/Actif=1|Total]
		[COUNT Boutique/ConfigPack/[!Cpk::Id!]/Options|TotalOpt]
		[IF [!TotalOpt!]]
			<div class="Optionsbtn"> 
				<a href="#"  class="btn [IF [!Cpk::ChoixObligatoire!]=1]btn-kirigami[ELSE]btn-fushia[/IF] btn-pack col-md-12"  style="white-space: normal;" onclick="AfficheMasqueOption([!Cpk::Id!]);" id="blochoixR-[!Cpk::Id!]">[!Cpk::Nom!]</a>
				<a href="#"  class="btn btn-gris-kirigami btn-pack col-md-12"  style="white-space: normal;display:none;" onclick="AfficheMasqueOption([!Cpk::Id!]);" id="blochoixG-[!Cpk::Id!]">[!Cpk::Nom!]</a>
			</div>
			// ce pack est lié aux options et non au reference
			<div id="lesoptions-[!Cpk::Id!]" style="display:none;">
				[STORPROC Boutique/ConfigPack/[!Cpk::Id!]/Options|Opt|||Ordre|ASC]
					<div class="row uneOptionFiche[!Pos!]"><div class="col-md-12" >
						[IF [!Opt::Commentaires!]!=]
							<div class="BlocFichDeclinaisons">
								<div class="BlocFichDeclinaisonsLibelle"><p>[!Opt::Commentaires!]</p></div>
							</div>
						[/IF]
						<div class="BlocFichDeclinaisons" style="margin:5px 0;">
							[SWITCH [!Opt::TypeOptions!]|=]
								[CASE 1]
									[!Limitation:=50!]
									[IF [!Opt::LimiteCar!]!=][!Limitation:=[!Opt::LimiteCar!]!][/IF]
									<textarea id="OptionsChoix-[!Cpk::Id!]-[!Opt::Id!]" class="col-md-12 [IF [!Opt::LimiteCar!]=] texteDecli[/IF]"  onkeyup="javascript:LimitationTexteSaisi(this, [!Limitation!]);" style="[IF [!Limitation!]!=][!Hgt:=[!Limitation!]!][!Hgt*=.9!]height:[!Hgt!]px;[/IF]max-height:150px;"></textarea>
									<script type="text/javascript">
										function LimitationTexteSaisi(lechamp,longeurchamp){
											if (lechamp.value.length >= longeurchamp) {
												lechamp.value = lechamp.value.substring(0, longeurchamp);
												alert('Votre texte ne doit pas dépasser '+longeurchamp+' caractères! ');
											}
										}
									</script>
	
								[/CASE]
								[CASE 4]
									<select id="OptionsChoix-[!Cpk::Id!]-[!Opt::Id!]" class="col-md-12">
										[STORPROC Boutique/Options/[!Opt::Id!]/OptionsDetails|SdOpt|||Ordre|ASC]
											<option value="[!SdOpt::Id!]">[!SdOpt::Nom!]</option>
										[/STORPROC]
									</select>
								[/CASE]
								[CASE 5]
									<div class="row"><div class="col-md-12">
										[STORPROC Boutique/Options/[!Opt::Id!]/OptionsDetails|SdOpt|||Ordre|ASC]
											<div class="pull-left">
												<div class="AttributGraphiqueNom">[!SdOpt::Nom!]</div>
												<div class="AttributGraphiqueImg">
													[IF [!SdOpt::Image!]!=]
														[!Taille:=130x120!]
														[IF [!SdOpt::Largeur!]!=][!Taille:=[!SdOpt::Largeur!]!][/IF]
													<a href="[!Domaine!]/[!SdOpt::Image!]" rel="shadowbox;" ><img src="[!Domaine!]/[!SdOpt::Image!].mini.[!Taille!].jpg" class="img-responsive img-thumbnail" /></a>[/IF]
												</div>
												<div class="AttributGraphiqueChoix" style="text-align:center;">
													<input type="radio" name="OptionsChoix-[!Cpk::Id!]-[!Opt::Id!]"  value="[!SdOpt::Id!]"  class="OptionsChoix-[!Cpk::Id!]-[!Opt::Id!]"  />
												</div>
											</div>
		
										[/STORPROC]
									</div></div>
								[/CASE]
							[/SWITCH]
						</div>
					</div></div>
				[/STORPROC]
				<input type="hidden" name="config[[!Cpk::Id!]]" id="PackChoix-[!Cpk::Id!]" value="" class="PackChoix">
				<div id="PackChoixOpt-[!Cpk::Id!]"></div>
			</div>
		[ELSE]
			<div class="BlocChoixPack">
				[IF [!Total!]=1&&[!Cpk::AffichePopup!]=0]
					// MODIFICATION DEMANDE PAR MATHILDE LE 19 AVRIL 2014
					// SI UN SEUL CHOIX POSSIBLE DANS LE PACK ON N AFFICHE PAS LE CHOIX CAR IL SERA DE FAIT
					// SAUF SI ON VEUT OBLIGER À CHOISIR POUR AUGMENTATION DE PRIX
					[STORPROC Boutique/ConfigPack/[!Cpk::Id!]/Reference/Actif=1|Ref][/STORPROC]
					[STORPROC Boutique/Produit/Reference/[!Ref::Id!]|Prod|0|1][/STORPROC]
					[STORPROC Boutique/Reference/[!Ref::Id!]|R|0|1][/STORPROC]
					<input type="hidden" name="config[[!Cpk::Id!]]" id="PackChoix-[!Cpk::Id!]"  data-id="[!Cpk::Id!]" value="[!Ref::Id!]" class="PackChoix">
					<div id="PackChoixRef-[!Cpk::Id!]" ></div>
					[SWITCH [!Cpk::EtapeVisu!]|=]
						[CASE 1]
							<script type="text/javascript">
								initPopupImg='[!R::ImagePng!]';
								initInterImg='[!R::ImageFondPng!]';
							</script>
						[/CASE]
						[CASE 3]
							<script type="text/javascript">
								 initCarteImg='[!R::ImagePng!]';
							</script>
						[/CASE]
						[CASE 7]
							<script type="text/javascript">
								 initEncarImg='[!R::ImagePng!]';
							</script>
						[/CASE]
					[/SWITCH]

				[ELSE]
					// pour le configurateur meme avec un seul choix on passe ici
					[!LeTitre:=[!P::Nom!] <h4> !]
					[!LeTitre+=[!Cpk::Nom!]</h4>!]
					[!LeTitre2:=[!LeTitre!]!]
//[IF [!Cpk::ChoixObligatoire!]=0]gris-[/IF]
					<a href="#" onclick="AffichePopup([!Cpk::Id!],'[UTIL ADDSLASHES][!LeTitre2!][/UTIL]');" id="PackButton-[!Cpk::Id!]"  onblur="ScrollversEtape([!Cpk::Id!]);"  class="btn [IF [!Cpk::ChoixObligatoire!]=1]btn-kirigami[ELSE]btn-fushia[/IF] btn-pack col-md-12" style="white-space: normal;">[!Cpk::Nom!]</a>
					<input type="hidden" name="config[[!Cpk::Id!]]" id="PackChoix-[!Cpk::Id!]" data-id="[!Cpk::Id!]" value="" class="PackChoix">
					<div id="PackChoixRef-[!Cpk::Id!]"></div>
					[STORPROC Boutique/ConfigPack/[!Cpk::Id!]/Reference/Actif=1&DefPerso=1|RefC|0|1]
						[SWITCH [!Cpk::EtapeVisu!]|=]
							[CASE 7]
								<script type="text/javascript">
									initEncarImg='[!RefC::ImagePng!]';
								</script>
							[/CASE]
						[/SWITCH]
					[/STORPROC]
				[/IF]
			</div>
		[/IF]
	[/STORPROC]
	[STORPROC Boutique/Produit/[!P::Id!]/Reference|Re|0|1][/STORPROC]
	<input type="hidden" name="Reference" id="Reference" value="[!Re::Reference!]" >
	<input type="hidden" name="StockAvailable" value="1" >
	<input type="hidden" name="IdReference" value="[!Re::Id!]" >

</div>

// Surcouche JS
<script type="text/javascript">

	function AffichePopup ($Lepack,$LeTitre) {
		$('#myModalLabel').html($LeTitre);
	
		$('#lemodal').modal({
			keyboard: false,
			remote: '/Boutique/Produit/PackPopupconfigurateur.htrc?LePack='+$Lepack
		}).modal('show');
				
		//alert("ici on mettra a jour la photo");
	}
	
	function AffichePopupDesc ($Laref) {
	
		$('#myModalLabel').html("");
	
		$('#lemodal').modal({
			keyboard: false,
			remote: '/Boutique/Produit/PackVoirChoix.htrc?Laref='+$Laref
		}).modal('show');
				
	
	}
	
	function choixPack(pack,ref,leprod,nomRef,imagefondpng, imageref,couleurhexa, etape, nometape) {
		$('#PackChoix-'+pack).val(ref);
		$('#PackChoixRef-'+pack).html("");
		$('#PackChoixRef-'+pack).html('<div class="PackNomProduit"><span class="gris"> '+nometape+ "</span><br />" + nomRef+ '</div><type="button" name="modifier" class="PackBtnMod btn btn-gris-kirigami" onclick="AffichePopup(' + pack +');" >Modifier</a>');

		$('#PackButton-'+pack).css('display','none');
		$('#lemodal').modal('hide');
		if (couleurhexa!='') { 
			couleurElement(etape,couleurhexa);
			if (etape==2) couleurElement('6',couleurhexa) ;
		} else {
			choixElement(etape,imageref) ;
			if (etape==1 && imagefondpng!='') choixElement('5',imagefondpng) ;
		}

		var sel = $('.PackChoix');
		var leprix =0;
		var config = {};


		//On va chercher tous les choix du pack
		$(sel).each(function (index,item){
			if ($(item).val() !="") {
				console.log('Config '+$(item).attr('data-id')+' = '+$(item).val());
				if (!config['config']) config['config']={};
				config['config'][$(item).attr('data-id')] = $(item).val();
			}
			
		});
		//récupération de la référence du produit
		config['quantite'] = $('#Qte').val();
		config['Reference'] = $('#Reference').val();
		
		var r = $.ajax({
			type: "POST",
			url: '/Boutique/Produit/[!P::Id!]/getTarif.json',
			data: config,
			success: function (data){
				console.log(data);
				//alert(data.priceUnit);
				$('#tarif').html(data.price+' €');
				if ( $('#PackType').val()=='5') {
					 $('#tarifunite').html("soit "+ data.priceUnit+" € lunité");
				}
			},
			dataType: 'json'
		});


	}
	

	function AfficheMasqueOption(lepack) {
		if ($('#lesoptions-'+lepack).is(':visible')) {
			$('#lesoptions-'+lepack).css('display','none');
			$('#blochoixR-'+lepack).css('display','block');
			$('#blochoixG-'+lepack).css('display','none');
		} else {
			$('#lesoptions-'+lepack).css('display','block');
			$('#blochoixR-'+lepack).css('display','none');
			$('#blochoixG-'+lepack).css('display','block');
			ScrollversOption(lepack);		
			
		}
		
	}
	function ScrollversOption(lepack) {

		var the_id = $('#lesoptions-'+lepack);  
		
		$('html, body').animate({  
			scrollTop:$(the_id).offset().top  
		}, 'slow');  
		return false;  
	}
	
	function ScrollversEtape(lepack) {
//alert(lepack);
		return;
		var the_id = $('#PackButton-'+lepack); 
		
		$('html, body').animate({  
			scrollTop:$(the_id).offset().top  
		}, 'slow');  
		return false;  
	}

</script>
		

