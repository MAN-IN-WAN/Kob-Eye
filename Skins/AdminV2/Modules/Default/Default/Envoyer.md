<div class="ContenuEntete"> 
	[MODULE Systeme/Interfaces/BarreAction]
</div>

<div class="ContenuData"> 
	<div class="Panel"  style="position:absolute;top:0;bottom:0px;">
		[IF [!Action!]="Envoyer"]
			<h1>Envoie de la newsletter en cours ... </h1>
			//Creation de l envoi
			[OBJ Newsletter|Envoi|Env]
			[STORPROC [!Query!]|L|0|1][/STORPROC]
			[METHOD Env|Set][PARAM]DateEnvoi[/PARAM][PARAM][!TMS::Now!][/PARAM][/METHOD]
			[METHOD Env|Set][PARAM]Progression[/PARAM][PARAM]0[/PARAM][/METHOD]
			[METHOD Env|Set][PARAM]Sujet[/PARAM][PARAM][!L::Sujet!][/PARAM][/METHOD]
			[METHOD Env|Set][PARAM]Liste[/PARAM]
				[PARAM]
				[STORPROC Newsletter/GroupeEnvoi/[!Form_GroupEnvoi!]|G]
					[!G::Titre!]
				[/STORPROC]
				[/PARAM]
			[/METHOD]
			[METHOD Env|Set][PARAM]Contenu[/PARAM]
				[PARAM]
					[MODULE Newsletter/Modeles/[!L::Modele!]?Id=[!L::Id!]]
				[/PARAM]
			[/METHOD]
			//On ajoute le groupe d envoi en parent
			[METHOD Env|AddParent][PARAM]Newsletter/GroupeEnvoi/[!Form_GroupEnvoi!][/PARAM][/METHOD]
			[METHOD Env|AddParent][PARAM][!Query!][/PARAM][/METHOD]
			[METHOD Env|Save][/METHOD]

			//On demarre le processus d expedition
			//<iframe src="/[!Query!]/EnvoiProcess.htm?Form_GroupEnvoi=[!Form_GroupEnvoi!]&Env=[!Env::Id!]" style="width:100%;height:30px;visibility:hidden;">
			//</iframe>

			//Affichage barre de progression
			[HEADER]
				<style>
				/* progress bar container */  #progressbar{    border:1px solid black;    width:74%;    height:20px;    position:relative;    color:black;   }  /* color bar */  #progressbar div.progress{    position:absolute;    width:0;    height:100%;    overflow:hidden;    background-color:#369;  }  /* text on bar */  #progressbar div.progress .text{    position:absolute;    text-align:center;    color:white;  top:5px;}  /* text off bar */  #progressbar div.text{    position:absolute;    width:100%;    height:100%;    text-align:center;  top:5px;}
				</style>
			[/HEADER]
			<div id="progress" style="margin:auto;width:300px;height:15px;margin-top:200px;text-align:center;"><img src="/Skins/AdminV2/Img/ajax-loader.gif" style="width:128px;height:15px;"/> </div>
			<script>
				//Script de progression de l envoi de mail
				var pct=0;
				var handle=0;
				function update(){
					//On va cherche l information de progression
					var Url = "/Newsletter/Envoi/[!Env::Id!]/getProgress.htm";
					var Aja = new Request.HTML().get('Url');
					Ajax.addEvent('success',setValue);
				}
				function setValue(Val) {
					if(Val>=100){
						clearInterval(handle);
						$$("#run").value("start");
						pct=0;
					}
					
				}
				function start(){
					handle=setInterval("update()",2000);
				}
				//Demarrage Envoi
				Req = new Request.HTML({
				        url:"/[!Query!]/EnvoiProcess.htm?Form_GroupEnvoi=[!Form_GroupEnvoi!]&Env=[!Env::Id!]",
					method:"GET",
					async:true,
					onSuccess:function (data) {
						document.getElementById('progress').innerHTML = "ENVOI TERMINE AVEC SUCCES.";
					}
				});
				Req.send();
				//Demarrage barre de progression
				//start();
			</script>
		[ELSE]

			<h1>Envoyer la newsletter </h1>
			<form action="" method="post" name="frm" >
			<div class="Propriete">
				<div class="ProprieteTitre">Lettre &agrave; envoyer : </div>
				<div class="ProprieteValeur">&nbsp;
					[STORPROC [!Query!]|L]
						[!L::Sujet!]
					[/STORPROC]
				</div>
			</div>
			<div class="Propriete">
				<div class="ProprieteTitre">Groupe d'envoi : </div>
				<div class="ProprieteValeur">&nbsp;
					<select name="Form_GroupEnvoi">
					[STORPROC Newsletter/GroupeEnvoi|Gr]
						<option value="[!Gr::Id!]">[!Gr::Titre!]</option>
					[/STORPROC]
					</select>
				</div>
			</div>
			[STORPROC [!Query!]|L]
			<iframe src="/Newsletter/Modeles/[!L::Modele!].htm?Id=[!L::Id!]" style="width:99%;height:75%;position:absolute;left:10px;right:10px;bottom:50px;top:90px;overflow:auto;">
			</iframe>
				[/STORPROC]
			<input type="hidden" name="Action" value="Envoyer"/>
			<div class="Nav"><INPUT TYPE="SUBMIT"  class="BoutonBlanc" VALUE="Envoyer" name="TYarggla">
			</div>
			</form>
		[/IF]
	</div>
</div>
