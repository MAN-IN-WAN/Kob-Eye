<!-- Block permanent links module HEADER -->
<div class="blockparmanentlinks hidden-phone">
	[OBJ Systeme|Menu|M0]
	[STORPROC [!M0::getTopMenus()!]|M]
	<div class="nav-item">
		<div class="item-top">
			<a href="/[!M::Url!]" title="contact">[!M::Titre!]</a>
		</div>
	</div>
	[/STORPROC]
	[IF [!Sys::User::Public!]=]
	<div class="nav-item">
		<div class="item-top">
			<a href="/Systeme/Deconnexion" class="btn btn-primary" title="contact">Déconnexion</a>
		</div>
	</div>
	[/IF]
</div>
<!-- /Block permanent links module HEADER -->
