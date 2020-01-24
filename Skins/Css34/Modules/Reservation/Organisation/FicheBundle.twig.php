<?php
$temp = explode('/',$vars['Query'],2);
$vars['organisation'] = Sys::getOneData('Reservation',$temp[1]);

