//Evenement en cours
[STORPROC [!Query!]|MonEv|0|1]

//Le spectacle concerne
[STORPROC Reservation/Spectacle/Evenement/[!MonEv::Id!]|Obj|0|1][/STORPROC]

//Structure organisatrice
[STORPROC Reservation/Organisation/Spectacle/[!Obj::Id!]|MonOrg|0|1][/STORPROC]

//La salle concernee
[STORPROC Reservation/Salle/Evenement/[!MonEv::Id!]|MaSalle|0|1][/STORPROC]


<div style="width:800px;padding-bottom:20px;">
	<div style="overflow:hidden;background:white;border:1px solid #ccc">
		<span style="margin:5px;font-size:11px;font-style:italic;float:right">[!Domaine!]</span>
	</div>

<p style="overflow:hidden; padding:5px">Nous vous remercions d'<span style='text-decoration:underline'>imprimer</span> et de <span style='text-decoration:underline'>conserver</span> ce document après avoir rempli la case <span style='text-decoration:underline'>PRESENCE</span> des publics (et de nous laisser un éventuel commentaire)<br />
Nous avons besoin de vos retours sur les places proposées qui ont été effectivement utilisées.<br />
Nous récupérerons l'ensemble des documents lors d'un contact en fin de saison que nous conviendrons ensemble.<br />
L'équipe culture et sport solidaires 34.
</p>

	<div style="overflow:hidden; padding:5px">
		<img src="/Skins/Css34/Css/images/css34-logo.jpg" alt="Logo" style="float:left;display:block;position:relative; height:100px"/>
		<div style="margin-left:50px">
			<h2 style="color:#000;font-family:Arial;color:#3F3F3F;font-weight:bold;margin:4px 0">Spectacle : [!Obj::Nom!]</h2>
			<h4>Structure organisatrice : <span style="color:#E33654;"> [!MonOrg::Nom!]</span></h4>
			<div style="overflow:hidden;width:100%;display:block;position:relative;margin:0;padding:0;">
				<h4 style="float:left;width:40%;margin:4px 0">Date :<span style="color:#E33654;"> [DATE d/m/y][!MonEv::DateDebut!][/DATE] &agrave; [DATE H:i][!MonEv::DateDebut!][/DATE]</span></h3>
				<h4 style="float:left;margin:4px 0">Lieu : <span style="color:#E33654;"> [!MaSalle::Nom!]</span></h4>
			</div>
			<div style="overflow:hidden;width:100%;display:block;position:relative;margin:0;">
				[COUNT [!Query!]/Reservations|R]
				<h4 style="float:left;width:40%;margin:2px 0">Nombre de r&eacute;servations : [IF [!R!]>0] <span style="color:#E33654;">[!R!]</span>[ELSE] <span style="color:#E33654;"> aucune</span>[/IF]</h4>
				[STORPROC [!Query!]/Reservations|MaRes|0|1|Id|DESC|SUM(m.NbPlace)|j0t.EvenementId]
					<h4 style="float:left;width:40%;margin:2px 0">Nombre de places r&eacute;serv&eacute;es :[IF [!MaRes::SUM(m.NbPlace)!]>0] <span style="color:#E33654;"> [!MaRes::SUM(m.NbPlace)!] </span>[ELSE] <span style="color:#E33654;"> aucune</span>[/IF]</h4>
				[/STORPROC]
			</div>
		</div>
	</div>
	<div style="magin:20px 0">
	
		[STORPROC [!Query!]/Reservations|LesResa|0|200|tmsCreate|Desc]
			<table cellspacing="0" cellspadding="5" style="border:0;width:100%;margin-bottom:15px;font-size:13px;" border="collapse">
				<tr>
					<td  style="border:1px solid #cccccc;width:15%;padding-left:5px;background:#F2F5F9;" bgcolor="red" >R&eacute;f&eacute;rence</td>
					<td  style="border:1px solid #cccccc;width:35%;padding-left:5px;background:#F2F5F9;">Relais social</td>
					<td  style="border:1px solid #cccccc;width:13%;padding-left:5px;background:#F2F5F9;text-align:center;">Places r&eacute;serv&eacute;es</td>
					<td  style="border:1px solid #cccccc;padding-left:5px;background:#F2F5F9;">Personnes</td>
					<td  style="border:1px solid #cccccc;width:15%;padding-left:5px;background:#F2F5F9;">Date de r&eacute;servation</td>
					<td  style="border:1px solid #cccccc;width:13%;padding-left:5px;background:#F2F5F9;text-align:center;">Pr&eacute;sence</td>
				</tr>
				<tr>
					<td  style="border:1px solid #cccccc;padding-left:5px;">[!LesResa::Reference!]</td>
					<td  style="border:1px solid #cccccc;padding-left:5px;">
						[STORPROC Reservation/Client/Reservations/[!LesResa::Id!]|Cli|0|1][/STORPROC][!Cli::Nom!]
					</td>
					<td  style="border:1px solid #cccccc;padding:5px;text-align:center;">[!LesResa::NbPlace!]</td>
					<td  style="border:1px solid #cccccc;padding:5px;">
						[STORPROC [!Query!]/Reservations/[!LesResa::Id!]/Personne|P]
							[!P::Prenom!] [!P::Nom!] [IF [!P::PMR!]!=0] : Personne  &agrave; Mobilit&eacute; r&eacute;duite[/IF]<br />
						[/STORPROC]
					</td>
					<td  style="border:1px solid #cccccc;padding:5px;">[DATE d/m/Y][!LesResa::tmsCreate!][/DATE]</td>
					<td  style="border:1px solid #cccccc;padding:5px;text-align:center;"><input type="text" size="2" /></td>
					
				</tr>	
			</table>
		[/STORPROC]

	</div>

	<span style="text-transform:uppercase;font-weight:bold">Commentaire :</span>
</div>

[NORESULT]
L'évènement a été supprimé.
[/NORESULT]

[/STORPROC]