<form method="POST">
<div class="Panel">
	<h1>R&eacute;capitulatif de la propri&eacute;t&eacute;</h1>
	<div class="Propriete">
		<div class="ProprieteTitre">Nom de la propri&eacute;t&eacute; : </div>
		<div class="ProprieteValeur">[!NomProp!]</div>
		<INPUT type="hidden" name="NomProp" value="[!NomProp!]" />
	</div>
	<div class="Propriete">
		<div class="ProprieteTitre">Type de la propri&eacute;t&eacute; : </div>
		<div class="ProprieteValeur">[!TypeProp!]</div>
		<INPUT type="hidden" name="TypeProp" value="[!TypeProp!]" />
	</div>
	<div class="Propriete">
		<div class="ProprieteTitre">Cible : </div>
		<div class="ProprieteValeur">[!EnfantProp!]</div>
		<INPUT type="hidden" name="EnfantProp" value="[!EnfantProp!]" />
	</div>
	<div class="Propriete">
		<div class="ProprieteTitre">Groupe : </div>
		<div class="ProprieteValeur">[!GroupProp!]</div>
		<INPUT type="hidden" name="GroupProp" value="[!GroupProp!]" />
	</div>
	<div class="Propriete">
		<div class="ProprieteTitre">Niveau d'action : </div>
		<div class="ProprieteValeur">[!LevelProp!]</div>
		<INPUT type="hidden" name="LevelProp" value="[!LevelProp!]" />
	</div>
	<div class="Propriete">
		<div class="ProprieteTitre">Ordre : </div>
		<div class="ProprieteValeur">[!OrderProp!]</div>
		<INPUT type="hidden" name="OrderProp" value="[!OrderProp!]" />
	</div>
</div>
<div class="Panel">
	<h1>Propri&eacute;t&eacute;s multi-langues</h1>
	[STORPROC [!CONF::GENERAL::LANGUAGE!]|Lang]
		<div class="SousPanel">
			<h1>Valeurs pour [!Lang::TITLE!] [IF [!Lang::DEFAULT!]](Langage par defaut)[/IF]</h1>
			<div class="Propriete">
				<div class="ProprieteTitre">Titre de la propri&eacute;t&eacute; :</div>
				<div class="ProprieteValeur">
					<input  class="Champ" type="text" name="[!Key!]-TitreProp" size="40" value="[![!Key!]-TitreProp!]" />
				</div>
			</div>
			<div class="Propriete">
				<div class="ProprieteTitre">Valeur de la propri&eacute;t&eacute; :</div>
				<div class="ProprieteValeur">
					<TEXTAREA ROWS="3" class="Champ" name="[!Key!]-ValueProp">[![!Key!]-ValueProp!]</TEXTAREA
				</div>
			</div>
		</div>
	[/STORPROC]
</div>
<div class="Nav">
	<div class="boutonGauche">
		<form action="/[!Query!]/GestionHeritage" method="post" style="display:inline;">
		<INPUT type="submit"  value="Annuler" />
		</form>
	</div>
	[IF [!Action!]=="Modifier"]
		<div class="boutonDroite">
			<INPUT type="submit" name="Modifier" value="Valider" />
		</div>
	[ELSE]
		<div class="boutonDroite">
			<INPUT type="hidden" name="Action" value="Ajouter" />
			<INPUT type="submit" name="Ajouter" value="Valider" />
		</div>
	[/IF]
</div>
</form>

