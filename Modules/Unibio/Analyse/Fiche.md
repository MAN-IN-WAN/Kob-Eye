
[!filtre:=!]
[IF [!Domaine!]=http://intranet.unibio2.abtel.fr||[!Domaine!]=http://intranet.unibio.abtel.fr||[!Domaine!]=http://intranet.unibio.fr||[!Domaine!]=http://intranet.unibio.local||[!Domaine!]=https://intranet.unibio.fr]
	[!filtre+=niveau!=Admin!]
	[!AffichPublic:=0!]
[ELSE]
	[!filtre+=niveau=Internet!]
	[!AffichPublic:=1!]

[/IF]


[STORPROC [!Query!]|O|0|1]
	[!Groups:=[!O::getElements()!]!]
	[STORPROC [!Groups!]|C]
		[LIMIT 0|1000]
			[IF [!Key!]!=Configuration]
				<div class="AnalyseCategorie">[IF [!Key!]!=Principal]<h2>[!Key!]</h2>[/IF]</div>
				<div style="margin:5px;border-bottom:1px dotted #66ADD5; ">
					[!Idx:=0!]
					[STORPROC [!C!]|T]
						[STORPROC [!T!]/[!filtre!]|Prop]
							[LIMIT 0|1000]
								[!Idx+=1!]
								<div class="LigneForm" style="[IF [!Utils::isPair([!Idx!])!]]background:#DFF2F6[/IF]">
									<label style="width:250px">[IF [!Prop::description!]][!Prop::description!][ELSE][!Prop::name!][/IF]</label>
									<div style="font-weight:bold;margin-left:260px">
										[SWITCH [!Prop::type!]|=]
											[CASE boolean]
												[IF [!Prop::value!]=1]Oui[ELSE]Non[/IF]
											[/CASE]
											[CASE text]
												[UTIL BBCODE][!Prop::value!][/UTIL]
											[/CASE]
											[DEFAULT]
												[IF [!Utils::isArray([!Prop::Values!])!]]
													[STORPROC [!Prop::Values!]|Val]
														[!T:=[![!Val!]:/::!]!]
														[COUNT [!T!]|S]
														[IF [!S!]>1]
															[IF [!Prop::value!]=[!T::0!]] [!T::1!] [/IF]
														[ELSE]
															[IF [!Prop::value!]=[!Val!]] [!Val!] [/IF]
														[/IF]
													[/STORPROC]
												[ELSE]
													[IF [!Prop::query!]]
														[!Found:=0!]
														[STORPROC [!Prop::query!]|Val|0|100|[!Ov!]|ASC]
															[IF [!Key!]!=[!Pos:-1!]][!Valeur:=[!Key!]!][ELSE][!Valeur:=[!Val!]!][/IF]
															[IF [!Key!]=[!Prop::value!]]
																[!Found:=1!]
																[!Val!]
															[/IF]
														[/STORPROC]
														[IF [!Found!]=0]
															Non défini
														[/IF]
													[ELSE]
														[!Prop::value!]
													[/IF]
												[/IF]
											[/DEFAULT]
										[/SWITCH]
									</div>
								</div>
								
							[/LIMIT]
						[/STORPROC]
					[/STORPROC]
				</div>
			[/IF]
		[/LIMIT]
	[/STORPROC]
	[COUNT [!Query!]/Document/Public=[!AffichPublic!]|Nb]
	[IF [!Nb!]]
		<div class="AnalyseCategorie"><h2>Espace Documents</h2></div>
		<div style="margin:5px;border-bottom:1px dotted #66ADD5; ">
	
			<div class="LigneForm" style="[IF [!Utils::isPair([!Idx!])!]]background:#DFF2F6[/IF]">
				<label style="width:250px">Les Documents</label>
				<div style="font-weight:bold;margin-left:260px">
					[STORPROC [!Query!]/Document/Public=[!AffichPublic!]|Do]
						<a href="[IF [!Do::URL!]~http][ELSE]/[/IF][!Do::URL!]" alt="[!Do::Titre!]" style="overflow:hidden;display:block;">[!Do::Titre!]</a>
					[/STORPROC]
				</div>
			</div>
		</div>
	[/IF]
[/STORPROC]


<div class="LigneForm" style="padding:10px">
	<button type="button" onclick="history.go(-1)" class="RetourRecherche">Retour</button>
</div>