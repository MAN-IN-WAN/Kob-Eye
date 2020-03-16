<?php
$temp = explode('/',$vars['Query'],2);
$vars['client'] = Sys::getOneData('Reservation',$temp[1]);

