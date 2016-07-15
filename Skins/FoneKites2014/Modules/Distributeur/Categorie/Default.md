[INFO [!Lien!]|I]

[HEADER]
<style>
body {
	  zoom: 100%;
}
</style>
[/HEADER]

<div>
	<div>
		<div id="map"></div>
		<script type="text/javascript">
			[IF [!Lien!]=[!Systeme::CurrentMenu::Url!]]
				var CURRENT_CAT = "";
			[ELSE]
				[STORPROC [!Query!]|C|0|1]
					[IF [!C::ObjectType!]=Categorie]
						var CURRENT_CAT = "[!C::Url!]";
					[/IF]
					[IF [!C::ObjectType!]=Shop]
						[!CURRENT_SHOP:=[!C!]!]
						[STORPROC [!C::getParents(Categorie)!]|Cat]
							var CURRENT_CAT = "[!Cat::Url!]";
							[NORESULT]
							var CURRENT_CAT = "";
							[/NORESULT]
						[/STORPROC]
					[/IF]
					[NORESULT]
					var CURRENT_CAT = "";
					[/NORESULT]
				[/STORPROC]
			[/IF]
			$(document).ready(function (){
				//Affichage de la carte par défaut
				var optionsCarte = {
					zoom: 6,
					[IF [!CURRENT_SHOP::Id!]]
						center: new google.maps.LatLng([!CURRENT_SHOP::Latitude!],[!CURRENT_SHOP::Longitude!]),
					[ELSE]
						center: new google.maps.LatLng(-49.738682,7.602539),
					[/IF]
					mapTypeId: google.maps.MapTypeId.ROADMAP
				};
				var liste_marqueurs = [];
				var marqueur_courant = null;
				var mainmap = new google.maps.Map(document.getElementById("map"), optionsCarte);
				var zoneMarqueurs = new google.maps.LatLngBounds();

				//bloquage du zoom
				google.maps.event.addListener(mainmap, 'zoom_changed', function() {
					if (this.getZoom() < 4 ) {
						// Change max/min zoom here
						this.setZoom(4);
					}
				});
				
				//verification de l'existence du cookie geoloc
				loc_cookies = $.cookie('location_coordonates');
				if (loc_cookies&&loc_cookies!="timeout"){
					var tmp=loc_cookies.split('XXXX',2);
					location_coordonates = {
						latitude:tmp["0"],
						longitude:tmp["1"]
					}
				}

				//ecoute de l'evenement de geolocalisation
				$(document).on('geoloc',function (event, latitude, longitude){
					mainmap.setCenter(new google.maps.LatLng(latitude,longitude));
				})
				
				//positionnement sur la position actuelle
				[IF [!CURRENT_SHOP::Id!]][ELSE]
				if ((location_coordonates&&location_coordonates!="timeout")){
					mainmap.setCenter(new google.maps.LatLng(location_coordonates.latitude,location_coordonates.longitude));
				}else{
					//position undefined
					mainmap.setCenter(new google.maps.LatLng(29.3932255,-43.6162103));
					mainmap.setZoom(1);
					console.log('position undefined');
				}
				[/IF]
				
				//Alimentation des valeurs du formulaire
				function setFormInformations(obj){
					/*$(document).scrollTo(  '200px', 800 );*/
					$("#TitreEC").html("Contact " + obj.Name+' ('+obj.Category+')');
					$("#CoteTitreEC").html(obj.Name);
					var ladress=obj.Adress+ " "+obj.PostalCode + " " +obj.City + ' - '+obj.Country;
					$("#CoteAdressEC").html(ladress);
					$("#CotePhoneEc").html(obj.Phone);
					$("#CoteFaxEc").html(obj.Fax);
					$("#CoteEmailEc").val(obj.Id);
					$("#FormCoteEmailEc").html(obj.Email);
					if (obj.Website=="") {
						$('#website-detail').css('display','none');
						$('#website-infobulle').css('display','none');
					}else{
						$('#website-detail').css('display','block');
						$('#website-infobulle').css('display','block');
					}
					$("#FormWebsite").html('<a href="http://'+obj.Website+'" target="_blank">'+obj.Website+'</a>');
					$("#FormContact").css("display","block");
					$('#col-form-contact').css('display','block');
				}
				
				function addMarqueur(obj) {
					//creation du point
					var point = new google.maps.LatLng(obj.Latitude, obj.Longitude);
					var optionsMarqueur = {
						position: new google.maps.LatLng(obj.Latitude,obj.Longitude),
						map: mainmap,
						title: obj.Name
					}
					var marqueur = new google.maps.Marker(optionsMarqueur);
					iconFile = obj.IconMarqueur;
					marqueur.setIcon(iconFile) 
					zoneMarqueurs.extend(marqueur.getPosition());
					var couleur="Verte";
					var contenuInfoBulle = '<div class="BulleInfo '+obj.Couleur+' ">'+
						'<span class="fleche '+obj.Couleur+'"></span>'+
						'<div class="row">'+
							'<div class="col-lg-10 col-xs-10">'+
								'<h3 class="">'+obj.Category+': '+obj.Name+' </h3>'+
							'</div>'+
						'</div>'+
						'<div class="row">'+
							'<div class="col-lg-10 col-xs-10">'+
								'<h5>'+obj.Adress+'</h5><h5>'+obj.PostalCode+' '+ obj.City +' - '+ obj.Country +'</h5>'+
							'</div>'+
							'<div class="col-lg-2 col-xs-2">'+
								'<span class="adresse-icon"></span>'+
							'</div>'+
						'</div>'+
						'<div class="row">'+
							'<div class="col-lg-10 col-xs-10">'+
								'<h5 class="top20"><strong>Tel: </strong>'+obj.Phone+'</h5>'+
								((obj.Fax!='')?'<h5><strong>Fax: </strong>'+obj.Fax+'</h5>':'')+
							'</div>'+
							'<div class="col-lg-2 col-xs-2">'+
								'<span class="adresse-fone"></span>'+
							'</div>'+
						'</div>'+
						'<div class="row">'+
							'<div class="col-lg-10 col-xs-10">'+
								'<h5 class="top20"><strong>E-mail: </strong>'+obj.Email+'</h5>'+
							'</div>'+
							'<div class="col-lg-2 col-xs-2">'+
								'<span class="adresse-mail"></span>'+
							'</div>'+
						'</div>'+((obj.Website!="")?
						'<div class="row" id="website-infobulle">'+
							'<div class="col-lg-10 col-xs-10">'+
								'<h5 class="top20"><strong>Website: </strong><a style="color:white" href="http://'+obj.Website+'" target="_blank">'+obj.Website+'</a></h5>'+
							'</div>'+
							'<div class="col-lg-2 col-xs-2">'+
							'</div>'+
						'</div>':"")+((obj.Proshop||obj.ProshopGermany||obj.TestCenter||obj.ProSchool)?
						'<div class="row" id="website-infobulle" style="margin:15px 0 15px">'+
							'<div class="col-lg-4 col-xs-4">'+
								((obj.Proshop)?'<img src="/Skins/FoneKites2014/img/logos/pro-shop_france.png" class="img-responsive"/>':'')+
								((obj.ProshopGermany)?'<img src="/Skins/FoneKites2014/img/logos/pro-shop_germany.png" class="img-responsive"/>':'')+
							'</div>'+
							'<div class="col-lg-4 col-xs-4">'+
								((obj.TestCenter)?'<img src="/Skins/FoneKites2014/img/logos/test_center.png"  class="img-responsive"/>':'')+
							'</div>'+
							'<div class="col-lg-4 col-xs-4">'+
								((obj.ProSchool)?'<img src="/Skins/FoneKites2014/img/logos/pro_school.png"  class="img-responsive"/>':'')+
							'</div>'+
						'</div>':"")
					'</div>';

					var infoBulle = new InfoBox({
						content: contenuInfoBulle,
						position: point,
						shadowStyle: 1,
						padding: '5px',
						backgroundColor: 'rgb(0,204,153)',
						borderRadius: 4,
						arrowSize: 10,
						borderWidth: 1,
						pixelOffset: new google.maps.Size(40, -100),
						borderColor: '#2c2c2c',
						disableAutoPan: true,
						hideCloseButton: true,
						arrowPosition: 30,
						backgroundClassName: 'phoney',
						arrowStyle: 2
					});
	
					// Association de l'infobulle au marqueur
					marqueur._infowindow = infoBulle;
				
					google.maps.event.addListener(marqueur, 'click', function() { 						
						if(marqueur_courant){
							marqueur_courant._infowindow.close();
						}		
						marqueur_courant = this;
						// ! IMPORTANT on utilise this et non pas marqueur
						this._infowindow.open(mainmap, this);
						setFormInformations(obj);
					});
					
					//Ajout du marqueur à la liste
					liste_marqueurs.push(marqueur);
					
					//si le marqueur est demandé dans l'url alors on affiche son infobuille
					[IF [!CURRENT_SHOP::Id!]>0]
						if ([!CURRENT_SHOP::Id!]==obj.Id){
							marqueur_courant = marqueur;
							marqueur._infowindow.open(mainmap, marqueur);
							setFormInformations(obj);
						}
					[/IF]
				}
				var stopload = false;
				var ajaxload;
				//chargement des points en fonction de la page d'arrivée
				function loadPoints(cat,nb,page,reset){
					nb = nb>0?nb:"10";
					page = page>0?page:"0";
					reset = reset==undefined?true:false;
					if (cat!=undefined)
						CURRENT_CAT=cat;
					console.log('loadpoints '+cat+' '+nb+' '+page);
					if (reset) {
						if (ajaxload!=undefined)ajaxload.abort();
						//suppression des points existants
						for (var m in liste_marqueurs){
							liste_marqueurs[m].setMap(null);
						}
						stopLoad=false;
					}
					//chargement des points
					var sel = this;
					ajaxload = $.ajax({
						url: '/Distributeur/Shop/getJsonShop.json?CAT='+CURRENT_CAT+'&NB='+nb+'&PAGE='+page,
						success: function (data) {
							for (var i in data.items){
								addMarqueur(data.items[i]);
							}
							if (data.end!=1&&!stopLoad) {
								loadPoints(CURRENT_CAT,nb,data.nextpage,false);
							}
						},
						dataType: 'json'
					});
				}
				
				//Chargement initial
				loadPoints(CURRENT_CAT,50,0);
				
				
				$('.filters a.filter').each(function (index,item){
					$(this).click(function(e){
						e.preventDefault();
						$('.filters a.filter.filteractive').removeClass('filteractive');
						$('.filters .active').removeClass('active');
						var selector = $(this).attr('data-filter');
						$('a[data-filter="'+selector+'"]').addClass('filteractive');
						//rechargement des points
						var classname = selector.replace('.','');
						classname = classname.replace('*','');
						stopLoad = true;
						loadPoints(classname);
					});
				});
				//Modification des comportements des filtres

				//center on country name
				function centerOnCountryName(country){
					//mainmap.setUIToDefault();
					CURRENT_CAT='';
					loadPoints();
					var geocoder = new google.maps.Geocoder();
					geocoder.geocode( { 'address': country}, function(results, status) {
						if (status == google.maps.GeocoderStatus.OK) {
							mainmap.setCenter(results[0].geometry.location);
						} else {
							alert("Geocode was not successful for the following reason: " + status);
						}
					});
				}
				
				//Ajout de l'evenement sur la sélection du pays
				$('#SelCountry').on('change',function (){
					centerOnCountryName($(this).find('option:selected').html());
				});
				
			});
		</script>
		<div class="second-menu hidden-xs">
			[!Req:=[!Systeme::CurrentMenu::Alias!]!]
			<div class="container nopadding-left nopadding-right">
//				<div class="collapse navbar-collapse navbar-ex1-collapse">
				<div class="wrapper nav navbar-second-nav filters" style="margin:0;">
//					<ul class="nav navbar-second-nav filters">
//						<li [IF [!Lien!]=[!Systeme::CurrentMenu::Url!]] class="active" [/IF] >
						<aside class="aside aside-1 [IF [!Lien!]=[!Systeme::CurrentMenu::Url!]] active [/IF]" >
							<a href="[IF [!Systeme::CurrentMenu::Url!]~http][ELSE]/[/IF][!Systeme::CurrentMenu::Url!]" [IF [!Systeme::CurrentMenu::Url!]~http]target="_blank"[/IF] data-filter="*" class="filter">
								ALL LOCATOR
							</a>
						</aside>
//						</li>
						[STORPROC [!Req!]|SCat|0|10|Ordre|ASC]
//							<li [IF [!Lien!]~[!Systeme::CurrentMenu::Url!]/[!SCat::Url!]] class="active" [/IF]>
							<aside class="aside aside-1 [IF [!Lien!]~[!Systeme::CurrentMenu::Url!]/[!SCat::Url!]] active [/IF]" >
								<a href="[IF [!SCat::Url!]~http][ELSE]/[/IF]/[!Systeme::CurrentMenu::Url!]/[!SCat::Url!]" [IF [!SCat::Url!]~http]target="_blank"[/IF]  data-filter=".[!SCat::Url!]" class="filter">
									[!SCat::Nom!] 
								</a>
							</aside>
//							</li>
						[/STORPROC]
//					</ul>
				</div>
			</div>
		</div>
	
	
		<div class="select-country">
			<div class="container  nopadding-left nopadding-right">
				<div class="form-control-locator">
					<select name="SelCountry" id="SelCountry">	
						<option selected> - $SELECTCOUNTRY$</option>
						[STORPROC Geographie/Pays|Pa|||Nom|ASC]
							[STORPROC Distributeur/Shop/CountryNew=[!Pa::Code!]|Sh|0|1]
								<option value="[!Pa::Code!]" [IF [!SelCountry!]=[!Pa::Code!]]selected[/IF]>[!Pa::Nom!] </option>
							[/STORPROC]
						[/STORPROC]
					</select>
				</div>
			</div>
		</div>
	</div>
</div>







<div class="container conbot nopadding-left nopadding-right">
	<h3 class="titreh3"  id="TitreEC">__MSG_LOCATOR__</h3>
	<form id="FormContact" method="post" action="/[!Lien!]" style="display:none;"  >
		<div class="col-lg-9 nopadding-left">
			
			<div class="col-lg-4-1">
				<input type="text" class="form-control" placeholder="__FULL_NAME__" name="FullName" id="FullName"  >
			</div>
			<div class="col-lg-4-2">
				<input type="text" class="form-control" placeholder="__EMAIL_ADDRESS__" name="Email" id="Email">
			</div>
			<div class="col-lg-4-3">
				<select name="Country" id="Country" class="form-control error formu">	
					<option selected> __COUNTRY__</option>
					[STORPROC Geographie/Pays|Pa|||Nom|ASC]
						<option value="[!Pa::Nom!]" [IF [!Country!]=[!Pa::Nom!]]selected[/IF]>[!Pa::Nom!] </option>
					[/STORPROC]
				</select>
			</div>
			<div class="col-lg-4-3">
				<input type="text" class="form-control" placeholder="__SUBJECT__" name="Subject" id="Subject">
			</div>
			<div class="col-lg-4-3">
				<textarea rows="13" class="form-control" placeholder="__MESSAGE__" name="Message" id="contact_Message" style="text-transform: none;"></textarea>
			</div>
			<div class="col-lg-4-3">
				<div class="checkbox">
					<label>
					<input type="checkbox" name="agree" id="agree" value="1" [IF [!SendContact!]=1&&[!agree!]=][ELSE]checked="checked"[/IF]> __AGREE_ALL_TERMS_AND_CONDITIONS__
					</label>
				</div>
			</div>
			<div class="col-lg-4-3">
				<div class="checkbox">
					<label>
					<input type="checkbox" name="newsletter" id="newsletter" value="1" [IF [!SendContact!]=1&&[!newsletter!]=][ELSE]checked="checked"[/IF]> __SUSCRIBE_TO_F-ONE_NEWSLETTER__
					</label>
				</div>
			</div>
			<div class="col-lg-4-3">
				<button class="btn btn-primary btn-send" type="submit" value="SendContact">__SEND_MESSAGE__</button>
			</div>
		</div>
		<input type="hidden" name="Destinataire" readonly id="CoteEmailEc" value="[IF [!CoteEmailEc!]=][!CONTACTMAIL!][/IF]"  >
	</form>
    	<div class="col-lg-3-1  nopadding-right" id="col-form-contact" style="display:none;">
		<div class="etiquette">
        		CONTACT DETAILS
        	</div>
		<div class="adresse">
	        	<div class="adresse-icon"></div>
       			<h6 id="CoteTitreEC"></h6>
       			<h6 id="CoteAdressEC"></h6>
        		<div class="adresse-fone"></div>
  			<p><strong>__PHONE__ :</strong> <span id="CotePhoneEc" ></span></p>
        		<div class="adresse-email"></div>
  			<p><strong>__EMAIL__ :</strong> <span id="FormCoteEmailEc" ></span></p>
  			<p id="website-detail"><strong>Website :</strong> <span id="FormWebsite" ></span></p>
	        </div>
		<div class="etiquette">
			__DETAILS_REGISTRATION__
		</div>
		<div class="details-register">
			<p>__TEXT_CONDITIONS__</p>
		</div>
      	</div>
</div>
<script type="text/javascript">
	$(document).ready(function () {
		$('#FormContact').submit(function (event){
			//on ne valide pas le formulaire
			event.preventDefault();
			//on envoi les informations 
			$.ajax({
			    dataType: "json",
			    type: 'POST',
			    url: "/Distributeur/Categorie/sendContact.json",
			    data:{
				FullName: $('#FullName').val(),
				Email: $('#Email').val(),
				Country: $('#Country option:selected').val(),
				Subject: $('#Subject').val(),
				agree: $('#agree').is(':checked')?1:0,
				newsletter: $('#newsletter').is(':checked')?1:0,
				Message: $('#contact_Message').val(),
				CoteEmailEc: $('#CoteEmailEc').val(),
				SendContact:1
			    }
			}).done(function (data){
				if (data.success){
					//cache le formulaire
					$('#FormContact').css('display','none');
					$('#col-form-contact').css('display','none');
					$('#TitreEC').html(data.message);
				}else{
				    $('#newsletter_modal .modal-header h4').html('CONTACT FORM');
				    $('#newsletter_modal .modal-header').removeClass('success');
				    $('#newsletter_modal .modal-header').addClass('error');
				    $('#newsletter_modal .btn.btn-default').removeClass('btn-success');
				    $('#newsletter_modal .btn.btn-default').addClass('btn-danger');
					$('#newsletter_modal .modal-body p').html(data.message);
					$('#newsletter_modal').modal('show');
				}
			});
		});
	});
</script>