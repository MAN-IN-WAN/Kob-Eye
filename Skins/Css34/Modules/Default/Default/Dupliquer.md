[STORPROC [!Query!]|Objet|0|1]
	[!Ob:=[!Objet::getClone()!]!]
	[METHOD Ob|Save][/METHOD]
[/STORPROC]
<p>Duplication réalisée avec succès</p>
<a href="#/[!Sys::CurrentMenu::Url!]/[!Ob::Id!]" onclick="$(this).parents('.modal').on('hidden.bs.modal', function (e) {window.location.href = '#/[!Sys::CurrentMenu::Url!]/[!Ob::Id!]';});$(this).parents('.modal').modal('hide');return false;"> Voir l'objet dupliqué </a>
