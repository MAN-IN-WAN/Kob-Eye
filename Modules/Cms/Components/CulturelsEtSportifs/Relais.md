[HEADER]
	<script type='text/javascript' src='/Skins/AdminV2/Js/datetimepicker_css.js'></script>
[/HEADER]
<div id="EncartForm" >
	
	<form id="FormRechAvanc" enctype="application/x-www-form-urlencoded"  method="get" action="/[!Lien!]">
		<div style="background:#EEEFE9;padding-bottom:5px;">
		<h1 class="Titre">Recherche [IF [!ObCl!]~Client]de relais[ELSE][!ObCl!][/IF]</h1>
		[OBJ [!Module!]|[!ObCl!]|O]
		[STORPROC [!O::SearchOrder!]|Prop]
			[SWITCH [!Prop::type!]|=]
				[CASE image]
				[/CASE]
				[CASE int]
				[/CASE]
				[CASE link]
				[/CASE]
				[CASE date]
					[IF [!Lien!]~RelaisSociaux]
					[ELSE]
						<div class="ForSpe">
							<label>[!Prop::Description!]</label>
							du <input onclick="javascript:NewCssCal('C_Date','ddmmyyyy','dropdown',true);" type="text" class="ncalendar" name="[!Prefixe!][!Prop::Nom!]Du" value="[![!Prefixe!][!Prop::Nom!]!]"/>
							au <input onclick="javascript:NewCssCal('C_Date','ddmmyyyy','dropdown',true);" type="text" class="ncalendar" name="[!Prefixe!][!Prop::Nom!]Au" value="[![!Prefixe!][!Prop::Nom!]!]"/>
						</div>
					[/IF]
				[/CASE]
				[DEFAULT]
					<div class="ForSpe">
					<label>[!Prop::Description!]</label>
					[IF [!Prop::Values!]]
						<select value="[!Prop::Valeur!]" name="[!Prefixe!][!Prop::Nom!]" id="[!Prefixe!][!Prop::Nom!]" onChange="setFilter()">
							<option value="">Indifférent</option>
							[STORPROC [!Prop::Values!]|P]<option value="[!P!]" [IF [!P!]=[![!Prefixe!][!Prop::Nom!]!]]selected="selected"[/IF]>[!P!]</option>[/STORPROC]
						</select>
					[ELSE]
						[IF [!Prop::query!]]
							[STORPROC [![!Prop::query!]:/::!]|Q|0|1][/STORPROC]
							[STORPROC [![!Prop::query!]:/::!]|T|1|1][/STORPROC]
							[STORPROC [![!Prop::query!]:/::!]|S|2|1][/STORPROC]
							<select value="[!Prop::Valeur!]" name="[!Prefixe!][!Prop::Nom!]">
								//[STORPROC [!Q!]|P|0|1000]<option value="[!P::[!T!]!]">[!P::[!S!]!]</option>[/STORPROC]
								<option value="">Indifférent</option>
								[STORPROC [!Q!]|P|0|1000|Nom|ASC]<option value="[!P::[!T!]!]" [IF [!P::[!T!]!]=[![!Prefixe!][!Prop::Nom!]!]]selected="selected"[/IF]>[!P::[!T!]!]</option>[/STORPROC]
							</select>
						[ELSE]
							<input type="text" name="[!Prefixe!][!Prop::Nom!]"  id="[!Prefixe!][!Prop::Nom!]" value="[![!Prefixe!][!Prop::Nom!]!]"/>
						[/IF]
					[/IF]
					</div>
				[/DEFAULT]
			[/SWITCH]
		[/STORPROC]
		<p> 
			<input type="submit" name="Recherche"  value="Rechercher"  class="BtnSearch"/>
		</p>
		</div>

		[IF [!C!]>1]
			//Affiche la liste
			[MODULE Reservation/Client/Liste?Chemin=[!REQUETE!]&REQUETE=[!REQUETE!]&Filter=[!FILTER!]]
		[ELSE]
			//Affiche un message Pas de resultat
			[MODULE Reservation/Spectacle/Message?Chemin=[!REQUETE!]&Filter=[!FILTER!]]
		[/IF]
	</form>
</div>