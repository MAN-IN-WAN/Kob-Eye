<?php
$classe = $_SESSION['inscription'];
$retour = $_SESSION['retour'];
$vars['result'] = Inscription::WebAddClasse($classe);