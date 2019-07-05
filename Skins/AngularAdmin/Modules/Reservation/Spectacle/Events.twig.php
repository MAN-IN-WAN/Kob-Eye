<?php

if (is_object(Sys::$CurrentMenu))
    $vars['CurrentUrl'] = Sys::$CurrentMenu->Url;
else $vars['CurrentUrl'] = $vars['Query'];