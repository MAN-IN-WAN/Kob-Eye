[TITLE]Admin Kob-Eye | Redirection...[/TITLE]
[SWITCH [!FormSys_Valid!]|=]
	[CASE Modif]
		<meta http-equiv="refresh" content="2; url=/[!Query!]" />
		<div class="PetiteBoiteDeDialogue">
			<div class="Titre">
				Veuillez patienter...
			</div>
				Modification en cours. Vous allez &ecirc;tre redirig&eacute; dans 5 secondes. Si cela ne se fait pas, cliquez 
				<a href="/[!Query!]" class="LienModule">ici</a>.
		</div>
	[/CASE]
	[CASE Supprimer]
		<meta http-equiv="refresh" content="2; url=/[!OldQuery!]" />
		<div class="PetiteBoiteDeDialogue">
			<div class="Titre">
				Veuillez patienter...
			</div>
				Suppression en cours. Vous allez &ecirc;tre redirig&eacute; dans 5 secondes. Si cela ne se fait pas, cliquez 
				<a href="/[!OldQuery!]" class="LienModule">ici</a>.
		</div>
	[/CASE]
	[CASE Link]
		<meta http-equiv="refresh" content="2; url=/[!Module::Actuel::Nom!]" />
		<div class="PetiteBoiteDeDialogue">
			<div class="Titre">
				Veuillez patienter...
			</div>
				Le site se met &agrave; jour. Vous allez &ecirc;tre redirig&eacute; dans 5 secondes. Si cela ne se fait pas, cliquez 
				<a href="/[!Query!]" class="LienModule">ici</a>.
		</div>
	[/CASE]
	[CASE Unlink]
		<meta http-equiv="refresh" content="2; url=/[!Query!]" />
		<div class="PetiteBoiteDeDialogue">
			<div class="Titre">
				Veuillez patienter...
			</div>
				Le site se met &agrave; jour. Vous allez &ecirc;tre redirig&eacute; dans 5 secondes. Si cela ne se fait pas, cliquez 
				<a href="/[!Query!]" class="LienModule">ici</a>.
		</div>
	[/CASE]
[/SWITCH]