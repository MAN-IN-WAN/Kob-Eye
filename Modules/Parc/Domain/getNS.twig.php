<?php

$domain = $_GET['domain'];
$ns = shell_exec("dig ".$domain." NS @8.8.8.8 | grep 'IN' | grep 'NS' | grep abtel");

if(!empty($ns)) echo 'OUI';