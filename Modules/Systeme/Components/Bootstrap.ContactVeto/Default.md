<iframe class="contactMaps" height="400" width="100%" style="border:0"  src="https://www.google.com/maps/embed/v1/place?q=Occitanie,France&key=AIzaSyA8WzSVq6D0wZFAsNwdsqSExgHlNO3_S68" allowfullscreen style="width: 100%;"></iframe>
[IF [!CONTACTMAIL!]=]
	[!CONTACTMAIL:=[!CONF::MODULE::SYSTEME::CONTACT!]!]
[/IF]
[IF [!SendContact!]!=]
	//Verification des informations du formulaire
	[!C_Error:=0!]
	[IF [!nom!]][ELSE][!C_Nom_Error:=1!][!C_Error:=1!][/IF]
	[IF [!mail!]][ELSE][!C_Mail_Error:=1!][!C_Error:=1!][/IF]
	[IF [!n3:+[!n4!]!]!=[!tot2!]][!C_Calc_Error:=1!][!C_Error:=1!][/IF]
	[IF [!C_Error!]]
		// Si il y a des erreurs, on les affiche
	    <div class="alert alert-error">
			<strong>Veuillez remplir les champs obligatoires suivants :</strong>
	    	<ul>
				[IF [!C_Nom_Error!]]<li>Merci de renseigner votre Nom</li>[/IF]
				[IF [!C_Mail_Error!]]<li>Merci de renseigner votre adresse email</li>[/IF]
				[IF [!C_Calc_Error!]=1]<p>Calcul de vérification erroné</p>[/IF]
			</ul>
		</div>
	[ELSE]
		// Sinon envoi du mail
		[LIB Mail|LeMail]
		[METHOD LeMail|Subject][PARAM]Message de [!Domaine!] - [!C_Objet!][/PARAM][/METHOD]
		[METHOD LeMail|From][PARAM][!C_Mail!][/PARAM][/METHOD]
		[METHOD LeMail|ReplyTo][PARAM][!C_Mail!][/PARAM][/METHOD]
		[METHOD LeMail|To][PARAM][!CONTACTMAIL!][/PARAM][/METHOD]
		[IF [!CONTACTMAILCC!]!=][METHOD LeMail|Cc][PARAM][!CONTACTMAILCC!][/PARAM][/METHOD][/IF]
		[IF [!CONTACTMAILBCC!]!=][METHOD LeMail|Bcc][PARAM][!CONTACTMAILBCC!][/PARAM][/METHOD][/IF]
		[METHOD LeMail|Body]
			[PARAM]
				[BLOC Mail]
					<font face="arial" color="#000000" size="2">
					<strong>Envoyé par</strong> : <span style="text-transform:uppercase">[!nom!]</span> [!prenom!]<br/>
					[IF [!numero!]]
					<strong>Numéro de téléphone</strong> : [!numero!]<br/>
					[/IF]
					<strong>Adresse e-mail</strong> : [!mail!]<br/>
					[IF [!message!]]
    					<strong>Message</strong> : [!message!]<br /></font>
					[/IF]
					<strong>Envoyé par</strong> : <span style="text-transform:uppercase">[!nom!]</span> [!prenom!]<br/>
				[/BLOC]
			[/PARAM]
		[/METHOD]
		[METHOD LeMail|BuildMail][/METHOD]
		[METHOD LeMail|Send][/METHOD]
		// Mail de confirmation
		[LIB Mail|LeMail]
		[METHOD LeMail|Subject][PARAM]Message de [!Domaine!] - Confirmation[/PARAM][/METHOD]
		[METHOD LeMail|From][PARAM][!CONTACTMAIL!][/PARAM][/METHOD]
		[METHOD LeMail|ReplyTo][PARAM][!CONTACTMAIL!][/PARAM][/METHOD]
		[METHOD LeMail|To][PARAM][!C_Mail!][/PARAM][/METHOD]
		[METHOD LeMail|Body]
			[PARAM]
				[BLOC Mail]
					Bonjour [!prenom!] <span style="text-transform:uppercase">[!nom!]</span>,<br />
					Nous avons bien reçu votre demande par email et vous remercions de votre confiance.<br />
					Nous traitons votre demande dans les plus brefs délais.
				[/BLOC]
			[/PARAM]
		[/METHOD]
		[METHOD LeMail|Priority][PARAM]5[/PARAM][/METHOD]
		[METHOD LeMail|BuildMail][/METHOD]
		[METHOD LeMail|Send][/METHOD]
		<div class="alert alert-success">Message envoy&eacute; avec succ&egrave;s.</div>
		<div class="alert alert-info">Un mail de confirmation vous a été adressé.</div>
		<div class="well darker">
	        <div class="row-fluid">
            <input type="hidden" name="SendContact" value="1">
	            <div class="span5 offset1">
				  <a href="/[!Systeme::CurrentMenu::Url!]" class="btn btn-block btn-info">Nouveau message</a>
				</div>
	            <div class="span5 ">
				  <a href="/[!Systeme::CurrentMenu::Url!]" class="btn btn-block btn-danger">Retour à l'accueil</a>
				</div>
			</div>
        </div>
	[/IF]
[/IF]
[IF [!SendContact!]=||[!C_Error!]]
	    <form method="post" class="Contact">
        	<div class="row">
                <div class="col-lg-7 col-md-9 col-sm-9 ">
                    <label for="nom" class="labelform ">Nom :</label>
        		    <input type="text" placeholder="Nom" id="nom" class="inputform" >
        		    <label for="prenom" class="labelform">Prénom :</label>  
        		    <input type="text" placeholder="Prenom" id="prenom" class="inputform">
        		    <label for="mail" class="labelform">Adresse mail de contact :</label>
        		    <input type="mail" placeholder="Adresse Mail" id="mail" class="inputform">
        		    <label for="numero" class="labelform">Téléphone :</label>
        		    <input type="text" placeholder="Téléphone" id="numero" class="inputform">
        		    <label for="message" class="labelform">Message :</label>
        		    <textarea id="message" placeholder="Votre message ici" class="textform"></textarea>
        		    <!-- <img src="[!Domaine!]/Skins/Vetoccitan1/Images/recaptacha.jpg" class="img-responsive" alt="controle" title="controle" /><br>-->
        		    <button type="submit">Envoyer</button>
                </div>
                <div class="col-lg-5 col-sm-3 col-md-3">
                // vide correspond à la maquette
                </div>
        	</div>
        </form>
[/IF]
