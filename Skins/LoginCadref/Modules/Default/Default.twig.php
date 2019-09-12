<?php
$vars['logform'] = isset($_POST['logform']) ? $_POST['logform'] : '';
$classe = isset($_GET['classe']) ? $_GET['classe'] : '';
if(!empty($classe)) $vars['classe'] = "/?classe=$classe";
