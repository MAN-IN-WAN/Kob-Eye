<?php
$obj = PhotoSession::getCurrent();
$status = $obj->getStatus();
$vars['result'] = json_encode($status);