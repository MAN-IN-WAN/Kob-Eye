[IF [!SERVER::REMOTE_ADDR!]=185.71.149.9||[!Ab!]=23686]
	<head>
		<script src="/Skins/AdminV2/Js/mootools.js"></script>
		<script src="/Skins/AdminV2/Js/mootools-more.js"></script>
		<script src="/Skins/AdminV2/Js/cal.js"></script>
		<script src="/Skins/AdminV2/Js/datepicker.js"></script>
		<script src="/Skins/Public2012/Js/autocomplete.js"></script>
		<link href="/Skins/AdminV2/Js/datepicker_vista/datepicker_vista.css" rel="stylesheet" type="text/css" ></link>
		<link href="/Skins/Public2012/Css/autocomplete.css" rel="stylesheet" type="text/css" ></link>
	</head>
	<div id="Container">
		<h2>Export des réservations des structures culturelles</h2>
		<div style="margin:0;font-size:15px;overflow: auto;">
			<form action="" name="ventes" >
				<input type="hidden" value="1" name="exportventes" >
				<input type="hidden" value="[!Ab!]" name="Ab" >
				//<label for="cp">Code Postal des structures</label>
				//<input id="cp"  type="text" value="[!cp!]" name="cp" style="display: inline;">
				<br />
				<label for="ville">Ville des structures</label><br />
				<input type="text" name="ville" id="ville" autocomplete="off" value="[!ville!]"  />
				<script type="text/javascript">autoCompleteField('ville', 'Geographie/Ville','[!ville!]', 'Nom','Nom');</script>
				<br />	<br />
				<label for="start">Date début</label>
				<input id="start" class="ncalendar" type="text" value="[!start!]" name="start" style="display: inline;">

				<label for="stop">Date fin</label>	
				<input id="stop" class="ncalendar" type="text" value="[!stop!]" name="stop" style="display: inline;">
				<script type="text/javascript">
					new DatePicker('.ncalendar', { pickerClass: 'datepicker_vista', timePicker:true, format:'Y-m-d H:i:s', allowEmpty: true });
				</script>
				<button type="submit">OK</button>	
			</form>
		</div>
		<h2>Export des mails des structures culturelles</h2>
		<div style="margin:0;font-size:15px;overflow: auto;">
			<form action="" name="mailsculturels" >
				<input type="hidden" value="1" name="exportmailculturels" >
				<input type="hidden" value="[!Ab!]" name="Ab" >
				<button type="submit">OK</button>	
			</form>
		</div>
		<h2>Export des mails des structures sociales</h2>
		<div style="margin:0;font-size:15px;overflow: auto;">
			<form action="" name="mailsocials" >
				<input type="hidden" value="1" name="exportmailsocials" >
				<input type="hidden" value="[!Ab!]" name="Ab" >
				<button type="submit">OK</button>	
			</form>
		</div>

		
		
	</div>
	[IF [!exportventes!]]
		<div style="font-family:Arial;font-size:12px">
			[!TotalResa:=0!]
			[!TotalPers:=0!]
			[STORPROC Reservation/Client/Ville=[!ville!]|St]
				[!NbResa:=0!]
				[!NbPers:=0!]
				<div style="font-weight:bold">[!St::Nom!]</div>
				[STORPROC Reservation/Client/[!St::Id!]/Reservations/|Res]
					[STORPROC Reservation/Evenement/Reservations/[!Res::Id!]|Ev]
						[IF [!Ev::DateDebut!]>=[!start!]&[!Ev::DateDebut!]<=[!stop!]]
							[COUNT Reservation/Reservations/[!Res::Id!]/Personne|Cpt]
							[!NbResa+=1!]
							[!NbPers+=[!Cpt!]!]
						[/IF]
					[/STORPROC]
				[/STORPROC]
				<div style="color:#826">[!NbResa!] réservation(s)</div>
				<div style="color:#268">[!NbPers!] personnes(s)</div>
				[!TotalResa+=[!NbResa!]!]
				[!TotalPers+=[!NbPers!]!]
				<br />
			[/STORPROC]
			<hr />
			<div style="font-weight:bold">TOTAL</div>
			<div style="color:#826">[!TotalResa!] réservation(s)</div>
			<div style="color:#268">[!TotalPers!] personnes(s)</div>
		</div>
	[/IF]
	[IF [!exportmailculturels!]]
		[OBJ Reservation|Client|Org]
		[!Org::sendHeader()!]

		[!LigneEntete:=Date création;Nom;Adresse;Téléphone;Fax;Email;Utilisateur!]
		[!Org::addLigne([!LigneEntete!])!]	
		[STORPROC Reservation/Organisation|Req]
				[!Ligne:=[!Utils::getDate(d/m/Y,[!Req::tmsCreate!])!];!]
				[!Ligne+=[!Req::Nom!];!]
				[!Ligne+=[!Req::Adresse!]- [!Req::CodePos!] [!Req::Ville!];!]
				[!Ligne+=[!Req::Tel!];!]
				[!Ligne+=[!Req::Fax!];!]
				[!Ligne+=[!Req::Mail!];!]
				[STORPROC Systeme/Groupe/[!NumeroGroupe!]|Grp|0|1]
					[STORPROC Systeme/Groupe/[!Grp::Id!]/User|Us]
						[!Ligne+=[!Grp::Id!]-[!Grp::Titre!] : [!Us::Nom!] - [!Us::Mail!]<br />!]
					[/STORPROC]
				[/STORPROC]
				[IF [!Cpt!]=0][!Org::addLigne([!LigneEntete!])!][/IF]
				[METHOD Org|addLigne][PARAM][!Ligne!][/PARAM][/METHOD]
				//[!Org::addLigne([!Ligne!])!]
				[!Cpt+=1!]
		[/STORPROC]
		[!Ligne:=Nombre de structures :;;;!]
		[!Ligne+=[!Cpt!];!]
		[!Org::addLigne([!Ligne!])!]	
	[/IF]

	[IF [!exportmailsocials!]]
		[OBJ Reservation|Client|Org]
		[!Org::sendHeader()!]
		[!LigneEntete:=Date création;Nom;Adresse;Téléphone;Fax;Email;Utilisateur!]
		[STORPROC Reservation/Client|Req]
				[!Ligne:=[!Utils::getDate(d/m/Y,[!Req::tmsCreate!])!];!]
				[!Ligne+=[!Req::Nom!];!]
				[!Ligne+=[!Req::Adresse!]-[!Req::CodePos!] [!Req::Ville!];!]
				[!Ligne+=[!Req::Tel!];!]
				[!Ligne+=[!Req::Fax!];!]
				[!Ligne+=[!Req::Mail!];!]
				[STORPROC Systeme/Groupe/[!NumeroGroupe!]|Grp|0|1]
					[STORPROC Systeme/Groupe/[!Grp::Id!]/User|Us]
						[!Ligne+=[!Grp::Id!]-[!Grp::Titre!] : [!Us::Nom!] - [!Us::Mail!]<br />!]
					[/STORPROC]
				[/STORPROC]
				[IF [!Cpt!]=0][!Org::addLigne([!LigneEntete!])!][/IF]
				[METHOD Org|addLigne][PARAM][!Ligne!][/PARAM][/METHOD]
				//[!Org::addLigne([!Ligne!])!]
				[!Cpt+=1!]
		[/STORPROC]
		[!Ligne:=Nombre de structures :;;;!]
		[!Ligne+=[!Cpt!];!]
		
		[!Org::addLigne([!Ligne!])!]	
	[/IF]
	
[ELSE]
	vous n'avez pas accès à cette fonctionnalité
[/IF]	
	
	
	
	
