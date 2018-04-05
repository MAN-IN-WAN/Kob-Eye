<?php
$vars['NbAppareils'] = Sys::getCount('LightBox','Apn');
$vars['NbPhotos'] = Sys::getCount('LightBox','Photo');
$vars['NbSessions'] = Sys::getCount('LightBox','Session');