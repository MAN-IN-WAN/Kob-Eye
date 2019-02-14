<?php
$payload = file_get_contents('php://input');
$payload = json_decode($payload);
if (isset($payload->emails)){
    $info= Info::getInfos($vars['Query']);
    $obj = Sys::getOneData($info['Module'],$vars['Query']);
    $vars['success'] = ($obj->ImportEmails($payload->emails))?1:0;
    $vars['submitted'] = true;
    $errors = array();
    foreach ($obj->Error as $e){
        $errors[] = Utils::cleanJson($e["Message"]);
    }
    $vars['errors'] = json_encode($errors);
}