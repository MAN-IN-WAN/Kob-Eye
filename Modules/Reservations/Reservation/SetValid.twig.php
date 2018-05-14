<?php


$query = explode('/',$vars['Query'],2);

$res = Sys::getOneData($query[0],$query[1]);

$res->setValide();

echo '<div class="alert alert-success">
        La réservation a été validée avec succès.<br/>
        Un confirmation viens d\'être envoyée à l\'utilisateur.
</div>';