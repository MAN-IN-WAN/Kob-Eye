//RESULTAT CAT 
[!TypeEnf:=Categorie!]
[IF [!Recherche!]!=]
	//------------------RECHERCHE MOTS CLEFS-----------------//
	//Titre
	[!Titre:=Recherche par mot-clefs "[!Recherche!]"!]
	[!Roch:=[![!Recherche!]:/ !]!]
	[STORPROC [!Roch!]|R]
		[COUNT Redaction/BlackList/[!R!]|Bl]
		[IF [!Bl!]=0]
			[STORPROC Redaction/Categorie/Motclef/Nom~[!R!]|M|0|10]
				[IF [!Pos!]>1][!Details+= | !][/IF]
				[!Details+= <a href="?Recherche=[!M::Nom!]">[!M::Nom!]</a>!]
			[/STORPROC]
			[IF [!Pos!]>1][!Re+= !][/IF]
			[!Re+=[!Utils::Canonic([!R!])!]!]
		[/IF]
	[/STORPROC]
	//Cas Recherche
	[!Requete:=Redaction/Categorie/Motclef/Canon~[!Re!]!]
	
	[!G:=1!]
	//------------------FIN RECHERCHE MOTS CLEFS-----------------//
[ELSE]
	[MODULE Redaction/Recherche?Null=1]
[/IF]
//------------------AFFICHAGE-----------------//
[MODULE Systeme/Structure/Droite]
<div id="Milieu">
	<h1>[!Titre!]</h1>
	[STORPROC [!Requete!]|Cat]
		<div class="Result">
			<h4>[!Cat::Nom!]</h4>
			[IF [!Cat::Description!]]	
				<p>[SUBSTR 200|(...)][!Cat::Description!][/SUBSTR]</p>
			[/IF]
			<a href="/Resultats-recherche/[!Cat::Url!]" title="Acc&eacute;der &agrave; la page" style="display:block;margin-top:10px;">Lire la suite</a>
		</div>
	[/STORPROC]
</div>
<div class="Clear"></div>

