<?php
$vars['ValidForm'] = isset($_GET['ValidForm']) && $_GET['ValidForm'];
$classe = isset($_GET['classe']) ? $_GET['classe'] : '';
if(!empty($classe)) $vars['classe'] = "?classe=$classe";
