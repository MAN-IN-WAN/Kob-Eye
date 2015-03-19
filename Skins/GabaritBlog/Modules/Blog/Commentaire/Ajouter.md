[STORPROC Systeme/User/[!Systeme::User::Id!]|This][/STORPROC]
//Si le formulaire est valide
[IF [!Val!]=OK]
	<p class="Bold">Merci !<br />Votre commentaire est en ligne !</p>
[/IF]
<h2><a name="Commentaire" title="Ajouter un commentaire &agrave; [!Post::Titre!]" class="Ancre">Ajouter un commentaire</a></h2>
[IF [!valide!]=Enregistrer]
	//On verifie les champs du formulaire
	[IF [!Auteurs!]=][!Auteurs_Error:=1!][!C_Error:=1!][/IF]
	[IF [!UserMail!]=][!UserMail_Error:=1!][!C_Error:=1!][/IF]
	[IF [!C_Com!]==][!C_Com_Error:=1!][!C_Error:=1!][/IF]	
	[IF [!n1:+[!n2!]!]!=[!tot!]][!Calc_Error:=1!][!C_Error:=1!][/IF]
	[IF [!C_Error!]!=1]
		//Si pas d erreur,,on poste le commentaire
		[MODULE Blog/Commentaire/Save|GLOBAL]
	[ELSE]
		<div class="Error3">
			[IF [!Auteurs!]=]Merci de renseigner votre Nom ou votre pseudo<br />[/IF]
			[IF [!UserMail!]=]Merci de renseigner votre adresse email <br />[/IF]
			[IF [!Calc_Error!]=1]Votre calcul est faux ! <br />[/IF]
			//On met le double egal pour permettre de valider tout type de caractere
			[IF [!C_Com!]==]Merci de laisser un commentaire[/IF]
		</div>
	[/IF]
[/IF]
[IF [!valide!]!=Enregistrer||[!C_Error!]]
	<form action="#" method="post" id="AddComment">
		<div class="LigneForm">
			<label [IF [!Auteurs_Error!]]class="Error2"[/IF]>Nom ou pseudo</label>
			<input type="text" name="Auteurs" value="[IF [!Systeme::User::Id!]!=120][!Systeme::User::Prenom!] [!Systeme::User::Nom!][ELSE][!Auteurs!][/IF]" class=" [IF [!Auteurs_Error!]]Error[/IF]" tabindex="1"/>
		</div>
		<div class="LigneForm">
			<label [IF [!UserMail_Error!]]class="Error2"[/IF]>Adresse e-mail</label>
			<input type="text" name="UserMail" value="[IF [!Systeme::User::Id!]!=120][!Systeme::User::Mail!][ELSE][!UserMail!][/IF]" class=" [IF [!UserMail_Error!]]Error[/IF]" tabindex="2"/>
		</div>
		<div class="LigneForm">
			<!--<label>&nbsp;</label>-->
			<p class="Italic">(ne sera pas visible avec le commentaire)</p>
		</div>
		<div class="LigneForm">
			<label>Site <span class="Italic">(sans http://)</span></label>
			<input type="text" name="UserSite" value="[IF [!Systeme::User::Id!]!=120][!Systeme::User::Site!][ELSE][!UserSite!][/IF]" class="" tabindex="3"/>
		</div>
		<div class="LigneForm">
			<label [IF [!C_Com_Error!]]class="Error2"[/IF]>Commentaire</label>
			<textarea cols="5" rows="5" name="C_Com" class=" [IF [!C_Com_Error!]]Error[/IF]" tabindex="4">[!C_Com!]</textarea>
		</div>
		<div>
			<p [IF [!Calc_Error!]]class="Error3"[/IF]>Résoudre l'opération ci-dessous pour valider votre commentaire</p>
			<input type="text" name="n1" id="n1" value="[!Math::Random(10)!]"  maxlength="2" readonly="readonly"   style="font-weight:bold;float:none;width:20px;background:transparent;text-align:center;"/>+<input type="text" name="n2" value="[!Math::Random(10)!]" maxlength="2" readonly="readonly" style="font-weight:bold;float:none;width:20px;background:transparent;text-align:center;"/> =&nbsp; <input type="text" name="tot" value=""  maxlength="2"  style="float:none;width:30px;" class=" [IF [!Calc_Error!]]Error[/IF]" tabindex="5"/>
		</div>
		<div class="LigneForm">
			<input type="hidden" name="valide" value="Enregistrer"/>
			<input type="submit" value="Valider"  name="" class="BtnComment" tabindex="6"/>
		</div>
	</form>
[/IF]
