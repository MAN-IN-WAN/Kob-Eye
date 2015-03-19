[IF [!Null!]]
	<div class="Error" style="margin-top:10px;">Merci de pr&eacute;ciser le mot sur lequel la recherche doit s'effectuer</div>
[/IF]
<h3>Recherche par mot-clefs</h3>
<form action="/Redaction/Recherche/Resultat" method="get" id="FormSearch">
	<div>
		<input type="submit" class="OK" value="OK" />
		<input type="text" name="Recherche" value="[!Recherche!]"  />
		<div class="Clear"></div>
	</div>
</form>