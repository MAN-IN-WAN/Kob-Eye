<?php
class WebService extends Root{
	public function __construct() {}

 	public function __call($method,$arguments) {
		//Analyse du lien
		$Lien = $GLOBALS["Systeme"]->Lien;
		$temp = explode("/",$Lien);
		$Module = $temp[0];
		$GLOBALS["Systeme"]->Log->log("SOAP $method $arguments ");
		//Renvoie les details de la requete 
		switch ($method) {
			case "Ajouter":
			break;
			case "Supprimer":
			break;
			case "MajCompte":
				return $this->MajCompte($arguments);
			break;
			case "GetFile":
				return $this->GetFile($arguments);
			break;
			case "GetPack":
				return $this->GetPack($arguments);
			break;
			case "GetClient":
				return $this->GetClient($arguments);
			break;
			case "Synchro":
				return $this->AddProdMethod($arguments,$Module);
			break;
			case "AddProd":
				return $this->AddProdMethod($arguments,$Module);
			break;
			case "GetClient":
				return $this->GetClient($arguments);
			break;
			case "getData":
				return $this->getData($arguments);				
			break;
			case "getNode":
				return $this->getNode($arguments);				
			break;
			case "getInfo":
				return $this->getInfo($arguments);				
			break;
			case "getCount":
				return $this->getCount($arguments);
				break;
			default:
				return  "<h1>natom.kob-eye.com ERROR $method </h1>";
			break;
		}
 	}	


	/*
	 * axenergie
	 * 
	 */
	function Axenergie($cat) {
	//	$cat = 
	}


	/*
	 * load json form
	 */
	function getJsonForm($url) {
		$data = "";
		$Sys=$GLOBALS["Systeme"];
		$Sys->Lien = $url;
		$Sys->AnalyseVars();
		Parser::Init();
		$Skin = new Skin();
		$Sys->CurrentSkin=$Skin; 
		$Sys->CurrentSkin->Template = false;  
		$data .= $Sys->getContenu();
		$data = $Skin->ProcessLang($data);
		return $data;
	}


	/*
	 * get events and alerts
	 */
	function getAlerts($lastAlert) {
		Connection::CloseSession();
		if(! MULTITHREAD) {
			return '{"lastAlert":"","alerts":[]}';
		}
		$alerts = array();
		$cnt = 20;
		while($cnt) {
			$cnt -= 5;
			sleep(5);
			$GLOBALS["Systeme"]->restartTransaction();
			//$GLOBALS["Systeme"]->connectSQL();
			$time = microtime(true);
			foreach(Sys::$Modules as $mod) {
				foreach ($mod->Db->ObjectClass as $obj) {
					if($mod->Nom == $obj->Module) {
						$cls = genericClass::createInstance($obj->Module, $obj->titre);
						$alrt = $cls->getAlerts($lastAlert, $time);
						if($alrt) $alerts = array_merge($alerts, $alrt);
					}
				}
			}
			if(count($alerts)) break;
			$lastAlert = $time;
			//if (is_object($this->Db[0])) {
			//	$this->Db[0]->query("COMMIT");
			//	$this->Db[0]->close();
			//}
		}
		return '{"lastAlert":"'.$time.'","alerts":'.json_encode($alerts).'}';
	}



	/*
	 * 
	 */
	static function WSData($label, $start, $count, $rows, $query, $child, $filter, $module, $object, $data) {
	 	$json = WebService::WSDataString($label, $start, $count, $rows, $query, $child, $filter, $module, $object, $data);
		return '{'.$json.'}';
	}
	
	static function WSDataString($label, $start, $count, $rows, $query, $child, $filter, $module, $object, $data, $time=0) {
		if(! $time) $time = time();
		$json = 
'"data":{
"time":"'.$time.'",
"identifier":"Id",
"label":"'.$label.'",
"start":"'.$start.'",
"count":"'.$count.'",
"numRows":"'.$rows.'",
"query":"'.$query.'",
"typeChild":"'.$child.'",
"filter":"'.$filter.'",
"module":"'.$module.'",
"objectclass":"'.$object.'",
"items":'.json_encode($data).'}';
		return $json;
	 }
	
	
	/*
	 * 
	 */
	
	static function WSStatus($type, $success, $id, $module, $object, $parent, $parentId, $errors, $result, $parents=null, $more=Array()) {
		$json = WebService::WSStatusString($type, $success, $id, $module, $object, $parent, $parentId, $errors, $result, $parents, $more);
		return '{"status":'.$json.'}';
}
	
	static private function WSStatusString($type, $success, $id, $module, $object, $parent, $parentId, $errors, $result, $parents=null, $more=Array(), $time=0) {
		if(! $time) $time = time();
		$json = 
'{"type":"'.$type.'",
"time":"'.$time.'",
"success":'.$success.',
"Id":"'.$id.'",
"module":"'.$module.'",
"objectClass":"'.$object.'",
"parentClass":"'.$parent.'",
"parentId":"'.$parentId.'",
"parents":'.json_encode($parents).',
"errors":'.json_encode($errors).',';
		if (sizeof($more))foreach ($more as $n=>$m)
			$json.= '
"'.$n.'":"'.$m.'",';
		$json.='
"result":'.json_encode($result).'}';
		return $json;
	}
	
	

	static function WSStatusMulti($a, $data=null) {
		if (! is_array($a) && ! $data) return;
		$json = '{';
		if($data) {
			$json .= $data;
			if(! $a) return $json.'}';
			$json .= ',';
		}
		$f=0;
		$json .= '"status":[';
		foreach ($a as $s){
			$json.=($f)?',':'';
			$f++;
			$type = $s[0];
			$success = $s[1];
			$id = $s[2];
			$module = $s[3];
			$object = $s[4];
			$parent = $s[5];
			$parentId = $s[6];
			$errors = $s[7];
			$result = $s[8];
			$parents = $s[9];
			$more = $s[10];
			$json .= WebService::WSStatusString($type, $success, $id, $module, $object, $parent, $parentId, $errors, $result, $parents, $more);
		}
		$json.=']}';
		return $json;
	 }
	/*
	 * execute a method
	 * method must send back a json string
	 */
	 function getMethod($json) {
	 	$arg = json_decode($json);
	 	$ret = '{}';
	 	$data = $arg->data;
	 	switch($arg->method) {
			case 'query':
				$url = $this->getURL($data);
				$arr = Array($url, $data->offset, $data->limit, $data->order, $data->sortField, $data->select, $data->groupBy);
				$rec = $this->getRawData($arr);
				foreach($rec as $rc) {
					$obj = genericClass::createInstance($data->module, $rc);
					$ret = call_user_func_array(array($obj, $arg->function), $arg->args);
				}
				break;
			case 'object':
				$obj = genericClass::createInstance($data->module, $data->objectClass);
				// EM 130418
				if($data->id > 0) $obj->initFromId($data->id);
				// PGF 130315
				//if($data->parentClass) $obj->addParent($data->module.'/'.$data->parentClass.'/'.$data->parentId);
				if (!method_exists($obj, $arg->function))
					return "La methode (".$arg->function.") de l'object ".$data->module."/".$data->objectClass." n'existe pas...";
				$ret = call_user_func_array(array($obj, $arg->function), $arg->args);
//$GLOBALS["Systeme"]->Log->log('WebService.getMethod:'.$arg->function, $arg->args);
				break;
			case 'direct':
				break;
			case 'form':
				return $this->getJsonForm('Repertoire/Tiers/FormBase');
		}

		return $ret;
		
		$json = 
"{\"status\":{
	\"type\":\"method\",
	\"success\":1,
	\"Id\":\"$data->id\",
	\"module\":\"$data->module\",
	\"objectClass\":\"$data->objectClass\",
	\"parentClass\":\"$data->parentClass\",
	\"parentId\":\"$data->parentId\",
	\"errors\":[],
	\"result\":$ret
}}";
		return $json;
	 }


		/**
		 * remoteSave
		 * Save object remotely
		 */
		function remoteSave($json){
			$arg = json_decode($json);
			$ret = '{}';
			$data = $arg->data;
			$obj = genericClass::createInstance($data->module, $data->objectClass);
			if($data->id>0) $obj->initFromId($data->id);
			//			$ret = $obj->saveRemote($arg->args[0]);
			try {
				$ret = $obj->saveRemote($arg);
			} catch(Exception $exp) {
				$ret = $exp->getMessage();
			}
			//suppresion de la gestino des paretns du kobeye class pour permettre la gestion des déplacements independement du kobeyeClass 
			//if($data->parentClass) $obj->addParent($data->module.'/'.$data->parentClass.'/'.$data->parentId);
			return $ret;
		}
		
		/******
		  * remoteDelete
		  */
		function remoteDelete($json) {
			$arg = json_decode($json);
			$data = $arg->data;
			$obj = genericClass::createInstance($data->module, $data->objectClass);
			$st = 0;
			if($data->id > 0) {
				$obj->initFromId($data->id);
				try {
					$st = $obj->Delete();
					if(!is_array($st)&&!$st)$er = array(array('message'=>'No return value for Delete()'));
				}
				catch(Exception $e) {
					$er = array(array('message'=>$e->getMessage()));
					return WebService::WSStatus('delete', 0,'','','', '', '', $er, null);
				}
			}
			else $er = Array('Invalid Id = 0');
			if (is_array($st)) return WebService::WSStatusMulti($st);
			return WebService::WSStatus('delete', $st, $data->id, $data->module, $data->objectClass, '', '', $er, null);
		}

		/**
		 * changeRights
		 * Change rights recursively
		 */
		 function changeRights($json){
		 	$arg = json_decode($json);
		 	$ret = '{}';
		 	$data = $arg->data;
			$obj = genericClass::createInstance($data->module, $data->objectClass);
			if($data->id>0) $obj->initFromId($data->id);
			$ret = $obj->changeRights($arg->args[0]);
			//return "CHANGE RIGHTS";
			return $ret;
		 }

		/*
		 * return url
		 * Construit l'url pour le chargement des data
		 */
		private function getURL($data) {
			$url = $data->module;
			if($data->parentClass) {
				$url .= '/' . $data->parentClass;
				if($data->parentId) $url .= '/' . $data->parentId;
			}
			$url .= '/' . $data->objectClass;
			if($data->id) $url .= '/' . $data->id;
			return $url;
		}


	/*
	 * get count for a kob-eye query
	 */
	function getCount($arg) {
		Connection::CloseSession();
		$count = $this->getCountRows($arg);
		$json = "{\"count\":{
\"numRows\":\"$count\",
\"query\":\"$arg\",
\"typeChild\":\"COUNT\",
\"filter\":\"\"
}}";
		return $json;
	}

	/*
	 * get count for a kob-eye query
	 */
	private function getCountRows($arg) {
		Connection::CloseSession();
		$temp = explode("/",$arg);	
		$module = $temp[0];
		$rec = Sys::$Modules[$module]->callData($arg,false,0,1,'','','COUNT(DISTINCT(m.Id))','');
		return $rec[0]['COUNT(DISTINCT(m.Id))'];	
	}

	/**
	 * specific to windev
	 * get data from a kob-eye query
	 * @param $arg String (Query)
	 */
	function WDgetData($arg) {
		//require_once("Class/Lib/xml2array.class.php");
		//return print_r($arg,true);
		$temp=$arg;
		$arg=Array($temp,0,1000);
		$temp = explode("/",$arg[0]);
		$module = $temp[0];
		$rec = Sys::$Modules[$module]->callData($arg[0],false,$arg[1],$arg[2],$arg[3],$arg[4],$arg[5],$arg[6],false);
//if($arg[0] == 'Repertoire/Contact/3') return json_encode($rec);		
		// objet kob-eye
		$i = Info::getInfos($arg[0]);
		$tc = $i["TypeChild"];
		$obj = genericClass::createInstance($module, $tc);
		if(! is_object($obj)) return "getData: request error : $arg[0]";
		$So = $obj->SearchOrder();
		$Key=false;
		if (sizeof($So)){
			if(is_array($So))
				foreach ($So as $S) {
					if ($S["searchOrder"]==1 && $S["Titre"]!="Url") $Key = $S["Titre"];
				}
		}

		$n = sizeof($rec);
		for($i = 0; $i < $n; $i++) {
			$r = &$rec[$i];
			unset($r['ObjectType']);
			unset($r['note']);
			unset($r['QueryType']);
			unset($r['Query']);
			unset($r['Module']);
			$r["module"] = $module;
			$r["objectclass"] = $tc;
		}
//		$rec=new Array("test","test2","test3");
		return $rec;
	}
	/**
	 * get data from a kob-eye query
	 * @param $arg[0] Query
	 * @param $arg[1] Offset
	 * @param $arg[2] Limit
	 * @param $arg[3] OrderField
	 * @param $arg[4] Order
	 * @param $arg[5] Selection
	 * @param $arg[6] GroupBy
	 * @param $arg[7] Children list to count
	 * @param $arg[8] Sys & rights infos
	 * 
	 */
	function getData($arg) {
		Connection::CloseSession();
		if(substr($arg[0],0,7)=='GENERAL') {
			$data = $GLOBALS['Systeme']->Conf->get($arg[0]);
			$rows = sizeof($rec);
			return $this->WSData('', $arg[1], $arg[2], $rows, $arg[0], 'CONF', '', '', '', $data);
		}
		//initialisation
		$i = Info::getInfos($arg[0]);
		$tc = $i["TypeChild"];
		$module = $i["Module"];
		// objet kob-eye
		$obj = genericClass::createInstance($module, $tc);
		if(! is_object($obj)) return "getData: request error on object creation $module, $tc : $arg[0]";
		//query data
		$rec = $this->getRawData($arg);
		//recherche du label
		$rec = $this->cleanData($rec);
		$So = $obj->SearchOrder();
		$Key=false;
		if (sizeof($So)){
			if(is_array($So))
				foreach ($So as $S) {
					if ($S["searchOrder"]==1 && $S["Titre"]!="Url") $Key = $S["Titre"];
				}
		}
		$rows = Sys::$Modules[$module]->callData($arg[0],false,0,1,'','','COUNT(DISTINCT(m.Id))',$arg[6]);
		$rows = $rows[0]['COUNT(DISTINCT(m.Id))'];
		return $this->WSData($Key, $arg[1], $arg[2], $rows, $arg[0], $tc, '', $module, $tc, $rec);
	}

	private function getRawData($arg){
//Klog::l(">>>>>>>>>>>> QUERY => ".$arg[0]);
		$i = Info::getInfos($arg[0]);
		$tc = $i["TypeChild"];
		$module = $i["QueryModule"];
		$select = $arg[5];
		//VERIFICATION DES TRAITEMENTS SYSTEMES
		$_SYS_ = $arg[8];
		if ($_SYS_=="true"){
			$select .= (!empty($select) ? ',':'m.*,').'m.uid,m.gid,m.umod,m.omod,m.gmod';
		}
		//$GLOBALS["Systeme"]->Log->log("GET RAW DATA $module",$i);
		$rec = Sys::$Modules[$module]->callData($arg[0],false,$arg[1],$arg[2],$arg[3],$arg[4],$select,$arg[6]);
		if (!is_array($rec)||!sizeof($rec))return;
		// objet kob-eye
		$obj = genericClass::createInstance($module, $tc);
		if(! is_object($obj)) return "getData: request error : $arg[0]";
		//recherche du label
		$So = $obj->SearchOrder();
		$Key=false;
		if (sizeof($So)){
			if(is_array($So))
				foreach ($So as $S) {
					if ($S["searchOrder"]==1 && $S["Titre"]!="Url") $Key = $S["Titre"];
				}
		}
		//count children
		if (isset($arg[7])&&!empty($arg[7])){
			$list = explode(",",$arg[7]);
			$q = $module."/".$tc;
			$n = sizeof($rec);
			for($i = 0; $i < $n; $i++) {
				$r = &$rec[$i];
				$temp = $q."/".$r["Id"];
				$childCount = 0;
				foreach ($list as $l){
					$childCount+=$this->getCountRows($temp."/".$l);
				}
				$r["childCount"] = $childCount;
				$r["query"] = $arg[0];
				$r['label'] = $r[$Key];
			}
		}
		//traitement systeme
		if ($_SYS_){
			$n = sizeof($rec);
			$U = Sys::$User;
			$G= Array();
			for ($s=0;$s<sizeof($U->Groups);$s++) $G[] = $U->Groups[$s]->Id;
			for($i = 0; $i < $n; $i++) {
				$r = &$rec[$i];
				if(isset($r['uid'])) {
					//recupération des champs systèmes
					$r["sys_uid"] = $r["uid"];
					$r["sys_gid"] = $r["gid"];
					$r["sys_ur"] = ($r["umod"]>=2)?true:false;
					$r["sys_uw"] = ($r["umod"]>=4)?true:false;
					$r["sys_gr"] = ($r["gmod"]>=2)?true:false;
					$r["sys_gw"] = ($r["gmod"]>=4)?true:false;
					$r["sys_or"] = ($r["omod"]>=2)?true:false;
					$r["sys_ow"] = ($r["omod"]>=4)?true:false;
					//test propriétaires
					$write =false;
					$read =false;
					if ($U->Admin)$write=true;
					elseif ($r["sys_uid"]==$U->Id&&$r["umod"]>=4) $write =true;
					elseif (in_array($r["gid"],$G)&&$r["gmod"]>=4) $write =true;
					elseif ($r["omod"]>=4)$write=true;
					$r["write"] = $write;
					//nettoyage
					unset($r["uid"]);
					unset($r["gid"]);
					unset($r["umod"]);
					unset($r["gmod"]);
					unset($r["omod"]);
				}
			}
		}
		return $rec;
	}
	
	private function cleanData($rec) {
		$n = sizeof($rec);
		for($i = 0; $i < $n; $i++) {
			$r = &$rec[$i];
			$r["module"] = $r['Module'];
			$r["objectclass"] = $r['ObjectType'];
			unset($r['ObjectType']);
			unset($r['note']);
			unset($r['QueryType']);
			unset($r['Query']);
			unset($r['Module']);
			unset($r['Historique']);
		}
		return $rec;	
	}

	/**
	 * get data from a kob-eye node
	 * @param $arg[0] Query base
	 * @param $arg[1] Offset
	 * @param $arg[2] Limit
	 * @param $arg[3] OrderField
	 * @param $arg[4] Order
	 * @param $arg[5] Selection
	 * @param $arg[6] GroupBy
	 * @param $arg[7] Children list to query
	 *
	 */
	function getNode($args) {
		Connection::CloseSession();
		$i = Info::getInfos($args[0]);
		$tc = $i["ChildType"];
		$module = $i["Module"];
		$out = Array();
		foreach ($args as $arg){
			$rec = $this->getRawData($arg);
			$rec = $this->cleanData($rec);
			if (sizeof($rec))
				$out = array_merge($out, $rec);
		}
		//json creation
		$data = json_encode($out);
		$count = sizeof($out);
		$json = "{\"node\":{
\"identifier\":\"Id\",
\"label\":\"label\",
\"count\":\"$arg[2]\",
\"numRows\":\"$count\",
\"query\":\"$arg[0]\",
\"typeChild\":\"$tc\",
\"filter\":\"\",
\"user\":\"".Sys::$User->Id."\",
\"module\":\"$module\",
\"objectclass\":\"$tc\",
\"items\":$data
}}";
		return $json;
	}
	/**
	 * getMenu
	 * recupère la liste des menus de l'utilisateur
	 */
	function getMenu() {
		Connection::CloseSession();
		$json = '{"menus":';
		$json.= json_encode(Sys::$User->Menus);
		$json.='
		}';
		return $json;
	}

	/**
	 * getUser
	 * recupère l'utilisateur
	 */
	function getUser() {
		$usr = array('Id'=>Sys::$User->Id,'Nom'=>Sys::$User->Nom,'Prenom'=>Sys::$User->Prenom,
		'Developper'=>Sys::$User->Developper,'Privilege'=>Sys::$User->Privilege,'Public'=>Sys::$User->Public);
		Connection::CloseSession();
		$json = '{"user":';
		$json.= json_encode($usr);
		$json.='
		}';
		return $json;
	}


	
	/*
	 * get information on a kob-eye object
	 */
	function getInfo() {
		Connection::CloseSession();
		$json = '{"modules":[';
		$f0 = false;
		foreach (Sys::$Modules as $M){
			if ($f0)$json.=',';else $f0=true;
			$json.='{"nom":"'.$M->Nom.'","objectclass":[';
			$f1 = false;
			foreach ($M->Db->ObjectClass as $O){
				if ($f1)$json.=',';else $f1=true;
				$json.='{"nom":"'.$O->titre.'","children":[';
				$f2 = false;
				$C = $O->getChild();
				foreach ($C as $Ch){
					$Co = $Ch->getChildObjectClass();
					if ($f2)$json.=',';else $f2=true;
					$json.='{"nom":"'.$Co->titre.'"}';
				}
				$json.='],"parents":[';
				$f2 = false;
				$P = $O->getParent();
				foreach ($P as $Pa){
					$Po = $Pa->getParentObjectClass();
					if ($f2)$json.=',';else $f2=true;
					$json.='{"nom":"'.$Po->titre.'"}';
				}
				$json.=']';
				if($O->Dico) $json .= ',"dico":1';
				$json .= '}';
			}
			$json.=']}';
		}
		$json.=']
		}';
		return $json;
	}


	//-----------------------------------------
	// TRUC POURRIS
	//-----------------------------------------

	function AddObj($tempO,$Module,$parent=0,$ocParent="",$Niv=0){
		$Pref="";
		for ($i=0;$i<$Niv;$i++){
			$Pref .="------"; 
		}
		//Detection del objectclass
		if (is_array($tempO))foreach ($tempO as $k=>$t) {
			$temp = $t;
			$ObjClass = $k;
			if (is_array($temp))foreach ($temp as $C){
				//Recherche de l existance de la rubrique
				$Search = $Module."/".$ObjClass."/RmId=".$C["#"]["Id"][0]["#"];
	// 			$result = print_r($Search,true);
				$Tab = Sys::$Modules[$Module]->callData($Search,"",0,1);
	// 			$result = print_r($Tab,true);
				if (is_array($Tab)&&sizeof($Tab)>0){
					//On le modifie
					$result .= $Pref."ON MODIFIE ".$C["#"]["Id"][0]["#"]."\r\n";
					$Obj = genericClass::createInstance($Module,$Tab[0]);
				}else{
					//On le cree
					$result .= $Pref."ON CREE ".$C["#"]["Id"][0]["#"]."\r\n";
					$Obj = genericClass::createInstance($Module,$ObjClass);
					$Obj->Set("RmId",$C["#"]["Id"][0]["#"]);
					$Obj->resetParents($ocParent);
				}
				if ($parent!=0&&$ocParent!=""){
					$result .= $Pref."-------->DEFAULT PARENT $ocParent -> ".$parent."\r\n";
					//Recherche de l id local du parent
					$Search = $Module."/".$ocParent."/RmId=".$parent;
					$Tab2 = Sys::$Modules[$Module]->callData($Search,"",0,1);
					if (is_array($Tab2)&&sizeof($Tab2)>0){
						$Obj->AddParent($Module."/".$ocParent."/".$Tab2[0]["Id"]);
						$result.=$Pref."-----------> OK ".$Tab2[0]["Id"]."\r\n";
					}else{
						$result.=$Pref."----------->ERREUR PARENT ".$parent." NON TROUVE REQUETE ".$Search."\r\n";
					}
				}
				$Obj->Save();
				if (is_array($C["#"]))foreach ($C["#"] as $P=>$D) {
					if ($P=="Id"){
						$Obj->Set("IdRm",urldecode(($D[0]["#"])));
						$result .= $Pref."----->RMID $P -> ".$D[0]["#"]."\r\n";
					}elseif ($Obj->isProperty($P)) {
						$Pr = $Obj->getProperty($P);
						if ($Pr["Type"]=="file"){
							//Type fichier
							switch ($D[0]["@"]["format"]){
								case "base64":
									//Methode encod�e en base 64
									//Nom du fichier
									$NF = urldecode($D[0]["@"]["Nom"]);
									//Type mime
									$TF = urldecode($D[0]["@"]["Mime"]);
									//Decodage du fichier 
									$Data = base64_decode($D[0]["#"]);
									//Enregistrement et nommage
									$Fi = genericClass::createInstance("Explorateur","_Fichier");
									$Fi->Set("Nom",$NF);
									$Fi->Set("Contenu",$Data);
									$Fi->Set("Type",$TF);
									$Fi->Set("Url","/Home/".Sys::$User->Id."/Boutique/Produit");
									$Fi->Save();
									//Enregistrement dans l objet
									$Obj->Set($P,$Fi->Url);
								break;
							}
						}else{
							$Obj->Set($P,urldecode($D[0]["#"]));
						}	
						$result .= $Pref."----->PROP $P (".$Pr["type"].")-> ".$Obj->$P."\r\n";
					}elseif ($Obj->isChild($P)){
						//Enfants
						$result .= $Pref."----->CHILD $P -> ".$D[0]["#"]."\r\n";
						$z[$P] = $D;
						$result.=$this->AddObj($z,$Module,$C["#"]["Id"][0]["#"],$ObjClass,$Niv+1);
					}elseif ($Obj->isParent($P)&&$D[0]["@"]["link"]=="parent"){
						//Parents
						$result .= $Pref."----->PARENT $P -> ".$D[0]["#"]."\r\n";
						$Obj->resetParents($P);
						if (is_array($D))foreach ($D as $Pa){
							$result .= $Pref."-------->PARENT $P -> ".$Pa["#"]["Id"]["0"]["#"]."\r\n";
							//Recherche de l id local du parent
							$Search = $Module."/".$P."/RmId=".$Pa["#"]["Id"]["0"]["#"];
							$Tab2 = Sys::$Modules[$Module]->callData($Search,"",0,1);
							if (is_array($Tab2)&&sizeof($Tab2)>0){
								$Obj->AddParent($Module."/".$P."/".$Tab2[0]["Id"]);
								$result.=$Pref."-----------> OK ".$Tab2[0]["Id"]."\r\n";
							}else{
								$result.=$Pref."----------->ERREUR PARENT ".$Pa["#"]["Id"]["0"]["#"]." NON TROUVE\r\n";
							}
						}
					}else{
						$result .= $Pref."----->ERROR NON GERe |$P| ".$Obj->isProperty($P)." \r\n";
					}
				}
				if ($Obj->Verify()){
					$Obj->Save();
					$result.=$Pref." EDIT ID ".$Obj->Id;
				}else $result.=$Pref."ERREUR ".$Obj->Verify();
				$result.="\r\n";
			}else{
				$result=$Pref."ERREUR CE N EST PAS UN TABLEAU TEMP".$temp;
			}
		}else{
			$result=$Pref."ERREUR CE N EST PAS UN TABLEAU TEMPO  ".$temp0;
		}
		return $result;
	}

	function AddProdMethod($a,$Module){
		//On recoit un document xml avec la liste des elements a mettre a jour ou a creer
		$temp = $a[0];
		//On le transforme en tableau
		$x = new xml2array(utf8_encode($temp));
		$temp = $x->getResult();
		$temp = $temp["Xml"]["#"];
// 		$result =utf8_decode("TEST");
		Sys::$Modules[$Module]->loadSchema();
		//$result = print_r($temp,true);
		$result = $this->AddObj($temp,$Module);
		//Mise ajour de la base de donnee
		Sys::$Modules[$Module]->Db->Check();
		return $result;
	}

	//**************************************************************//
	//			METHODE NATOM				//
	//**************************************************************//
	//MAJCOMPTE
	//Mise a jour du compte pour le logiciel
	//$A est le NUMERO DE SERIE
	//$B est la version du logiciel
	function MajCompte($A,$B){
// 		$result = print_r($_SERVER,true);
		$GLOBALS["Systeme"]->Log->log("CONNEXION SOAP $A");
		//Recherche du client.
		if (!$A) return "ERREUR VEUILLEZ FOURNIR UN NUMERO DE SERIE VALIDE";
		Sys::$Modules["Boutique"]->loadSchema();
		$Search = "Boutique/Client/NatomSerie=".$A;
		$Tab = Sys::$Modules["Boutique"]->callData($Search,"",0,1);
		$GLOBALS["Systeme"]->Log->log("CONNEXION SERIAL OK $A");
// 		$result = print_r($Tab,true);
		if (is_array($Tab)){
			//Le numero de serie est valide , on met donc la version a jour
			$Obj = genericClass::createInstance("Boutique",$Tab[0]);
			$Obj->Set("Version",$B);
			$Obj->Save();
			// Constrcution du Xml avec les fichiers
			$ProdSearch = "Boutique/Client/".$Tab[0]["Id"]."/Produit";
			$Prod = Sys::$Modules["Boutique"]->callData($ProdSearch);
			$result="<xml>";
			if (is_array($Prod))foreach ($Prod as $P){
				if ($P["Type"]=="Pack"){
					//On recupere le nombre de fichier contenu dans le pack
					$PackSearch = "Boutique/Produit/".$P["Id"]."/Pack";
					$Pack = Sys::$Modules["Boutique"]->callData($PackSearch);
					//Nom du fichier
					$p = explode("/",$Pack[0]["FichierXmn"]);
// 					return print_r($P,true);
					$fn = $p[sizeof($p)-1];
					$n = preg_replace("#(\-[0-9]+)\.#",".",$fn);
					$n = preg_replace("#\_#","-",$n);
					//On calcule le nombre de fichier a recuperer
					$ProdsSearch = "Boutique/Pack/".$Pack[0]["Id"]."/Produit";
					$Prods = Sys::$Modules["Boutique"]->callData($ProdsSearch);
					$result.="
	<item id='".$P["Id"]."' type='2' crc=''>
	</item>";
				}else{
					//Calcul du checksum
					$file_string = file_get_contents($P["ImageCrypt"]);
					$crc = crc32($file_string);
					//Nom du fichier
					$p = explode("/",$P["ImageCrypt"]);
// 					return print_r($P,true);
					$fn = $p[sizeof($p)-1];
					$n = preg_replace("#(\-[0-9]+)\.#",".",$fn);
					$n = preg_replace("#\_#","-",$n);
					$result.="
	<item id='".$P["Id"]."' type='1' crc='".sprintf("%x",$crc)."'>
		<libelle>".$P["Libelle"]."</libelle>
		<file>".$n."</file>
		<path>".DEST_PATH_PLANCHE."</path>
	</item>";
					//Calcul du checksum
					$file_string = file_get_contents($P["ImageCryptLegende"]);
					$crc = crc32($file_string);
					//Nom du fichier
					$p = explode("/",$P["ImageCryptLegende"]);
					$fn = $p[sizeof($p)-1];
					$n = preg_replace("#(\-[0-9]+)\.#",".",$fn);
					$n = preg_replace("#\_#","-",$n);
					$result.="
	<item id='".$P["Id"]."-L' type='1' crc='".sprintf("%x",$crc)."'>
		<libelle>".$P["Libelle"]."</libelle>
		<file>".$n."</file>
		<path>".DEST_PATH_PLANCHE."</path>
	</item>";	
				}
			}
			//Insertion des fichiers de mise a jour
			$ProdSearch = "Boutique/Natom/Active=1";
			$Maj = Sys::$Modules["Boutique"]->callData($ProdSearch);
			if (is_array($Maj))foreach ($Maj as $M){
				//Calcul du checksum
				$file_string = file_get_contents($M["_Fichier"]);
				$crc = crc32($file_string);
				$result.="
		<item id='".$M["Id"]."-MAJ' type='".$M["Type"]."' crc='".sprintf("%x",$crc)."'>
			<libelle>".$M["Nom"]."</libelle>
			<file>".$M["NomFichier"]."</file>
			<path>".$M["Path"]."</path>
		</item>";
			}
			$result.="
</xml>";
		}else{
			$result="ERREUR AUCUN CLIENT CORRESPONDANT";
		}
		$GLOBALS["Systeme"]->Log->log("SOAP RETOUR ".$result);
		return $result;
	}

	//GETFILE
	//TELECHARGEMENT DES FICHERS
	//$Id est le numero du produit
	//$Serial est le NUMERO DE SERIE
	function GetFile($Id,$Serial){
		if (!$Serial) return "ERREUR VEUILLEZ FOURNIR UN NUMERO DE SERIE VALIDE";
// 		if ($Id=="Test") {
// 			$file_string = file_get_contents('Home/87/Natom.exe');
// 			return chunk_split (base64_encode($file_string));
// 		}
// 		if ($Id=="Francais.lnn") {
// 			$file_string = file_get_contents('Home/87/Français.lnn');
// 			return chunk_split (base64_encode($file_string));
// 		}
		$GLOBALS["Systeme"]->Log->log("SOAP GETFILE $Id $Serial ");
		Sys::$Modules["Boutique"]->loadSchema();
		$Search = "Boutique/Client/NatomSerie=".$Serial;
		$Tab = Sys::$Modules["Boutique"]->callData($Search,"",0,1);
// 		$result = print_r($Tab,true);
		if (is_array($Tab)){
			if (sizeof(explode("-L",$Id))>1){
				$Id = explode("-L",$Id);
				$Id = $Id[0];
				Sys::$Modules["Boutique"]->loadSchema();
				$Search = "Boutique/Produit/".$Id;
				$Tab = Sys::$Modules["Boutique"]->callData($Search,"",0,1);
				$file_string = file_get_contents($Tab[0]["ImageCryptLegende"]);
				return chunk_split (base64_encode($file_string));
			}elseif (sizeof(explode("-MAJ",$Id))>1){
				$Id = explode("-MAJ",$Id);
				$Id = $Id[0];
				Sys::$Modules["Boutique"]->loadSchema();
				$Search = "Boutique/Natom/".$Id;
				$Tab = Sys::$Modules["Boutique"]->callData($Search,"",0,1);
				$file_string = file_get_contents($Tab[0]["Fichier"]);
				return chunk_split (base64_encode($file_string));
			}elseif (sizeof(explode("-P",$Id))>1){
				$Id = explode("-P",$Id);
				$Id = $Id[0];
				Sys::$Modules["Boutique"]->loadSchema();
				$Search = "Boutique/Produit/".$Id."/Pack";
				$Tab = Sys::$Modules["Boutique"]->callData($Search,"",0,1);
				$file_string = file_get_contents($Tab[0]["FichierXmn"]);
				return chunk_split (base64_encode($file_string));
			}else{
				Sys::$Modules["Boutique"]->loadSchema();
				$Search = "Boutique/Produit/".$Id;
				$Tab = Sys::$Modules["Boutique"]->callData($Search,"",0,1);
				if ($Tab[0]["Type"]=="Pack"){
					$Search2 = "Boutique/Produit/".$Id."/Pack";
					$Tab2 = Sys::$Modules["Boutique"]->callData($Search2,"",0,1);
					$file_string = file_get_contents($Tab2[0]["FichierXmn"]);
				}else{
					$file_string = file_get_contents($Tab[0]["ImageCrypt"]);
				}
				return chunk_split (base64_encode($file_string));
			}
		}else{
			return "VEUILLEZ UN NUMERO DE SERIE VALIDE";
		}
	}
	//GETPACK
	//TELECHARGEMENT DES PACKS
	//$A est le NUMERO DE SERIE
	function GetPack($Id,$A){
// 		$result = print_r($_SERVER,true);
		//Recherche du client.
		if (!$A) return "ERREUR VEUILLEZ FOURNIR UN NUMERO DE SERIE VALIDE";
		Sys::$Modules["Boutique"]->loadSchema();
		$Search = "Boutique/Client/NatomSerie=".$A;
		$Tab = Sys::$Modules["Boutique"]->callData($Search,"",0,1);
// 		$result = print_r($Tab,true);
		if (is_array($Tab)){
			// Constrcution du Xml avec les fichiers
			$PackSearch = "Boutique/Produit/".$Id."/Pack";
			$Pack = Sys::$Modules["Boutique"]->callData($PackSearch);
			//Calcul du checksum
			$file_string = file_get_contents($Pack[0]["FichierXmn"]);
			$crc = crc32($file_string);
			$result="<xml>";
			$fn = explode("/",$Pack[0]["FichierXmn"]);
			$fn = $fn[sizeof($fn)-1];
			$result.="
	<item id='".$Id."-P' type='0' crc='".sprintf("%x",$crc)."'>
		<libelle>".$Pack[0]["Nom"]."</libelle>
		<file>".$fn."</file>
		<path></path>
	</item>";
			$ProdSearch = "Boutique/Pack/".$Pack[0]["Id"]."/Produit";
			$Prod = Sys::$Modules["Boutique"]->callData($ProdSearch);
			if (is_array($Prod))foreach ($Prod as $P){
				if ($P["ImageCrypt"]!=""){
				//Calcul du checksum
				$file_string = file_get_contents($P["ImageCrypt"]);
				$crc = crc32($file_string);
				//Nom du fichier
				$p = explode("/",$P["ImageCrypt"]);
				$fn = $p[sizeof($p)-1];
				$n = preg_replace("#(\-[0-9]+)\.#",".",$fn);
				$n = preg_replace("#\_#","-",$n);
				$result.="
	<item id='".$P["Id"]."' type='0' crc='".sprintf("%x",$crc)."'>
		<libelle>".$P["Libelle"]."</libelle>
		<file>".$n."</file>
		<path>".DEST_PATH_PLANCHE."</path>
	</item>";
				}
				if ($P["ImageCryptLegende"]!=""){
				//Calcul du checksum
				$file_string = file_get_contents($P["ImageCryptLegende"]);
				$crc = crc32($file_string);
				//Nom du fichier
				$p = explode("/",$P["ImageCryptLegende"]);
				$fn = $p[sizeof($p)-1];
				$n = preg_replace("#(\-[0-9]+)\.#",".",$fn);
				$n = preg_replace("#\_#","-",$n);
				$result.="
	<item id='".$P["Id"]."-L' type='0' crc='".sprintf("%x",$crc)."'>
		<libelle>".$P["Libelle"]."</libelle>
		<file>".$n."</file>
		<path>".DEST_PATH_PLANCHE."</path>
	</item>";	
				}
			}
			$result.="
</xml>";
		}else{
			$result="ERREUR AUCUN CLIENT CORRESPONDANT";
		}
		return $result;
	}
	function GetClient($Serial){
		$GLOBALS["Systeme"]->Log->log("SOAP GETCLIENT $Serial ");
		if (!$Serial) return "ERREUR VEUILLEZ FOURNIR UN NUMERO DE SERIE VALIDE";
		Sys::$Modules["Boutique"]->loadSchema();
		$Search = "Boutique/Client/NatomSerie=".$Serial;
		$Tab = Sys::$Modules["Boutique"]->callData($Search,"",0,1);
		$P = $Tab[0];
		if (is_array($Tab)){
			$Result="<xml>
	<nom>".$P["Nom"]."</nom>
	<prenom>".$P["Prenom"]."</prenom>
	<activite>".$P["Activite"]."</activite>
	<specialite>".$P["Specialite"]."</specialite>
	<adresse>".$P["Adresse"]."</adresse>
	<adresse2>".$P["Adresse2"]."</adresse2>
	<codepostal>".$P["CodPos"]."</codepostal>
	<ville>".$P["Ville"]."</ville>
	<pays>".$P["Pays"]."</pays>
	<mail>".$P["Mail"]."</mail>
	<telephone>".$P["Tel"]."</telephone>
</xml>";	
		}else{
			$Result="ERREUR AUCUN CLIENT CORRESPONDANT";
		}
		return $Result;
	}
}

?>
