[IF [!Systeme::User::Public!]!=1||[!Ab!]=23686]
	<div style="font-family:Arial;font-size:12px">
		[!TotalEv:=0!]
		[!TotalResa:=0!]
		[!TotalPers:=0!]
		[STORPROC Reservation/Organisation/[!Org!]/Spectacle/tmsCreate>1325372400|Spe]
			<div style="font-weight:bold">[!Spe::Nom!]</div>
			[!NbEv:=0!]
			[!NbResa:=0!]
			[!NbPers:=0!]
			[STORPROC Reservation/Spectacle/[!Spe::Id!]/Evenement|Ev]
				[!NbEv+=1!]
				[STORPROC Reservation/Evenement/[!Ev::Id!]/Reservations|Res]
					[COUNT Reservation/Reservations/[!Res::Id!]/Personne|Cpt]
					[!NbResa+=1!]
					[!NbPers+=[!Cpt!]!]
				[/STORPROC]
			[/STORPROC]
			<div style="color:#286">[!NbEv!] évènement(s)</div>
			<div style="color:#826">[!NbResa!] réservation(s)</div>
			<div style="color:#268">[!NbPers!] personnes(s)</div>
			[!TotalEv+=[!NbEv!]!]
			[!TotalResa+=[!NbResa!]!]
			[!TotalPers+=[!NbPers!]!]
			<br />
		[/STORPROC]
		<hr />
		<div style="font-weight:bold">TOTAL</div>
		<div style="color:#286">[!TotalEv!] évènement(s)</div>
		<div style="color:#826">[!TotalResa!] réservation(s)</div>
		<div style="color:#268">[!TotalPers!] personnes(s)</div>
	</div>


[/IF]