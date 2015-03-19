<div class="Arborescence">
	[IF [!Visit[!TypeEnf!]!]!=][!Chemin:=[!Visit[!TypeEnf!]!]/[!TypeEnf!]!][/IF]
	[INFO [!Chemin!]|Che]
	[STORPROC [!Che::Historique!]|H|[!Niveau!]|1][/STORPROC]
    	<ul>
		//Si la datasource est bien la bonne
		[IF [!H::DataSource!]=[!TypeEnf!]]
			[!Requete:=[!Requete!]/[!H::DataSource!]!]
			//Affichage de la liste
			[!Niveau+=1!]
			[STORPROC [!Requete!]|Objet|0|100]
				//Test si Visit
				[IF [!Pos!]=1]<ul>[/IF]
				//On verifie si il est selectionne
				[!Ch:=0!]
				[STORPROC [![!Var!]Tab!]|C]
					[IF [!C!]=[!Objet::Id!]]
						[!Ch:=1!]
						[COUNT [!TabFirst!]|N]
						[!TabFirst::[!N!]:=[!C!]!]
						[IF [!FirstTime!]][ELSE][!Test:=1!][/IF]
					[/IF]
				[/STORPROC]
				[STORPROC [!Check!]|Par][IF [!Objet::Id!]=[!Par::Id!]][!Ch:=1!][/IF][/STORPROC]
				[IF [!Objet::isTail!]]
					<li [IF [!Pos!]=[!NbResult!]]class="ArboLast"[ELSE]class="ArboItem"[/IF]>
						<span style="" >
						[MODULE Systeme/Interfaces/Arborescence/Ligne?Objet=[!Objet!]&Type=[!Type!]&Prefixe=[!Prefixe!]&PrefixeVar=[!PrefixeVar!]&Inter=[!Inter!]&Ch=[!Ch!]&Check=[!Check!]&TypeEnf=[!TypeEnf!]]
						</span>
					</li>
				[ELSE]
					<li [IF [!Type!]!=Mini][IF [!Pos!]=[!NbResult!]]class="ArboExpandLast"[ELSE]class="ArboExpand"[/IF][ELSE][IF [!Pos!]=[!NbResult!]]class="ArboLast"[ELSE]class="ArboItem"[/IF][/IF]>
						<div class="Arborescence" >
							<input type="submit"  class="ArboNav" name="Visit[!TypeEnf!]" value="[!Objet::getUrl!]"/>
							<div class="ArborescenceTitre">
							[MODULE Systeme/Interfaces/Arborescence/Ligne?Objet=[!Objet!]&Type=[!Type!]&Prefixe=[!Prefixe!]&PrefixeVar=[!PrefixeVar!]&Inter=[!Inter!]&Ch=[!Ch!]&Test=[!Test!]&Check=[!Check!]&TypeEnf=[!TypeEnf!]]
							//[!Objet::getFirstSearchOrder!]
							</div>
							[IF [!H::Value!]=[!Objet::Id!]]
								[MODULE Systeme/Interfaces/Arborescence/RecursivArbo?Niveau=[!Niveau!]&Chemin=[!Chemin!]&TypeEnf=[!TypeEnf!]&Requete=[!Requete!]/[!H::Value!]&Prefixe=[!Prefixe!]&PrefixeVar=[!PrefixeVar!]&Inter=[!Inter!]&Check=[!Check!]&Type=[!Type!]|GLOBAL]
							[/IF]
						</div>
					</li>
				[/IF]
				[IF [!Pos!]=[!NbResult!]]</ul>[/IF]
				//Test si Dernier
			[/STORPROC]
			[!Requete:=[!Requete!]/[!H::Value!]!]			
		[ELSE]
			[!Requete:=[!Requete!]/[!H::DataSource!]/[!H::Value!]!]			
		[/IF]
	</ul>		
</div>
