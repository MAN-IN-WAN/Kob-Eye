[STORPROC Systeme/User/[!Systeme::User::Id!]|This][/STORPROC]
//Si le formulaire est valide
[IF [!Val!]=OK]
<div style="width:98%;color:#FFBB00;font-size:11px;margin:10px 5px 10px;font-weight:bold;">
	Merci !<br />
	Votre commentaire est en ligne !
</div>
[/IF]
[IF [!valide!]=Enregistrer]
	//On verifie les champs du formulaire
	[IF [!Auteurs!]=][!Auteurs_Error:=1!][!C_Error:=1!][/IF]
	[IF [!UserMail!]=][!UserMail_Error:=1!][!C_Error:=1!][/IF]
	[IF [!C_Com!]==][!C_Com_Error:=1!][!C_Error:=1!][/IF]	
	[IF [!n1:+[!n2!]!]!=[!tot!]][!Calc_Error:=1!][!C_Error:=1!][/IF]
	[IF [!C_Error!]!=1]
		//Si pas d erreur,,on poste le commentaire
		[MODULE Blog/Commentaire/Save|GLOBAL]
		//ENVOE !!!!
	[ELSE]
		<div style="width:98%;color:#FFBB00;font-size:11px;margin:10px 5px 10px;font-weight:bold;">Veuillez remplir les champs obligatoires suivants :<br /> 
			[IF [!Auteurs!]=]Votre Nom ou votre pseudo<br />[/IF]
			[IF [!UserMail!]=]Votre adresse email <br />[/IF]
			[IF [!Calc_Error!]=1]Votre calcul est faux ! <br />[/IF]
			//On met le double egal pour permettre de valider tout type de caractere
			[IF [!C_Com!]==]Merci de laisser un commentaire[/IF]
		</div>
	[/IF]
[/IF]
[IF [!valide!]!=Enregistrer||[!C_Error!]]
	<form action="#" method="post" id="AddComment">
		<h2><a name="Commentaire" title="Ajouter un commentaire &agrave; [!Post::Titre!]" class="Ancre">Ajouter un commentaire</a></h2>
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
			<p style="font-style:italic;padding:0;margin:0;">( ne sera pas visible avec le commentaire )</p>
		</div>
		<div class="LigneForm">
			<label>Site ( sans <i>http://</i> )</label>
			<input type="text" name="UserSite" value="[IF [!Systeme::User::Id!]!=120][!Systeme::User::Site!][ELSE][!UserSite!][/IF]" class="" tabindex="3"/>
		</div>
		<div class="LigneForm">
			<label [IF [!C_Com_Error!]]class="Error2"[/IF]>Commentaire</label>
			<textarea cols="5" rows="5" name="C_Com" class=" [IF [!C_Com_Error!]]Error[/IF]" tabindex="4">[!C_Com!]</textarea>
		</div>
		<div >
			<p [IF [!Calc_Error!]]class="Error3"[/IF]>Résoudre l'opération ci-dessous pour valider votre commentaire</p>
			<input type="text" name="n1" id="n1" value="[!Math::Random(9)!]"  maxlength="2" readonly="readonly"   style="font-weight:bold;float:none;width:15px;background:transparent;color:white;text-align:center;"/>+<input type="text" name="n2" value="[!Math::Random(9)!]" maxlength="2" readonly="readonly" style="font-weight:bold;float:none;width:15px;background:transparent;color:white;"/> =&nbsp; <input type="text" name="tot" value=""  maxlength="2"  style="float:none;width:20px;" class=" [IF [!Calc_Error!]]Error[/IF]" tabindex="5"/>
		</div>
		<div class="LigneForm">
			<input type="hidden" name="valide" value="Enregistrer"/>
			<input type="submit" value=""  name="" class="BtnComment" tabindex="6"/>
		</div>
	</form>
[/IF]
