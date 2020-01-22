<?php
$vars['partenaires'] = Sys::getData('Reservation','Partenaire');
$vars['NBCOL'] = !empty($vars['NBCOL'])?$vars['NBCOL']:1;