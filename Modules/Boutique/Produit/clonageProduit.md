[IF [!Systeme::User::Public!]]
	[MODULE Systeme/Login]
[ELSE]
	[STORPROC [!Query!]|P|0|1]
		<h1>Clonage de [!P::Nom!]</h1>
		[!P::getClone()!]
	[/STORPROC]
[/IF]