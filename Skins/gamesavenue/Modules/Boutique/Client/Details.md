// details d un vendeur
[!AnnoncePair:=0!]
<div class="VendeurBlocNoirCote">
	<img src="/Skins/gamesavenue/Images/bando-vendeur-gauche.png">
</div>
<div class="VendeurBlocNoirCentre">
	<div class="TitreVendeur">A propos du vendeur</div>
</div>
<div class="VendeurBlocNoirCote">
	<img src="/Skins/gamesavenue/Images/bando-vendeur-droite.png">
</div>
[IF [!Utils::isPair([!AnnoncePair!])!]]  
	<div class="coinFinGriscontent">
[ELSE]
	<div class="coinFinGriscontent" style="background:#ebebeb;">
[/IF]
	[!AnnoncePair+=1!]
	ici interieur
</div>
<b class="coinNoirFondBlancborderbottom">
	<b class="coinFinGris4">&nbsp;</b>
	<b class="coinFinGris3">&nbsp;</b>
	<b class="coinFinGris2">&nbsp;</b>
	<b class="coinFinGris1">&nbsp;</b>
</b>
