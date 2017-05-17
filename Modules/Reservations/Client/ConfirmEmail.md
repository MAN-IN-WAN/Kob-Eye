[IF [!Module::Reservations::confirmEmail([!code!])!]]
<div class="alert alert-success">Félication vous avez activé votre compte avec succès. Veuillez vous rendre à la page d'accueil pour vous connecter: <a href="/">Connexion</a></div>
[ELSE]
<div class="alert alert-danger">Impossible d'activer ce compte, ou compte introuvable. veuillez ressayer ou contacter l'administrateur.  <a href="/">Connexion</a></div>
[/IF]