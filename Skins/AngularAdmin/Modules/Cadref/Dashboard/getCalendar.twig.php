<?php
$args = json_decode(file_get_contents('php://input'),true);
$vars['data'] = str_replace('\n', '<br />', json_encode(Cadref::GetCalendar($args),1));
