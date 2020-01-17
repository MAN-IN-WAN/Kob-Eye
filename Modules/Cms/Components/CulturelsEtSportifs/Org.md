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
					<div class="ForSpe">
						<label>[!Prop::Description!]</label>
						du <input onclick="javascript:NewCssCal('C_Date','ddmmyyyy','dropdown',true);" type="text" class="ncalendar" name="[!Prefixe!][!Prop::Nom!]Du" value="[![!Prefixe!][!Prop::Nom!]!]" />
						au <input onclick="javascript:NewCssCal('C_Date','ddmmyyyy','dropdown',true);" type="text" class="ncalendar" name="[!Prefixe!][!Prop::Nom!]Au" value="[![!Prefixe!][!Prop::Nom!]!]" />
					</div>
				[/CASE]
				[DEFAULT]
					<div class="ForSpe">
						[IF [!Org_Departement!]=&&[!Prop::Nom!]="Ville"]		
							//cas du departement non choisi
						[ELSE]
							<label>[!Prop::Description!]</label>
							[IF [!Prop::Values!]]
								<select name="[!Prefixe!][!Prop::Nom!]" id="[!Prefixe!][!Prop::Nom!]" onChange="setFilter()">
									<option value="">Indifférent</option>
									[STORPROC [!Prop::Values!]|P]
										<option value="[!P!]" [IF [!P!]=[![!Prefixe!][!Prop::Nom!]!]]selected="selected"[/IF]>
											[!P!]
										</option>
									[/STORPROC]
								</select>
							[ELSE]
								[IF [!Prop::query!]]
									[IF[!Prop::Nom!]="Ville"] 
										[IF[!Org_Departement!]!="Indifferent"]
											[STORPROC [!Geographie/Pays/6/Departement/[!Org_Departement!]/Ville::Nom:/::!]|Q|0|1][/STORPROC]
											[STORPROC [![!Geographie/Pays/6/Departement/[!Org_Departement!]/Ville::Nom!]:/::!]|T|1|1][/STORPROC]
											[STORPROC [![!Geographie/Pays/6/Departement/[!Org_Departement!]/Ville::Nom!]:/::!]|S|2|1][/STORPROC]
										[/IF]
									[ELSE]
										[STORPROC [![!Prop::query!]:/::!]|Q|0|1][/STORPROC]
										[STORPROC [![!Prop::query!]:/::!]|T|1|1][/STORPROC]
										[STORPROC [![!Prop::query!]:/::!]|S|2|1][/STORPROC]
									[/IF]
									<select name="[!Prefixe!][!Prop::Nom!]" onChange="javascript:submit();">
										[IF[!Prop::Nom!]="Departement"]
											//<option value="Gard" [IF[!Org_Departement!]="Gard"]selected[/IF]>Gard</option>
											<option value="Hérault" [IF[!Org_Departement!]="Hérault"]selected[/IF]>Hérault</option>
											//<option value="Lozère" [IF[!Org_Departement!]="Lozère"]selected[/IF]>Lozère</option>
											//<option value="Pyrénées Orientales" [IF[!Org_Departement!]="Pyrénées Orientales"]selected[/IF]>Pyrénées Orientales</option>
										[ELSE]
											<option value="">Indifférent</option>
											[STORPROC [!Q!]|P]
												<option value="[!P::[!T!]!]" [IF [!P::[!T!]!]=[![!Prefixe!][!Prop::Nom!]!]]selected="selected"[/IF]>
													[!P::[!T!]!]
												</option>
											[/STORPROC]
										[/IF]			
									</select>
								[ELSE]
									<input type="text" name="[!Prefixe!][!Prop::Nom!]"  id="[!Prefixe!][!Prop::Nom!]" value="[![!Prefixe!][!Prop::Nom!]!]"/>
								[/IF]
							[/IF]
						[/IF]
					</div>
				[/DEFAULT]
			[/SWITCH]
		[/STORPROC]
		<p>
			<input type="submit" name="Recherche"  value="Rechercher"  class="BtnSearch" />
		</p>

		</div>
		[IF [!C!]>1]
			//Affiche la liste
			[MODULE Reservation/Organisation/Liste?Chemin=[!REQUETE!]&REQUETE=[!REQUETE!]&Filter=[!FILTER!]]
		[ELSE]
			//Affiche un message Pas de resultat
			[MODULE Reservation/Spectacle/Message?Chemin=[!REQUETE!]&Filter=[!FILTER!]]
		[/IF]
	</form>
</div>