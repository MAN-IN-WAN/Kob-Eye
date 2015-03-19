<div class="ContenuEntete"> 
	[MODULE Systeme/Interfaces/BarreAction]
</div>

<div class="ContenuData"> 
	<div class="Panel"  style="position:absolute;top:0;bottom:0px;">
		<h1>Reprise de l'envoie de la newsletter en cours ... </h1>
		//Creation de l envoi
		[STORPROC [!Query!]|Env]
			[METHOD Env|Set][PARAM]Busy[/PARAM][PARAM]0[/PARAM][/METHOD]
			[METHOD Env|Save][/METHOD]
			[STORPROC Newsletter/GroupeEnvoi/Envoi/[!Env::Id!]|Ge|0|1][/STORPROC]
			//Affichage barre de progression
			[HEADER]<script type='text/javascript' src='/Skins/AdminV2/Js/jqueryprogressbar.js'></script>[/HEADER]
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
				        url:"/[!Query!]/EnvoiProcess.htm?Form_GroupEnvoi=[!Ge::Id!]&Env=[!Env::Id!]",
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

		[/STORPROC]
	</div>
</div>