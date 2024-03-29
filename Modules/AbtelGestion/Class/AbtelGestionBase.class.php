<?php

class AbtelGestionBase extends genericClass {
	protected $entity = 'entites';
	public $props = array();
	protected $parents = array();
    protected $con_handle = null;
    protected $api_token = null;
    protected $identifier = 'Id';
	const PARCURL = 'https://parcapi.abtel.fr';
	const APIKEY = 'b06b9cfc31-5c5855df69dad';
	const APIUSER = 'api_gestion';
    const APIPASS = '21wyisey';


    /**
     * @param $prop
     * @param $newValue
     * @return bool
     */
    public function Set($prop, $newValue){
        if(isset($this->{$prop}) || $prop == 'Interface') {
            parent::Set($prop,$newValue);
        } else {
            if(strpos($prop,'__z__')){
                $prop = substr($prop,5);
            }


            $fields = $this->getQueryFields(!$this->getOrigin());

            if(array_key_exists($prop,$fields) ) {
                $index = $prop;
            } else {
                $index = array_search($prop,$fields);
            }
            if($index){
                $this->props[$index] = $newValue;
            }
        }
        return true;
    }

    /**
     * @param $prop
     * @return bool|mixed
     */
    public function Get($prop, $Nom = false){
        if(isset($this->{$prop}) || $prop == 'Interface') {
            return parent::Get($prop);
        } else {
            $fields = $this->getQueryFields(!$this->getOrigin());
            if(array_key_exists($prop,$fields) ) {
                return $this->props[$prop];
            } else {
                $key = array_search($prop,$fields);
                if( $key !== false ){
                    return $this->props[$key];
                }
            }
        }

        return false;
    }


    /**
     * @return bool|int
     */
    public function Verify(){
        return true;
    }

    public function addParent($par='',$null='') {
        $this->parents[] = $par;

        return true;
    }

    /**
     * @return bool
     */
    public function makeRequest(){
        $query = $GLOBALS['Systeme']->getRegVars('Query');
        $inf = $this->reworkQuery($query,1);
        if($this->getOrigin()){
            //Gestion donc on appelle l'api du parc
            $url = self::PARCURL.'/'.$inf['Route'];
            $params = array();
            if(!empty($inf['Where'])){
                foreach($inf['Where'] as $w){
                    $params[$w[0]] = $w[1];
                }
            }
            if(count($this->parents)){
                foreach($this->parents as $p){
                    if(is_object($p)) {
                        if(isset($this->props[$p->ObjectType])){
                            $this->props[$p->ObjectType][] =  $p->Id;
                        } else {
                            $this->props[$p->ObjectType] =  array($p->Id);
                        }
                    } else{
                        $i = Info::getInfos($p);
                        if(isset($this->props[$i->ObjectType])){
                            $this->props[$i->ObjectType][] =  $i->LastId;
                        } else {
                            $this->props[$i->ObjectType] =  array($i->LastId);
                        }
                    }
                }
            }

            $params['data'] = $this->props;
            curl_setopt($this->con_handle,CURLOPT_URL,$url);
            curl_setopt($this->con_handle, CURLOPT_CUSTOMREQUEST, $_SERVER['REQUEST_METHOD']);
            $data =json_encode(array('API_KEY'=>self::APIKEY,'AUTH_TOKEN'=>$this->api_token,"params"=>$params));
            curl_setopt($this->con_handle, CURLOPT_POSTFIELDS, $data);
            curl_setopt($this->con_handle, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data))
            );

            $ret = curl_exec($this->con_handle);
            if(!$ret)  $ret = curl_exec($this->con_handle); //en cas de timeout
            //var_dump($ret);
            $ret = json_decode($ret,true);
            if(!$ret['success']) {
                $ret["url"] = $url;
                $ret["data"] = $params;
                $fields = $this->getQueryFields($this->getOrigin());
                if (!empty($ret['error_description'])) {
                    foreach ($ret['error_description'] as &$d) {
                        if (!empty($d['Prop'])) {
                            if (array_key_exists($d['Prop'], $fields)) {
                                continue;
                            } else {
                                $key = array_search($d['Prop'], $fields);
                                if ($key !== false) {
                                    $d['Prop'] = $key;
                                } else {
                                    $d['Prop'] = ' *** ' . $d['Prop'] . '( Propriété uniquement pour le parc ) ***';
                                }
                            }
                        }
                    }
                }
                die (json_encode($ret));
            }

            //$res = $ret['pagination']['total'];
        } else{
            //Parc donc on requete la base de la gestion
            $req = $this->buildRequest($inf);
            //file_put_contents('/tmp/testsql',$req.PHP_EOL,8);
            $q = $this->con_handle->query($req);

            if(count($this->parents)){
                foreach($this->parents as $p){
                    if(is_object($p)) {
                        $this->sqlAddParent($p->ObjectType,$p->Id);
                    } else{
                        $i = Info::getInfos($p);
                        $this->sqlAddParent($i->ObjectType,$i->LastId);
                    }
                }
            }

        }

        return true;
    }

    protected function sqlAddParent($type ,$id){
        return true;
    }


    /**
     * @return bool
     */
    public function Save(){
        if(!$this->getOrigin()){
            $this->props['ModifTms'] = date('YmdHis00',time() - 300);
        }

        return $this->makeRequest();
    }

    /**
     * @return bool
     */
    public function Delete(){
        return $this->makeRequest();
    }


    /**
     * @param $At
     * @param string $v
     * @param bool $flat
     * @param string $L
     * @return array|bool
     */
    public function getElementsByAttribute($At, $v='', $flat=false, $L=''){
        $entity = Sys::getOneData('AbtelGestion',"Entite/Nom=".$this->entity);
        if(!is_object($entity)) return false;
        $fields = $entity->getChildren('Champ');

        $elements = array();
        foreach($fields as $field){
            $elements[] = array("name"=>$field->Nom);
            if($field->NomDistant && $field->NomDistant != '')
                $elements[] = array("name"=>$field->NomDistant);

        }
        return $elements;
    }


    /**
     * @param $module
     * @param $query
     * @return int|mixed|null
     */
    public function getDbCount($module, $query){
	    if($module != 'AbtelGestion')
	        return parent::getDbCount($module,$query);

	    $inf = $this->reworkQuery($module.'/'.$query);
	    $res = null;

	    if($this->getOrigin()){
	        //Gestion donc on appelle l'api du parc
            $url = self::PARCURL.'/'.$inf['Route'];
            $params = array();
            if(!empty($inf['Where'])){
                foreach($inf['Where'] as $w){
                    $params[$w[0]] = $w[1];
                }
            }
            curl_setopt($this->con_handle,CURLOPT_URL,$url);
            curl_setopt($this->con_handle, CURLOPT_CUSTOMREQUEST, "GET");
            $data =json_encode(array('API_KEY'=>self::APIKEY,'AUTH_TOKEN'=>$this->api_token,"params"=>$params));
            curl_setopt($this->con_handle, CURLOPT_POSTFIELDS, $data);
            curl_setopt($this->con_handle, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data))
            );

            $ret = json_decode(curl_exec($this->con_handle),true); //en cas de timeout
            if(!$ret)  $ret = json_decode(curl_exec($this->con_handle),true);
            $res = $ret['pagination']['total'];
        } else{
	        //Parc donc on requete la base de la gestion
            $where ='1';
            if(!empty($inf['Where'])){
                foreach($inf['Where'] as $w){
                    $where .= ' AND `'.$w[0].'` = \''.$w[1].'\'';
                }
            }

            $req = "SELECT COUNT(*) FROM ".$inf['Table']." WHERE ".$where.";";
            $q = $this->con_handle->query($req);
            $res = $q->fetchColumn();

        }
	    return $res;
    }

    /**
     * @param $module
     * @param $query
     * @param string $offset
     * @param string $limit
     * @param string $orderType
     * @param string $orderVar
     * @param string $select
     * @param string $groupBy
     * @param bool $noRights
     * @return array|mixed|null
     */
    public function getDbData($module, $query, $offset="", $limit="", $orderType="", $orderVar="", $select="", $groupBy="", $noRights = false){
        if($module != 'AbtelGestion')
            return parent::getDbData($module,$query, $offset, $limit, $orderType, $orderVar, $select, $groupBy, $noRights);

        $inf = $this->reworkQuery($module.'/'.$query);
        $res = null;

        if($this->getOrigin()){
            //Gestion donc on appelle l'api du parc
            $url = self::PARCURL.'/'.$inf['Route'];
            $params = array();
            if(!empty($inf['Where'])){
                foreach($inf['Where'] as $w){
                    $params[$w[0]] = $w[1];
                }
            }

            if(!empty($offset))$params['offset'] = $offset;
            if(!empty($limit))$params['limit'] = $limit;
            if(!empty($orderType))$params['order'] = $orderType;
            if(!empty($orderVar))$params['orderBy'] = $orderVar;

            curl_setopt($this->con_handle,CURLOPT_URL,$url);
            curl_setopt($this->con_handle, CURLOPT_CUSTOMREQUEST, "GET");
            $data =json_encode(array('API_KEY'=>self::APIKEY,'AUTH_TOKEN'=>$this->api_token,"params"=>$params));
            curl_setopt($this->con_handle, CURLOPT_POSTFIELDS, $data);
            curl_setopt($this->con_handle, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data))
            );

            $ret = json_decode(curl_exec($this->con_handle),true);
            if(!$ret)  $ret = json_decode(curl_exec($this->con_handle),true); // en cas de timeout
            $res = $ret['data'];

            if($_SERVER['REQUEST_METHOD'] == 'GET') {
                $fields = $this->getQueryFields(0);
                foreach ($res as $k => $r) {
                    $t = array();
                    array_walk($r, function ($value, $key) use ($fields, &$t) {
                        $newkey = array_key_exists($key, $fields) ? $fields[$key] : false;
                        if ($newkey) {
                            $t[$newkey] = $value;
                        }


                    });
                    $res[$k] = $t;
                }
            }
        } else{
            //Parc donc on requete la base de la gestion
            $fields = $this->getQueryFields(0);
            $fields = implode(', ',$fields);

            //Parc donc on requete la base de la gestion
            $where ='1';
            if(!empty($inf['Where'])){
                foreach($inf['Where'] as $w){
                    $where .= ' AND `'.$w[0].'` = \''.$w[1].'\'';
                }
            }



            $req = "SELECT ".$fields." FROM ".$inf['Table']." WHERE ".$where.(!empty($orderVar)?' ORDER BY '.$orderVar:'').(!empty($orderType)?' '.$orderType:'')." LIMIT ".($limit?$limit:"15")." OFFSET ".($offset?$offset:"0").";";
            $q = $this->con_handle->query($req);
            $res = $q->fetchAll(PDO::FETCH_ASSOC);

            if($_SERVER['REQUEST_METHOD'] == 'GET') {
                $fields = $this->getQueryFields(1);
                foreach ($res as $k => $r) {
                    $t = array();
                    array_walk($r, function ($value, $key) use ($fields, &$t) {
                        $newkey = array_key_exists($key, $fields) ? $fields[$key] : false;
                        if ($newkey) {
                            $t[$newkey] = $value;
                        }


                    });
                    $res[$k] = $t;
                }
            }
        }

        foreach ($res as $k=>$ps) {
            $o = genericClass::createInstance($inf['M'], $inf['O']);
            foreach ($ps as $prop => $val) {
                $o->{$prop} = $val;
            }
            $res[$k]=$o;
        }

        return $res;
    }

    /**
     * @param $module
     * @param $query
     * @param string $offset
     * @param string $limit
     * @param string $orderType
     * @param string $orderVar
     * @param string $select
     * @param string $groupBy
     * @param bool $noRights
     * @return genericClass|Object
     */
    public function getOneDbData($module, $query, $offset="", $limit="", $orderType="", $orderVar="", $select="", $groupBy="", $noRights = false){
        if($module != 'AbtelGestion')
            return parent::getOneDbData($module,$query, $offset, $limit, $orderType, $orderVar, $select, $groupBy, $noRights);

        $inf = $this->reworkQuery($module.'/'.$query);
        $res = null;
        if($this->getOrigin()){
            //Gestion donc on appelle l'api du parc
            $url = self::PARCURL.'/'.$inf['Route'];
            $params = array();
            if(!empty($inf['Where'])){
                foreach($inf['Where'] as $w){
                    $params[$w[0]] = $w[1];
                }
            }
            $params['offset'] = 0;
            $params['limit'] = 1;
            if(!empty($orderType))$params['order'] = $orderType;
            if(!empty($orderVar))$params['orderBy'] = $orderVar;

            curl_setopt($this->con_handle,CURLOPT_URL,$url);
            curl_setopt($this->con_handle, CURLOPT_CUSTOMREQUEST, "GET");
            $data =json_encode(array('API_KEY'=>self::APIKEY,'AUTH_TOKEN'=>$this->api_token,"params"=>$params));
            curl_setopt($this->con_handle, CURLOPT_POSTFIELDS, $data);
            curl_setopt($this->con_handle, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data))
            );

            $ret = json_decode(curl_exec($this->con_handle),true);
            if(!$ret)  $ret = json_decode(curl_exec($this->con_handle),true); //en cas de timeout
            $res = $ret['data'];
            $ret = null;

            if($_SERVER['REQUEST_METHOD'] == 'GET') {
                $fields = $this->getQueryFields(0);
                foreach ($res as $k => $r) {
                    $t = array();
                    array_walk($r, function ($value, $key) use ($fields, &$t) {
                        $newkey = array_key_exists($key, $fields) ? $fields[$key] : false;
                        if ($newkey) {
                            $t[$newkey] = $value;
                        }


                    });
                    $res[$k] = $t;
                }
            }
        } else{
            //Parc donc on requete la base de la gestion
            $fields = $this->getQueryFields(0);
            $fields = implode(', ',$fields);

            //Parc donc on requete la base de la gestion
            $where ='1';
            if(!empty($inf['Where'])){
                foreach($inf['Where'] as $w){
                    $where .= ' AND `'.$w[0].'` = \''.$w[1].'\'';
                }
            }

            $req = "SELECT ".$fields." FROM ".$inf['Table']." WHERE ".$where.(!empty($orderVar)?' ORDER BY '.$orderVar:'').(!empty($orderType)?' '.$orderType:'')." LIMIT 1 OFFSET 0;";
            $q = $this->con_handle->query($req);
            $res = $q->fetchAll(PDO::FETCH_ASSOC);



            if($_SERVER['REQUEST_METHOD'] == 'GET') {
                $fields = $this->getQueryFields(1);
                foreach ($res as $k => $r) {
                    $t = array();
                    array_walk($r, function ($value, $key) use ($fields, &$t) {
                        $newkey = array_key_exists($key, $fields) ? $fields[$key] : false;
                        if ($newkey) {
                            $t[$newkey] = $value;
                        }


                    });
                    $res[$k] = $t;
                }
            }


        }

        $o = genericClass::createInstance($inf['M'],$inf['O']);
        foreach($res[0] as $prop=>$val){
            $o->{$prop} = $val;
        }

        return $o;
    }

    /*
     * On recupère l'origine de la demande en fonction de l'ip qui appelle (Pas forcement le plus propre) et on initie la connection si besoin (curl/pdo)
     *
     */
    public function getOrigin(){
        $user = Sys::$User;

        if($user->Id == 7){
        //if($user->Id != 7){
            //Ouverture connection pdo si besoin
            if(empty($this->con_handle)){
                $this->con_handle = new PDO('mysql:host=127.0.0.1;dbname=gestion', 'root', '', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
                $this->con_handle->query("SET AUTOCOMMIT=1");
                $this->con_handle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            return 0; //Parc
        } else {
            //Ouverture connection curl si besoin
            if(empty($this->con_handle)){
                $this->con_handle = curl_init(self::PARCURL);
                curl_setopt($this->con_handle, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($this->con_handle, CURLOPT_CUSTOMREQUEST, "POST");
                $data =json_encode(array('API_KEY'=>self::APIKEY,'login'=>self::APIUSER,'pass'=>self::APIPASS));
                curl_setopt($this->con_handle, CURLOPT_POSTFIELDS, $data);
                curl_setopt($this->con_handle, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($data))
                );
                curl_setopt($this->con_handle, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($this->con_handle, CURLOPT_SSL_VERIFYPEER, 0);
                $ret = json_decode(curl_exec($this->con_handle),true);
                if(!$ret)  $ret = json_decode(curl_exec($this->con_handle),true); // Retry en cas de timeout
                $this->api_token = $ret['auth_token'];
                $ret = null;
            }
            return 1; //Gestion
        }
    }

    /*
     * On recupère les champs utiles à la requète en focntion de l'origine de la demande
     *
     */
    public function getQueryFields($gestion, $entityType = null){
        $entityType = $entityType ? $entityType : $this->entity;

        $entity = Sys::getOneData('AbtelGestion',"Entite/Nom=".$entityType);
        $fields = $entity->getChildren('Champ');

        $res = array();
        foreach($fields as $f){
            if(!empty($f->NomDistant))   $res[$f->Nom] = $f->NomDistant;
        }


	    if(!$gestion) $res =  array_flip($res);



	    return array_unique($res);
    }


    /**
     * On traite la requete pour pouvoir générer correctement la query sql ou l'appel rest selon l'origine
     * @param $query
     * @return array|bool
     */
    public function reworkQuery($query,$getId=false){
        $split= explode('/',$query);
        if(sizeof($split) == 3){
            $split[2] = Utils::KEAddSlashes(rawurldecode($split[2]));
            $query = implode('/',$split);
        }
        $query = rawurldecode($query);

        $info = Info::getInfos($query);

        if($info['TypeSearch'] == 'Direct' && $getId && $this->getOrigin()){
            $qs = explode('/',$query);
            $fields = $this->getQueryFields($this->getOrigin());
            $f = $fields[$this->identifier];
            $qs[sizeof($qs)-1] = $this->props[$f];
            $query = implode('/',$qs);
            $info = Info::getInfos($query);
        }

        $where = array();
        if(!empty($info['LastId'])) {
            $entity = Sys::getOneData('AbtelGestion', "Entite/Nom=" . $this->entity);
            if (!is_object($entity)) return false;


            if (strpos($info['LastId'],'&') || strpos($info['LastId'],'=')){
                $whereBase = explode('&', $info['LastId']);

                foreach ($whereBase as $w) {
                    if (!empty($w)) $where[] = $w;
                }

            } else{
                    $where[] = $this->identifier. '=' . $info['LastId'];
            }

            if($this->getOrigin()){
                //Gestion donc on appelle l'api du parc
                array_walk($where,function(&$a) use ($entity){
                    $a =  explode('=',$a);
                    if($a[0] == 'Numero') return;

                    $field = $entity->getOneChild('Champ/Nom='.$a[0]);
                    $a[0] = $field->NomDistant;
                });
            }else{
                //Parc donc on requete la base de la gestion
                array_walk($where,function(&$a) use ($entity){
                    $a =  explode('=',$a);
                    if($a[0] == 'NumeroTicket' || $a[0] == $this->identifier) return;

                    $field = $entity->getOneChild('Champ/NomDistant='.$a[0]);
                    $a[0] = $field->Nom;
                });
            }
        }



        switch( $info['ObjectType'] ){
            case 'Tache' :
                if($this->getOrigin()){
                    //Gestion donc on appelle l'api du parc
                    $id='';
                    if ($info['TypeSearch'] == 'Direct' && $getId) {
                        $q = explode('/',$query,2);
                        $obj = $this->getOneDbData($q[0],$q[1]);
                        if($obj) {
                            $id = '/'.$obj->Id;
                        }
                    }

                    return array(
                        'M'=>'AbtelGestion',
                        'O'=>'Tache',
                        'Module' => 'Parc',
                        'ObjectType' => 'Ticket',
                        'Route' => 'gestion/ticket'.$id,
                        'Where' => $where
                    );
                } else{
                    //Parc donc on requete la base de la gestion
                    return  array(
                        'M'=>'AbtelGestion',
                        'O'=>'Tache',
                        'Table' => 'taches',
                        'Where' => $where
                    );
                }
                break;
            case 'Action':
                if($this->getOrigin()){
                    //Gestion donc on appelle l'api du parc
                    $id='';
                    if ($info['TypeSearch'] == 'Direct' && $getId) {
                        $q = explode('/',$query,2);
                        $obj = $this->getOneDbData($q[0],$q[1]);
                        if($obj) {
                            $id = '/'.$obj->Id;
                        }
                    }

                    return array(
                        'M'=>'AbtelGestion',
                        'O'=>'Action',
                        'Module' => 'Parc',
                        'ObjectType' => 'Action',
                        'Route' => 'gestion/action'.$id,
                        'Where' => $where
                    );
                } else{
                    //Parc donc on requete la base de la gestion
                    return  array(
                        'M'=>'AbtelGestion',
                        'O'=>'Action',
                        'Table' => 'actions',
                        'Where' => $where
                    );
                }
                break;
            case 'Entite':
                if($this->getOrigin()){
                    //Gestion donc on appelle l'api du parc
                    $id='';
                    if ($info['TypeSearch'] == 'Direct' && $getId) {
                        $q = explode('/',$query,2);
                        $obj = $this->getOneDbData($q[0],$q[1]);
                        if($obj) {
                            $id = '/'.$obj->Id;
                        }
                    }

                    return array(
                        'M'=>'AbtelGestion',
                        'O'=>'Entite',
                        'Module' => 'Abtel',
                        'ObjectType' => 'Entite',
                        'Route' => 'gestion/entite'.$id,
                        'Where' => $where
                    );
                } else{
                    //Parc donc on requete la base de la gestion
                    return  array(
                        'M'=>'AbtelGestion',
                        'O'=>'Entite',
                        'Table' => 'entites',
                        'Where' => $where
                    );
                }
                break;
            case 'Client':
                if($this->getOrigin()){
                    //Gestion donc on appelle l'api du parc
                    $id='';
                    if (($info['TypeSearch'] == 'Direct' || $info['TypeSearch'] == 'Multi') && $getId ) {
                        $q = explode('/',$query,2);
                        if($q[1] == 'Client/0') $q[1] = 'Client/ABT_0'; //Gestion du cas special codegestion = 0
                        $obj = $this->getOneDbData($q[0],$q[1]);
                        if($obj) {
                            $id = '/'.$obj->Id;
                        }
                    }

                    return array(
                        'M'=>'AbtelGestion',
                        'O'=>'Client',
                        'Module' => 'Parc',
                        'ObjectType' => 'Client',
                        'Route' => 'gestion/client'.$id,
                        'Where' => $where
                    );
                } else{
                    //Parc donc on requete la base de la gestion
                    return  array(
                        'M'=>'AbtelGestion',
                        'O'=>'Client',
                        'Table' => 'tiers',
                        'Where' => $where
                    );
                }
                break;
            case 'Contrat':
                if($this->getOrigin()){
                    //Gestion donc on appelle l'api du parc
                    $id='';
                    /*if ($info['TypeSearch'] == 'Direct') {
                        $obj = $this->getOneDbData('Parc', 'Ticket/NumeroTicket='.$info['LastId']);
                        if($obj) $id = '/'.$obj->Id;
                    }*/

                    return array(
                        'M'=>'AbtelGestion',
                        'O'=>'Contrat',
                        'Module' => 'Abtel',
                        'ObjectType' => 'Contrat',
                        'Route' => 'gestion/contrar'.$id,
                        'Where' => $where
                    );
                } else{
                    //Parc donc on requete la base de la gestion
                    return  array(
                        'M'=>'AbtelGestion',
                        'O'=>'Contrat',
                        'Table' => 'contrats',
                        'Where' => $where
                    );
                }
                break;
//            case 'LigneContrat':
//                if($this->getOrigin()){
//                    //Gestion donc on appelle l'api du parc
//                    $id='';
//                    /*if ($info['TypeSearch'] == 'Direct') {
//                        $obj = $this->getOneDbData('Parc', 'Ticket/NumeroTicket='.$info['LastId']);
//                        if($obj) $id = '/'.$obj->Id;
//                    }*/
//
//                    return array(
//                        'M'=>'AbtelGestion',
//                        'O'=>'Entite',
//                        'Module' => 'Abtel',
//                        'ObjectType' => 'Entite',
//                        'Route' => 'gestion/entite'.$id,
//                        'Where' => $where
//                    );
//                } else{
//                    //Parc donc on requete la base de la gestion
//                    return  array(
//                        'M'=>'AbtelGestion',
//                        'O'=>'Entite',
//                        'Table' => 'entites',
//                        'Where' => $where
//                    );
//                }
//                break;
        }

        return true;
    }



    /**
     * On construit la requete pour la base gestion
     * @param $infos
     * @return string
     */
    private function buildRequest($infos){
        $req = '';
        $cols = array_keys($this->props);
        array_walk($cols,function(&$a){
            $a = '`'.$a.'`';
        });
        $vals = array_values($this->props);
        array_walk($vals,function(&$a){
            if(is_string($a))
                $a = $this->con_handle->quote($a);
            if(is_null($a))
                $a = 'NULL';
            if($a === false)
                $a = 0;
            if($a === true)
                $a = 1;
        });

        $where ='1';
        if(!empty($infos['Where'])){
            foreach($infos['Where'] as $w){
                $where .= ' AND `'.$w[0].'` = \''.$w[1].'\'';
            }
        }


        switch($_SERVER['REQUEST_METHOD']){
            case 'POST':
                $req = 'INSERT INTO '.$infos['Table'].' ( '.implode(',',$cols).' ) VALUES ( '.implode(',',$vals).' ); ';
                break;
            case 'PATCH':
                if($where === '1') return false; // secu pour eviter de modifier la table
                $couples = array_map(function($col,$val){
                    return $col.' = '.$val;
                },$cols,$vals);
                $req = 'UPDATE '.$infos['Table'].' SET '.implode(',',$couples).' WHERE '.$where;
                break;
            case 'PUT':
                if($where === '1') return false; // secu pour eviter de modifier la table
                $entity = Sys::getOneData('AbtelGestion',"Entite/Nom=".$this->entity);
                $fields = $entity->getChildren('Champ');

                $baseObj = array();
                foreach ($fields as $f){
                    if(in_array('`'.$f->Nom.'`',$cols)) continue;

                    $baseObj[] = '`'.$f->Nom.'` = '.(!empty($f->DefaultValue) ? (is_string($f->DefaultValue) ? '\''.$f->DefaultValue.'\'' : $f->DefaultValue): 'NULL');
                }
                $couples = array_map(function($col,$val){
                    return $col.' = '.$val;
                },$cols,$vals);
                $couples = array_merge($couples,$baseObj);

                $req = 'UPDATE '.$infos['Table'].' SET '.implode(',',$couples).' WHERE '.$where;
                break;
            case 'DELETE':
                if($where === '1') return false; // secu pour eviter de vider la table
                $req = 'DELETE FROM '.$infos['Table'].' WHERE '.$where;
                break;
        }

        return $req;
    }

}
