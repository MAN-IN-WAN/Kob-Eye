[STORPROC [!Query!]|MonEv|0|1][/STORPROC]
//Construction du mail et envoi &agrave; la structure organisatrice

[IF [!MonEv::Envoyer()!]]
	<h2>Envoyé avec succès.</h2>
[ELSE]
	<h4>Impossible d'envoyer le rapport : la structure organisatrice n'a pas renseign&eacute; d'adresse email de contact ou la fonction est désactivée pour cette structure culturelle</h4>
[/IF]