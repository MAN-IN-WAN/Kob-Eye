<?php
if (is_object(Sys::$CurrentMenu))
    $vars['controller'] = str_replace('/','',Sys::$CurrentMenu->Url);
else
    $vars['controller'] = str_replace('/','',$vars['Query']);

?>