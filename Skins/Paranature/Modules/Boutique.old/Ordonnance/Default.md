	    <div class="well">
                <div class="row-fluid">
                    <div class="span3">
                        <img src="/Skins/[!Systeme::Skin!]/img/preparatrice.jpg" class="img-responsive" />
                    </div>
                    <div class="span9">
                        <h1>Préparation de vos ordonnances</h1>
                        <p>
                            Nous préparons vos ordonnances dès que vous nous avez envoyé une copie de votre ordonnance (scan / photo ...). Dès la réception de votre ordonnance nos préparateurs commenceront à la traiter et vous recevrez aussitôt un email ainsi qu'un sms vous informant de l'état de votre commande. Uen fois la commande prête, vous recevrez à nouveau un email ainsi qu'un sms contenant le numéro de préparation afin que vous puissiez retirer votre commande au guichet à la file rapide prévue à cet effet. Si vous avez des question n'hésitez pas à nous contacter.
                        </p>
                    </div>
                </div>
	    </div>


[IF [!SendContact!]!=]
    //enregistrement de la demande
    [OBJ Pharmacie|Ordonnance|Or]
    [STORPROC [!Or::Proprietes!]|P]
        [METHOD Or|Set]
            [PARAM][!P::Nom!][/PARAM]
            [PARAM][!Form_[!P::Nom!]!][/PARAM]
        [/METHOD]
    [/STORPROC]
    [IF [!Or::Verify()!]]
        //Enregistrement
        [METHOD Or|Save][/METHOD]
        <div class="alert alert-success">
            Votre demande de préparation d'ordonnance a bien étées prise en compte. Nous vous avertirons par mail et/ou par sms de l'état de la préparation.
        </div>
    [ELSE]
        //Affichage des erreurs
        [!Form_Error:=1!]
        <div class="alert alert-danger">
        [STORPROC [!Or::Error!]|E]
            [!Form_[!C::Prop!]_Error:=1!]
            <div>[!E::Message!]</div>
        [/STORPROC]
        </div>
    [/IF]
[/IF]

[IF [!SendContact!]=||[!Form_Error!]]
	    <form id="FormContact" method="post" action="/[!Lien!]" class="form-horizontal" enctype="multipart/form-data">
			<div class="well">
				    <div class="row-fluid">
						<div class="span12">
							    <div class="control-group  [IF [!Form_Nom_Error!]]error[/IF]">
									<label class="control-label" for="Form_Nom">Nom <span class="Obligatoire">*</span></label>
									<div class="controls">
										    <input type="text" id="Form_Nom" name="Form_Nom" class="input-block-level" style="text-transform:uppercase" value="[!Form_Nom!]" required/>
									</div>
							    </div>
							    <div class="control-group  [IF [!Form_Prenom_Error!]]error[/IF]">
									<label class="control-label" for="Form_Prenom">Prénom</label>
									<div class="controls">
										    <input type="text" name="Form_Prenom" class="input-block-level" value="[!Form_Prenom!]" />
									</div>
							    </div>
							    <div class="control-group [IF [!Form_Telephone_Error!]]error[/IF]">
									<label class="control-label" for="Form_Telephone">Numéro de téléphone</label>
									<div class="controls">
										    <input type="text" name="Form_Telephone" class="input-block-level"  value="[!Form_Telephone!]"/>
									</div>
							    </div>
							    <div class="control-group  [IF [!Form_Email_Error!]]error[/IF]">
									<label class="control-label" for="Form_Email">Adresse e-mail <span class="Obligatoire">*</span></label>
									<div class="controls">
										<input type="text" id="Form_Email" name="Form_Email" value="[!Form_Email!]" class="input-block-level" required/>
									</div>
							    </div>
							    <div class="control-group  [IF [!Form_Image_Error!]]error[/IF]">
									<label class="control-label" for="Form_Mail">Image de l'ordonnance <span class="Obligatoire">*</span></label>
									<div class="controls">
                                                                            <span class="exclusive btn-file">
                                                                                __BROWSE__ <input type="file" id="Form_Image" name="Form_Image" value="[!Form_Image!]" class="input-block-level" required/>
                                                                            </span>
									</div>
							    </div>
						</div>
				    </div>
			</div>
			[IF [!CAPTCHA_ACTIF!]]
			<div class="well">
				    <div class="row-fluid">
						<div class="span12">
							    <div class="control-group last [IF [!Form_Calc_Error!]]error[/IF]">
									<label class="control-label span6" for="Form_Nom">Merci de résoudre l'opération ci-dessous avant de valider <span class="Obligatoire">*</span></label>
									<div class="controls form-inline">
										    <input type="text" name="n3" id="n3" value="[!Utils::Random(9)!]" maxlength="2" readonly="readonly" class="span1"/>+
										    <input type="text" name="n4" value="[!Utils::Random(9)!]" maxlength="2" readonly="readonly" class="span1"/>
										    <span style="width:40px;text-align:center;">=</span>
										    <input type="text" name="tot2" value=""  maxlength="2" class="span1 [IF [!Calc2_Error!]]Error[/IF]" required/>
									</div>
							    </div>
						</div>
				    </div>
			</div>
			[/IF]
			<div class="well darker">
				    <div class="row-fluid">
						<input type="hidden" name="SendContact" value="1">
						<div class="span5 offset7">
							    <button type="submit" class="button_small pull-right" style="height: 29px;margin: 0 5px;background-color: green;">Envoyer</button>
							    <a href="/[!Systeme::CurrentMenu::Url!]" class="button_small pull-right">Annuler</a>
						</div>
				    </div>	
			</div>
	    </form>
	    
	    <div class="well">
			<div class="row-fluid">
				    <div class="span6">
						<p>Les champs marqués (<span class="Obligatoire">*</span>) sont obligatoires.</p>
						<p class="ContactTel">Vous pouvez aussi nous contacter par :<br />
						Tel : [!Systeme::User::Tel!]<br />
						Fax : [!Systeme::User::Fax!]</p>
				    </div>
				    <div class="span6">
						<p>
						Conformément à la loi n°78-17 du 6 janvier 1978 relative à l'informatique, aux fichiers et aux libertés,
						vous disposez d'un droit d'accès, de rectification, de suppression des informations qui vous concernent que vous pouvez exercer en vous adressant à
						[!Systeme::User::Nom!] - [!Systeme::User::Adresse!] - [!Systeme::User::CodPos!] [!Systeme::User::Ville!] - [!Systeme::User::Pays!].
						</p>
	    
						<p>
						[!TEXT_BAS!]
						</p>
				    </div>
			</div>
	    </div>

[/IF]
