<?php
$data = file_get_contents('/tmp/dataSerial.log');
$data = unserialize($data);

$vars['data'] = $data;