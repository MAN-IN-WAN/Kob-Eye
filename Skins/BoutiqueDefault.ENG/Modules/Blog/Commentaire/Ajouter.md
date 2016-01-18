[STORPROC Systeme/User/[!Systeme::User::Id!]|This][/STORPROC]
//Si le formulaire est valide
[IF [!Val!]=OK]
<div class=" alert alert-success" >
	Merci !<br />
	Le modérateur regarde votre commentaire et le publiera s'il est conforme à la charte du blog !
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
		
	[ELSE]
		<div class=" alert alert-danger">Veuillez remplir les champs obligatoires suivants :<br /> 
			[IF [!Auteurs!]=]Votre Nom ou votre pseudo<br />[/IF]
			[IF [!UserMail!]=]Votre adresse email <br />[/IF]
			[IF [!Calc_Error!]=1]Votre calcul est faux ! <br />[/IF]
			//On met le double egal pour permettre de valider tout type de caractere
			[IF [!C_Com!]==]Merci de laisser un commentaire[/IF]
		</div>
	[/IF]
[/IF]
[IF [!valide!]!=Enregistrer||[!C_Error!]]
<div class="well">
	<form action="#" method="post" id="AddComment" class=" form-horizontal">
		<h2><a name="Commentaire" title="Ajouter un commentaire &agrave; [!Post::Titre!]" class="Ancre">Ajouter un commentaire</a></h2>
		<div class="control-group">
			<label [IF [!Auteurs_Error!]]class="Error2"[/IF] class="control-label" for="Auteurs">Nom ou pseudo</label>
			<div class="controls">
				<input type="text" id="Auteurs" name="Auteurs" value="" class="[IF [!Auteurs_Error!]]Error[/IF]" tabindex="1"/>
			</div>
		</div>
		<div class="control-group">
			<label [IF [!UserMail_Error!]]class="Error2"[/IF] class="control-label" for="UserMail">Adresse e-mail</label>
			<div class="controls">
				<input type="text" id="UserMail" name="UserMail" value="" class=" [IF [!UserMail_Error!]]Error[/IF]" tabindex="2"/>
			</div>
		</div>
		<div class="control-group">
			<p style="font-style:italic;padding:0;margin:0;">( ne sera pas visible avec le commentaire )</p>
		</div>
		<div class="control-group">
			<label class="control-label" for="UserSite">Site ( sans <i>http://</i> )</label>
			<div class="controls">
				<input type="text" id="UserSite" name="UserSite" value="" class="" tabindex="3"/>
			</div>
		</div>
		<div class="control-group">
			<label [IF [!C_Com_Error!]]class="Error2"[/IF] class="control-label" for="C_Com">Commentaire</label>
			<div class="controls">
				<textarea cols="5" rows="5" id="C_Com" name="C_Com" class=" [IF [!C_Com_Error!]]Error[/IF]" tabindex="4">[!C_Com!]</textarea>
			</div>
		</div>
		<div class="control-group">
			<p [IF [!Calc_Error!]]class="Error3"[/IF] class="control-label" for="n1">Résoudre l'opération ci-dessous pour valider votre commentaire</p>
			<div class="controls">
				<input type="text" id="n1" name="n1" id="n1" value="[!Utils::Random(9)!]"  maxlength="2" readonly="readonly"   style="font-weight:bold;float:none;width:15px;background:transparent;text-align:center;"/>+<input type="text" name="n2" value="[!Utils::Random(9)!]" maxlength="2" readonly="readonly" style="font-weight:bold;float:none;width:15px;background:transparent;text-align:center;"/> =&nbsp; <input type="text" name="tot" value=""  maxlength="2"  style="float:none;width:20px;" class=" [IF [!Calc_Error!]]Error[/IF]" tabindex="5"/>
			</div>
		</div>
		<div class="row-fluid">
			<input type="hidden" name="valide" value="Enregistrer"/>
			<input type="submit" value="Proposer"  name="" tabindex="6" class="btn btn-success"/>
		</div>
	</form>
</div>
[/IF]
