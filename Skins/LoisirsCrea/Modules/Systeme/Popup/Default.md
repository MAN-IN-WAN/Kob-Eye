//Affichage d'un popup
<script type="text/javascript">
	$(document).ready(function () {
		show();
	});

	function show() {
		$('PopupDebut').setStyle ("display","block");
	}

 	function hidePopup() {
		$('PopupDebut').setStyle ("display","none");
	}
</script>

<div id="PopupDebut" >
	<div class="PopupBack" ></div>
	<div class="Popup" >
		<div class="PopupCentre">
			[SWITCH [!LeQuel!]|=]
				[CASE InscNewsletter]
					[IF [!EmailNewsletter!]]
						[IF [!Action!]=Desabo]
							[STORPROC Newsletter/Contact/Email=[!EmailNewsletter!]|Con|0|1]
								[METHOD Con|DelParent][PARAM]Newsletter/GroupeEnvoi/1[/PARAM][/METHOD]
								[METHOD Con|DelParent][PARAM]Newsletter/GroupeEnvoi/2[/PARAM][/METHOD]
								[METHOD Con|AddParent][PARAM]Newsletter/GroupeEnvoi/3[/PARAM][/METHOD]
								[METHOD Con|Save][/METHOD]
								<h2>Vous êtes désabonné(e)!</h2>
							[/STORPROC]
						[ELSE]
							[COUNT Newsletter/Contact/Email=[!EmailNewsletter!]|C]
							[IF [!C!]=0]
								[OBJ Newsletter|Contact|Con]
								[METHOD Con|Set]
									[PARAM]Email[/PARAM][PARAM][!EmailNewsletter!][/PARAM]
								[/METHOD]
								[METHOD Con|Set][PARAM]Actif[/PARAM][PARAM]1[/PARAM][/METHOD]
								[METHOD Con|AddParent][PARAM]Newsletter/GroupeEnvoi/2[/PARAM][/METHOD]
								[METHOD Con|Save][/METHOD]
								<h2>Votre inscription a bien été prise en compte :[!EmailNewsletter!]</h2>
							[ELSE]
								[STORPROC Newsletter/Contact/Email=[!EmailNewsletter!]|Con|0|1]
									[METHOD Con|AddParent][PARAM]Newsletter/GroupeEnvoi/2[/PARAM][/METHOD]
									[METHOD Con|Save][/METHOD]
									<h2>Vous êtes déjà inscrit(e)</h2>
								[/STORPROC]
							[/IF]
						[/IF]
					[/IF]
				[/CASE]
			[/SWITCH]
		</div>
		<div class="LiensFermePopup"  onclick="javascript:hidePopup();"></div>
	</div>
</div>


