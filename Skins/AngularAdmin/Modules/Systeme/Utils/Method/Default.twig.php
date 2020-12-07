<?php

    $data = json_decode(file_get_contents('php://input'),true);
    $funcCall =  json_decode($data['Func'],true);
    $path = $funcCall['query'];
    $name = $funcCall['name'];

    $info = Info::getInfos($path);

		//Si pas de retro compat on vérifie que l'objet ai une method portant ce nom
		if(! intval(explode('/',$path)[2])) $obj = genericClass::createInstance($info['Module'],$info['ObjectType']);
		else $obj = Sys::getOneData($info['Module'],explode('/',$path,2)[1]);
		$methods = get_class_methods($obj);

		if(!is_array($methods) || !in_array($name,$methods)){
			$error = "La fonction que vous essayez d'exectuer n'est pas définie pour l'objet souhaité";
			$vars['toReturn']['errors'][] = array("Message"=>$error);
		} else {
			if($funcCall['explodeArgs'])
				$temp = call_user_func_array(array($obj, $name), $funcCall['args']);
			else
				$temp = $obj->{$name}($funcCall['args']);
			
			$vars['toReturn']['data'] = $temp;
			$vars['toReturn']['success'] = true;
		}
    
    $vars['toReturn'] = json_encode($vars['toReturn']);



