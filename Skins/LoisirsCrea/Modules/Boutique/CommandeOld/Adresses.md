[STORPROC Boutique/Client/[!CLCONN::Id!]/Adresse/Type=[!Type!]|Adr|0|10|tmsEdit|DESC]
	// Plus qu une adresse - On masque les autres ?
	[IF [!Pos!]=2]
		<a class="ChooseMoreAdresses btn btn-gris BoutonContinuer" href="javascript:;" style="display:none;" onclick="showMoreAdresses(this, '[!Type!]')">Choisir une autre adresse...</a>
		<script type="text/javascript">
			$(document).ready(function() {
				var pos = 0;
				$.each($('div.AdresseType[!Type!]'), function(index,div) {
					if(pos!=0) $(div).css('display','none');
					pos++;
				});
			});
		</script>
	[/IF]

	// Adresse courante ?
	[!Checked:=-1!]
	[IF [![!Type!]!]]
		[IF [!Adr::Id!]=[![!Type!]!]][!Checked:=1!][/IF]
	[ELSE]
		[IF [!Pos!]=1][!Checked:=1!][/IF]
	[/IF]
	<div class="AdresseStep3 AdresseType[!Type!]">
		<div class="AdresseSelect">
			<input type="radio" name="[!Type!]" value="[!Adr::Id!]" [IF [!Checked!]=1] checked="checked" [/IF] class="AdresseRadio[!Type!]" />
		</div>
		<div class="AdresseDesc">
			<div class="AdresseName">[!Adr::Civilite!] [!Adr::Prenom!] [!Adr::Nom!]</div>
			<div class="AdresseRue">[!Adr::Adresse!]</div>
			<div class="AdresseVille">[!Adr::CodePostal!] [!Adr::Ville!]</div>
		</div>
	</div>
	[NORESULT]
		[BLOC Erreur]Vous devez créer au minimum une adresse de [!Type!] dans la rubrique <a href="/[!Systeme::getMenu(Mon-compte)!]/Adresses?Type=[!Type!]" class=" btn btn-kirigami btn-small">Ajouter</a>[/BLOC]
	[/NORESULT]
[/STORPROC]

<div class=" MoreAdresses"><a href="/[!Systeme::getMenu(Mon-compte)!]/Adresses?Type=[!Type!]" class="btn btn-kirigami btn-small">Ajouter/Modifier</a></div>

