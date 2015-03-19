[TITLE]Admin Kob-Eye | Gestion des droits[/TITLE]
[!Prefixe:=RechDroits!]
[STORPROC [!Query!]|Objet]
	[IF [!detail!]="Valider"]
		[!Objet::ModifierDroits([![!Prefixe!]uid!],[![!Prefixe!]gid!],[![!Prefixe!]Umod!],[![!Prefixe!]Gmod!],[![!Prefixe!]Omod!])!]
		//[MODULE Systeme/Interfaces/Objet/InfoObjet?Objet=[!Objet!]]
		[REDIRECT][!Query!][/REDIRECT]
	[ELSE]
		[MODULE Systeme/Interfaces/InfoObjet]
		[BLOC PopUpForm|Informations propri&eacute;taires]
			//Affichage des Droits
			<div class="Propriete">
				[!P::Nom:=uid!]
				[!P::description:=Utilisateur propriétaire!]
				[!P::Valeur:=[!Objet::uid!]!]
				[!P::Type:=ObjectClass!]
				[!P::query:=Systeme/User!]
				<div class="Propriete">
					<div class="ProprieteTitre">[BLOC Rounded|background-color:#8BB2C2;color:#FFFFFF;][!P::description!] : [/BLOC]</div>
					<div class="ProprieteValeur">&nbsp;
						[IF [!Valeur!]!=][!P::Valeur:=[!Valeur!]!][/IF]
						[!T:=[![!Prefixe!]Explore[!P::Nom!]!]!]
						[IF [!Utils::isArray([!T!])!]]
							[STORPROC [!T!]|E]
								[!VAL:=[!E!]!]
							[/STORPROC]
						[/IF]
						[IF [!VAL!]=][!VAL:=[![!Prefixe!][!P::Nom!]!]!][/IF]
						[IF [!VAL!]=][!VAL:=[!P::Valeur!]!][/IF]
						<input type="text" class="Champ" name="[!Prefixe!][!P::Nom!]" value="[!VAL!]" style="width:80%;">
						<input type="submit" name="[!Prefixe!]Explore[!P::Nom!]_explore" value="OK" class="ExplorerBouton" />
						[IF [![!Prefixe!]Explore[!P::Nom!]_explore!]=OK]
							[INFO [!P::query!]|Test]
							[MODULE Systeme/Interfaces/Explorer?Prop=[!P!]&Prefixe=[!Prefixe!]Explore]
						[/IF]
					</div>
				</div>
				[!VAL:=!]
				[!P::Nom:=gid!]
				[!P::description:=Groupe propriétaire!]
				[!P::Valeur:=[!Objet::gid!]!]
				[!P::Type:=ObjectClass!]
				[!P::query:=Systeme/Group!]
				<div class="Propriete">
					<div class="ProprieteTitre">[BLOC Rounded|background-color:#8BB2C2;color:#FFFFFF;][!P::description!] : [/BLOC]</div>
					<div class="ProprieteValeur">&nbsp;
						[IF [!Valeur!]!=][!P::Valeur:=[!Valeur!]!][/IF]
						[!T:=[![!Prefixe!]Explore[!P::Nom!]!]!]
						[IF [!Utils::isArray([!T!])!]]
							[STORPROC [!T!]|E]
								[IF [!E!]!=ROOT][!VAL:=[!E!]!][/IF]
							[/STORPROC]
						[/IF]
						[IF [!VAL!]=][!VAL:=[![!Prefixe!][!P::Nom!]!]!][/IF]
						[IF [!VAL!]=][!VAL:=[!P::Valeur!]!][/IF]
						<input type="text" class="Champ" name="[!Prefixe!][!P::Nom!]" value="[!VAL!]" style="width:80%;">
						<input type="submit" name="[!Prefixe!]Explore[!P::Nom!]_explore" value="OK" class="ExplorerBouton" />
						[IF [![!Prefixe!]Explore[!P::Nom!]_explore!]=OK]
							[INFO [!P::query!]|Test]
							[MODULE Systeme/Interfaces/Explorer?Prop=[!P!]&Prefixe=[!Prefixe!]Explore]
						[/IF]
					</div>
				</div>
				<div class="ProprieteTitre">[BLOC Rounded|background-color:#8BB2C2;color:#FFFFFF;]Droits Utilisateur : [/BLOC]</div>
				<div class="ProprieteValeur">
					<select name="[!Prefixe!]Umod">
						<option value="0" [IF [!Objet::umod!]=0]selected="selected"[/IF]>0. Aucun</option>
						<option value="1" [IF [!Objet::umod!]=1]selected="selected"[/IF]>1. Existence</option>
						<option value="2" [IF [!Objet::umod!]=2]selected="selected"[/IF]>2. Affichage</option>
						<option value="3" [IF [!Objet::umod!]=3]selected="selected"[/IF]>3. Existence + Affichage</option>
						<option value="4" [IF [!Objet::umod!]=4]selected="selected"[/IF]>4. Ecriture</option>
						<option value="5" [IF [!Objet::umod!]=5]selected="selected"[/IF]>5. Existence + Ecriture</option>
						<option value="6" [IF [!Objet::umod!]=6]selected="selected"[/IF]>6. Ecriture + Lecture</option>
						<option value="7" [IF [!Objet::umod!]=7]selected="selected"[/IF]>7. Tout</option>
					</select>
				</div>
				<div class="ProprieteTitre">[BLOC Rounded|background-color:#8BB2C2;color:#FFFFFF;]Droits Groupe : [/BLOC]</div>
				<div class="ProprieteValeur">
					<select name="[!Prefixe!]Gmod">
						<option value="0" [IF [!Objet::gmod!]=0]selected="selected"[/IF]>0. Aucun</option>
						<option value="1" [IF [!Objet::gmod!]=1]selected="selected"[/IF]>1. Existence</option>
						<option value="2" [IF [!Objet::gmod!]=2]selected="selected"[/IF]>2. Affichage</option>
						<option value="3" [IF [!Objet::gmod!]=3]selected="selected"[/IF]>3. Existence + Affichage</option>
						<option value="4" [IF [!Objet::gmod!]=4]selected="selected"[/IF]>4. Ecriture</option>
						<option value="5" [IF [!Objet::gmod!]=5]selected="selected"[/IF]>5. Existence + Ecriture</option>
						<option value="6" [IF [!Objet::gmod!]=6]selected="selected"[/IF]>6. Ecriture + Lecture</option>
						<option value="7" [IF [!Objet::gmod!]=7]selected="selected"[/IF]>7. Tout</option>
					</select>
				</div>
				<div class="ProprieteTitre">[BLOC Rounded|background-color:#8BB2C2;color:#FFFFFF;]Droits Autres : [/BLOC]</div>
				<div class="ProprieteValeur">
					<select name="[!Prefixe!]Omod">
						<option value="0" [IF [!Objet::omod!]=0]selected="selected"[/IF]>0. Aucun</option>
						<option value="1" [IF [!Objet::omod!]=1]selected="selected"[/IF]>1. Existence</option>
						<option value="2" [IF [!Objet::omod!]=2]selected="selected"[/IF]>2. Affichage</option>
						<option value="3" [IF [!Objet::omod!]=3]selected="selected"[/IF]>3. Existence + Affichage</option>
						<option value="4" [IF [!Objet::omod!]=4]selected="selected"[/IF]>4. Ecriture</option>
						<option value="5" [IF [!Objet::omod!]=5]selected="selected"[/IF]>5. Existence + Ecriture</option>
						<option value="6" [IF [!Objet::omod!]=6]selected="selected"[/IF]>6. Ecriture + Lecture</option>
						<option value="7" [IF [!Objet::omod!]=7]selected="selected"[/IF]>7. Tout</option>
					</select>
				</div>
			</div>
		[/BLOC]
	[/IF]
[/STORPROC]

