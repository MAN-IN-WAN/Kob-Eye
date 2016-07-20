[INFO [!Lien!]|I]
//Recherche du menu racine
[COUNT [!I::Historique!]|NbNiv]
//[!NbNiv!]
//[!SelCountry!]
<div class="navbar-wrapper">
	<script type="text/javascript">
		var vLocations = new Array();
	</script>
	[!Position:=0!]
	<div class="navbar-wrapper">
		<script type="text/javascript">
			var vLocations = new Array();
		</script>
		[!Position:=0!]
		[IF [!NbNiv!]>1]
			[!Req:=[!Query!]/Shop/Longitude!=&Latitude!=&Email!=!]
		[ELSE]
			[!Req:=Distributeur/Categorie/*/Shop/Longitude!=&Latitude!=&Email!=!]
		[/IF]
		[IF [!SelCountry!]!=]
			[!Req+=&CountryNew=[!SelCountry!]!]
		[/IF]
		[COUNT [!Req!]|NbDistrib]
//		[!Req!]	--> [!NbDistrib!]
		[STORPROC [!Req!]|D|0|10000]
			<script type="text/javascript">
				vLocations[[!Position!]] = "[!D::Name!]/[!D::Longitude!]/[!D::Latitude!]/[!D::City!]/[!D::Country!]/[!Utils::nl2br([!D::Adress!])!]/[!D::PostalCode!]/[!D::Phone!]/[!D::Fax!]/[!D::Email!]";
			</script>
			[!Position+=1!]
		[/STORPROC]

		<div id="map">   </div>
		//<iframe width="100%" height="700px" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="https://maps.google.fr/?ie=UTF8&amp;ll=49.738682,7.602539&amp;spn=9.774445,23.269043&amp;t=m&amp;z=6&amp;output=embed"></iframe>
		<script type="text/javascript" src="https://maps.google.fr/?[IF [!SelCountry!]!=]&country=[!SelCountry!]&[/IF]ie=UTF8&amp;ll=49.738682,7.602539&amp;spn=9.774445,23.269043&amp;t=m&amp;z=6&amp;output=embed"></script>
		<script type="text/javascript">
			$(document).ready(function () {
				var marqueur_courant = null;
				var optionsCarte = {
					zoom: 5,
					center: new google.maps.LatLng(49.738682,7.602539),
					mapTypeId: google.maps.MapTypeId.ROADMAP
				};
				var maCarte = new google.maps.Map(document.getElementById("map"), optionsCarte);
				var zoneMarqueurs = new google.maps.LatLngBounds();
				for(var i=0; i<vLocations.length; i++)	{
					var vInformations = vLocations[i].split('/');
					var vTitre = vInformations[0];
					var vLongitude = vInformations[1];
					var vLatitude = vInformations[2];
					var vVille = vInformations[3];
					var vCountry = vInformations[4];
					var vAdress = vInformations[5];
					var vCodPos = vInformations[6];
					if (vInformations[7]!='') { var vPhone = '<span class="libelle">$PHONE$</span>'+vInformations[7]; } else  { var vPhone = ''; }
					if (vInformations[8]!='') { var vFax = '<span class="libelle">$FAX$</span>'+vFaxvInformations[8];} else  { var vFax = ''; }
					if (vInformations[9]!='') { var vEmail = '<span class="libelle">$MAIL$</span>' + vInformations[9];} else  { var vEmail = ''; }
					var point = new google.maps.LatLng(vLatitude, vLongitude);
					var optionsMarqueur = {
						position: new google.maps.LatLng(vLatitude,vLongitude),
						map: maCarte,
						title: vTitre
					}
					var marqueur = new google.maps.Marker(optionsMarqueur);
					iconFile = '[!Domaine!]/Skins/[!Systeme::Skin!]/img/punaise.png';
					marqueur.setIcon(iconFile) 
					zoneMarqueurs.extend(marqueur.getPosition());
					var contenuInfoBulle = '<div class="BulleVerte ">'+
						'<div class="container">'+
							'<div class="col-lg-10">'+
								'<h3>'+vTitre+'</h3>'+'<h5>'+vAdress+'</h5><h5>'+vCodPos+' '+ vVille +'</h5>'+
							'</div>'+
							'<div class="col-lg-2">'+
								'<div class="adresse-icon"></div>'+
							'</div>'+
						'</div>'+
						'<div class="container">'+
							'<div class="col-lg-10">'+
								'<h5>'+vPhone+'</h5><h5>'+vFax+'</h5>'+
							'</div>'+
							'<div class="col-lg-2">'+
								'<div class="adresse-fone"></div>'+
							'</div>'+
						'</div>'+
						'<div class="container">'+
							'<div class="col-lg-10">'+
								'<h5>'+vEmail+'</h5>'+
							'</div>'+
							'<div class="col-lg-2">'+
								'<div class="adresse-mail"></div>'+
							'</div>'+
						'</div>'+
					'</div>';
					var infoBulle = new google.maps.InfoWindow({
						content: contenuInfoBulle,
						position: point });
	
					// Association de l'infobulle au marqueur
					marqueur._infowindow = infoBulle;
				
					// Création de la fonction Clic
					google.maps.event.addListener(marqueur, 'mouseover', function() { 						
						if(marqueur_courant){
							marqueur_courant._infowindow.close();
						}		
						marqueur_courant = this;
						// ! IMPORTANT on utilise this et non pas marqueur
						this._infowindow.open(maCarte, this);
						$("#TitreEC").val(this.title);
						$("#CoteTitreEC").val(this.title);
						var ladress=vInformations[5]+ " "+vInformations[6] + " " +vInformations[3];
						$("#CoteAdressEC").val(ladress);
						$("#CotePhoneEc").val(vInformations[7]);
						$("#CoteFaxEc").val(vInformations[8]);
						$("#CoteEmailEc").val(vInformations[9]);

					});
				}
				[IF [!NbDistrib!]=1]maCarte.setZoom(3);[/IF]
				maCarte.fitBounds(zoneMarqueurs);
				
			});
		</script>
		<div class="second-menu">
			[!Req:=[!Systeme::CurrentMenu::Alias!]!]
			<div class="container">
				<div class="collapse navbar-collapse navbar-ex1-collapse">
					<ul class="nav navbar-second-nav">
						<li [IF [!Lien!]=[!Systeme::CurrentMenu::Url!]] class="active" [/IF] >
							<a href="[IF [!Systeme::CurrentMenu::Url!]~http][ELSE]/[/IF][!Systeme::CurrentMenu::Url!]" [IF [!Systeme::CurrentMenu::Url!]~http]target="_blank"[/IF] >
								ALL LOCATOR
							</a>
						</li>
						[STORPROC [!Req!]|SCat|0|10|Ordre|ASC]
							<li [IF [!Lien!]~[!Systeme::CurrentMenu::Url!]/[!SCat::Url!]] class="active" [/IF]>
								<a href="[IF [!SCat::Url!]~http][ELSE]/[/IF][!Systeme::CurrentMenu::Url!]/[!SCat::Url!]" [IF [!SCat::Url!]~http]target="_blank"[/IF]  >
									[!SCat::Nom!] 
								</a>
							</li>
						[/STORPROC]
					</ul>
				</div>
			</div>
		</div>
	
	
		<div class="select-country">
			<div class="container">
				<div class="form-control-locator">
					<form name="locate">
						<select name="SelCountry" onChange="submit();">	
							<option selected> - $SELECTCOUNTRY$</option>
							[STORPROC Geographie/Pays|Pa|||Nom|ASC]
								<option value="[!Pa::Code!]" [IF [!SelCountry!]=[!Pa::Code!]]selected[/IF]>[!Pa::Nom!] </option>
							[/STORPROC]
						</select>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>






[IF [!CONTACTMAIL!]=]
	[!CONTACTMAIL:=[!CONF::MODULE::SYSTEME::CONTACT!]!]
[/IF]

<div class="container conbot ">
	<h3 class="titreh3"><input type="text" readonly class="titreh3" value="$MSGTITRECONTACT$" id="TitreEC" ></h3>
	<form id="FormContact" method="post" action="/[!Lien!]" >
		<div class="col-lg-8">
			[IF [!SendContact!]!=]
				//Verification des informations du formulaire
				[!C_Error:=0!]
				[IF [!FullName!]][ELSE][!FullName_Error:=1!][!C_Error:=1!][/IF]
				[IF [!Email!]][ELSE][!Email_Error:=1!][!C_Error:=1!][/IF]
				[IF [!Subject!]][ELSE][!Subject_Error:=1!][!C_Error:=1!][/IF]
				[IF [!Message!]][ELSE][!Message_Error:=1!][!C_Error:=1!][/IF]
				[IF [!C_Error!]]
					// Si il y a des erreurs, on les affiche
					<div class="col-lg-4-1">
						<div class="alert alert-error">
							<strong>Veuillez remplir les champs obligatoires suivants :</strong>
							<ul>
								[IF [!FullName_Error!]]<li>Merci de renseigner votre Nom</li>[/IF]
								[IF [!Email_Error!]]<li>Merci de renseigner votre adresse email</li>[/IF]
								[IF [!Subject_Error!]]<li>Merci de renseigner le sujet de votre demande</li>[/IF]
								[IF [!Message_Error!]]<li>Merci de laisser votre message</li>[/IF]
							</ul>
						</div>
					</div>
				[ELSE]
					// Sinon envoi du mail
					[LIB Mail|LeMail]
					[METHOD LeMail|Subject][PARAM]Message de [!Domaine!] - [!C_Objet!][/PARAM][/METHOD]
					[METHOD LeMail|From][PARAM][!Email!][/PARAM][/METHOD]
					[METHOD LeMail|ReplyTo][PARAM][!Email!][/PARAM][/METHOD]
					[METHOD LeMail|To][PARAM][!CONTACTMAIL!][/PARAM][/METHOD]
					[IF [!CONTACTMAILBCC!]!=][METHOD LeMail|Bcc][PARAM]myriam@abtel.fr[/PARAM][/METHOD][/IF]
					[METHOD LeMail|Body]
						[PARAM]
							[BLOC Mail]
								<font face="arial" color="#000000" size="2">
								<strong>Adresse Ip</strong> : <span><a href="http://geotool.flagfox.net/?ip=[!SERVER::REMOTE_ADDR!]">[!SERVER::REMOTE_ADDR!]</a></span><br/><br />
								<strong>Objet de la demande</strong> : [!Subject!]<br/>
								<strong>Envoyé par</strong> : <span style="text-transform:uppercase">[!FullName!]</span><br/>
								<strong>Adresse e-mail</strong> : [!Email!]<br/>
								<strong>Message</strong> : [UTIL BBCODE][!Message!][/UTIL]<br /></font>
							[/BLOC]
						[/PARAM]
					[/METHOD]
					[METHOD LeMail|BuildMail][/METHOD]
					[METHOD LeMail|Send][/METHOD]
			
					// Enregistrement Contact + Message
					[STORPROC [!CONF::MODULE!]|Mod]
						[IF [!Key!]=NEWSLETTER]
							// 1 - on vérifie que le groupe existe, s'il n'existe pas on le créé
							[STORPROC Newsletter/GroupeEnvoi/10|GR|0|1]
							[/STORPROC]
					
							// 2 - on vérifie que le contact existe, s'il n'existe pas on le créé
							[STORPROC Newsletter/Contact/Email=[!Email!]|Con|0|1]
								[NORESULT]
									[OBJ Newsletter|Contact|Con]
									[METHOD Con|Set]
										[PARAM]Email[/PARAM]
										[PARAM][!Email!][/PARAM]
									[/METHOD]
									[METHOD Con|Set]
										[PARAM]Nom[/PARAM]
										[PARAM][!FullName!][/PARAM]
									[/METHOD]
									[METHOD Con|AddParent]
										[PARAM]Newsletter/GroupeEnvoi/[!GR::Id!][/PARAM]
									[/METHOD]
									[METHOD Con|Save][/METHOD]
								[/NORESULT]
							[/STORPROC]
							
							// 3 - enregistrement du message
							[OBJ Newsletter|Reception|Rec]
							[METHOD Rec|Set]
								[PARAM]Contenu[/PARAM]
								[PARAM][!Message!][/PARAM]
							[/METHOD]
							[METHOD Rec|Set]
								[PARAM]Sujet[/PARAM]
								[PARAM][!Subject!][/PARAM]
							[/METHOD]
							[METHOD Rec|AddParent]
								[PARAM]Newsletter/Contact/[!Con::Id!][/PARAM]
							[/METHOD]
							[METHOD Rec|Save][/METHOD]
						[/IF]
					[/STORPROC]
						
					// Mail de confirmation
					[LIB Mail|LeMail]
					[METHOD LeMail|Subject][PARAM]Message de [!Domaine!] - Confirmation[/PARAM][/METHOD]
					[METHOD LeMail|From][PARAM][!CONTACTMAIL!][/PARAM][/METHOD]
					[METHOD LeMail|ReplyTo][PARAM][!CONTACTMAIL!][/PARAM][/METHOD]
					[METHOD LeMail|To][PARAM][!Email!][/PARAM][/METHOD]
					[METHOD LeMail|Body]
						[PARAM]
							[BLOC Mail]
								Bonjour [!FullName!],<br />
								Nous avons bien reçu votre demande par email et vous remercions de votre confiance.<br />
								Nous traitons votre demande dans les plus brefs délais.
							[/BLOC]
						[/PARAM]
					[/METHOD]
					[METHOD LeMail|Priority][PARAM]5[/PARAM][/METHOD]
					[METHOD LeMail|BuildMail][/METHOD]
					[METHOD LeMail|Send][/METHOD]
					<div class="col-lg-4-1">
						<div class="alert alert-success">Message envoy&eacute; avec succ&egrave;s.</div>
					</div>
				[/IF]
			[/IF]
			
			<div class="col-lg-4-1">
				<input type="text" class="form-control" placeholder="FullName" name="FullName"  >
			</div>
			<div class="col-lg-4-2">
				<input type="text" class="form-control" placeholder="Email Adress" name="Email">
			</div>
			<div class="col-lg-4-3">
				<input type="text" class="form-control" placeholder="Subject" name="Subject">
			</div>
			<div class="col-lg-4-3">
				<textarea rows="13" id="textarea" class="form-control" placeholder="Message" name="Message"></textarea>
			</div>
			<div class="col-lg-4-3">
				<button class="btn btn-primary btn-send" type="submit" value="SendContact">Send message</button>
			</div>
		</div>
		<input type="text" name="Destinataire" readonly id="CoteEmailEc" value="[!CONTACTMAIL!]"  >
	</form>
    	<div class="col-lg-3-1">
		<div class="etiquette">
        		CONTACT DETAILS
        	</div>
		<div class="adresse">
	        	<div class="adresse-icon"></div>
       			<h6><input type="text" class="CoteTitreEC" readonly id="CoteTitreEC" ></h6>
			<input type="text" class="CoteAdressEC" readonly id="CoteAdressEC" >
        		<div class="adresse-fone"></div>
            		<p><strong>Phone :</strong> <input type="text" class="CotePhoneEc" readonly id="CotePhoneEc" ></p>
			<p><strong>Fax :</strong> <input type="text" class="CoteFaxEc" readonly id="CoteFaxEc" ></p>
	        </div>
      	</div>
</div>
 						