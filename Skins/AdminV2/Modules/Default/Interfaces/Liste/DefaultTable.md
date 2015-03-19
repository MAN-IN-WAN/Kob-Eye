//------------------------------------------------
//--		INFOS				--
//------------------------------------------------
//********INPUT********
//Chemin	//Chemin a utiliser pour afficher les objets
//NbChamp	//Nombre de champ a afficher
//TypeEnf   	//Type d objet a afficher
//Type   	//Type d interface
//	Full 		//AFFICHAGE STANDARD
//	Explorer	//AFFICHAGE VALIDATION 
//	Mini		//AFFICHAGE RESTREINT
//	Select		//AFFICHAGE POUR LA SELECTION
//Prefixe	//Prefixe de la variable
//RechPrefixe	//Prefixe des variables de recherche
//Inter		//Type des input de ligne
//Disable	//Tableau contenant les IDS a desactiver
//Check		//Tableau contenant les IDS a checker 
//********OUTPUT********
//Select 	//Tableau contenant les nouvelles selections
//UnSelect	//Tableau contenant les selections supprimées
//------------------------------------------------
//--		PARAMETRES			--
//------------------------------------------------
//CONFIG REQUETE
[INFO [!Chemin!]|Test]
[!TypeEnf:=[!Test::TypeChild!]!]
//PAGINATION
[!Page[!TypeEnf!]:=1!]
[!Module:=[!Test::Module!]!]
[!MaxLine:=30!]
[!PagNbNum:=3!]
[!Order:=Id!]
[!OrderType:=DESC!]
[IF [!NbChamp!]=][!NbChamp:=1!][/IF]
[IF [!RechPrefixe!]=][!RechPrefixe:=Rech!][/IF]
//VARIABLES
[IF [!Var!]!=][ELSE][!Var:=[!Prefixe!][!TypeEnf!]!][/IF]
[IF [!OutVar!]=][!OutVar:=Id!][/IF]
[OBJ [!Module!]|[!TypeEnf!]|T]
//------------------------------------------------
//--		SELECTION			--
//------------------------------------------------
[!SELECTION:=m.Id,m.tmsCreate,m.tmsEdit,m.uid,m.gid!]
[STORPROC [!T::SearchOrder()!]|Prop|0|[!NbChamp:+1!]]
	[!SELECTION+=,m.[!Prop::Nom!]!]
[/STORPROC]

//------------------------------------------------
//--		RECHERCHES			--
//------------------------------------------------
//RECHERCHE FLOU
[!Shlass:=0!]
[IF [![!RechPrefixe!][!TypeEnf!]!]!=]
	[!Recherche:=~[![!RechPrefixe!][!TypeEnf!]!]!]
	[!Shlass:=1!]
[/IF]
[!RequeteT:=[!Chemin!]!]

//FILTRE
[STORPROC [!T::GetFilter()!]|P|0|100]
	[IF [![!RechPrefixe!]Filter[!P::Nom!]!]!=]
		[IF [!Shlass!]][!Recherche+=&!][ELSE][!Shlass:=1!][/IF]
		[!Recherche+=m.[!P::Nom!]=[![!RechPrefixe!]Filter[!P::Nom!]!]!]
	[/IF]
[/STORPROC]

//RECHERCHE PROPRIETE
[STORPROC [!T::SearchOrder()!]|P|0|100]
	[IF [![!RechPrefixe!]Prop[!P::Nom!]!]!=&&[!P::Filter!]=]
		[IF [!Shlass!]][!Recherche+=&!][ELSE][!Shlass:=1!][/IF]
		[!Recherche+=m.[!P::Nom!]~[![!RechPrefixe!]Prop[!P::Nom!]!]!]
	[/IF]
[/STORPROC]

//SYSTEME
[IF [![!RechPrefixe!]Filteruid!]!=]
	[IF [!Shlass!]][!Recherche+=&!][ELSE][!Shlass:=1!][/IF]
	[!Recherche+=m.uid=[![!RechPrefixe!]Filteruid!]!]
[/IF]
[IF [![!RechPrefixe!]Filtergid!]!=]
	[IF [!Shlass!]][!Recherche+=&!][ELSE][!Shlass:=1!][/IF]
	[!Recherche+=m.gid=[![!RechPrefixe!]Filtergid!]!]
[/IF]

//RECHERCHE PAR ENFANT
[OBJ [!Module::Actuel::Nom!]|[!TypeEnf!]|Obj]
[STORPROC [!Obj::typesEnfant!]|Enf]
	[IF [!Enf::search!]&&[![!RechPrefixe!]Enf[!Enf::Titre!]!]!=]
		//Alors recherche type enfant
		[!RechEnf:=[!Enf::Titre!]!]
	[/IF]
[/STORPROC]

//ASSEMBLAGE REQUETE
[IF [!RechEnf!]]
	[!Roch:=[![![!RechPrefixe!]Enf[!RechEnf!]!]:/ !]!]
	[STORPROC [!Roch!]|R]
		[IF [!Pos!]>1][!Re+= !][/IF]
		[!Re+=[!Utils::Canonic([!R!])!]!]
	[/STORPROC]
	[!RequeteT:=[!Module::Actuel::Nom!]/[!TypeEnf!]/[!RechEnf!]/Canon~[!Re!]&[!Recherche!]!]
[ELSE]
	[IF [!Recherche!]!=][!RequeteT+=/[!Recherche!]!][/IF]
[/IF]

//PAGINATION
[COUNT [!RequeteT!]|Test2]
[!TotalPage:=[!Test2:/[!MaxLine!]!]!]
[IF [!TotalPage!]>[!Math::Floor([!TotalPage!])!]]
	[!TotalPage:=[![!Math::Floor([!TotalPage!])!]:+1!]!]
[/IF]
[!LargeurDroite:=0!]
[!LargeurGauche:=0!]
[IF [!ChangeOrder!]!=]
	[IF [!Order!]=[!ChangeOrder!]]
		//On change l ordre
		[IF [!OrderType!]=ASC][!OrderType==DESC!][ELSE][!OrderType==ASC!][/IF]
	[ELSE]
		[!Order==[!ChangeOrder!]!]
		[!OrderType==ASC!]
	[/IF]
[/IF]

//------------------------------------------------
//--		ACTIONS				--
//------------------------------------------------
//SUPPRESSION
[IF [!ListeAction!]=Supprimer]
	[STORPROC [!Liste[!TypeEnf!]!]|P]
		[STORPROC [!Module::Actuel::Nom!]/[!TypeEnf!]/[!P!]|C]
			[METHOD C|Delete][/METHOD]
		[/STORPROC]
	[/STORPROC]
[/IF]
//SELECTION
[IF [!Type!]=Select]
	[IF [![!Var!]!]]
		//On doit comparer les champs precedement selectionner et les champs deselectionner
		[STORPROC [![!Var!]SelectTest!]|S]
			[!Te:=1!]
			[STORPROC [![!Var!]Select!]|T]
				[IF [!S!]=[!T!]][!Te:=0!][/IF]
			[/STORPROC]
			[IF [!Te!]]
				//Alors Id a supprimer
				[COUNT [!TabSup!]|C]
				[!TabSup::[!C!]:=[!S!]!]
			[/IF]
		[/STORPROC]
		[!TempTab:=!]
		[STORPROC [![!Var!]!]|C]
			[!Te:=1!]
			[STORPROC [!TabSup!]|T][IF [!C!]=[!T!]][!Te:=0!][/IF][/STORPROC]
			[IF [!Te!]]
				[COUNT [!TempTab!]|Y]
				[!TempTab::[!Y!]:=[!C!]!]
			[/IF]
		[/STORPROC]
		[STORPROC [!TempTab!]|X]
			[![!Var!]Tab::[!Key!]:=[!X!]!]
		[/STORPROC]
	[ELSE]
		//On doit sauvegarder les selections donc on ajoute autant de type hidden que necessaire
		[STORPROC [!Check!]|C]
			[![!Var!]Tab::[!Key!]:=[!C::Id!]!]
		[/STORPROC]
		[!FirstTime:=1!]
	[/IF]
[/IF]
//------------------------------------------------
//--		INTERFACE			--
//------------------------------------------------

//DEfinition des tailles en pourcent
[IF [!Type!]!=Mini&&[!Type!]!=Explorer]
    [!ActionCol:=7%!]
    [!UsGr:=7%!]
    [!Crea:=15%!]
    [!Modif:=15%!]
    [!NumCol:=15%!]
[ELSE]
    [IF [!Type!]=Mini]
	[!ActionCol:=7%!]
	[!UsGr:=7%!]
	[!Crea:=15%!]
	[!Modif:=15%!]
	[!NumCol:=15%!]
    [ELSE]
	[!ActionCol:=7%!]
	[!UsGr:=7%!]
	[!Crea:=15%!]
	[!Modif:=15%!]
	[!NumCol:=15%!]
    [/IF]
[/IF]

[IF [!Type!]!=Explorer&&[!Type!]!=Select&&[!Type!]!=Full]
	<form action="/[!Lien!]#[!TypeEnf!]" method="post" name="rech[!TypeEnf!]" class="FormRech">
[/IF]
<input type="hidden" name="Order" value="[!Order!]"/>
<input type="hidden" name="OrderType" value="[!OrderType!]"/>
[IF [!Type!]!=Mini]
	//RECHERCHE
	[BLOC Rounded|background-color:#9A9EA0;color:#FFFFFF;|margin-bottom:5px;]
		<div  class="Filter" style="position:relative;width:100%;height:20px;">
			<div class="Bouton" style="float:right;margin:0;padding:0;margin-top:-11px;margin-bottom:-10px;position:relative;">
				<b class="b1"></b>
				<b class="b2" style="text-align:center;display:inline;">
					<input type="submit" style="background-color:transparent;margin:0;padding:0;color:white;margin-left:15px;margin-right:15px;line-height:15px;height:15px;margin-top:-3px;" value="Envoyer" />
				</b>
				<b class="b3" style=""></b>
			</div>
			<span style="margin-left:5px;"> Recherche : <input type="text" name="[!RechPrefixe!][!TypeEnf!]" value="[![!RechPrefixe!][!TypeEnf!]!]"  style="background-color:white;margin:0;padding:0;width:110px;"> 
			</span>
		</div>
	[/BLOC]
[/IF]

<table class="Liste Liste[!Type!]">
    <thead>
	<tr>
	    <th>
		<input type="image" src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="margin:0px;padding:0;border:0;width:11px;height:11px;margin-top:-1px;margin-bottom:-1px;" name="ChangeOrder" value="Id" />
		Num
	    </th>
	    [OBJ [!Module!]|[!TypeEnf!]|Obj]
	    [STORPROC [!Obj::SearchOrder!]|Prop]
		<th>
		[BLOC Rounded|background-color:#147893;color:#FFFFFF;|float:left;]
		    <input type="image" src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="margin:0px;padding:0;border:0;width:11px;height:11px;margin-top:-1px;margin-bottom:-1px;" name="ChangeOrder" value="[LIMIT 0|1][!Prop::Nom!][/LIMIT]" />&nbsp;D&eacute;tails (
			[LIMIT 1|[!NbChamp:-1!]]
				[!Prop::Nom!] 
			[/LIMIT])
		    [/BLOC]
		</th>
	    [/STORPROC]
	    [IF [!Type!]=Full]
		<th>
		    <input type="image" src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="margin:0px;padding:0;border:0;width:11px;height:11px;margin-top:-1px;margin-bottom:-1px;" name="ChangeOrder" value="tmsCreate" />&nbsp;Cr&eacute;ation
		</th>
		<th>
		    <input type="image" src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="margin:0px;padding:0;border:0;width:11px;height:11px;margin-top:-1px;margin-bottom:-1px;" name="ChangeOrder" value="tmsEdit" />&nbsp;Modification
		</th>
		<th>
		    <input type="image" src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="margin:0px;padding:0;border:0;width:11px;height:11px;margin-top:-1px;margin-bottom:-1px;" name="ChangeOrder" value="Order" />&nbsp;Us/Gr
		</th>
		<th>
		    <img src="/Skins/AdminV2/Img/Liste/ListeFlecheTitre.jpg" style="float:left;margin-top:0px;" />&nbsp;Actions
		</th>	
	    [/IF]
	</tr>
    </thead>
</table>

//LISTE
<div style="overflow:auto;width:100%;[IF [!Type!]!=Mini]position:absolute;top:[!Top:+55!]px;left:0;right:0;bottom:[!Bottom:+25!]px;[/IF]" >
	//ON affiche les lignes
	[!TabFirst:=!]
	[STORPROC [!RequeteT!]|Ob|[![!Page[!TypeEnf!]:-1!]:*[!MaxLine!]!]|[!MaxLine!]|[!Order!]|[!OrderType!]|[!SELECTION!]]
		[!Ch:=0!][!Suffixe:=!][!Test:=0!]
		//On verifie si il est selectionne
		[STORPROC [![!Var!]Tab!]|C]
			[IF [!C!]=[!Ob::Id!]]
				[!Ch:=1!]
				[COUNT [!TabFirst!]|N]
				[!TabFirst::[!N!]:=[!C!]!]
				[IF [!FirstTime!]][ELSE][!Test:=1!][/IF]
			[/IF]
		[/STORPROC]
		[MODULE Systeme/Interfaces/Liste/Ligne?Ob=[!Ob!]&Type=[!Type!]&Var=[!Var!]&NbChamp=[!NbChamp!]&Check=[!Ch!]&Test=[!Test!]&OutVar=[!OutVar!]&Behaviour=[!Behaviour!]&TypeEnf=[!TypeEnf!]&Links=[!Links!]]
	[/STORPROC]
	[STORPROC [![!Var!]Tab!]|C]
		[!T:=1!]
		[STORPROC [!TabFirst!]|D][IF [!C!]=[!D!]][!T:=0!][/IF][/STORPROC]
		//[STORPROC [!TabSup!]|E][IF [!E!]=[!C!]][!T:=0!][/IF][/STORPROC]
		[IF [!T!]]
			<input type="hidden" name="[!Var!][]" value="[!C!]" />
		[ELSE]
			//[!DEBUG::AFFICHPAS-[!C!]!]
		[/IF]
	[/STORPROC]
</div>
//CONFIGURATION VARIABLES SORTIE
[!UnSelect:=!]

[IF [!Type!]!=Mini]
	//PAGINATION
	//[IF [!TotalPage!]>1]
	[BLOC Rounded|background-color:#9A9EA0;color:#FFFFFF;|position:absolute;left:0;right:0;bottom:[!Bottom:+15!]px;display:block;|left:0;right:0;]
	<div class="Pagination">
		[IF [!Page[!TypeEnf!]!]>1]
			<div class="FlechesG">
				<input class="Page1" type="submit" value="1" name="Page[!TypeEnf!]" />
				<input class="PagePrec" type="submit" value="[!Page[!TypeEnf!]:-1!]" name="Page[!TypeEnf!]" />
			</div>			
		[/IF]	
		<div class="NumPages">
			[!Decal:=[!PagNbNum:/3!]!]
			[!Depart:=[!Page[!TypeEnf!]:-[!Decal:+1!]!]!]
			//Affichage de la premiere
			[IF [!Depart!]>0]
				<input type="submit" value="1" name="Page[!TypeEnf!]" /> ...
			[/IF]
			[STORPROC [!PagNbNum!]|Pag]
				[!Cur:=[!Pos:+[!Depart!]!]!]
				[IF [!Cur!]!=[!Page[!TypeEnf!]!]&&[!Cur!]>0&&[!Cur!]<[!TotalPage!]]
					<input type="submit" value="[!Cur!]" name="Page[!TypeEnf!]" /> 
				[ELSE]
					[IF [!Cur!]=[!Page[!TypeEnf!]!]]<span>[!Page[!TypeEnf!]!]</span>[/IF]
				[/IF]
			[/STORPROC]
			//Affichage de la derniere
			[IF [!Depart!]<[!TotalPage:-[!Decal:+1!]!]]
				... <input type="submit" value="[!TotalPage!]" name="Page[!TypeEnf!]" /> 
			[/IF]
		</div>
		[IF [!TotalPage!]>1&&[!Page[!TypeEnf!]!]<[!TotalPage!]]
			<div class="FlechesD">
				<input class="PageSuiv" type="submit" value="[!Page[!TypeEnf!]:+1!]" name="Page[!TypeEnf!]" /> 
				<input  class="Page2" type="submit" value="[!TotalPage!]" name="Page[!TypeEnf!]" /> 
			</div>		
		[/IF]
	</div>
	<div style="float:right;">
		<input type="submit" value="Supprimer" name="ListeAction" /> 		
	</div>
	[/BLOC]
	//[/IF]
[/IF]
[IF [!Type!]!=Explorer&&[!Type!]!=Select&&[!Type!]!=Full]
	</form>
[/IF]
