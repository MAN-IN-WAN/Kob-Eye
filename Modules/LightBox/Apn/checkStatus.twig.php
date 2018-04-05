<?php
$apn = Apn::getCurrent();
$status = $apn->getStatus();
$vars['result'] = json_encode($status);