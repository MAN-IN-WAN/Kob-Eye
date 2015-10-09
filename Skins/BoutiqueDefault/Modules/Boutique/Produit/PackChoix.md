// Affichage des choix des packs
[STORPROC [!Query!]|P|0|1][/STORPROC]

<div class="BlocChoixPackListe" >

	[STORPROC Boutique/Produit/[!P::Id!]/ConfigPack|Cpk|||Ordre|ASC]
		[!Total:=0!]
		[COUNT Boutique/ConfigPack/[!Cpk::Id!]/Reference/Actif=1|Total]
		<div class="BlocChoixPack">
			[IF [!Total!]=1&&[!Cpk::AffichePopup!]=0]
				// MODIFICATION DEMANDE PAR MATHILDE LE 19 AVRIL 2014
				// SI UN SEUL CHOIX POSSIBLE DANS LE PACK ON N AFFICHE PAS LE CHOIX CAR IL SERA DANS LA DESCRIPTION

				[STORPROC Boutique/ConfigPack/[!Cpk::Id!]/Reference/Actif=1|Ref][/STORPROC]
				[STORPROC Boutique/Produit/Reference/[!Ref::Id!]|Prod|0|1][/STORPROC]
				<input type="hidden" name="config[[!Cpk::Id!]]" data-id="[!Cpk::Id!]" id="PackChoix-[!Cpk::Id!]" value="[!Ref::Id!]" class="PackChoix">
				<div id="PackChoixRef-[!Cpk::Id!]" style="display:none;">
				//	<div class="PackNomProduit">[!Cpk::Nom!] - [!Prod::Nom!]</div>
				//	<a href="/[!Prod::getUrl()!]"  class="btn btn-gris-kirigami " target="_blank">Voir le produit</a>
				</div>
			[ELSE]
				[!LeTitre:=[!P::Nom!] <h4> !]
				[!LeTitre+=[!Cpk::Nom!]</h4>!]
				[!LeTitre2:=[!LeTitre!]!]
				<a href="#" onclick="AffichePopup([!Cpk::Id!],'[UTIL ADDSLASHES][!LeTitre2!][/UTIL]');" id="PackButton-[!Cpk::Id!]" class="btn btn-kirigami btn-pack">[!Cpk::Nom!]</a>
				<input type="hidden" name="config[[!Cpk::Id!]]" data-id="[!Cpk::Id!]" id="PackChoix-[!Cpk::Id!]" value="[!config::[!Cpk::Id!]!]" class="PackChoix">
				<div id="PackChoixRef-[!Cpk::Id!]"></div>
			[/IF]
		</div>
	[/STORPROC]
	[STORPROC Boutique/Produit/[!P::Id!]/Reference|Re|0|1][/STORPROC]
	<input type="hidden" name="Reference" id="Reference" value="[!Re::Reference!]" >
	<input type="hidden" name="StockAvailable" value="1" >
	<input type="hidden" name="IdReference" value="[!Re::Id!]" >

</div>

// Surcouche JS
<script type="text/javascript">

	function AffichePopup (Lepack,LeTitre) {

		

		$('#myModalLabel').html(LeTitre);
		$('#lemodal').modal({
			keyboard: false,
			remote: '/Boutique/Produit/PackPopup.htrc?LePack='+Lepack
		}).modal('show');
				
	
	}
	
	function AffichePopupDesc ($Laref) {
	
		$('#myModalLabel').html("");
	
		$('#lemodal').modal({
			keyboard: false,
			remote: '/Boutique/Produit/PackVoirChoix.htrc?Laref='+$Laref
		}).modal('show');
				
	
	}
	
	function choixPack(pack,ref,leprod,nomRef) {
		$('#PackChoix-'+pack).val(ref);
		$('#PackChoixRef-'+pack).html("");
		$('#PackChoixRef-'+pack).html('<div class="PackNomProduit"> '+nomRef+ '</div><button  type="button" name="modifier" class="PackBtnMod btn btn-gris-kirigami" onclick="AffichePopup(' + pack +');" >Modifier</a>');

		$('#PackButton-'+pack).css('display','none');
		if ($('#lemodal').hasClass('in'))
			$('#lemodal').modal('hide');
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
				$('#tarif').html(data.price+' €');
			},
			dataType: 'json'
		});
	
	}
	$(document).ready(function () {
		//détetction des config en parametres
		[STORPROC [!packconfig!]|C]
			[STORPROC Boutique/Reference/[!C!]|R|0|1][/STORPROC]
			choixPack([!Key!],[!C!],'[UTIL ADDSLASHES][!R::Nom!][/UTIL]','[UTIL ADDSLASHES][!R::Nom!][/UTIL]');
		[/STORPROC]
	});

</script>