[IF [!Systeme::User::Login!]!=CommercialAdm]

	[COUNT Systeme/User/[!Systeme::User::Id!]/Action/Type=Optionner|Opt]
	[COUNT Systeme/User/[!Systeme::User::Id!]/Action/Type=Reserver|Rsv]
	[COUNT Systeme/User/[!Systeme::User::Id!]/Action/Type=Vendu|Vd]
	[COUNT Systeme/User/[!Systeme::User::Id!]/Denonciation/Obsolete=0|Dn]
[ELSE]
	[COUNT ParcImmobilier/Lot/Statut=2&Publier=1|Opt]
	[COUNT ParcImmobilier/Lot/Statut=3&Publier=1|Rsv]
	[COUNT ParcImmobilier/Lot/Statut=4&Publier=1|Vd]
	[COUNT ParcImmobilier/Denonciation/Obsolete=0|Dn]

[/IF]

<div class="BlocEncours">
	<div class="TitreBloc">Mes encours immobiliers</div>
	<div class="BlocContent">
		<div class="BlocBlanc">
			<div class="BlocCpt BlocOptions">
				<a class="BlocButton" href="/Residences?Affichage=Lots&amp;FiltreActions=Optionnes"></a>
				<div class="BlocCount">[!Opt!] options en cours</div>
			</div>
			<div class="BlocCpt BlocReservations">
				<a class="BlocButton" href="/Residences?Affichage=Lots&amp;FiltreActions=Reserves"></a>
				<div class="BlocCount">[!Rsv!] réservations en cours</div>
			</div>
			<div class="BlocCpt BlocVentes">
				<a class="BlocButton" href="/Residences?Affichage=Lots&amp;FiltreActions=Vendus"></a>
				<div class="BlocCount">[!Vd!] ventes en cours</div>
			</div> 
			<div class="BlocCpt BlocDenonciations">
				<a class="BlocButton" href="/Denonciations?Affichage=Liste"></a>
				<div class="BlocCount">[!Dn!] dénonciations en cours</div>
			</div>
		</div>
	</div>
</div>
