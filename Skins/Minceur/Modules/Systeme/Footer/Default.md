	<!-- Footer Module Position -->
	<footer class="footer row-fluid">
		<div class="Footerfond"></div>
		<div class="custom ">
			<div class="row-fluid">
				//<div class="span4">
				//	<strong>[!Systeme::User::Nom!]</strong>
				//	<p><img src="/[!Systeme::User::Avatar!]" /></p>
				//</div>
				<div class="span6">
					<p>
						[!Systeme::User::Nom!]
						[!Systeme::User::Adresse!]
						[!Systeme::User::CodPos!]
						[!Systeme::User::Ville!]
						[!Systeme::User::Pays!]
					</p>
				</div>
				<div class="span6">
				[IF [!Systeme::User::Informations!]]
					<p>
						Informations Légales:
						[!Systeme::User::Informations!]
					</p>
				[/IF]
				</div>
			</div>
		</div>

	</footer>
