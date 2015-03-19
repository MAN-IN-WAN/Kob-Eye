<?php
class MasDocument extends genericClass {
	
	function __construct($Mod,$Tab) {
		genericClass::__construct($Mod,$Tab);
	}
	
	function Save($mode=false) {
		$new = $this->Id == '';
		parent::Save();
		$usrs = array();
		$uids = array();
		$tmp = 'Categorie.CategorieId';
		if(! $mode && ! count($this->$tmp)) $err = array(array("message"=>"Catégorie non défini."));
		$tmp = 'Role.RoleId';
		if(! count($this->$tmp)) $err = array(array("message"=>"Rôle non défini."));
		if($err) return array(array($new ? 'add' : 'edit', 0, $this->Id, 'Mas', 'Document', '', '', $err, null));

		$this->resetChilds('Role');
		foreach($this->$tmp as $rid) {
			$this->addChild('Role', $rid);
			$this->roleUsers($usrs, $uids, $rid);
		}
//		if($new && count($uids)) 
//			AlertUser::addAlert('Nouveau document : '.$this->Titre,'','Mas','Document',$this->Id,$uids,'','');
		$this->addUser($usrs); //, $new);
		return array(array($new ? 'add' : 'edit', 1, $this->Id, 'Mas', 'Document', '', '', null, null));
	}


	function SaveAll() {
		$tmp = 'Role.RoleId';
		$doc = Sys::getData('Mas', 'Document:NOVIEW');
		foreach($doc as $d) {
			$ar = array();
			$rs = $d->getChildren('Role');
			foreach($rs as $r) $ar[] = $r->Id;
			$d->$tmp = $ar;
			$d->Save(true);
		}
		$this->checkAlert();
		$s = 'Les documents ont été réaffectés';
		return WebService::WSStatus('method', 0, '', '', '', '', '', array(array('message'=>$s)), null);
	}


	
	function Delete() {
		$uds = $this->getChildren('UserDocument');
		foreach($uds as $ud) $ud->Delete();
		$sts = array();
		return parent::Delete();
	}
	
	private function checkAlert() {
		$sql = "select u.DocumentId,u.UserId,d.Titre,sum(au.Id) as s
from `##_Mas-UserDocument` u
inner join `loc-Mas-Document` d on d.Id=u.DocumentId
left join `##_Systeme-Alert` a
on a.AlertModule='Mas' and a.AlertObject='Document' and a.ObjectId=u.DocumentId
left join `##_Systeme-AlertUser` au on au.AlertId=a.Id and au.uid=u.UserId
where u.DateConsultation is null
group by u.DocumentId,u.UserId
having s is null";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(! $pdo) return null;
		$rec = $pdo->fetchALL(PDO::FETCH_ASSOC);
		foreach($rec as $r) {
			AlertUser::addAlert('Nouveau document : '.$r['Titre'],'','Mas','Document',$r['DocumentId'],array($r['UserId']),'','');
		}
/*
		$sql = "select distinct u.DocumentId,d.Titre
from `##_Mas-UserDocument` u
inner join `##_Mas-Document` d on d.Id=u.DocumentId
left join `##_Systeme-Alert` a
on a.AlertModule='Mas' and a.AlertObject='Document' and a.ObjectId=u.DocumentId
where u.DateConsultation is null and a.Id is null";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(! $pdo) return null;
		$rec = $pdo->fetchALL(PDO::FETCH_ASSOC);
		foreach($rec as $r) {
			$a = genericClass::createInstance('Systeme', 'Alert');
			$a->Title = 'Nouveau document : '.$r['Titre'];
			$a->Date = time();
			$a->Tag = '';
			$a->AlertModule = 'Mas';
			$a->AlertObject = 'Document';
			$a->ObjectId = $r['DocumentId'];
			$a->Icon = '';
			$a->Author = Sys::$User->Nom;
			$a->UserId = Sys::$User->Id;
			$a->Save();
		}
		$time = microtime(true);
		$sql = "select u.UserId,a.Id
from `##_Mas-UserDocument` u
left join `##_Systeme-Alert` a
on a.AlertModule='Mas' and a.AlertObject='Document' and a.ObjectId=u.DocumentId
left join `##_Systeme-AlertUser` au on au.AlertId=a.Id and au.uid=u.UserId
where u.DateConsultation is null and au.Id is null";
		$sql = str_replace('##_', MAIN_DB_PREFIX, $sql);
		$pdo = $GLOBALS['Systeme']->Db[0]->query($sql);
		if(! $pdo) return null;
		$rec = $pdo->fetchALL(PDO::FETCH_ASSOC);
		foreach($rec as $r) {
			$u = genericClass::createInstance('Systeme', 'AlertUser');
			$u->Time = $time;
			$u->AlertId = $r['Id'];
			$u->uid = $r['UserId'];
			$u->gid = 0;
			$u->umod = 7;
			$u->gmod = 1;
			$u->omod = 1;
			$u->Save();
			$rec = Sys::$Modules['Systeme']->callData('AlertTime', false, 0, 1);
			if(is_array($rec) && count($rec)) $t = genericClass::createInstance('Systeme', $rec[0]);
			else $t = genericClass::createInstance('Systeme', 'AlertTime');
			$t->Time = $time;
			$t->uid = $r['UserId'];
			$t->gid = 0;
			$t->umod = 7;
			$t->gmod = 1;
			$t->omod = 1;
			$t->Save();
		}
*/
	}
	
	private function roleUsers(&$usrs, &$uids, $rid) {
		$grs = Sys::$Modules['Systeme']->callData("Role/$rid/Group/*/Group", false, 0, 0, '', '', 'Id');
		foreach($grs as $gr) {
			$uss = Sys::getData('Systeme', 'Group/'.$gr['Id'].'/User');
			foreach($uss as $us) {
				$id = $us->Id;
				foreach($usrs as $usr) {
					if($id == $usr->Id) {
						$id = '';
						break;
					}
				}
				if($id) {
					$usrs[] = $us;
					$uids[] = $id;
				}
			}
		}
	}
	
	private function addUser(&$usrs) {
		$udocs = $this->getChildren('UserDocument');
		foreach($usrs as $usr) {
			$id = $usr->Id;
			foreach($udocs as $udoc) {
				$us = $udoc->getParents('User');
				if(count($us) && $id == $us[0]->Id) {
					$id = '';
					$udoc->Id = '';
					break;
				}
			}
			if($id) {
				$ud = genericClass::createInstance('Mas', 'UserDocument');
				$ud->addParent($this);
				$ud->addParent($usr);
				$ud->DateConsultation = null;
				$ud->Save();
				AlertUser::addAlert('Nouveau document : '.$this->Titre,'','Mas','Document',$this->Id,array($usr->Id),'','');
			}
		}
		foreach($udocs as $udoc) {
			if($udoc->Id) {
				$udoc->Delete();
			}
		}
	}
	
	

	function GetDocuments($id, $offset, $limit, $sort, $order, $filter) {
		$rec = $this->getCat('', $filter);
		$c = count($rec);
		return WebService::WSData('',0,$c,$c,'','','','','',$rec);
	}
	
	private function getCat($id, $filter) {
		$uid = Sys::$User->Id;
		$aut = Sys::$User->ExternalAuth == "1" ? 1 :0;
		$items = array();
		$req = 'Categorie';
		if($id) $req .= "/$id/Categorie";
		$cat = Sys::getData('Mas', $req, 0, 999);
		foreach($cat as $c) {
			$cid = $c->Id;
			$chl = $this->getCat($cid, $filter);
			$calert = '';
			foreach($chl as $ch) {
				if($ch['AlertIcon']) {
					if($calert != 'alertIcon') $calert = $ch['AlertIcon'];
				}
			}
			$dat = time();
			$req = "Categorie/$cid/Document:DocumentUser/userCreate=$uid+(!(!DateExpiration!!+DateExpiration>$dat!)&UserDocument1.UserId=$uid!)";
			if($filter) $req .= "&$filter";
			$doc = Sys::getData('Mas', $req, 0, 9999, 'ASC','Id');
			$rid = '';
			foreach($doc as $d) {
				if($rid == $d->Id) continue;
				$rid = $d->Id;
				
				if($d->userCreate == $uid) {
					$alert = 'infoIcon';
					if($calert != 'alertIcon') $calert = $alert;
				}
				elseif(! $d->DateConsultation) $calert = $alert = 'alertIcon';
				else $alert = '';
				
				$chl[] = array('Id'=>$d->Id,'Titre'=>$d->Titre,'DatePublication'=>$d->DatePublication,
						'DateConsultation'=>$d->DateConsultation,'Titre_ToolTip'=>$d->Description,
						'AlertIcon'=>$alert,'module'=>'Mas','objectClass'=>'Document','write'=>$aut);
			}
			$items[] = array('Id'=>$cid,'Categorie'=>$c->Categorie,'children'=>$chl,
						'AlertIcon'=>$calert,'module'=>'Mas','objectClass'=>'Categorie','write'=>$aut);
		}
		return $items;
	}
	
	function ViewDocument($id) {
		$doc = genericClass::createInstance('Mas', 'Document');
		$doc->initFromId($id);
		$res = array();
		$res['printFiles'] = array($doc->Document);
		$uid = Sys::$User->Id;
		$ud = Sys::getData('Mas', "UserDocument/DocumentId=$id&UserId=$uid");
		if(count($ud)) {
			$ud = $ud[0];
			$ud->DateConsultation = time();
			$ud->Save();
		}
		return WebService::WSStatus('edit', 1, '', 'Mas', 'Document', '', '', null, $res);
	}
	
}