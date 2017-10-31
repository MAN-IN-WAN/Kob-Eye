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
//[IF [!Page[!TypeEnf!]!]=][!Page[!TypeEnf!]:=1!][/IF]
[!Module:=[!Test::Module!]!]
[IF [!MaxLine!]=]
    [!MaxLine:=25!]
[/IF]
[IF [!PagNbNum!]=]
    [!PagNbNum:=20!]
[/IF]
[!Order:=!]
[!OrderType:=!]
[IF [!NbChamp!]=][!NbChamp:=1!][/IF]
[IF [!RechPrefixe!]=][!RechPrefixe:=Rech!][/IF]
//VARIABLES
[IF [!Var!]!=][ELSE][!Var:=[!Prefixe!][!TypeEnf!]!][/IF]
[IF [!OutVar!]=][!OutVar:=Id!][/IF]
[OBJ [!Module!]|[!TypeEnf!]|T]
//[!DEBUG::Check!]
[IF [!Recherche[!TypeEnf!]!]!=][!RechReset:=1!][/IF]
//------------------------------------------------
//--		SELECTION			--
//------------------------------------------------
[!SELECTION:=m.Id,m.tmsCreate,m.tmsEdit,m.uid,m.gid!]
[STORPROC [!T::SearchOrder()!]|Prop|0|[!NbChamp!]]
	[!SELECTION+=,m.[!Prop::Nom!]!]
[/STORPROC]
[IF [!Type!]=Select&&[!OutVar!]!=]
    [!SELECTION+=,m.[!OutVar!]!]
[/IF]
//RECHERCHE D UN CHAMPS DE TYPE ORDER
[STORPROC [!T::getOrderField()!]|OF]
    [!SELECTION+=,m.[!Key!]!]
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
		[IF [!P::type!]=boolean]
			[IF [![!RechPrefixe!]Prop[!P::Nom!]!]=-1]
				[!Recherche+=m.[!P::Nom!]=0!]
			[ELSE]
				[!Recherche+=m.[!P::Nom!]=[![!RechPrefixe!]Prop[!P::Nom!]!]!]
			[/IF]
		[ELSE]
			[!Recherche+=m.[!P::Nom!]~[![!RechPrefixe!]Prop[!P::Nom!]!]!]
		[/IF]
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

[IF [!Filter!]!=]
	[IF [!Shlass!]][!Recherche+=&!][ELSE][!Shlass:=1!][/IF]
	[!Recherche+=[!Filter!]!!!]
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
	//correction sans la vue
	//[!RequeteT:=[!Module::Actuel::Nom!]/[!TypeEnf!]/[!RechEnf!]/Canon~[!Re!]&[!Recherche!]!]
	[!RequeteT:=[!Module::Actuel::Nom!]/[!TypeEnf!]/[!RechEnf!]:NOVIEW/Canon~[!Re!]&[!Recherche!]!]
[ELSE]
//correction sans la vue
	[IF [!Recherche!]!=][!RequeteT+=:NOVIEW/[!Recherche!]!][/IF]
	//[IF [!Recherche!]!=][!RequeteT+=/[!Recherche!]!][/IF]
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
[IF [!ListeActions!]=Supprimer]
	[STORPROC [!Liste[!TypeEnf!]!]|P]
		[STORPROC [!Module::Actuel::Nom!]/[!TypeEnf!]/[!P!]|C]
		    [METHOD C|Delete][/METHOD]
		[/STORPROC]
	[/STORPROC]
[/IF]
//SELECTION
[IF [!Type!]=Select||[!Type!]=MultiSelect]
	[COUNT [!Check!]|C]
	[COUNT [![!Var!]!]|T]
	[IF [!T!]=&&[!C!]>0]
		//On remplit la variable avec la selection
		[STORPROC [!Check!]|f]
			[![!Var!]::[!Key!]:=[!f::Id!]!]
		[/STORPROC]
	[/IF]
[/IF]
//------------------------------------------------
//--		INTERFACE			--
//------------------------------------------------

//Definition des tailles en pourcent
<input type="hidden" name="Order" value="[!Order!]"/>
<input type="hidden" name="OrderType" value="[!OrderType!]"/>
[IF [!Type!]!=Mini&&[!NoRech!]!=True]
	//RECHERCHE
	[BLOC Rounded|background-color:#9A9EA0;color:#FFFFFF;|margin-bottom:5px;]
		<div  class="Filter" style="position:relative;width:100%;height:18px;line-height:16px;">
		    <input type="submit" name="Recherche[!TypeEnf!]" class="KEBouton" value="Rechercher" style="float:right;margin-top:2px;";/>
			<span style="margin-left:5px;"> Recherche : <input type="text" name="[!RechPrefixe!][!TypeEnf!]" value="[![!RechPrefixe!][!TypeEnf!]!]"  style="background-color:white;margin:0;margin-top:0;padding:0;width:110px;"> 
			</span>
		</div>
	[/BLOC]
[/IF]

<div class="ListeContainer" [IF [!Type!]!=Mini&&[!Type!]!=Select&&[!Type!]!=MultiSelect]style="margin-bottom:35px;"[/IF]>
<table class="Liste Liste[!Type!]">
    <thead>
	<tr>
	    <th class="NumCol" [IF [!Type!]=MultiSelect]colspan="2"[/IF]>
            //RECHERCHE D UN CHAMPS DE TYPE ORDER
            [STORPROC [!T::getOrderField()!]|OF]
                [!Key!]
                [NORESULT]
        	    Num
                [/NORESULT]
            [/STORPROC]
	    </th>
	    [OBJ [!Module!]|[!TypeEnf!]|Obj]
	    [IF [!Type!]=Select&&[!OutVar!]!=]
		    <th class="NomCol">
			[!OutVar!]
		    </th>
	    [ELSE]
		[STORPROC [!Obj::SearchOrder!]|Prop|0|[!NbChamp!]]
		    <th class="NomCol" width="[!60:/[!NbResult!]!]%">
			[!Prop::Nom!]
		    </th>
		[/STORPROC]
	    [/IF]
	    [IF [!Type!]=Full||[!Type!]=Select||[!Type!]=MultiSelect]
		<th class="CreaCol">
		    Cr&eacute;ation
		</th>
		<th class="ModifCol">
		    Modification
		</th>
		<th class="UsersCol">
		    Us/Gr
		</th>
	    [/IF]
	    [IF [!Type!]=Col||[!Type!]=Mini||[!Type!]=Full]
		<th class="ActionsCol" [IF [!Type!]=Full]colspan=2[/IF]>
		    Actions
		</th>	
	    [/IF]
	</tr>
    </thead>
</table>
<div style="overflow:auto;position:absolute;[IF [!Type!]=Col]top:80px;[ELSE]top:55px;[/IF]bottom:30px;left:5px;right:5px;">
	<table class="Liste Liste[!Type!]">
		<tbody>
			//ON affiche les lignes
			[!TabFirst:=!]
			[IF [!Page[!TypeEnf!]!]!=][!NumPage:=[!Page[!TypeEnf!]!]!][ELSE]
			[!NumPage:=[!CurrentPage[!TypeEnf!]!]!][/IF]
			[IF [!NumPage!]=||[!RechReset!]=1][!NumPage:=1!][/IF]
			[COUNT [!RequeteT!]|N]
			[IF [!N!]<[![!Page[!TypeEnf!]:-1!]:*[!MaxLine!]!]]
				[!U:=[!Math::Floor([!N:/[!MaxLine!]!]!]!]
				[!U+=1!]
				[!NumPage:=[!U!]!]
			[/IF]
			[!Pc:=0!]
			[STORPROC [!RequeteT!]|Ob|[![!NumPage:-1!]:*[!MaxLine!]!]|[!MaxLine!]|[!Order!]|[!OrderType!]|[!SELECTION!]]
				[!Ch:=0!][!Suffixe:=!][!Test:=0!]
				//On verifie si il est selectionne
				[STORPROC [![!Var!]!]|C]
					[IF [!C!]=[!Ob::Id!]]
						[!Ch:=1!]
						[![!Var!]Check::[!Pc!]:=[!C!]!]
						[!Pc+=1!]
					[/IF]
				[/STORPROC]
				<tr>
					[MODULE Systeme/Interfaces/Liste/Ligne?Ob=[!Ob!]&Type=[!Type!]&Var=[!Var!]&NbChamp=[!NbChamp!]&Check=[!Ch!]&Test=[!Test!]&OutVar=[!OutVar!]&Behaviour=[!Behaviour!]&TypeEnf=[!TypeEnf!]&Links=[!Links!]&Chemin=[!Chemin!]&Inter=[!Inter!]]
				</tr>
			[/STORPROC]
			[STORPROC [![!Var!]!]|C]
				[!T:=1!]
				[STORPROC [![!Var!]Check!]|D][IF [!C!]=[!D!]][!T:=0!][/IF][/STORPROC]
				[IF [!T!]]
					[IF [!Prefixe!]=Dep&&[!Inter!]=radio]
					[ELSE]
					<input type="hidden" name="[!Var!][]" value="[!C!]" />
					[/IF]
				[/IF]
			[/STORPROC]
		</tbody>
	</table>
</div>


//CONFIGURATION VARIABLES SORTIE
[!UnSelect:=!]
//[IF [!Type!]!=Mini]
	//PAGINATION
	//[IF [!TotalPage!]>1]
	[BLOC Rounded|background-color:#9A9EA0;color:#FFFFFF;margin-top:5px;position:absolute;bottom:5px;left:5px;right:5px;]
	<div class="Pagination">
	    [IF [!Type!]=Full]
		<input type="submit" style="float:right;margin-top:2px;" class="KEBouton" value="Supprimer" name="ListeActions"/>
	    [/IF]
	    [!MoreClass:=!]
	    [IF [!NumPage!]>1]
	    [ELSE]
		[!MoreClass:=True!]
	    [/IF]
			<div class="FlechesG">
				<input class="Page1  [IF [!MoreClass!]=True]  Page1D  [/IF]" type="submit" value="1" name="Page[!TypeEnf!]" [IF [!MoreClass!]=True]disabled="disabled"[/IF]/>
				<input class="PagePrec  [IF [!MoreClass!]=True]  PagePrecD  [/IF]" type="submit" value="[!NumPage:-1!]" name="Page[!TypeEnf!]" [IF [!MoreClass!]=True]disabled="disabled"[/IF]/>
			</div>			
		<div class="NumPages">
			//Affichage de la premiere
			[!Depart:=[!NumPage!]!]
			[!Depart-=[!Math::Floor([!PagNbNum:/2!])!]!]
			[!Depart-=1!]
			
			[IF [!Depart!]<1]
			    [!Depart:=0!]
			[/IF]
			[IF [!Depart!]>[!TotalPage:-[!PagNbNum!]!]]
			    [!Depart:=[!TotalPage:-[!PagNbNum!]!]!]
			[/IF]

			[IF [!Depart!]>1]
			    <input type="submit" value="1" name="Page[!TypeEnf!]" class="KEBouton" style="float:left;padding:0px;padding-bottom:2px;margin:0px;margin-left:3px;"/>
			    <div style="display:block;float:left;-moz-border-radius:5px 5px;background:none;font-size:14pt;color:white;border:0;padding:0px 3px 0px 3px;margin-top:-2px;margin-left:3px;margin-top:2px;font-size:14pt;font-weight:bolder;">...</div>
			    [!PagNbNum-=1!]
			[/IF]
			[IF [!Depart:+[!PagNbNum!]!]<[!TotalPage!]]
			    [!PagNbNum-=1!]
			[/IF]
			[!Depart:=[!NumPage!]!]
			[!Depart-=[!Math::Floor([!PagNbNum:/2!])!]!]
			[!Depart-=1!]
			
			[IF [!Depart!]<1]
			    [!Depart:=0!]
			[/IF]
			[IF [!Depart!]>[!TotalPage:-[!PagNbNum!]!]]
			    [!Depart:=[!TotalPage:-[!PagNbNum!]!]!]
			[/IF]

			[STORPROC [!PagNbNum!]|Pag]
			    [LIMIT 0|1000]
				[!Cur:=[!Pos:+[!Depart!]!]!]
				[IF [!Cur!]!=[!NumPage!]&&[!Cur!]>0&&[!Cur!]<=[!TotalPage!]]
					<input type="submit" value="[!Cur!]" name="Page[!TypeEnf!]" class="KEBouton" style="float:left;padding:0px;padding-bottom:2px;margin:0px;margin-left:3px;"/> 
				[ELSE]
					[IF [!Cur!]=[!NumPage!]]<div style="display:block;float:left;-moz-border-radius:5px 5px;background:white;color:#FF5A00;border:0;padding:0px 3px 0px 3px;margin-top:0px;margin-left:3px;margin-top:1px;font-size:9pt;">[!NumPage!]</div>[/IF]
				[/IF]
			    [/LIMIT]
			[/STORPROC]
			[IF [!Depart:+[!PagNbNum!]!]<[!TotalPage!]]
			    <div style="display:block;float:left;-moz-border-radius:5px 5px;background:none;font-size:14pt;color:white;border:0;padding:0px 3px 0px 3px;margin-top:-2px;margin-left:3px;margin-top:2px;font-size:14pt;font-weight:bolder;">...</div>
			    <input type="submit" value="[!TotalPage!]" name="Page[!TypeEnf!]" class="KEBouton" style="float:left;padding:0px;padding-bottom:2px;margin:0px;margin-left:3px;"/>
			[/IF]
			//Affichage de la derniere
		</div>
		[!MoreClass:=!]
		[IF [!TotalPage!]>1&&[!NumPage!]<[!TotalPage!]]
		[ELSE]
		    [!MoreClass:=True!]
		[/IF]
			<div class="FlechesD">
				<input class="PageSuiv   [IF [!MoreClass!]=True]  PageSuivD  [/IF] " type="submit" value="[!NumPage:+1!]" name="Page[!TypeEnf!]" [IF [!MoreClass!]=True]disabled="disabled"[/IF]/> 
				<input  class="Page2   [IF [!MoreClass!]=True]  Page2D  [/IF] " type="submit" value="[!TotalPage!]" name="Page[!TypeEnf!]" [IF [!MoreClass!]=True]disabled="disabled"[/IF]/> 
			</div>		
	</div>
	<input type="hidden" value="[!NumPage!]" name="CurrentPage[!TypeEnf!]" /> 

	[/BLOC]
	//[/IF]
//[/IF]
</div>
<script type="text/javascript">
	Fl.makePopup();
</script>
