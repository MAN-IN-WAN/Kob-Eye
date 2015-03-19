[SWITCH [!Prop::Type!]|=]
	[CASE "private"]
		<div class="Propriete">
			<div class="ProprieteTitre">[!Prop::Nom!] : </div>
			[IF [!Prop::Valeur!]=EMPTY]
				<div class="ProprieteValeur">Non renseign&eacute;</div>
			[ELSE]
				<div class="ProprieteValeur">[!Prop::Valeur!]</div>
			[/IF]
		</div>
	[/CASE]
	[CASE "titre"]
		<div class="Propriete">
			<div class="ProprieteTitre">[!Prop::Nom!] : </div>
			[IF [!Prop::Valeur!]=EMPTY]
				<div class="ProprieteValeur">Non renseign&eacute;</div>
			[ELSE]
				<div class="ProprieteValeur">[!Prop::Valeur!]</div>
			[/IF]
		</div>
	[/CASE]
	[CASE "txt"]
		<div class="Propriete">
			<div class="ProprieteTitre">[!Prop::Nom!] : </div>
			[IF [!Prop::Valeur!]=EMPTY]
				<div class="ProprieteValeur">Non renseign&eacute;</div>
			[ELSE]
				<div class="ProprieteValeurTxt">[!Prop::Valeur!]</div>
			[/IF]
		</div>
	[/CASE]
	[CASE "image"]
		<div class="Propriete">
			<div class="ProprieteTitre">[!Prop::Nom!] : </div>
			[IF [!Prop::Valeur!]=EMPTY]
				<div class="ProprieteValeur">Non renseign&eacute;</div>
			[ELSE]
				<div class="ProprieteValeur"><img src="[!Prop::Valeur!]" /></div>
			[/IF]
		</div>
	[/CASE]
	[CASE "link"]
		<div class="Propriete">
			<div class="ProprieteTitre">[!Prop::Nom!] : </div>
			[IF [!Prop::Valeur!]=EMPTY]
				<div class="ProprieteValeur">Non renseign&eacute;</div>
			[ELSE]
				<div class="ProprieteValeur"><a href="[!Prop::Valeur!]">[!Prop::Valeur!]</a></div>
			[/IF]
		</div>
	[/CASE]
	[CASE "varchar"]
		<div class="Propriete">
			<div class="ProprieteTitre">[!Prop::Nom!] : </div>
			[IF [!Prop::Valeur!]=EMPTY]
				<div class="ProprieteValeur">Non renseign&eacute;</div>
			[ELSE]
				<div class="ProprieteValeur">[!Prop::Valeur!]</div>
			[/IF]
		</div>
	[/CASE]
	[CASE "VARCHAR"]
		<div class="Propriete">
			<div class="ProprieteTitre">[!Prop::Nom!] : </div>
			[IF [!Prop::Valeur!]=EMPTY]
				<div class="ProprieteValeur">Non renseign&eacute;</div>
			[ELSE]
				<div class="ProprieteValeur">[!Prop::Valeur!]</div>
			[/IF]
		</div>
	[/CASE]
	[CASE "date"]
		<div class="Propriete">
			<div class="ProprieteTitre">[!Prop::Nom!] : </div>
			[IF [!Prop::Valeur!]=EMPTY]
				<div class="ProprieteValeur">Non renseign&eacute;</div>
			[ELSE]
				<div class="ProprieteValeur">[!Prop::Valeur!]</div>
			[/IF]
		</div>
	[/CASE]
	[CASE "file"]
		<div class="Propriete">
			<div class="ProprieteTitre">[!Prop::Nom!] : </div>
			[IF [!Prop::Valeur!]=EMPTY]
				<div class="ProprieteValeur">Non renseign&eacute;</div>
			[ELSE]
				<div class="ProprieteValeur"><a href="[!Prop::Valeur!]">[!Prop::Valeur!]</a></div>
			[/IF]
		</div>
	[/CASE]
	[CASE "url"]
		<div class="Propriete">
			<div class="ProprieteTitre">[!Prop::Nom!] : </div>
			[IF [!Prop::Valeur!]=EMPTY]
				<div class="ProprieteValeur">Non renseign&eacute;</div>
			[ELSE]
				<div class="ProprieteValeur"><a href="[!Prop::Valeur!]">[!Prop::Valeur!]</a></div>
			[/IF]
		</div>
	[/CASE]
	[CASE "int"]
		<div class="Propriete">
			<div class="ProprieteTitre">[!Prop::Nom!] : </div>
			[IF [!Prop::Valeur!]=EMPTY]
				<div class="ProprieteValeur">Non renseign&eacute;</div>
			[ELSE]
				<div class="ProprieteValeur">[!Prop::Valeur!]</div>
			[/IF]
		</div>
	[/CASE]
	[CASE "INT"]
		<div class="Propriete">
			<div class="ProprieteTitre">[!Prop::Nom!] : </div>
			[IF [!Prop::Valeur!]=EMPTY]
				<div class="ProprieteValeur">Non renseign&eacute;</div>
			[ELSE]
				<div class="ProprieteValeur">[!Prop::Valeur!]</div>
			[/IF]
		</div>
	[/CASE]
	[DEFAULT]
		<div class="Propriete">
			<div class="ProprieteTitre">[!Prop::Nom!] : </div>
			[IF [!Prop::Valeur!]=EMPTY]
				<div class="ProprieteValeur">Non renseign&eacute;</div>
			[ELSE]
				<div class="ProprieteValeur">[!Prop::Valeur!]</div>
			[/IF]
		</div>	
	[/DEFAULT]
[/SWITCH]
