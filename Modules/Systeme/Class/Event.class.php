<?php
class Event extends genericClass {

	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}
    /**
     * @param bool $z
     */
	function Save($z = false) {
	    if ($z){
	        parent::Save();
	        $this->cache_set($this->MicroTime,$this);
	        return;
        }
        $e = Sys::getOneData('Systeme','Event/EventType='.$this->EventType.'&EventModule='.$this->EventModule.'&EventObjectClass='.$this->EventObjectClass.'&EventId='.$this->EventId);
        if (!$e){
            $this->MicroTime = microtime(true);
            $this->cache_set($this->MicroTime,$this);
            parent::Save();
        }
        else{
            $this->cache_delete($e->MicroTime);
            $e->MicroTime = microtime(true);
            $e->Titre = $this->Titre;
            $e->Data = $this->Data;
            $e->EventType = $this->EventType;
            $e->EventModule = $this->EventModule;
            $e->EventObjectClass = $this->EventObjectClass;
            $e->EventId = $this->EventId;
            $e->UserId = $this->UserId;
            $e->Save(true);
        }
    }
    public static function __set_state($in) {
	    return $in;
        $e = genericClass::createInstance('Systeme',$in);
        return $e;
    }
    /**
     * opcache
     * use opcache cache
     */
    function cache_set($microtime, $val) {
        $time = explode('.',$microtime);
        $micro = $time[1];
        $time = $time[0];
        $val = var_export($val, true);
        if (!file_exists("Data/Events/$time"))
            mkdir("Data/Events/$time/",0777,true);
        $tmp = "Data/Events/$time/$micro.".uniqid('', true).".tmp";
        file_put_contents($tmp, '<?php $mt'.$time.$micro.' = ' . $val . ';', LOCK_EX);
        if (file_exists("Data/Events/$time/$micro"))
            unlink("Data/Events/$time/$micro");
        rename($tmp, "Data/Events/$time/$micro");
    }
    function cache_delete($microtime){
        $time = explode('.',$microtime);
        $micro = $time[1];
        $time = $time[0];
        if (file_exists("Data/Events/$time/$micro")){
            unlink("Data/Events/$time/$micro");
        }
    }
    function cache_get($microtime) {
        $now = time();
        $time = explode('.',$microtime);
        $micro = $time[1];
        $time = $time[0];
        $out = Array();
        //echo 'test '.$microtime." \r\n";
        for ($t=$time;$t<=$now;$t++){
            if (file_exists("Data/Events/$t")){
                //on liste les fichiers
                $files = scandir("Data/Events/$t");
                foreach ($files as $f){
                    if ($f=='.'||$f=='..')continue;
                    //echo '--> '.$t.' >= '.$time.' file '.$f.'('.intval($f).') > '.$micro."(".intval($micro).") \r\n";
                    if (intval($t)>intval($time) || ($time==$t && intval($f)>intval($micro))){
                        @include "Data/Events/$t/$f";
                        $var = 'mt'.$t.$f;
                        //echo '--> BINGO file '.$f." \r\n";
                        if (is_array(${$var})) {
                            //print_r(${$var});
                            array_push($out, ${$var});
                        }
                    }
                }
            }
        }
        return $out;
    }
    /**
     * @param $lastAlert
     * @param $time
     * @return array|null
     */
	
	function getAlerts($lastAlert, $time) {
		if($lastAlert) {
			$rec = Sys::$Modules['Systeme']->callData('Event/tmsCreate>'.$lastAlert.'&tmsCreate<='.$time, false, 0, 30, '', '', 'EventType,EventModule,EventObjectClass,EventId,tmsCreate,uid','EventType,EventModule,EventObjectClass,EventId');
			Sys::$Modules['Systeme']->Db->clearLiteCache();
//klog::l(">>>>>>>>>>>>>>>",$rec);
			if(is_array($rec) && count($rec)) {
				$alrt = array();
				foreach($rec as $rc) {
					$alrt[] = array('type'=>'Event', 'subtype'=>$rc['EventType'], 'module'=>$rc['EventModule'], 'objectClass'=>$rc['EventObjectClass'], 'Id'=>$rc['EventId'], 'time'=>$rc['tmsCreate'], 'uid'=>$rc['uid']);
				}
				return $alrt;
			}
		}
		return null;
	}
	
	function addEvent($title,$data,$event,$module,$object,$id,$uid=0) {
	    //vérrifie si un objet n'existe pas déjà
        $e = Sys::getOneData('Systeme','Event/EventType='.$event.'&EventModule='.$module.'&EventObjectClass='.$object.'&EventId='.$id);
        if (!$e)
		    $e = genericClass::createInstance('Systeme', 'Event');
		$e->Titre = $title.' BY ADDEVENT';
		$e->Data = $data;
		$e->EventType = $event;
		$e->EventModule = $module;
		$e->EventObjectClass = $object;
		$e->EventId = $id;
		$e->UserId = $uid;
		$e->Save(); 
	}


	function pollEvents($module,$object=null,$lastAlert=0,$interval = 1000,$maxDuration=15){
        Connection::CloseSession();
        if($lastAlert == '' || $lastAlert == null || $lastAlert == 'NULL'|| $lastAlert == "0")
            $lastAlert=time();
        if($interval == '' || $interval == null || $interval == 'NULL' || $interval == 0)
            $interval=1000;
        if($maxDuration == '' || $maxDuration == null || $maxDuration == 'NULL' || $interval == 0)
            $maxDuration =15;

        $GLOBALS['Systeme']->Db[0]->query("COMMIT");
        $GLOBALS['Systeme']->Db[0]->query("SET AUTOCOMMIT=1");
	    $i = 0;
	    $nbIt = ceil($maxDuration*1000 /$interval);
	    $delay = $interval*300;

        $query = 'Event/tmsCreate>'.$lastAlert.'&EventModule='.$module;
        if($object != '' && $object != null && $object != 'NULL')
            $query.='&EventObjectClass='.$object;

	    while($i<$nbIt){
            $rec = Sys::$Modules['Systeme']->callData($query, false, 0, 30);
            Sys::$Modules['Systeme']->Db->clearLiteCache();
            if(is_array($rec) && count($rec)) {
                return $rec;
            }
            $i++;
            usleep($delay);
	    }
    }

    /**
     * @param int $lastAlert
     * @param int $interval
     * @param int $maxDuration
     * @return array
     */
    function pollAll($lastAlert=0,$interval = 50,$maxDuration=15){
        Connection::CloseSession();
        if($lastAlert == '' || $lastAlert == null || $lastAlert == 'NULL')
            $lastAlert=microtime(true);
        if($interval == '' || $interval == null || $interval == 'NULL' || $interval == 0)
            $interval=1000;
        if($maxDuration == '' || $maxDuration == null || $maxDuration == 'NULL' || $interval == 0)
            $maxDuration =15;

        $GLOBALS['Systeme']->Db[0]->query("COMMIT");
        $GLOBALS['Systeme']->Db[0]->query("SET AUTOCOMMIT=1");

        $i = 0;
        $nbIt = ceil($maxDuration*1000 /$interval);
        $delay = $interval*1000;
        $queryEv = 'Event/MicroTime>'.$lastAlert;
        $queryAu = 'AlertUser::AlertUserList/tmsCreate>'.$lastAlert;

        $res=array('Ev' => Array(),'Au' => Array());
        $ret =false;

        //recherche des enregistrements push
        $push = Event::getRegisteredPush();

        while($i<$nbIt){
            //$recEv = Sys::$Modules['Systeme']->callData($queryEv, false, 0, 30,'Id','ASC');
            $recEv = $this->cache_get($lastAlert);
            $recAu = Sys::$Modules['Systeme']->callData($queryAu, false, 0, 30);
            Sys::$Modules['Systeme']->Db->clearLiteCache();
            if(is_array($recEv) && count($recEv)) {
                $res['Ev']=$recEv;
                $ret =true;
            }
            if(is_array($recAu) && count($recAu)) {
                $res['Au']=$recAu;
                $ret =true;
            }

            if($ret) {
                $out = array();
                foreach ($res['Ev'] as $k=>$ev){
                    $obj = unserialize($res['Ev'][$k]['Data']);
                    if($obj){
                        $o = $obj->getWebServiceData();
                        $res['Ev'][$k]['Data'] = json_encode($o);
                    }
                    $ev = $res['Ev'][$k];
                    //résolution du contexte
                    //on vérifie déjà que le push a été enregistré pour ce type de donnée
                    if (isset($push->{$ev['EventModule'].$ev['EventObjectClass']})) {
                        if ($ev['EventType'] == "Create") {
                            //dans le cas d'un ajout il faut fournir et multiplier les events
                            //afin de fournir un event par contexte de manière pertinente.
                            //ce qui permet d'eviter les chargement de liste
                            $contexts = $push->{$ev['EventModule'].$ev['EventObjectClass']};
                            foreach ($contexts as $name=>$context){
                                $query = explode('/',$context->query);
                                //si on est pas en page une on ne dispatch pas l'evenement
                                if ($context->offset>0) {
                                    continue;
                                }

                                //cas du filtre
                                if ($context->filters!='~'&&$context->filters!='~&'){
                                    if (!Event::filterCheck($context->filters,$o)){
                                        continue;
                                    }
                                }

                                //cas de query complexe
                                if (sizeof($query)>1){
                                    //récupération du nom de la clef
                                    $ps = $obj->getParentElements();
                                    $flag = false;
                                    foreach ($ps as $p){
                                        //Vérification du lien parent
                                        if ($p['objectName']==$query[0]&&$o->{$p['name']}==$query[1]) $flag=true;
                                    }
                                    if (!$flag) continue;
                                }

                                //on ajoute à la liste
                                $ev2 = unserialize(serialize($ev));
                                $ev2['Context'] = $name;
                                array_push($out,$ev2);
                            }
                        }else{
                            //TODO Gérer les déplacement et modifiacation de clefs.
                            //on publie les évenements
                            array_push($out,$ev);
                        }
                    }
                }
                $res['Ev'] = $out;
                if (sizeof($out))
                    return $res;
            }

            $i++;
            usleep($delay);
        }
    }

    /**
     *
     */
    public static function clearEvents () {
        $limit = time();
        //toutes les 60 secondes
        $now = $limit -= 60;
        $evs = Sys::getData('Systeme','Event/tmsEdit<='.$limit,0,10000);
        foreach ($evs as $ev){
            $ev->Delete();
        }
        for ($t = $limit-60;$t<$now;$t++){
            try{
                Utils::deleteDir('Data/Events/'.$t);
            }catch (Exception $e){

            }
        }
    }

    /**
     * @return object
     */
    public static function getRegisteredPush() {
        if (!file_exists("Data/Push/".Sys::$User->Id.".push")){
            //creation d'une nouvelle configuration push
            return (object) [
            ];
        }else{
            @include "Data/Push/".Sys::$User->Id.".push";
            $var = 'push'.Sys::$User->Id;
            return ${$var};
        }
    }

    /**
     * @param $module
     * @param $objectclass
     * @param $query
     * @param $filters
     * @param $offset
     * @param $limit
     * @param $context
     */
    public static function registerPush($module,$objectclass,$query,$filters,$offset,$limit,$context){
        $obj = self::getRegisteredPush();
        //ajout de la requete et des paramètres
        if (!isset($obj->{$module.$objectclass}))
            $obj->{$module.$objectclass} = array(
                $context => (object) [
                    'query' => $query,
                    'filters' => $filters,
                    'offset' => $offset,
                    'limit' => $limit
                ]
            );
        else $obj->{$module.$objectclass}[$context] = (object) [
                'query' => $query,
                'filters' => $filters,
                'offset' => $offset,
                'limit' => $limit
            ];
        //mis à jour du fichier
        $obj = var_export($obj, true);
        $obj = str_replace('stdClass::__set_state', '(object)', $obj);
        if (!file_exists("Data/Push"))
            mkdir("Data/Push",0777,true);
        $tmp = "Data/Push/".Sys::$User->Id.'.'.uniqid('', true).".tmp";
        file_put_contents($tmp, '<?php $push'.Sys::$User->Id.' = ' . $obj . ';', LOCK_EX);
        if (file_exists("Data/Push/".Sys::$User->Id.".push"))
            unlink("Data/Push/".Sys::$User->Id.".push");
        rename($tmp, "Data/Push/".Sys::$User->Id.".push");
    }

    /**
     * @param $filters
     * @param $data
     * Check if data is valid with filters
     */
    public static function filterCheck($filters,$data) {
        $filters=explode('&',$filters);
        foreach ($filters as $f){
            //cas supérieur
            if (preg_match('#^(.*)?>([^=]+)$#',$f,$fi)){
                if (floatval($data->{$fi[1]}) <= floatval($fi[2])){
                    return false;
                }
            }
            //cas supérieur ou égal
            if (preg_match('#^(.*)?>=(.+)$#',$f,$fi)){
                if (floatval($data->{$fi[1]}) < floatval($fi[2])){
                    echo floatval($data->{$fi[1]}).' < '.floatval($fi[2]);
                    print_r($fi);
                    return false;
                }
            }
            /*//cas inférieur
            if (preg_match('#^(.*)?<([^\=]+)$#',$f,$fi)){
                if (floatval($data->{$fi[1]}) >= floatval($fi[2])) return false;
            }
            //cas inférieur ou égal
            if (preg_match('#^(.*)?<=(.+)$#',$f,$fi)){
                if (floatval($data->{$fi[1]}) > floatval($fi[2])) return false;
            }
            //cas égalité
            if (preg_match('#^(.*)?[\=](.+)$#',$f,$fi)){
                if ($data->{$fi[1]} != $fi[2]) return false;
            }
            //cas flou
            if (preg_match('#~(.+)#',$f,$fi)){
                $flag=false;
                foreach ($data as $k=>$d) {
                    if (is_string($d) && strpos($d,$fi[1])){
                        $flag = true;
                    }
                }
                if (!$flag){
                    return false;
                }
            }*/
        }
        return true;
    }
}