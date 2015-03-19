<div id="contact_block">
		<div id="contact_canvas"></div>
		<div class="contact_helper">
			<div class="btn-group">
				<button class="btn contactotop"><i class="icon-chevron-up"></i></button>
				<button class="btn contacttobottom"><i class="icon-chevron-down"></i></button>
				<button class="btn cntactocenter"><i class="icon-screenshot"></i></button>
			</div>
		</div>
		[STORPROC [!Query!]|C][/STORPROC]
		<div class="contact_form">
			<div class="contact_form_inner">
				<h1>[!C::Titre!]</h1>
				<div class="contact_content">
					<div class="contact_left">	
						<h2>[!C::SousTitre!]</h2>	
						<div class="contact_note">
							[!C::Contenu!]
						</div>
						<ul>
							<li>
								<i class="icon-envelope"></i>
								<div class="loc-content">
									<div>[!Systeme::User::Email!]</div>							
								</div>
							</li>			
							<li class="view-map">
								<i class="icon-map-marker"></i>
								<div class="loc-content">
									<div>Voir le détail de la carte</div>							
								</div>
							</li>						
						</ul>
					</div>
					<div class="contact_right contact-wrapper">
						<h2>Formulaire de contact</h2>
						<form id="contactform">
							<div class="input-wrapper">
								<label for="contact_name">Nom : <span class="contact_error"></span> </label>
								<input id="contact_name" class="textfield" type="text" value="" name="contact_name">
							</div>
							<div class="input-wrapper">
								<label for="contact_email">Votre Email : <span class="contact_error"></span> </label>
								<input id="contact_email" class="textfield" type="text" value="" name="contact_email">
							</div>
							<div class="input-wrapper">
								<label for="contact_message">Message : <span class="contact_error"></span> </label>
								<textarea id="contact_message" class="required" name="contact_message"></textarea>
							</div>
							<div class="input-wrapper contact_button">
								<button class="btn btn-inverse"><i class="icon-white icon-ok"></i> Envoyer message</button>
								<div class="contact_loader">&nbsp;</div>
							</div>
						</form>
					</div>
				</div>
					
				<div class="icon-white icon-chevron-right hideform">hidethis</div>
			</div>
			<div class="contactflag">
				<div class="contactflagwrapper">
					<div class="misc-mail"></div>
				</div>
			</div>
		</div>
		
		<div class="contact_location">
			<div class="contact_location_inner">
				<h1>Notre position</h1>
				<div class="icon-white icon-chevron-left hidelocation">hidethis</div>
				<div class="locationlist">				
					<div data-alt="Cliquez pour obtenir notre position" class="locdetail" data-x="44.167407" data-y="4.613687" data-index="0">					
						<h2>Position</h2>
						<ul>
							<li>
								<i class="icon-map-marker icon-standard"></i>
								<div class="loc-content">
									<div> [!Systeme::User::Adresse!]</div>
									<div> [!Systeme::User::CodPos!], [!Systeme::User::Ville!]</div>
								</div>
							</li>						
							<li>
								<i class="phone-icon"></i>
								<div class="loc-content">
									<div> [!Systeme::User::Tel!]</div>		
									<div> [!Systeme::User::Fax!]</div>
								</div>
							</li>						
						</ul>
					</div>
				</div>	
			</div>
			<div class="locationflag">
				<div class="locationflagwrapper">
					<div class="misc-safari"></div>
				</div>
			</div>
		</div>		
		<div id="jeg-loader"></div>
	</div>
  	
  	<script type="text/javascript" src="/Skins/JPhotolio/js/jegcontact.js"></script>
	<script type="text/javascript">
	jQuery(document).ready(function($)
	{		
		resize_window("#contact_canvas");
	
		/** bind jeg default **/
		$(window).jegdefault({
			curtain : 1,
			rightclick 	: 1,
			clickmsg	: "Disable Right Mouse Click" 
		});
	
		var jegcontact = $("#contact_block").jegcontact({
			location			: [{"x":"44.167407","y":"4.613687","title":"Ma position","address":["[!Systeme::User::Adresse!]","[!Systeme::User::CodPos!], [!Systeme::User::Ville!]"],"phone":[" [!Systeme::User::Tel!]"," [!Systeme::User::Fax!]"]}],
			zoomfactor			: 14,
			cantgetdirection 	: "Impossible de trouver la direction . . . ",
			dummyposition		: "Direction non trouvée, utilisation d'une direction erronée . . . ",
			geonotsupport		: "Votre navigateur ne supporte pas la géolocalisation . . .",
			entername			: "Veuillez entrer votre nom",
			nameminlength		: "Au moins {0} caractères requis",
			enteremail			: "Veuillez entrer votre mail",
			validemail			: "Veuillez saisir un email valide",
			entermessage		: "Veuillez entrer un message",
			messageminlength	: "Au moins {0} caractères requis",
			sendmessage			: "Envoi du message . . .",
			messagesent			: "Message envoyé . . . ",
			failsentmessage		: "Erreur lors de l'envoi du message . . . "
		});
	});
	</script>
  	