<?php

$tech = Sys::getOneData('Parc','Technicien/UserId='.Sys::$User->Id);
$vars['isTech'] = !!$tech;