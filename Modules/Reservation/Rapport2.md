[IF [!Systeme::User::Public!]!=1||[!Ab!]=23686]
	<div style="font-family:Arial;font-size:12px">
		[STORPROC Reservation/Spectacle/DateDebut>1420412400|Spe]
			
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
			[IF [!Spe::Disponibilite!]>0]
				<div style="font-weight:bold">[!Spe::Nom!]</div>
				<div style="color:#286">[!NbEv!] évènement(s)</div>
				<div style="color:#826">[!NbResa!] réservation(s)</div>
				<div style="color:#268">[!NbPers!] personnes(s)</div>
				<div style="color:#268">[!Spe::Disponibilite!] restant(s)</div>
				<br />
			[/IF]
		[/STORPROC]
	</div>

[/IF]