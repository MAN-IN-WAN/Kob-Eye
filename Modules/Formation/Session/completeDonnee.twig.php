<?php

$sess = Sys::getOneData('Formation',$vars['Query']);
$sess->completeDonnee();
echo 'OK';